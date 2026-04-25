<?php
// voiture.php
class voiture
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Lister tous les voitures
    public function getAll()
    {
        $query = "SELECT * FROM  voiture";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lire un voiture par son immatriculation
    public function getOne($immatriculation)
    {
        $query = "SELECT * FROM voiture WHERE immatriculation = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$immatriculation]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Créer un voiture
    public function create($data)
    {
        try {
        $query = "INSERT INTO voiture (immatriculation, marque, couleur) VALUES (:immatriculation, :marque, :couleur)";
        $stmt = $this->conn->prepare($query);
        $success = $stmt->execute([
            'immatriculation' => $data['immatriculation'],
            'marque' => $data['marque'],
            'couleur' => $data['couleur']
        ]);
        return $success;
        // si id en auto incrémente return $this->conn->lastInsertId();
     } catch(PDOException $e) {
        print "Error!: " . $e->getMessage() . "</br>";
        return false;
    }

    }

    // Mettre à jour un voitu
    public function update($immatriculation, $data)
    {
                try {
        $query = "UPDATE voiture SET marque = :marque, couleur = :couleur WHERE immatriculation = :immatriculation";
        $stmt = $this->conn->prepare($query);
        $sucess= $stmt->execute([
            'immatriculation' => $immatriculation,
            'marque' => $data['marque'],
            'couleur' => $data['couleur']
        ]);
        //retourne toujours true même si la voture n'existe pas, 
        $sucess= $stmt->rowCount();
        //retourne le nombre de lignes modifiées 
        return $sucess;

     } catch(PDOException $e) {
        print "Error!: " . $e->getMessage() . "</br>";
        return false;
    }



    }

    // Supprimer un voiture
    public function delete($immatriculation)
    {
        try {
        $query = "DELETE FROM voiture WHERE immatriculation = ?";
        $stmt = $this->conn->prepare($query);
         $sucess=$stmt->execute([$immatriculation]);
        //retourne toujours true même si la voture n'existe pas, 
        $sucess= $stmt->rowCount();
        //retourne le nombre de lignes modifiées 
        return $sucess;

             } catch(PDOException $e) {
        print "Error!: " . $e->getMessage() . "</br>";
        return false;
    }
    }
}
?>