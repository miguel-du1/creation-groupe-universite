<?php
class Critere {
    
    // Attributs
    private $conn;

    // Constructeur
    public function __construct ($db) {
        $this->conn = $db;
    }

    // Methodes
    public function getAll () {
        $query = "  SELECT idCritere, nomCritere, nomTypeReponse, optionsCritere 
                    FROM AG_CRITERE C
                    INNER JOIN AG_TYPE_REPONSE TR ON C.typeReponse = TR.idTypeReponse";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (!$rows) return [];

        foreach ($rows as &$row) {
            $decoded = json_decode($row['optionsCritere'] ?? '', true);
            $row['optionsCritere'] = $decoded ?? [];
        }
        unset($row);

        return $rows;
    }

    public function getOne ($id) {
        $query = "  SELECT idCritere, nomCritere, nomTypeReponse, optionsCritere 
                    FROM AG_CRITERE C
                    INNER JOIN AG_TYPE_REPONSE TR ON C.typeReponse = TR.idTypeReponse
                    WHERE idCritere = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) return null;

        // optionsCritere: string JSON -> object/array JSON
        $decoded = json_decode($row['optionsCritere'], true);
        $row['optionsCritere'] = (is_array($decoded) ? $decoded : new stdClass());

        return $row;
    }

    public function create ($data) {
        $nom = $data['nom'];
        $type = $data['type'];
        $options = $data['options'];
        $optionsJson = json_encode($options, JSON_UNESCAPED_UNICODE);
        try {
            $stmt = $this->conn->prepare("CALL ajouter_nouvelle_critere(?, ?, ?)");
            $ok = $stmt->execute([$nom, $type, $optionsJson]);
            if ($ok) return ["success" => 1, "message" => "Création du critère réussie"];
            return ["success" => 0, "message" => "Échec de la création du critère"];
        } catch (PDOException $e) {
            return ["success" => 0, "message" => "Échec de la création du critère"];
        }
    }

    public function update ($id, $data) {
        $nom = $data['nom'];
        $type = $data['type'];
        $options = $data['options'];
        $optionsJson = json_encode($options, JSON_UNESCAPED_UNICODE);

        try {
            $stmt = $this->conn->prepare("CALL update_critere (?, ?, ?, ?)");
            $ok = $stmt->execute([$id, $nom, $type, $optionsJson]);
            $ok= $stmt->rowCount();
            return $ok;
        } catch (PDOException $e) {
            print "Error!: " . $e->getMessage() . "</br>";
            return false;
        }
    }

    public function delete ($id) {
        try {
            $stmt = $this->conn->prepare("CALL supprimer_critere (?)");
            $ok = $stmt->execute([$id]);
            return ["success" => 1, "message" => "Suppression du critère réussie."];
        } catch (PDOException $e) {
            print "Error!: " . $e->getMessage() . "</br>";
            return ["success" => 0, "message" => "Échec de la suppression du critère."];
        }
    }
}
?>