<?php
session_start();

// Supprimer tous les paramètres de session
session_unset();

// Détruire la session
session_destroy();

// Rediriger vers la page de connexion
header('Location: /connexion');
exit;
