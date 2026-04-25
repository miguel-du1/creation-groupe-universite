<?php
class covoiturages {

    // Attributs
    private $conn;

    // Constructeur
    public function __construct($db) {
        $this->conn = $db;
    }

    // Methodes
    public function getAll() {
        $query = "  SELECT 
                        c.idCovoiturage, 
                        c.etatCovoiturage, 
                        c.nbMax,
                        e.idEtudiant as idEtudiant,
                        p.nomPersonne as nomEtudiant,
                        p.prenomPersonne as prenomEtudiant,
                        ce.roleCovoiturage as role
                    FROM AG_COVOITURAGE c
                    LEFT JOIN AG_COVOITURAGE_ETUDIANT ce ON c.idCovoiturage = ce.idCovoiturage
                    LEFT JOIN AG_ETUDIANT e ON ce.idEtudiant = e.idEtudiant
                    LEFT JOIN AG_PERSONNE p ON e.idSecuriteSocialPersonne = p.idSecuriteSocialPersonne
                    ORDER BY c.idCovoiturage";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Regroupement des données par covoiturage
        $covoiturages = [];

        foreach ($results as $row) {
            $id = $row['idCovoiturage'];

            if (!isset($covoiturages[$id])) {
                $covoiturages[$id] = [
                    'idCovoiturage' => $id,
                    'etatCovoiturage' => $row['etatCovoiturage'],
                    'nbMax' => (int)$row['nbMax'],
                    'membres' => []
                ];
            }

            // Si un étudiant est lié (le LEFT JOIN peut retourner NULL si pas de membres)
            if ($row['nomEtudiant']) {
                $covoiturages[$id]['membres'][] = [
                    'idEtudiant' => $row['idEtudiant'],
                    'nomEtudiant' => $row['nomEtudiant'],
                    'prenomEtudiant' => $row['prenomEtudiant'],
                    'role' => $row['role']
                ];
            }
        }

        // Réindexer le tableau pour obtenir une liste JSON propre [ {...}, {...} ]
        return array_values($covoiturages);
    }
}
?>