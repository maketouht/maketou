<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once('../config/db.php');

if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = [];
}

// Mise à jour des quantités
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['quantite'] as $id => $qte) {
        $_SESSION['panier'][$id]['quantite'] = max(1, (int)$qte);
    }
}

$total = 0;


?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mon Panier - MaketOu</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;900&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <style>
        body { background: #f5f6fa; font-family: 'Roboto', Arial, sans-serif; }
        .header-pro { background: #fff; border-bottom: 1px solid #e5e7eb; box-shadow: 0 2px 8px 0 #e5e7eb33; }
        .footer-pro { background: #fff; border-top: 1px solid #e5e7eb; color: #888; font-size: 0.95rem; }
        .btn-main { background: #2563eb; color: #fff; font-weight: 500; border-radius: 6px; padding: 0.5rem 1.2rem; transition: background 0.2s; border: none; cursor: pointer; }
        .btn-main:hover { background: #174ea6; }
        .btn-danger { background: #ef4444; color: #fff; border-radius: 6px; padding: 0.4rem 1rem; border: none; cursor: pointer; }
        .btn-danger:hover { background: #b91c1c; }
        .btn-warning { background: #f59e42; color: #fff; border-radius: 6px; padding: 0.4rem 1rem; border: none; cursor: pointer; }
        .btn-warning:hover { background: #b45309; }
        .btn-success { background: #22c55e; color: #fff; border-radius: 6px; padding: 0.4rem 1rem; border: none; cursor: pointer; }
        .btn-success:hover { background: #15803d; }
        .qty-input { width: 60px; text-align: center; border: 1px solid #e5e7eb; border-radius: 4px; padding: 0.2rem 0.5rem; font-size: 1rem; }
        .cart-table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 12px 0 #e5e7eb33; }
        .cart-table th, .cart-table td { text-align: center; padding: 12px; }
        .cart-table th { background: #f3f4f6; color: #222; font-weight: 600; }
        .cart-table tr { border-bottom: 1px solid #e5e7eb; }
        .cart-table tr:last-child { border-bottom: none; }
        .cart-img { border-radius: 8px; box-shadow: 0 2px 8px 0 #e5e7eb22; }
        @media (max-width: 700px) {
            .cart-table th, .cart-table td { padding: 6px; font-size: 0.95rem; }
            .cart-table { font-size: 0.95rem; }
        }
        .actions-bar { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 18px; }
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
<?php include 'header.php'; ?>
    <main class="max-w-3xl mx-auto bg-white rounded-xl shadow-lg p-8 mt-8">
        <h2 class="text-2xl font-bold mb-6 text-gray-800">Mon Panier</h2>
        <form method="post">
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Nom</th>
                        <th>Prix</th>
                        <th>Quantité</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($_SESSION['panier'] as $id => $item): 
                        if (!isset($item['prix'], $item['quantite'], $item['image'], $item['nom'])) continue; // Sécurité pour éviter les erreurs
                        $ligneTotal = $item['prix'] * $item['quantite'];
                        $total += $ligneTotal;
                    ?>
                    <tr>
                        <td><img src="../images/<?= htmlspecialchars($item['image']) ?>" width="60" class="cart-img"></td>
                        <td><?= htmlspecialchars($item['nom']) ?></td>
                        <td><?= number_format($item['prix'], 2) ?> $</td>
                        <td>
                            <input type="number" name="quantite[<?= $id ?>]" value="<?= (int)$item['quantite'] ?>" min="1" class="qty-input">
                        </td>
                        <td><?= number_format($ligneTotal, 2) ?> $</td>
                        <td>
                            <a href="supprimer_du_panier.php?id=<?= urlencode($id) ?>" class="btn-danger">Supprimer</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="flex flex-col md:flex-row md:justify-between items-center gap-4 mt-6">
                <h4 class="text-lg font-bold">Total : <?= number_format($total, 2) ?> $</h4>
                <div class="actions-bar">
                    <button type="submit" class="btn-main">Mettre à jour le panier</button>
                    <a href="vider_panier.php" class="btn-warning">Vider le panier</a>
                        <button type="submit" formaction="valider_commande.php"class="btn-gradient px-6 py-3 rounded-xl mt-4">
                             Commander
                        </button>
                </div>
            </div>
        </form>
    </main>
    <?php include 'footer.html'; ?>
</body>
</html>
