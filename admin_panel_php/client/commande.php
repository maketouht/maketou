<?php
session_start();
require_once '../config/db.php';

if(!isset($_SESSION['utilisateur_id'])) {
    header("Location : login.html");
    exit();
}

$user_id = $_SESSION['utilisateur_id'];

//recuperation les commandes de l'utilisateur
$req  = $pdo->prepare("SELECT * from commandes WHERE utilisateur_id = ? ORDER BY date_commande DESC");
$req->execute([$user_id]);
$commandes = $req->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="ma-w-4xl mx-auto py-10">
        <h1 class="text3xl font-bold mb-6 text-blue-800">Mes commandes</h1>

        <?php if (count($commandes) > 0): ?>
            <div class="bg-white shadow-md rounded-xl p-6">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-blue-800">
                            <th class="pb-2">No Commande</th>
                            <th class="pb-2">Date</th>
                            <th class="pb-2">Total</th>
                            <th class="pb-2">Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($commandes as $cmd): ?>
                            <tr class="border-t text-gray-700 hover:bg-gray-50">
                                <td class="py-2"><?=htmlspecialchars($cmd['numero_commande']) ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($cmd['date_commande'])) ?></td>
                                <td>$<?= number_format($cmd['total'], 2) ?></td>
                                <td><?= htmlspecialchars($cmd['statut']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                    </tbody>
                </table>

            </div>
            <?php else: ?>
                <p class="text-center text-gray-500">vous n'avez passe aucune commande pour le moment.</p>
            <?php endif; ?>

            <div class="mt-6 text-center">
                <a href="index.php" class="text-blue-600 hover:underline">Retour a l'accueil</a>
            </div>
    </div>
</body>
</html>