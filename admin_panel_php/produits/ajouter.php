<?php
include '../config/db.php';
include '../includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   $imageName = null;

if (!empty($_FILES['image']['name'])) {
    $imageName = time() . '_' . basename($_FILES['image']['name']);

    // Chemin absolu sécurisé
    $uploadDir = __DIR__ . '/../images/';
    $uploadPath = $uploadDir . $imageName;

    // Vérifie l’extension
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    $ext = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed)) {
        echo "Format d’image non autorisé.";
        exit;
    }

    // Déplace le fichier
    if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
        echo "Erreur lors du téléchargement de l’image.";
        exit;
    }
}


    $nom = $_POST['nom'] ?? '';
    $prix = $_POST['prix'] ?? '';
    $description = $_POST['description'] ?? '';
    $image = $imageName;
    $categorie_id = $_POST['categorie_id'] ?? '';
    $stock = $_POST['stock'] ?? 0;
    $actif = $_POST['actif'] ?? 1;

    $sql = "INSERT INTO produits (nom, prix, description, image, categorie_id, stock, actif) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $ok = $stmt->execute([$nom, $prix, $description, $image, $categorie_id, $stock, $actif]);
    if ($ok) {
        echo '<div class="alert alert-success">Produit ajouté avec succès !</div>';
    } else {
        echo '<div class="alert alert-danger">Erreur lors de l\'ajout du produit.</div>';
    }
}
?>
<h2>Ajouter un produit</h2>
<form method="post" enctype="multipart/form-data">
    <div class="mb-3">
        <label class="form-label">Nom</label>
        <input type="text" name="nom" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Prix</label>
        <input type="number" step="0.01" name="prix" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Image</label>
       <input type="file" name="image" accept="image/*" class="form-control" />
        <small class="form-text text-muted">Téléchargez une image pour le produit.</small>
    </div>
    <div class="mb-3">
        <label class="form-label">Catégorie</label>
        <select name="categorie_id" class="form-control">
            <option value="1">Vêtements</option>
            <option value="2">Chaussures</option>
            <option value="3">Accessoires</option>
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label">Stock disponible</label>
        <input type="number" name="stock" value="0" min="0" class="form-control">
    </div>
    <div class="mb-3">
        <label class="form-label">Actif</label>
        <select name="actif" class="form-control">
            <option value="1">Oui</option>
            <option value="0">Non</option>
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control"></textarea>
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
    <button type="submit" class="btn btn-success">Enregistrer</button>
</form>
<?php include '../includes/footer.php'; ?>