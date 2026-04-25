<?php
class Promotion
{

    // Attributs
    private $conn;
    private $table_name = "AG_PROMOTION";

    // Constructeur
    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Methodes
    public function getAll()
    {
        $query = "SELECT P.idPromotion, P.anneePromotion, P.etatPromotion, 
                  (SELECT COUNT(*) FROM AG_ETUDIANT E WHERE E.idPromotion = P.idPromotion) as nbEtudiantPromo
                  FROM " . $this->table_name . " P
                  ORDER BY P.anneePromotion DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOne($id)
    {
        $query = "SELECT P.idPromotion, P.anneePromotion, P.etatPromotion, 
                         (SELECT COUNT(*) FROM AG_ETUDIANT E WHERE E.idPromotion = P.idPromotion) AS nbEtudiantPromo
                  FROM " . $this->table_name . " P 
                  WHERE P.idPromotion = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $queryGroups = "SELECT G.idGroupe, G.codeGroupe, 
                            (SELECT COUNT(*) FROM AG_ETUDIANT E WHERE E.idGroupe = G.idGroupe) as nbEtudiantGroupe, 
                            TG.nomTypeGroupe, G.idPromotion, G.semestreGroupe 
                            FROM AG_GROUPE G
                            INNER JOIN AG_TYPE_GROUPE TG ON G.idTypeGroupe = TG.idTypeGroupe
                            WHERE G.idPromotion = ?";
            $stmtGroups = $this->conn->prepare($queryGroups);
            $stmtGroups->execute([$id]);
            $groups = $stmtGroups->fetchAll(PDO::FETCH_ASSOC);

            // Récupérer les étudiants sans groupe
            $queryEtudiantsSansGroupe = "SELECT idEtudiant, parcoursEtudiant, typeBac, formationAnglais, apprentissageEtudiant, statutAcademique, idGroupe, idPromotion 
                                         FROM AG_ETUDIANT 
                                         WHERE idPromotion = ? AND idGroupe IS NULL";
            $stmtEtudiants = $this->conn->prepare($queryEtudiantsSansGroupe);
            $stmtEtudiants->execute([$id]);
            $etudiantsSansGroupe = $stmtEtudiants->fetchAll(PDO::FETCH_ASSOC);

            $row['groupes'] = $groups;
            $row['etudiantsSansGroupe'] = $etudiantsSansGroupe;

            return $row;
        }
        return null;
    }
    public function saveGroups($idPromotion, $groupsData)
    {
        $logs = [];
        $logs[] = "Début du traitement pour la promotion $idPromotion. Nombre de groupes : " . (is_array($groupsData) ? count($groupsData) : 'pas un tableau');
        $globalSuccess = true;

        if (!is_array($groupsData)) {
            $logs[] = "Erreur : groupsData n'est pas un tableau.";
            return ["success" => false, "logs" => $logs];
        }

        foreach ($groupsData as $grp) {
            $code = isset($grp['code']) ? $grp['code'] : null;
            if ($code === null) {
                $logs[] = "Ignorer les données de groupe invalides (pas de code)";
                continue;
            }
            $logs[] = "Traitement du code de groupe : " . $code;
            
            // 1. Vérifier/Créer le groupe
            // Utilisation par défaut de idTypeGroupe=1 (TD) et semestreGroupe=1 pour l'instant
            $queryCheck = "SELECT idGroupe FROM AG_GROUPE WHERE idPromotion = ? AND codeGroupe = ?";
            $stmtCheck = $this->conn->prepare($queryCheck);
            $stmtCheck->execute([$idPromotion, $code]);
            $existing = $stmtCheck->fetch(PDO::FETCH_ASSOC);

            $idGroupe = null;
            if ($existing) {
                $idGroupe = $existing['idGroupe'];
                $logs[] = "  Groupe existant trouvé ID : $idGroupe";
            } else {
                // Utilisation de la procédure stockée creer_groupe pour gérer l'ID manuel et le nombre d'étudiants
                // IN: p_codeGroupe, p_idTypeGroupe, p_idPromotion, p_nbEtudiant, p_semestre
                // OUT: idGroupeNouveau
                $nbEtudiant = (isset($grp['etudiants']) && is_array($grp['etudiants'])) ? count($grp['etudiants']) : 0;
                
                try {
                    // Appel de la procédure avec paramètre de sortie
                    $sql = "CALL creer_groupe(:p_code, 1, :p_promo, :p_nb, 1, @out_id)"; 
                    $stmtCall = $this->conn->prepare($sql);
                    $stmtCall->bindParam(':p_code', $code);
                    $stmtCall->bindParam(':p_promo', $idPromotion);
                    $stmtCall->bindParam(':p_nb', $nbEtudiant);
                    
                    if ($stmtCall->execute()) {
                        $stmtCall->closeCursor(); // Important pour permettre la requête suivante
                        // Récupérer l'ID généré
                        $stmtSelect = $this->conn->query("SELECT @out_id as id");
                        $row = $stmtSelect->fetch(PDO::FETCH_ASSOC);
                        if ($row && $row['id']) {
                            $idGroupe = $row['id'];
                            $logs[] = "  Nouveau groupe créé via procédure ID : $idGroupe (nb étudiants: $nbEtudiant)";
                        } else {
                            $logs[] = "  ERREUR: Procédure exécutée mais ID non renvoyé";
                            $globalSuccess = false;
                            continue;
                        }
                    } else {
                        $errorInfo = $stmtCall->errorInfo();
                        $logs[] = "  ERREUR exécution procédure : " . json_encode($errorInfo);
                        $globalSuccess = false;
                        continue;
                    }
                } catch(PDOException $e) {
                     $logs[] = "  EXCEPTION procédure : " . $e->getMessage();
                     $globalSuccess = false;
                     continue;
                }
            }

            // 2. Affecter les étudiants
            if ($idGroupe && !empty($grp['etudiants']) && is_array($grp['etudiants'])) {
                $count = 0;
                // Préparer la requête de mise à jour
                $queryUp = "UPDATE AG_ETUDIANT SET idGroupe = ? WHERE idEtudiant = ?";
                $stmtUp = $this->conn->prepare($queryUp);
                foreach ($grp['etudiants'] as $idEtudiant) {
                    if ($stmtUp->execute([$idGroupe, $idEtudiant])) {
                        $count++;
                    } else {
                        $logs[] = "  Échec de l'affectation de l'étudiant $idEtudiant";
                        $globalSuccess = false;
                    }
                }
                $logs[] = "  Étudiants affectés/mis à jour ($count) pour le groupe $idGroupe";
            } else {
                $logs[] = "  Aucun étudiant à affecter ou échec de la création du groupe.";
            }
        }
        return ["success" => $globalSuccess, "logs" => $logs];
    }

    public function deleteGroups($idPromotion) {
        try {
            // 1. Unlink students
            $sqlUnlink = "UPDATE AG_ETUDIANT SET idGroupe = NULL WHERE idPromotion = ?";
            $stmtUnlink = $this->conn->prepare($sqlUnlink);
            $stmtUnlink->execute([$idPromotion]);

            // 2. Delete groups
            $sqlDelete = "DELETE FROM AG_GROUPE WHERE idPromotion = ?";
            $stmtDelete = $this->conn->prepare($sqlDelete);
            $stmtDelete->execute([$idPromotion]);
            
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
}
?>