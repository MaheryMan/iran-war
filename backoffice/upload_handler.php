<?php
// On définit le dossier de destination
$accepted_origins = array("http://localhost", "http://127.0.0.1"); // À adapter selon ton Docker
$imageFolder = "public/images/";

header('Content-Type: application/json; charset=UTF-8');

if (!isset($_FILES['file']) || !is_uploaded_file($_FILES['file']['tmp_name'])) {
    http_response_code(400);
    echo json_encode(['error' => 'No upload file received']);
    exit;
}

// 1. On récupère le fichier envoyé par TinyMCE
$file = $_FILES['file'];

// 1. On récupère l'extension du fichier (ex: jpg, png)
$extension = pathinfo($file['name'], PATHINFO_EXTENSION);

// 2. On génère un préfixe basé sur la date et les microsecondes
// Format : AnnéeMoisJour_HeureMinuteSeconde_Microsecondes
$prefixe = date('Ymd_His') . '_' . str_replace('0.', '', microtime(true));

// 3. On assemble le tout
$fileName = $prefixe . '.' . $extension;
$target = $imageFolder . $fileName;

// 2. On déplace le fichier du dossier temporaire vers notre dossier final
if (move_uploaded_file($file['tmp_name'], $target)) {
    // 3. On répond à TinyMCE avec l'URL de l'image
    // Attention : l'URL doit être accessible depuis le navigateur
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? "https://" : "http://";
    $baseurl = $protocol . $_SERVER['HTTP_HOST'] . "/";
    
    echo json_encode([
        'location' => $baseurl . $target
    ]);
    exit;
} else {
    // En cas d'erreur
    http_response_code(500);
    echo json_encode(['error' => 'Upload failed']);
    exit;
}
?>