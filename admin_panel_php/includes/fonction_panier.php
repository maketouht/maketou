<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function ajouter_au_panier($produit_id, $quantite = 1) {
    if (!isset($_SESSION['panier'])) {
        $_SESSION['panier'] = [];
    }

    if (isset($_SESSION['panier'][$produit_id])) {
        $_SESSION['panier'][$produit_id] += $quantite;
    } else {
        $_SESSION['panier'][$produit_id] = $quantite;
    }
}

function retirer_du_panier($produit_id) {
    if (isset($_SESSION['panier'][$produit_id])) {
        unset($_SESSION['panier'][$produit_id]);
    }
}

function vider_panier() {
    unset($_SESSION['panier']);
}

function total_panier($pdo) {
    $total = 0;
    if (!empty($_SESSION['panier'])) {
        foreach ($_SESSION['panier'] as $id => $qte) {
            $stmt = $pdo->prepare("SELECT prix FROM produits WHERE id = ?");
            $stmt->execute([$id]);
            $prod = $stmt->fetch();
            $total += $prod['prix'] * $qte;
        }
    }
    return $total;
}
?>
