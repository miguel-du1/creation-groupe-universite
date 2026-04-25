<?php
class sondage
{

    // Attributs
    private $conn;

    // Constructeur
    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Methodes
    public function getAll()
    {
        $query = "  SELECT 
                        s.idSondage as id, 
                        s.nomSondage as titre, 
                        s.dateDebutSondage as dateDebut, 
                        s.dateFinSondage as dateFin,
                        COUNT(se.idSondage) as nbReponses,
                        CASE
                            WHEN NOW() < s.dateDebutSondage THEN 'Pas encore publié'
                            WHEN NOW() > s.dateFinSondage THEN 'Terminé'
                            ELSE 'En cours'
                        END as etat,
                        CONCAT(p.nomPersonne, ' ', p.prenomPersonne) as auteur
                    FROM AG_SONDAGE s
                    LEFT JOIN AG_SONDAGE_ETUDIANT se ON s.idSondage = se.idSondage
                    LEFT JOIN AG_SONDAGE_ENSEIGNANT sens ON s.idSondage = sens.idSondage
                    LEFT JOIN AG_ENSEIGNANT e ON sens.idEnseignant = e.idEnseignant
                    LEFT JOIN AG_PERSONNE p ON e.idSecuriteSocialPersonne = p.idSecuriteSocialPersonne
                    GROUP BY s.idSondage
                    ORDER BY s.dateDebutSondage DESC
                    ";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOne($id)
    {
        // 1. Récupérer les informations de base
        $query = "  SELECT 
                        s.idSondage as id, 
                        s.nomSondage as titre, 
                        s.dateDebutSondage as dateDebut, 
                        s.dateFinSondage as dateFin,
                        COUNT(se.idSondage) as nbReponses,
                         CASE
                            WHEN NOW() < s.dateDebutSondage THEN 'Pas encore publié'
                            WHEN NOW() > s.dateFinSondage THEN 'Terminé'
                            ELSE 'En cours'
                        END as etat,
                        CONCAT(p.nomPersonne, ' ', p.prenomPersonne) as auteur
                    FROM AG_SONDAGE s
                    LEFT JOIN AG_SONDAGE_ETUDIANT se ON s.idSondage = se.idSondage
                    LEFT JOIN AG_SONDAGE_ENSEIGNANT sens ON s.idSondage = sens.idSondage
                    LEFT JOIN AG_ENSEIGNANT e ON sens.idEnseignant = e.idEnseignant
                    LEFT JOIN AG_PERSONNE p ON e.idSecuriteSocialPersonne = p.idSecuriteSocialPersonne
                    WHERE s.idSondage = ?
                    GROUP BY s.idSondage";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        $sondage = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($sondage) {
            // 2. Récupérer les critères
            $queryCri = "SELECT c.idCritere, c.nomCritere 
                         FROM AG_CRITERE c
                         JOIN AG_SONDAGE_CRITERE sc ON c.idCritere = sc.idCritere
                         WHERE sc.idSondage = ?";
            $stmtCri = $this->conn->prepare($queryCri);
            $stmtCri->execute([$id]);
            $sondage['criteres'] = $stmtCri->fetchAll(PDO::FETCH_ASSOC);

            // 3. Récupérer les réponses des étudiants
            $queryRep = "SELECT 
                            se.idEtudiant,
                            se.dateReponse,
                            p.nomPersonne, 
                            p.prenomPersonne, 
                            p.emailPersonne,
                            GROUP_CONCAT(CONCAT(c.nomCritere, ': ', se.reponseSondage) SEPARATOR ' | ') as reponses
                         FROM AG_SONDAGE_ETUDIANT se
                         JOIN AG_ETUDIANT etu ON se.idEtudiant = etu.idEtudiant
                         JOIN AG_PERSONNE p ON etu.idSecuriteSocialPersonne = p.idSecuriteSocialPersonne
                         JOIN AG_CRITERE c ON se.idCritere = c.idCritere
                         WHERE se.idSondage = ?
                         GROUP BY se.idEtudiant";
            $stmtRep = $this->conn->prepare($queryRep);
            $stmtRep->execute([$id]);
            $sondage['participations'] = $stmtRep->fetchAll(PDO::FETCH_ASSOC);

            // 4. Récupérer les enseignants responsables
            $queryResp = "SELECT e.idEnseignant FROM AG_SONDAGE_ENSEIGNANT se JOIN AG_ENSEIGNANT e ON se.idEnseignant = e.idEnseignant WHERE se.idSondage = ?";
            $stmtResp = $this->conn->prepare($queryResp);
            $stmtResp->execute([$id]);
            $sondage['personnesResponsable'] = $stmtResp->fetchAll(PDO::FETCH_ASSOC);
        }

        return $sondage;
    }

    public function create($data)
    {
        $status = true;
        try {
            // TABLE AG_SONDAGE
            $nomSondage = $data['titre'];
            $dateDebut = $data['dateDebut'];
            $dateFin = $data['dateFin'];
            $stmt = $this->conn->prepare("CALL creation_sondage(?, ?, ?, @id)");
            $ok = $stmt->execute([$nomSondage, $dateDebut, $dateFin]);
            $idSondageCourant = (int) ($this->conn->query("SELECT @id")->fetchColumn());
            if (!$ok)
                $status = false;

            // TABLE AG_SONDAGE_ENSEIGNANT
            $idEns = (int) $data['personnesResponsable'];
            $stmt = $this->conn->prepare("INSERT INTO AG_SONDAGE_ENSEIGNANT (idEnseignant, idSondage) VALUES (?,?)");
            $ok = $stmt->execute([$idEns, $idSondageCourant]);
            if (!$ok)
                $status = false;

            // TABLE AG_SONDAGE_CRITERE
            foreach ($data['critere'] as $c) {
                $idCri = (int) $c['idCritere'];
                $stmt = $this->conn->prepare("INSERT INTO AG_SONDAGE_CRITERE(idSondage, idCritere) VALUES (?,?)");
                $ok = $stmt->execute([$idSondageCourant, $idCri]);
                if (!$ok)
                    $status = false;
            }
            return $status;
        } catch (PDOException $e) {
            print "Error!: " . $e->getMessage() . "</br>";
            return false;
        }
    }

    public function update($id, $data)
    {
        $status = true;
        try {
            // TABLE AG_SONDAGE
            $nomSondage = $data['titre'];
            $dateDebut = $data['dateDebut'];
            $dateFin = $data['dateFin'];
            $query = "  UPDATE AG_SONDAGE 
                        SET 
                        nomSondage = ?,
                        dateDebutSondage = ?,
                        dateFinSondage = ? 
                        WHERE idSondage = ?";
            $stmt = $this->conn->prepare($query);
            $ok = $stmt->execute([$nomSondage, $dateDebut, $dateFin, $id]);
            $stmt->closeCursor();
            if (!$ok)
                $status = false;

            // TABLE AG_SONDAGE_ENSEIGNANT
            $stmt = $this->conn->prepare("DELETE FROM AG_SONDAGE_ENSEIGNANT WHERE idSondage = ?");
            $ok = $stmt->execute([$id]);
            $stmt->closeCursor();
            foreach ($data['personnesResponsable'] as $p) {
                $idEns = (int) $p['idEnseignant'];
                $stmt = $this->conn->prepare("INSERT INTO AG_SONDAGE_ENSEIGNANT (idEnseignant, idSondage) VALUES (?,?)");
                $ok = $stmt->execute([$idEns, $id]);
                $stmt->closeCursor();
                if (!$ok)
                    $status = false;
            }

            // TABLE AG_SONDAGE_CRITERE
            $stmt = $this->conn->prepare("DELETE FROM AG_SONDAGE_CRITERE WHERE idSondage = ?");
            $ok = $stmt->execute([$id]);
            $stmt->closeCursor();
            foreach ($data['critere'] as $c) {
                $idCri = (int) $c['idCritere'];
                $stmt = $this->conn->prepare("INSERT INTO AG_SONDAGE_CRITERE (idSondage, idCritere) VALUES (?,?)");
                $ok = $stmt->execute([$id, $idCri]);
                $stmt->closeCursor();
                if (!$ok)
                    $status = false;
            }
            return $status;
        } catch (PDOException $e) {
            print "Error!: " . $e->getMessage() . "</br>";
            return false;
        }
    }

    public function delete($id)
    {
        try {
            $stmt = $this->conn->prepare("CALL supprimer_sondage(?)");
            $sucess = $stmt->execute([$id]);
            return $sucess ? true : false;
        } catch (PDOException $e) {
            print "Error!: " . $e->getMessage() . "</br>";
            return false;
        }
    }
}
?>