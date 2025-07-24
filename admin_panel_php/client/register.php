<?php
require_once '../config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = $_POST['regNom']
    $telephone = $_POST['regTelephone'];
    $email = $_POST['regEmail'];
    $password = $_POST['regPassword'];
    $confirm = $_POST['regPassword2'];

    if ($password !== $confirm) {
        echo "Les mots de passe ne correspondent pas.";
        exit;
    }

    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->rowCount() > 0) {
        echo "Email déjà utilisé.";
        exit;
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, email, telephone, mot_de_passe, role, date_creation) VALUES (?, ?, ?, ?, NOW())");
    $stmt->execute(['Utilisateur', $email, $hash, 'client']);

    echo "success";
}
?>
