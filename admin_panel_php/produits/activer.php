<?php
require_once '../config/db.php';

if (isset($_GET['id']) && isset($_GET['etat'])) {
    $id = (int)$_GET['id'];
    $etat = (int)$_GET['etat'];

    $stmt = $pdo->prepare("UPDATE produits SET actif = ? WHERE id = ?");
    $stmt->execute([$etat, $id]);

    header("Location: index.php");
    exit;
} else {
    echo "Requête invalide.";
}
