<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$cart_count = 0;
if (isset($_SESSION['panier']) && is_array($_SESSION['panier'])) {
    foreach ($_SESSION['panier'] as $item) {
        if (isset($item['quantite'])) {
            $cart_count += (int)$item['quantite'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>MaketOu - Boutique en ligne</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;900&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js"></script>
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
        .search-bar {
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            background: #fff;
            box-shadow: 0 2px 8px 0 #e5e7eb22;
            display: flex;
            align-items: center;
            padding: 0.5rem 1rem;
        }
        .search-bar input {
            border: none;
            outline: none;
            flex: 1;
            font-size: 1rem;
            background: transparent;
        }
        .search-bar button {
            background: none;
            border: none;
            color: #2563eb;
            font-size: 1.2rem;
            cursor: pointer;
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
        .footer-pro {
            background: #fff;
            border-top: 1px solid #e5e7eb;
            color: #888;
            font-size: 0.95rem;
        }
        .category-pro {
            background: #f3f4f6;
            border-radius: 10px;
            box-shadow: 0 2px 8px 0 #e5e7eb22;
            transition: box-shadow 0.2s, transform 0.2s;
        }
        .category-pro:hover {
            box-shadow: 0 8px 32px 0 #2563eb11;
            transform: translateY(-2px) scale(1.01);
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
    </style>
</head>

<body>
  <?php include 'header.php'; ?>
    <!-- BARRE DE RECHERCHE -->
    <div class="py-6">
        <div class="max-w-2xl mx-auto search-bar">
            <input id="search-input" type="text" placeholder="Rechercher un produit, une catégorie..." />
            <button id="search-btn"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="22" height="22"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z" /></svg></button>
        </div>
        <div id="search-results" class="max-w-2xl mx-auto bg-white border rounded mt-2 hidden"></div>
    </div>
    <!-- SWIPER SLIDER -->
    <section class="relative max-w-7xl mx-auto rounded-xl overflow-hidden shadow-xl mb-8">
        <div class="swiper mySwiper">
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                    <img src="/admin_panel_php/images/b001.jpg" class="w-full h-80 object-cover" alt="Bannière 1">
                </div>
                <div class="swiper-slide">
                    <img src="/admin_panel_php/images/b002.jpg" class="w-full h-80 object-cover" alt="Bannière 2">
                </div>
                <div class="swiper-slide">
                    <img src="/admin_panel_php/images/b003.jpg" class="w-full h-80 object-cover" alt="Bannière 3">
                </div>
            </div>
            <div class="swiper-pagination"></div>
        </div>
    </section>
    <!-- SERVICES BADGES -->
    <section class="max-w-7xl mx-auto grid grid-cols-2 md:grid-cols-4 gap-4 py-8 px-4">
        <div class="category-pro p-4 text-center">
            <span class="text-2xl">🚚</span>
            <h4 class="font-semibold mt-2 mb-1 text-blue-700">Livraison Gratuite</h4>
            <p class="text-xs text-gray-500">Livraison offerte sur toutes vos commandes.</p>
        </div>
        <div class="category-pro p-4 text-center">
            <span class="text-2xl">🔒</span>
            <h4 class="font-semibold mt-2 mb-1 text-blue-700">Paiement Sécurisé</h4>
            <p class="text-xs text-gray-500">Paiements protégés par cryptage avancé.</p>
        </div>
        <div class="category-pro p-4 text-center">
            <span class="text-2xl">💸</span>
            <h4 class="font-semibold mt-2 mb-1 text-blue-700">Garantie Remboursement</h4>
            <p class="text-xs text-gray-500">Satisfait ou remboursé sous 30 jours.</p>
        </div>
        <div class="category-pro p-4 text-center">
            <span class="text-2xl">💬</span>
            <h4 class="font-semibold mt-2 mb-1 text-blue-700">Support 24/7</h4>
            <p class="text-xs text-gray-500">Équipe disponible à tout moment.</p>
        </div>
    </section>
    <!-- PROMO CATEGORIES -->
    <section class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-6 py-8 px-4">
        <div class="category-pro p-6 text-center relative">
            <img src="/admin_panel_php/images/col1.jpg" alt="Style Femme" class="mx-auto mb-4 w-full h-40 object-cover rounded-md">
            <span class="badge-promo">-70%</span>
            <h3 class="text-lg font-bold mb-2">Style Femme</h3>
            <a href="shop.html" class="btn-main w-full block mt-2">Acheter Maintenant</a>
        </div>
        <div class="category-pro p-6 text-center relative">
            <img src="/admin_panel_php/images/col2.jpg" alt="Sacs à Main" class="mx-auto mb-4 w-full h-40 object-cover rounded-md">
            <span class="badge-promo">-25%</span>
            <h3 class="text-lg font-bold mb-2">Sacs à Main</h3>
            <a href="shop.html" class="btn-main w-full block mt-2">Acheter Maintenant</a>
        </div>
        <div class="category-pro p-6 text-center relative">
            <img src="/admin_panel_php/images/col3.jpg" alt="Montres" class="mx-auto mb-4 w-full h-40 object-cover rounded-md">
            <span class="badge-promo">-45%</span>
            <h3 class="text-lg font-bold mb-2">Montres</h3>
            <a href="shop.html" class="btn-main w-full block mt-2">Acheter Maintenant</a>
        </div>
    </section>
    <!-- FEATURED PRODUCTS -->
    <section class="max-w-7xl mx-auto py-8 px-4">
        <h2 class="text-2xl font-bold mb-6 text-gray-800">Produits Vedettes</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <?php
            require_once '../../admin_panel_php/config/db.php';
            $stmt = $pdo->query("SELECT * FROM produits WHERE actif = 1 ORDER BY id DESC");
            $produits = $stmt->fetchAll();
            foreach ($produits as $produit): ?>
            <div class="card-pro p-4 text-center relative">
                <?php if (!empty($produit['promo'])): ?>
                <span class="badge-promo">-<?= (int)$produit['promo'] ?>%</span>
                <?php endif; ?>
                <a href="product.php?id=<?= $produit['id'] ?>">
                    <img src="/admin_panel_php/images/<?= htmlspecialchars($produit['image']) ?>" alt="<?= htmlspecialchars($produit['nom']) ?>" class="mb-4 w-full h-48 object-cover rounded-md">
                    <h3 class="product-title mb-2"><?= htmlspecialchars($produit['nom']) ?></h3>
                    <p class="product-price mb-2"><?= number_format($produit['prix'], 2) ?> $</p>
                </a>
                <button type="button" class="btn-main w-full mt-2 add-to-cart-btn" data-id="<?= $produit['id'] ?>"> Ajouter au panier </button>




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
    <script>
document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.getAttribute('data-id');
        fetch('ajouter_Panier.php?id=' + id)
            .then(response => response.json())
            .then(data => {
                if (data.cart_count !== undefined) {
                    document.getElementById('cart-count').textContent = data.cart_count;
                }
                // Optionnel : feedback visuel
                btn.textContent = 'Ajouté !';
                btn.classList.add('bg-green-500');
                setTimeout(() => {
                    btn.textContent = 'Ajouter au panier';
                    btn.classList.remove('bg-green-500');
                }, 1200);
            });
    });
});
</script>
</body>
</html>
