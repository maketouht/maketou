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
$isLogged = isset($_SESSION['utilisateur_id']) && !empty($_SESSION['utilisateur_id']);
$firstLetter = '';
if ($isLogged) {
    require_once '../config/db.php';
    $req = $pdo->prepare("SELECT nom FROM utilisateurs WHERE id = ?");
    $req->execute([$_SESSION['utilisateur_id']]);
    $user = $req->fetch();
    $firstLetter = strtoupper(substr($user['nom'], 0, 1));
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
        .glass {
            background: rgba(255,255,255,0.25);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.18);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border-radius: 1.5rem;
            border: 1px solid rgba(255,255,255,0.18);
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
            text-decoration: none;
        }
        .nav-link:hover {
            color: #2563eb;
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
    <?php if (!$isLogged): ?>
        <a href="login.html" class="nav-link">Connexion</a>
    <?php else: ?>
        <a href="profil.php" class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center text-lg font-bold">
            <?= $firstLetter ?>
        </a>
    <?php endif; ?>
<a href="panier.php" class="nav-link flex items-center gap-1">
    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path d="M7 18a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm10 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM2 2h2l3.6 7.59a1 1 0 0 0 .92.61h7.92a1 1 0 0 0 .92-.61L20 6H6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
    (<span id="cart-count"><?= $cart_count ?></span>)
</a>
</div>
        </div>
    </header>
