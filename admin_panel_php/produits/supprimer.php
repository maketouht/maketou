<?php
$id = (int)$_GET['id'];
$img = $pdo->prepare("SELECT image FROM produits WHERE id = ?");
$img->execute([$id]);
$image = $img->fetchColumn();
if ($image && file_exists(__DIR__ . '/../images/' . $image)) {
    unlink(__DIR__ . '/../images/' . $image);
}
$pdo->prepare("DELETE FROM produits WHERE id = ?")->execute([$id]);
header("Location: index.php");
exit;
?>