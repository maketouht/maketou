<?php
include '../config/db.php';
include '../includes/header.php';

// Pagination
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Recherche
$search = $_GET['search'] ?? '';
$where = $search ? "WHERE nom LIKE :search" : "";
$params = $search ? [':search' => "%$search%"] : [];

// Nombre total de produits (pour pagination)
$countStmt = $pdo->prepare("SELECT COUNT(*) FROM produits $where");
$countStmt->execute($params);
$total = $countStmt->fetchColumn();
$pages = ceil($total / $limit);

// Récupérer les produits avec pagination
$sql = "SELECT * FROM produits $where ORDER BY id DESC LIMIT :limit OFFSET :offset";
$stmt = $pdo->prepare($sql);

// Bind des paramètres
if ($search) {
    $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
}
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

$stmt->execute();
$produits = $stmt->fetchAll();

// Statistiques Dashboard
$totalProduits = $pdo->query("SELECT COUNT(*) FROM produits")->fetchColumn();
$totalActifs = $pdo->query("SELECT COUNT(*) FROM produits WHERE actif = 1")->fetchColumn();
$totalStock = $pdo->query("SELECT SUM(stock) FROM produits")->fetchColumn();
?>
<div class="container py-5">
    <h2 class="mb-4 text-center text-primary">Gestion des Produits</h2>
    <div class="row mb-4">
        <div class="col-md-8">
            <form method="get" class="d-flex">
                <input type="text" name="search" placeholder="Recherche..." class="form-control me-2" value="<?= htmlspecialchars($search) ?>">
                <button class="btn btn-outline-primary" type="submit">Rechercher</button>
            </form>
        </div>
        <div class="col-md-4 text-end">
            <a href="ajouter.php" class="btn btn-success">+ Ajouter un produit</a>
        </div>
    </div>
    <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white fw-bold">Liste des produits</div>
        <div class="table-responsive">
            <table class="table table-bordered align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Prix</th>
                        <th>Description</th>
                        <th>Image</th>
                        <th>Catégorie</th>
                        <th>Stock</th>
                        <th>Actif</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($produits as $prod): ?>
                    <tr>
                        <td><?= $prod['id'] ?></td>
                        <td class="fw-bold text-primary"><?= htmlspecialchars($prod['nom']) ?></td>
                        <td><span class="badge bg-info text-dark"><?= number_format($prod['prix'], 2) ?> $</span></td>
                        <td><?= substr(htmlspecialchars($prod['description']), 0, 50) ?>...</td>
                        <td><img src="../images/<?= htmlspecialchars($prod['image']) ?>" width="60" class="rounded shadow"></td>
                        <?php
                            $catStmt = $pdo->prepare("SELECT nom FROM categories WHERE id = ?");
                            $catStmt->execute([$prod['categorie_id']]);
                            $categorie = $catStmt->fetchColumn();
                        ?>
                        <td><?= htmlspecialchars($categorie) ?></td>
                        <td><?= $prod['stock'] ?></td>
                        <td><?= $prod['actif'] ? '<span class="badge bg-success">Oui</span>' : '<span class="badge bg-danger">Non</span>' ?></td>
                        <td>
                            <?php if ($prod['actif']): ?>
                                <span class="badge bg-success text-white">Actif</span>
                            <?php else: ?>
                                <span class="badge bg-danger text-white">Inactif</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="modifier.php?id=<?= $prod['id'] ?>" class="btn btn-sm btn-warning mb-1">Modifier</a>
                            <a href="supprimer.php?id=<?= $prod['id'] ?>" class="btn btn-sm btn-danger mb-1" onclick="return confirm('Supprimer ce produit ?');">Supprimer</a>
                            <a href="activer.php?id=<?= $prod['id'] ?>&etat=<?= $prod['actif'] ? 0 : 1 ?>" class="btn btn-sm <?= $prod['actif'] ? 'btn-secondary' : 'btn-success' ?> mb-1">
                                <?= $prod['actif'] ? 'Désactiver' : 'Activer' ?>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card text-center shadow">
                <div class="card-body">
                    <h5 class="card-title">Total produits</h5>
                    <p class="display-6 fw-bold text-primary"><?= $totalProduits ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center shadow">
                <div class="card-body">
                    <h5 class="card-title">Produits actifs</h5>
                    <p class="display-6 fw-bold text-success"><?= $totalActifs ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center shadow">
                <div class="card-body">
                    <h5 class="card-title">Stock total</h5>
                    <p class="display-6 fw-bold text-info"><?= $totalStock ?> unités</p>
                </div>
            </div>
        </div>
    </div>
    <nav class="mt-4">
        <ul class="pagination justify-content-center">
            <?php for ($i = 1; $i <= $pages; $i++): ?>
                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?><?= $search ? '&search=' . urlencode($search) : '' ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
</div>
<?php include '../includes/footer.php'; ?>
