<?php
class TypeReponse {
    
    // Attributs
    private $conn;

    // Constructeur
    public function __construct ($db) {
        $this->conn = $db;
    }

    // Methodes
    public function getAll () {
        $query = "SELECT * FROM AG_TYPE_REPONSE";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $rows;
    }
}
?>