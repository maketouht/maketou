<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (isset($_SESSION['panier'][$id])) {
    unset($_SESSION['panier'][$id]);
}
header("Location: panier.php");
exit;
