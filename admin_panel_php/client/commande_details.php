<?php
session_start();
require_once '../config/db.php';

// Vérification de connexion
if (!isset($_SESSION['utilisateur_id']) || empty($_SESSION['utilisateur_id'])) {
    header('Location: login.html');
    exit;
}

// ID de commande
$commande_id = $_GET['id'] ?? null;
if (!$commande_id) {
    echo "Commande introuvable.";
    exit;
}

// Récupération de la commande
$commandeStmt = $pdo->prepare("SELECT * FROM commandes WHERE id = ? AND utilisateur_id = ?");
$commandeStmt->execute([$commande_id, $_SESSION['utilisateur_id']]);
$commande = $commandeStmt->fetch();

if (!$commande) {
    echo "Cette commande n'existe pas ou ne vous appartient pas.";
    exit;
}

// Récupération des produits de la commande
$produitsStmt = $pdo->prepare("
    SELECT cp.quantite, cp.prix_unitaire, p.nom, p.image
    FROM commande_produits cp
    JOIN produits p ON cp.produit_id = p.id
    WHERE cp.commande_id = ?
");
$produitsStmt->execute([$commande_id]);
$produits = $produitsStmt->fetchAll();

// Récupération infos client
$clientStmt = $pdo->prepare("SELECT nom, email, telephone FROM utilisateurs WHERE id = ?");
$clientStmt->execute([$_SESSION['user_id']]);
$client = $clientStmt->fetch();

// Calcul du bonus 5%
$bonus = $commande['total'] * 0.05;
$total_avec_bonus = $commande['total'] + $bonus;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Détails de la commande</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 py-8 px-4">
    <div class="max-w-5xl mx-auto bg-white p-8 rounded shadow-lg space-y-8">

        <!-- Étape 1 : Produits commandés -->
        <section>
            <h2 class="text-2xl font-bold text-blue-700 mb-4">1. Produits commandés</h2>
            <?php foreach ($produits as $produit): ?>
                <div class="flex items-center border-b py-4">
                    <img src="../images/<?= htmlspecialchars($produit['image']) ?>" class="w-16 h-16 object-cover rounded mr-4" alt="">
                    <div>
                        <h3 class="font-semibold"><?= htmlspecialchars($produit['nom']) ?></h3>
                        <p>Quantité : <?= $produit['quantite'] ?> × <?= number_format($produit['prix_unitaire'], 2) ?> HTG</p>
                        <p>Total : <?= number_format($produit['quantite'] * $produit['prix_unitaire'], 2) ?> HTG</p>
                    </div>
                </div>
            <?php endforeach; ?>
        </section>

        <!-- Étape 2 : Infos Client & Livraison -->
        <section>
            <h2 class="text-2xl font-bold text-blue-700 mb-4">2. Informations du client & Livraison</h2>
            <p><strong>Nom :</strong> <?= htmlspecialchars($client['nom']) ?></p>
            <p><strong>Email :</strong> <?= htmlspecialchars($client['email']) ?></p>
            <p><strong>Téléphone :</strong> <?= htmlspecialchars($client['telephone']) ?></p>
            <p><strong>Adresse de livraison :</strong> <?= htmlspecialchars($commande['adresse_livraison']) ?></p>
        </section>

        <!-- Étape 3 : Méthode de paiement -->
        <section>
            <h2 class="text-2xl font-bold text-blue-700 mb-4">3. Paiement</h2>
            <p><strong>Méthode :</strong> <?= htmlspecialchars($commande['methode_paiement']) ?></p>
            <p><strong>Statut :</strong> <span class="text-green-600"><?= htmlspecialchars($commande['statut']) ?></span></p>
        </section>

        <!-- Étape 4 : Résumé & Bonus -->
        <section>
            <h2 class="text-2xl font-bold text-blue-700 mb-4">4. Confirmation & Résumé</h2>
            <p><strong>Date de commande :</strong> <?= date('d/m/Y H:i', strtotime($commande['date_commande'])) ?></p>
            <p><strong>Total de la commande :</strong> <?= number_format($commande['total'], 2) ?> HTG</p>
            <p><strong>Bonus +5% offert :</strong> <span class="text-green-700 font-bold">-<?= number_format($bonus, 2) ?> HTG</span></p>
            <p><strong>Total à payer :</strong> <span class="text-blue-800 font-bold"><?= number_format($total_avec_bonus, 2) ?> HTG</span></p>
        </section>

        <!-- Retour -->
        <div class="mt-6 text-center">
            <a href="commande.php" class="text-blue-600 hover:underline">← Retour à mes commandes</a>
        </div>
    </div>
</body>
</html>
