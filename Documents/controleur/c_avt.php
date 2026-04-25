<?php 
session_start();
require_once("config/connexion.php");

function afficherUIInfoCompteEns () {
    require __DIR__ . '/../vue/compte_Enseignant/infoPersoEnseignant.php';
}

function afficherUIInfoCompteEtu () {
    require __DIR__ . '/../vue/compte_Etudiant/infoPersoEtudiant.php';
}


function enregistrerNouveauAvt () {

    if (!isset($_FILES["image"]) || $_FILES["image"]["error"] !== UPLOAD_ERR_OK) {
        http_response_code(400);
        exit("Aucun fichier n’a été joint ou le fichier est corrompu.");
    }

    $file = $_FILES["image"];

    // limit size (vd 2MB)
    $maxSize = 2 * 1024 * 1024;
    if ($file["size"] > $maxSize) {
        http_response_code(400);
        exit("Le fichier est trop volumineux. Taille maximale : 2 Mo.");
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file["tmp_name"]);

    $allowed = [
        "image/jpeg" => "jpg",
        "image/png"  => "png",
        "image/webp" => "webp"
    ];

    if (!isset($allowed[$mime])) {
        http_response_code(400);
        exit("Seuls les formats JPG / PNG / WEBP sont acceptés.");
    }

    $ext = $allowed[$mime];

    // --- Ensure upload dir ---
    $uploadDir = __DIR__ . "/../assets/uploads";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // --- Generate safe filename ---
    $filename = bin2hex(random_bytes(16)) . "." . $ext;
    $targetPath = $uploadDir . "/" . $filename;

    // --- Move uploaded file ---
    if (!move_uploaded_file($file["tmp_name"], $targetPath)) {
        http_response_code(500);
        exit("Impossible d’enregistrer le fichier sur le serveur.");
    }

    $imageUrl = "assets/uploads/" . $filename;

    $id_compte = $_SESSION['user_id'] ?? 0;

    Connexion::connect();
    $stmt = Connexion::pdo()->prepare("UPDATE AG_COMPTE SET path_avt = ? WHERE idCompte = ?");
    $stmt->execute([$imageUrl, $id_compte]);


    $role_compte = $_SESSION['role_compte'];
    switch ($role_compte) {
        case 'Etudiant':
            header('Location: index.php?page=infoPersonneEtu');
            break;
        default:
            header('Location: index.php?page=infoPersonneEns');
            break;
    }
    exit;
}
?>