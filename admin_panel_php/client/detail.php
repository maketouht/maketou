<?php
// detail.php - Fiche produit dynamique pour MaketOu
require_once '../../admin_panel_php/config/db.php';

// Vérifier si l'ID du produit est passé en GET
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<h2>Produit introuvable.</h2>";
    exit;
}

$id = intval($_GET['id']);

// Récupérer les infos du produit
$stmt = $pdo->prepare("SELECT * FROM produits WHERE id = ?");
$stmt->execute([$id]);
$produit = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$produit) {
    echo "<h2>Produit introuvable.</h2>";
    exit;
}

// Récupérer les produits similaires (même catégorie, exclure le produit courant)
$stmt_similaires = $pdo->prepare("SELECT * FROM produits WHERE categorie_id = ? AND id != ? LIMIT 4");
$stmt_similaires->execute([$produit['categorie_id'], $id]);
$similaires = $stmt_similaires->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?= htmlspecialchars($produit['nom']) ?> - MaketOu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;900&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <style>
        body {
            background: #f5f6fa;
            min-height: 100vh;
            font-family: 'Roboto', Arial, sans-serif;
        }
        .header-pro {
            background: #fff;
            border-bottom: 1px solid #e5e7eb;
            box-shadow: 0 2px 8px 0 #e5e7eb33;
        }
        .nav-link {
            color: #222;
            font-weight: 500;
            padding: 0 12px;
            transition: color 0.2s;
        }
        .nav-link:hover {
            color: #2563eb;
        }
        .btn-main {
            background: #2563eb;
            color: #fff;
            font-weight: 500;
            border-radius: 6px;
            padding: 0.75rem 1.5rem;
            transition: background 0.2s;
            box-shadow: 0 2px 8px 0 #2563eb11;
        }
        .btn-main:hover {
            background: #174ea6;
        }
        .card-pro {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 12px 0 #e5e7eb33;
            transition: box-shadow 0.2s, transform 0.2s;
        }
        .card-pro:hover {
            box-shadow: 0 8px 32px 0 #2563eb22;
            transform: translateY(-4px) scale(1.02);
        }
        .product-title {
            font-size: 1.1rem;
            font-weight: 500;
            color: #222;
        }
        .product-price {
            color: #2563eb;
            font-size: 1.2rem;
            font-weight: 700;
        }
        .badge-promo {
            background: #ff4747;
            color: #fff;
            font-size: 0.85rem;
            font-weight: 600;
            border-radius: 4px;
            padding: 2px 8px;
            position: absolute;
            top: 12px;
            left: 12px;
        }
        .footer-pro {
            background: #fff;
            border-top: 1px solid #e5e7eb;
            color: #888;
            font-size: 0.95rem;
        }
    </style>
</head>
<body>
    <!-- HEADER -->
    <header class="header-pro sticky top-0 z-30">
        <div class="max-w-7xl mx-auto flex justify-between items-center py-3 px-6">
            <div class="text-2xl font-bold text-blue-700 tracking-tight">MaketOu</div>
            <nav class="hidden md:flex items-center space-x-2">
                <a href="index.php" class="nav-link">Accueil</a>
                <a href="shop.html" class="nav-link">Boutique</a>
                <a href="contact.html" class="nav-link">Contact</a>
            </nav>
            <div class="flex items-center space-x-3">
                <a href="login.html" class="nav-link">Connexion</a>
                <a href="panier.php" class="nav-link">Panier (<span id="cart-count">0</span>)</a>
            </div>
        </div>
    </header>
    <!-- BARRE DE RECHERCHE -->
    <div class="py-6">
        <div class="max-w-2xl mx-auto flex items-center" style="border:1px solid #e5e7eb; border-radius:6px; background:#fff; box-shadow:0 2px 8px 0 #e5e7eb22; padding:0.5rem 1rem;">
            <input id="search-input" type="text" placeholder="Rechercher un produit, une catégorie..." style="border:none; outline:none; flex:1; font-size:1rem; background:transparent;" />
            <button id="search-btn" style="background:none; border:none; color:#2563eb; font-size:1.2rem; cursor:pointer;"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="22" height="22"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z" /></svg></button>
        </div>
        <div id="search-results" class="max-w-2xl mx-auto bg-white border rounded mt-2 hidden"></div>
    </div>
    <!-- DETAIL PRODUIT -->
    <main class="max-w-7xl mx-auto py-10 px-4 grid grid-cols-1 md:grid-cols-2 gap-10">
        <div class="card-pro p-8 relative">
            <img src="/admin_panel_php/images/<?= htmlspecialchars($produit['image']) ?>" alt="<?= htmlspecialchars($produit['nom']) ?>" class="w-full h-96 object-cover rounded-md mb-4">
            <?php if (!empty($produit['promo'])): ?>
            <span class="badge-promo">-<?= (int)$produit['promo'] ?>%</span>
            <?php endif; ?>
        </div>
        <div class="card-pro p-8 flex flex-col justify-between">
            <h1 class="product-title mb-2" style="font-size:1.5rem; font-weight:700;"><?= htmlspecialchars($produit['nom']) ?></h1>
            <p class="product-price mb-4"><?= number_format($produit['prix'], 2, ',', ' ') ?> €</p>
            <a href="#" class="btn-main w-full mb-4">Ajouter au Panier</a>
            <div class="text-gray-700 mb-4"><?= nl2br(htmlspecialchars($produit['description'])) ?></div>
        </div>
    </main>
    <!-- PRODUITS SIMILAIRES -->
    <section class="max-w-7xl mx-auto py-12 px-4">
        <h2 class="text-2xl font-bold mb-8 text-gray-800">Produits Similaires</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-8">
            <?php foreach ($similaires as $sim): ?>
            <div class="card-pro p-6 text-center relative">
                <?php if (!empty($sim['promo'])): ?>
                <span class="badge-promo">-<?= (int)$sim['promo'] ?>%</span>
                <?php endif; ?>
                <a href="product.php?id=<?= $sim['id'] ?>">
                    <img src="/admin_panel_php/images/<?= htmlspecialchars($sim['image']) ?>" alt="<?= htmlspecialchars($sim['nom']) ?>" class="mb-4 w-full h-48 object-cover rounded-md">
                    <h3 class="product-title mb-2"><?= htmlspecialchars($sim['nom']) ?></h3>
                    <p class="product-price mb-2"><?= number_format($sim['prix'], 2) ?> $</p>
                </a>
                <a href="#" class="btn-main w-full mt-2">Ajouter au Panier</a>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
    <!-- FOOTER -->
    <footer class="footer-pro py-10 px-4 mt-12">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-4">
            <div>
                <span class="font-bold text-gray-700">MaketOu</span> &copy; 2025. Tous droits réservés.
            </div>
            <div class="flex space-x-4">
                <a href="index.php" class="nav-link">Accueil</a>
                <a href="shop.html" class="nav-link">Boutique</a>
                <a href="contact.html" class="nav-link">Contact</a>
                <a href="login.html" class="nav-link">Connexion</a>
            </div>
        </div>
    </footer>
</body>
</html>
