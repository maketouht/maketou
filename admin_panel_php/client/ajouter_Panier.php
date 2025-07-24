<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once '../config/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    $stmt = $pdo->prepare("SELECT id, nom, prix, image FROM produits WHERE id = ? AND actif = 1");
    $stmt->execute([$id]);
    $produit = $stmt->fetch();

    if ($produit) {
        if (!isset($_SESSION['panier'][$id])) {
            $_SESSION['panier'][$id] = [
                'nom' => $produit['nom'],
                'prix' => $produit['prix'],
                'image' => $produit['image'],
                'quantite' => 1
            ];
        } else {
            $_SESSION['panier'][$id]['quantite'] += 1;
        }
    }
}
header('Content-Type: application/json');
echo json_encode(['cart_count' => array_sum(array_column($_SESSION['panier'], 'quantite'))]);
exit;

?>