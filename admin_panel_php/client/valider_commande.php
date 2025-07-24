<?php
session_start();
require_once '../config/db.php';

// Afficher les erreurs en dev
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Activer les exceptions PDO
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Vérifier la session utilisateur
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.html');
    exit;
}

// Charger le panier
$panier = $_SESSION['panier'] ?? [];
if (empty($panier)) {
    header('Location: panier.php?error=empty');
    exit;
}

// Liste des indicatifs pays
$areaCodes = [
    '+1'   => 'États-Unis/Canada (+1)',
    '+33'  => 'France (+33)',
    '+509' => 'Haïti (+509)',
    '+44'  => 'Royaume-Uni (+44)',
];

// 1. Récupérer nom & téléphone existants
$stmt = $pdo->prepare("
    SELECT nom, telephone
    FROM utilisateurs
    WHERE id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Pré-remplissage phone
$existingPhone    = $user['telephone'] ?? '';
$selectedAreaCode = '+509';
$existingRest     = '';
foreach ($areaCodes as $code => $_) {
    if (strpos($existingPhone, $code) === 0) {
        $selectedAreaCode = $code;
        $existingRest     = substr($existingPhone, strlen($code));
        break;
    }
}

// 2. Traitement du formulaire
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['valider_commande'])) {
    // Nom
    $nom    = trim(filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_STRING));
    if ($nom === '') {
        $errors[] = 'Le nom est requis.';
    }

    // Téléphone en deux parties
    $areaCodeRaw = filter_input(INPUT_POST, 'area_code', FILTER_SANITIZE_STRING);
    $restRaw     = filter_input(INPUT_POST, 'phone_rest', FILTER_SANITIZE_STRING);
    $rest        = preg_replace('/\D+/', '', $restRaw);

    if (!isset($areaCodes[$areaCodeRaw])) {
        $errors[] = 'Indicatif invalide.';
    }
    if ($areaCodeRaw === '+509' && !preg_match('/^\d{8}$/', $rest)) {
        $errors[] = 'Pour Haïti, 8 chiffres requis après +509.';
    }
    elseif ($areaCodeRaw !== '+509' && !preg_match('/^\d{4,14}$/', $rest)) {
        $errors[] = 'Numéro entre 4 et 14 chiffres requis.';
    }
    $telephone = $areaCodeRaw . $rest;

    // Adresse de livraison
    $adresseLivraison = trim(
        filter_input(INPUT_POST, 'adresse_livraison', FILTER_SANITIZE_STRING)
    );
    if ($adresseLivraison === '') {
        $errors[] = 'L’adresse de livraison est requise.';
    }

    // Afficher les erreurs si besoin
    if ($errors) {
        echo '<pre>Validation failed:', "\n";
        print_r($errors);
        echo '</pre>';
        exit;
    }

    // 3. Transaction : update + insert
    try {
        $pdo->beginTransaction();

        // 3.1 Mettre à jour nom & téléphone
        $upd = $pdo->prepare("
            UPDATE utilisateurs
            SET nom = ?, telephone = ?
            WHERE id = ?
        ");
        $upd->execute([$nom, $telephone, $_SESSION['user_id']]);

        // 3.2 Calcul du total + bonus
        $total = 0;
        foreach ($panier as $item) {
            $total += $item['prix'] * $item['quantite'];
        }
        $bonus = $total * 0.05;
        $total += $bonus;

        // 3.3 Insérer dans la table commandes (avec “s”)
      $insCmd = $pdo->prepare("
    INSERT INTO commandes
      (utilisateur_id, date_commande, statut, total, adresse_livraison, methode_paiement)
    VALUES (?, NOW(), 'en_attente', ?, ?, ?)
");
$insCmd->execute([
    $_SESSION['user_id'],
    $total,
    $adresseLivraison,
    $methodePaiement
]);

        $commandeId = $pdo->lastInsertId();

        // 3.4 Insérer les produits
        $insProd = $pdo->prepare("
            INSERT INTO commande_produits
              (commande_id, produit_id, quantite, prix_unitaire)
            VALUES (?, ?, ?, ?)
        ");
        foreach ($panier as $item) {
            $insProd->execute([
                $commandeId,
                $item['id'],
                $item['quantite'],
                $item['prix']
            ]);
        }

        // 3.5 Commit & cleanup
        unset($_SESSION['panier']);
        $pdo->commit();

        header("Location: commande_details.php?id={$commandeId}");
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        echo '<pre>PDOException: ', $e->getMessage(), '</pre>';
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Valider votre commande</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
<div class="bg-white shadow-md rounded p-6 w-full max-w-md">
    <h2 class="text-blue-700 text-lg font-semibold mb-4">
        Vérifiez et complétez vos informations
    </h2>

    <form method="post" novalidate>
        <div class="mb-4">
            <label class="block font-medium">Nom complet</label>
            <input type="text" name="nom" required
                   class="w-full border px-3 py-2 rounded"
                   value="<?= htmlspecialchars($_POST['nom'] ?? $user['nom'] ?? '') ?>">
        </div>

        <div class="mb-4">
            <label class="block font-medium">Indicatif pays</label>
            <select name="area_code" required class="w-full border px-3 py-2 rounded">
                <?php foreach ($areaCodes as $code => $label): ?>
                    <option value="<?= $code ?>"
                        <?= $code === $selectedAreaCode ? 'selected' : '' ?>>
                        <?= $label ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-4">
            <label class="block font-medium">Numéro (sans indicatif)</label>
            <input type="text" name="phone_rest" required
                   class="w-full border px-3 py-2 rounded"
                   value="<?= htmlspecialchars($existingRest) ?>">
        </div>

        <div class="mb-4">
            <label class="block font-medium">Adresse de livraison</label>
            <input type="text" name="adresse_livraison" required
                   class="w-full border px-3 py-2 rounded"
                   value="<?= htmlspecialchars($_POST['adresse_livraison'] ?? '') ?>">
        </div class="mb-4">
        <div>
            <label for="methode_paiement">Méthode de paiement :</label>
            <select name="methode_paiement" required>
            <option value="Cash on Delivery">Cash on Delivery</option>
            <option value="Carte de crédit">Carte de crédit</option>
            <option value="Mobile Money">Mobile Money</option>
            </select>
        </div>

        <button type="submit" name="valider_commande"
                class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">
            Valider la commande
        </button>
    </form>
</div>
</body>
</html>
