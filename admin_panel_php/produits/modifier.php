<?php
include '../config/db.php';
include '../includes/header.php';

// Vérifie si l'ID est présent
if (!isset($_GET['id'])) {
    echo "ID du produit manquant.";
    exit;
}

$id = (int)$_GET['id'];

// Récupère les infos actuelles du produit
$stmt = $pdo->prepare("SELECT * FROM produits WHERE id = ?");
$stmt->execute([$id]);
$produit = $stmt->fetch();

if (!$produit) {
    echo "Produit introuvable.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'] ?? '';
    $prix = $_POST['prix'] ?? '';
    $description = $_POST['description'] ?? '';
    $categorie_id = $_POST['categorie_id'] ?? '';
    $stock = $_POST['stock'] ?? 0;
    $actif = $_POST['actif'] ?? 1;
    $imageName = $produit['image']; // Image par défaut : l’ancienne

    // Si une nouvelle image est envoyée
    if (!empty($_FILES['image']['name'])) {
        $imageName = time() . '_' . basename($_FILES['image']['name']);
        $uploadDir = __DIR__ . '/../images/';
        $uploadPath = $uploadDir . $imageName;

        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $ext = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            echo "Format d’image non autorisé.";
            exit;
        }

        if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
            echo "Erreur lors du téléchargement de l’image.";
            exit;
        }

        // Supprime l'ancienne image si elle existe
        if (!empty($produit['image']) && file_exists($uploadDir . $produit['image'])) {
            unlink($uploadDir . $produit['image']);
        }
    }

    $sql = "UPDATE produits SET nom = ?, prix = ?, description = ?, image = ?, categorie_id = ?, stock = ?, actif = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $ok = $stmt->execute([$nom, $prix, $description, $imageName, $categorie_id, $stock, $actif, $id]);

    if ($ok) {
        echo '<div class="alert alert-success">Produit modifié avec succès.</div>';
        // Met à jour les données pour réaffichage
        $produit['nom'] = $nom;
        $produit['prix'] = $prix;
        $produit['description'] = $description;
        $produit['image'] = $imageName;
        $produit['categorie_id'] = $categorie_id;
        $produit['stock'] = $stock;
        $produit['actif'] = $actif;
    } else {
        echo '<div class="alert alert-danger">Erreur lors de la modification.</div>';
    }
}
?>

<h2>Modifier le produit</h2>

<form method="post" enctype="multipart/form-data">
    <div class="mb-3">
        <label class="form-label">Nom</label>
        <input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($produit['nom']) ?>" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Prix</label>
        <input type="number" step="0.01" name="prix" class="form-control" value="<?= $produit['prix'] ?>" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Image actuelle</label><br>
        <?php if ($produit['image']): ?>
            <img src="../images/<?= htmlspecialchars($produit['image']) ?>" width="100">
        <?php else: ?>
            <p>Aucune image</p>
        <?php endif; ?>
    </div>

    <div class="mb-3">
        <label class="form-label">Nouvelle image (facultatif)</label>
        <input type="file" name="image" accept="image/*" class="form-control">
    </div>

    <div class="mb-3">
        <label class="form-label">Catégorie</label>
        <select name="categorie_id" class="form-control">
            <option value="1" <?= $produit['categorie_id'] == 1 ? 'selected' : '' ?>>Vêtements</option>
            <option value="2" <?= $produit['categorie_id'] == 2 ? 'selected' : '' ?>>Chaussures</option>
            <option value="3" <?= $produit['categorie_id'] == 3 ? 'selected' : '' ?>>Accessoires</option>
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">Stock</label>
        <input type="number" name="stock" class="form-control" value="<?= $produit['stock'] ?>">
    </div>

    <div class="mb-3">
        <label class="form-label">Actif</label>
        <select name="actif" class="form-control">
            <option value="1" <?= $produit['actif'] == 1 ? 'selected' : '' ?>>Oui</option>
            <option value="0" <?= $produit['actif'] == 0 ? 'selected' : '' ?>>Non</option>
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control"><?= htmlspecialchars($produit['description']) ?></textarea>
    </div>
<?PHP $errors = [];

if (empty($nom)) $errors[] = "Le nom est requis.";
if ($prix <= 0) $errors[] = "Le prix doit être supérieur à 0.";
if (!in_array($ext, $allowed)) $errors[] = "Format d’image non autorisé.";

if (!empty($errors)) {
    foreach ($errors as $err) {
        echo "<div class='alert alert-danger'>$err</div>";
    }
    exit;
}
?>
    <button type="submit" class="btn btn-success">Enregistrer les modifications</button>
</form>

<?php include '../includes/footer.php'; ?>
