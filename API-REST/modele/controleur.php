<?php
class Controleur {
    
    // Attributs
    private $conn;

    // Constructeur
    public function __construct ($db) {
        $this->conn = $db;
    }

    // Methodes
    public function insertNoteEtudiant ($id,$idEtudiant,$note,$commentaire) {
        try {
            $query = "  INSERT INTO `AG_CONTROLE_ETUDIANT`(`idEtudiant`, `idControle`, `note`, `commentaire`) 
                        VALUES (:idEtudiant, :idControle, :note, :commentaire)";
            $stmt = $this->conn->prepare($query);

            $success = $stmt->execute([
                'idEtudiant' => $idEtudiant,
                'idControle' => $id,
                'note' => $note,
                'commentaire' => $commentaire,
            ]);
            return $success ? 1 : 0;
        } catch(PDOException $e) {
            print "Error!: " . $e->getMessage() . "</br>";
            return 0;
        }
    }
}
?>
