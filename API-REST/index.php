<?php
header("Content-Type: application/json");
// Chargement des fichiers nécessaires
require_once 'Database.php';
require_once 'modele/compte.php';
require_once 'modele/covoiturages.php';
require_once 'modele/sondage.php';
require_once 'modele/critere.php';
require_once 'modele/controleur.php';
require_once 'modele/groupe.php';
require_once 'modele/enseignant.php';
require_once 'modele/typeReponse.php';
require_once 'modele/promotion.php';
require_once 'modele/etudiant.php';

// Instanciation de la base de données
$database = new Database();
$db = $database->getConnection();

// Vérification de la connexion
if (!$db) {
    http_response_code(500);
    echo json_encode(["error" => "Erreur de connexion à la base de données"]);
    exit;
}

// Déclaration des objets des classes pour interagir avec la base de données
$compte = new compte($db);
$covoiturages = new covoiturages($db);
$sondage = new sondage($db);
$critere = new critere($db);
$controleur = new controleur($db);
$groupe = new groupe($db);
$ensei = new enseignant($db);
$typeReponse = new typeReponse($db);
$promotion = new Promotion($db);
$etudiant = new Etudiant($db);

// Récupération de la méthode HTTP et de l'URL
$method = $_SERVER['REQUEST_METHOD'];
$url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$urlParts = explode('/', trim($url, '/'));

$resource = $urlParts[2] ?? null;

$action = $urlParts[3] ?? null;
$id = $urlParts[3] ?? null;

$detailAction = $urlParts[4] ?? null;



// ------ Routage ------ //

// URL: /API-REST
if ($resource === null) {
    http_response_code(200);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        "status" => "OK",
        "message" => "Server is running",
        "timestamp" => date('c')
    ]);
    exit;
}

// URL: /API-REST/auth/login
if ($resource === 'auth') {
    switch ($method) {
        case 'POST':
            switch ($action) {
                case 'login':
                    $json = file_get_contents("php://input");
                    $data = json_decode($json, true);
                    $resul = $compte->login($data);
                    if ($resul["success"] == 1)
                        http_response_code(200);
                    else
                        http_response_code(401);
                    echo json_encode($resul);
                    break;
            }
            break;
        default:
            http_response_code(405);
            echo json_encode(["error" => "Méthode non autorisée"]);
            break;
    }
    exit;
}

// URL: /API-REST/comptes
if ($resource === 'comptes') {
    switch ($method) {
        case 'GET':
            $data = $compte->getAll();
            echo json_encode($data);
            break;
        default:
            http_response_code(405);
            echo json_encode(["error" => "Méthode non autorisée"]);
            break;
    }
    exit;
}

// URL: /API-REST/compte/{id}
if ($resource === 'compte') {
    switch ($method) {
        case 'GET':
            $data = $compte->getOne($id);
            echo json_encode($data);
            break;
        default:
            http_response_code(405);
            echo json_encode(["error" => "Méthode non autorisée"]);
            break;
    }
    exit;
}

// URL: /API-REST/covoiturages
if ($resource === 'covoiturages') {
    switch ($method) {
        case 'GET':
            $data = $covoiturages->getAll();
            echo json_encode($data);
            break;
        default:
            http_response_code(405);
            echo json_encode(["error" => "Méthode non autorisée"]);
            break;
    }
    exit;
}

// URL: /API-REST/sondages
if ($resource === 'sondages') {
    switch ($method) {
        case 'GET':
            if ($id) {
                $data = $sondage->getOne($id);
                echo json_encode($data);
                break;
            }
            $data = $sondage->getAll();
            echo json_encode($data);
            break;
        case 'POST':


            $json = file_get_contents("php://input");
            $data = json_decode($json, true);
            $resul = $sondage->create($data);
            if ($resul)
                http_response_code(200);
            else
                http_response_code(401);
            echo json_encode([
                "success" => !empty($resul) ? true : false
            ]);
            break;
        case 'PUT':
            if ($id) {
                $json = file_get_contents("php://input");
                $data = json_decode($json, true);
                $resul = $sondage->update($id, $data);
                if ($resul)
                    http_response_code(200);
                else
                    http_response_code(401);
                echo json_encode([
                    "success" => !empty($resul) ? true : false
                ]);
            }
            break;
        case 'DELETE':
            if ($id) {
                $resul = $sondage->delete($id);
                http_response_code($resul ? 200 : 404);
                echo json_encode(["success" => $resul ? true : false]);
            }
            break;
        default:
            http_response_code(405);
            echo json_encode(["error" => "Méthode non autorisée"]);
            break;
    }
    exit;
}

// URL: /API-REST/criteres
if ($resource === "criteres") {
    switch ($method) {
        case 'GET':
            // Consulter un critère par id
            if ($id) {
                $data = $critere->getOne($id);
                echo json_encode($data);
                break;
            }
            // Consulter tous les critères
            $data = $critere->getAll();
            echo json_encode($data);
            break;
        case 'POST':
            $json = file_get_contents("php://input");
            $data = json_decode($json, true);
            $resul = $critere->create($data);
            if ($resul["success"] == 1)
                http_response_code(200);
            else
                http_response_code(401);
            echo json_encode([
                "success" => !empty($resul["success"]) ? true : false,
                "message" => (string) ($resul["message"] ?? "")
            ]);
            break;
        case 'PUT':
            if ($id) {
                $json = file_get_contents("php://input");
                $data = json_decode($json, true);
                $resul = $critere->update($id, $data);
                http_response_code($resul ? 200 : 400);
                echo json_encode(["success" => $resul ? true : false]);
            }
            break;
        case 'DELETE':
            if ($id) {
                $resul = $critere->delete($id);
                http_response_code($resul ? 200 : 404);
                echo json_encode(["success" => $resul ? true : false]);
            }
            break;
        default:
            http_response_code(405);
            echo json_encode(["error" => "Méthode non autorisée"]);
            break;
    }
    exit;
}

// URL: /API-REST/controleurs
if ($resource === 'controleurs') {
    switch ($method) {
        case 'GET':
            break;
        case 'POST':
            if ($id) {
                $json = file_get_contents("php://input");
                $data = json_decode($json, true);
                $status = true;
                foreach ($data as $i => $row) {
                    $idEtudiant = $row['idEtudiant'];
                    $note = $row['note'];
                    $commentaire = $row['commentaire'];
                    $resul = $controleur->insertNoteEtudiant($id, $idEtudiant, $note, $commentaire);
                    if ($resul === 0)
                        $status = false;
                }
                if ($status)
                    http_response_code(200);
                else
                    http_response_code(401);
                echo json_encode([
                    "success" => $status
                ]);
            }
            break;
        default:
            http_response_code(405);
            echo json_encode(["error" => "Méthode non autorisée"]);
            break;
    }
    exit;
}

// URL: /API-REST/groupes
if ($resource === 'groupes') {
    switch ($method) {
        case 'GET':
            if ($id && $detailAction === "etudiants") {
                $data = $groupe->getListesEtudiants($id);
                echo json_encode($data);
            } else {
                http_response_code(405);
                echo json_encode(["error" => "Méthode non autorisée ou non implémentée"]);
            }
            break;
        case 'POST':
            if ($id && $detailAction === "etudiants") {
                $json = file_get_contents("php://input");
                $data = json_decode($json, true);
                $status = true;
                foreach ($data as $i => $idEtudiant) {
                    $resul = $groupe->ajouterEtudiants($id, $idEtudiant);
                    if ($resul === 0)
                        $status = false;
                }
                if ($status)
                    http_response_code(200);
                else
                    http_response_code(401);
                echo json_encode([
                    "success" => $status
                ]);
            } elseif (!$id) {
                // Standard Creation: POST /groupes
                // Body: { codeGroupe, idTypeGroupe, idPromotion, ... }
                $json = file_get_contents("php://input");
                $data = json_decode($json, true);
                if ($data) {
                    $result = $groupe->create($data);
                    if ($result['success']) {
                        http_response_code(201);
                        echo json_encode($result);
                    } else {
                        http_response_code(500);
                        echo json_encode($result);
                    }
                } else {
                    http_response_code(400);
                    echo json_encode(["success" => false, "message" => "JSON invalide"]);
                }
            } else {
                http_response_code(400);
                echo json_encode(["error" => "Action non reconnue"]);
            }
            break;
        default:
            http_response_code(405);
            echo json_encode(["error" => "Méthode non autorisée"]);
            break;
    }
    exit;
}

// URL: /API-REST/enseignants
if ($resource === 'enseignants') {
    switch ($method) {
        case 'GET':
            $data = $ensei->getAll();
            echo json_encode($data);
            break;
        default:
            http_response_code(405);
            echo json_encode(["error" => "Méthode non autorisée"]);
            break;
    }
    exit;
}

// URL: /API-REST/typesReponse
if ($resource === 'typesReponse') {
    switch ($method) {
        case 'GET':
            $data = $typeReponse->getAll();
            echo json_encode($data);
            break;
        default:
            http_response_code(405);
            echo json_encode(["error" => "Méthode non autorisée"]);
            break;
    }
    exit;
}

// URL: /API-REST/etudiants
if ($resource === 'etudiants') {
    switch ($method) {
        case 'GET':
            if ($id) {
                $data = $etudiant->getOne($id);
                echo json_encode($data);
                break;
            }
            $idPromotion = isset($_GET['promotion']) ? $_GET['promotion'] : null;
            $data = $etudiant->getAll($idPromotion);
            echo json_encode($data);
            break;
        case 'PUT':
            if ($id) {
                $json = file_get_contents("php://input");
                $data = json_decode($json, true);
                if ($data) {
                    $resul = $etudiant->update($id, $data);
                    if ($resul) {
                        http_response_code(200);
                        echo json_encode(["success" => true, "message" => "Etudiant mis à jour"]);
                    } else {
                        http_response_code(500); // Ou 400 si champs invalides
                        echo json_encode(["success" => false, "message" => "Erreur mise à jour"]);
                    }
                } else {
                    http_response_code(400);
                    echo json_encode(["success" => false, "message" => "Données invalides"]);
                }
            } else {
                 http_response_code(400);
                 echo json_encode(["error" => "ID manquant"]);
            }
            break;
        default:
            http_response_code(405);
            echo json_encode(["error" => "Méthode non autorisée"]);
            break;
    }
    exit;
}

// URL: /API-REST/promotions
if ($resource === 'promotions') {
    switch ($method) {
        case 'GET':
            if ($id) {
                $data = $promotion->getOne($id);
                echo json_encode($data);
                break;
            }
            $data = $promotion->getAll();
            echo json_encode($data);
            break;
        case 'POST':
            // Logic for saving groups: /promotion/{id}/groupes
            if ($id && $detailAction === "groupes") {
                $json = file_get_contents("php://input");
                $data = json_decode($json, true);
                if ($data) {
                    $groupsList = isset($data['groupes']) ? $data['groupes'] : $data;
                    $res = $promotion->saveGroups($id, $groupsList);
                    if ($res['success']) {
                        http_response_code(200);
                        echo json_encode(["success" => true, "message" => "Groupes sauvegardés", "logs" => $res['logs']]);
                    } else {
                        http_response_code(500);
                        echo json_encode(["success" => false, "message" => "Erreur sauvegarde", "logs" => $res['logs']]);
                    }
                    exit;
                } else {
                    http_response_code(400);
                    echo json_encode(["success" => false, "message" => "Données invalides (format JSON incorrect)"]);
                    exit;

                }
            } else {
                http_response_code(400);
                echo json_encode(["error" => "Action non reconnue"]);
            }
            break;
        case 'DELETE':
            // Logic for deleting groups: /promotion/{id}/groupes
            if ($id && $detailAction === "groupes") {
                if ($promotion->deleteGroups($id)) {
                    http_response_code(200);
                    echo json_encode(["success" => true, "message" => "Groupes supprimés"]);
                } else {
                    http_response_code(500);
                    echo json_encode(["success" => false, "message" => "Erreur suppression"]);
                }
            } else {
                http_response_code(400);
                echo json_encode(["error" => "Action non reconnue"]);
            }
            break;
        default:
            http_response_code(405);
            echo json_encode(["error" => "Méthode non autorisée"]);
            break;
    }
    exit;
}
?>