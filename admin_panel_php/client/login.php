<?php
session_start();
require_once '../config/db.php'; 
// Sécurité basique
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['mot_de_passe']);

    // Requête SQL pour chercher l'utilisateur
    $req = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
    $req->execute([$email]);
    $user = $req->fetch();

    if ($user && password_verify($password, $user['mot_de_passe'])) {
        $_SESSION['utilisateur_id'] = $user['id'];
        echo "success";
    } else {
        echo "Email ou mot de passe incorrect.";
    }
}
?>
