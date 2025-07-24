<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['utilisateur_id'])) {
    header('Location: login.html');
    exit();
}

// Récupération de l'utilisateur
$req = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = ?");
$req->execute([$_SESSION['utilisateur_id']]);
$user = $req->fetch();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Profil</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        function toggleForm() {
            document.getElementById("editForm").classList.toggle("hidden");
        }
    </script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-white p-6 rounded-xl shadow-lg w-full max-w-lg">
        <!-- Affichage du profil -->
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-blue-600">Mon Profil</h2>
            <a href="logout.php" class="text-sm bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded">Déconnexion</a>
        </div>

        <!-- Infos utilisateur -->
        <div class="mb-6">
            <p><span class="font-semibold">Email :</span> <?= htmlspecialchars($user['email']) ?></p>
            <p><span class="font-semibold">Nom :</span> <?= htmlspecialchars($user['nom']) ?></p>
            <p><span class="font-semibold">Téléphone :</span> <?= htmlspecialchars($user['telephone']) ?></p>
            <p><span class="font-semibold">NO Client :</span> <?= htmlspecialchars($user['id']) ?></p>
            
            <!-- Tu peux ajouter d'autres infos ici -->
            <!-- <p><span class="font-semibold">Date d'inscription :</span> <?= $user['created_at'] ?? 'N/A' ?></p> -->
        </div>

        <!-- Bouton pour afficher le formulaire -->
        <button onclick="toggleForm()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded font-semibold mb-4">
            Modifier mon profil
        </button>

        <!-- Formulaire masqué -->
        <form id="editForm" class="hidden" method="POST">
            <div class="mb-3">
                <label for="new_email" class="block font-semibold">Nouvel email :</label>
                <input type="email" id="new_email" name="new_email" class="w-full border p-2 rounded" value="<?= htmlspecialchars($user['email']) ?>">
            </div>
            <div class="mb-3">
                <label for="new_password" class="block font-semibold">Nouveau mot de passe :</label>
                <input type="password" id="new_password" name="new_password" class="w-full border p-2 rounded">
            </div>
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded font-bold">
                Enregistrer les modifications
            </button>
        </form>

        <!-- Message -->
        <div class="mt-4 text-sm text-center text-green-700">
            <?php
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $new_email = trim($_POST['new_email']);
                $new_password = trim($_POST['new_password']);
                $updated = false;

                if (!empty($new_email)) {
                    $updateEmail = $pdo->prepare("UPDATE utilisateurs SET email = ? WHERE id = ?");
                    $updateEmail->execute([$new_email, $_SESSION['utilisateur_id']]);
                    $updated = true;
                }

                if (!empty($new_password)) {
                    $hashed = password_hash($new_password, PASSWORD_DEFAULT);
                    $updatePass = $pdo->prepare("UPDATE utilisateurs SET mot_de_passe = ? WHERE id = ?");
                    $updatePass->execute([$hashed, $_SESSION['utilisateur_id']]);
                    $updated = true;
                }

                echo $updated ? "Profil mis à jour avec succès. Rechargez la page." : "Aucune modification effectuée.";
            }
            ?>
        </div>
    </div>
</body>
</html>
