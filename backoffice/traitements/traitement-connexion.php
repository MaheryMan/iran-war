<?php
include '../includes/connexDB.php';
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($username) || empty($password)) {
    header('Location: /pages/connexion.php?error=' . rawurlencode('Veuillez remplir tous les champs.'));
    exit;
}

global $pdo;



$stmt = $pdo->prepare('SELECT * FROM utilisateurs WHERE username = ?');
$stmt->execute([$username]);
$result = $stmt->fetchAll();
if (count($result) === 0) {
    header('Location: /pages/connexion.php?error=' . rawurlencode('Nom d\'utilisateur ou mot de passe incorrect.'));
    exit;
}
$user = $result[0];
if (!password_verify($password, $user['password'])) {
    header('Location: /pages/connexion.php?error=' . rawurlencode('Nom d\'utilisateur ou mot de passe incorrect.'));
    exit;
}else {
    session_start();
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    header('Location: /pages/articles.php?success=' . rawurlencode('Connexion réussie.'));
    exit;
}
