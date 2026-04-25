<?php
class Groupe {

    // Attributs
    private $conn;

    // Constructeur
    public function __construct($db) {
        $this->conn = $db;
    }

    // Methodes
    public function getListesEtudiants ($id) {
        $query = "  SELECT E.idEtudiant, P.nomPersonne AS nom, P.prenomPersonne AS prenom, P.emailPersonne AS email, E.formationAnglais AS anglais, E.parcoursEtudiant AS parcours
                    FROM AG_ETUDIANT E
                    INNER JOIN AG_PERSONNE P ON E.idSecuriteSocialPersonne = P.idSecuriteSocialPersonne
                    WHERE E.idGroupe = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function ajouterEtudiants ($id, $idEtudiant) {
        try {
            $stmt = $this->conn->prepare("CALL mis_a_jour_etudiant_groupe(?, ?)");
            $ok = $stmt->execute([$id, $idEtudiant]);
            if ($ok) return ["success" => 1, "message" => "Mise à jour réussie"];
            return ["success" => 0, "message" => "Échec de la mise à jour"];
        } catch (PDOException $e) {
            return ["success" => 0, "message" => "Échec de la mise à jour"];
        }
    }


    public function create($data) {
        $status = true;
        try {
            $code = $data['codeGroupe'];
            $idType = (int) $data['idTypeGroupe'];
            $idPromo = (int) $data['idPromotion'];
            $nb = (int) $data['nbEtudiantGroupe'];
            $sem = (int) $data['semestreGroupe'];
            $etudiants = isset($data['idEtudiant']) ? $data['idEtudiant'] : [];

            // 1. Appel creation groupe
            $stmt = $this->conn->prepare("CALL creer_groupe(?, ?, ?, ?, ?, @id)");
            $ok = $stmt->execute([$code, $idType, $idPromo, $nb, $sem]);
            
            if (!$ok) return ["success" => false, "message" => "Erreur création groupe"];
            
            $stmt = $this->conn->query("SELECT @id");
            $idNouveau = $stmt->fetchColumn();

            // 2. Ajout des étudiants
            if ($idNouveau && !empty($etudiants)) {
                foreach($etudiants as $idEtu) {
                    // On utilise le code existant ou on appelle la proc directement
                    // CALL mis_a_jour_etudiant_groupe(p_idGroupe, p_idEtudiant)
                    $stmtAdd = $this->conn->prepare("CALL mis_a_jour_etudiant_groupe(?, ?)");
                    $res = $stmtAdd->execute([$idNouveau, $idEtu]);
                    // On ne stop pas s'il y a une erreur individuelle, mais on pourrait loguer
                    $stmtAdd->closeCursor();
                }
            }

            return ["success" => true, "id" => $idNouveau, "message" => "Groupe créé avec " . count($etudiants) . " étudiants."];

        } catch (PDOException $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }
}
?>