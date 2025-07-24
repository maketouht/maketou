<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'config/db.php';
echo "Connexion réussie à la base de données!";
?>