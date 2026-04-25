<?php
class Compte
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
        $query = "SELECT idCompte, nomUtilisateur FROM  AG_COMPTE";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOne($id)
    {
        $query = "SELECT idCompte, nomUtilisateur, roleCompte, idSecuriteSocialPersonne FROM AG_COMPTE WHERE idCompte = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    /*
        // Créer un compte
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

        // Mettre à jour un compte
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

        // Supprimer un compte
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
        */
    public function login($data)
    {
        $login = $data['login'];
        $password = $data['password'];

        // Requête enrichie pour récupérer les infos liées (Personne, Enseignant, Etudiant)
        $query = "SELECT 
                    c.idCompte,
                    c.nomUtilisateur,
                    c.motDePasse,
                    c.roleCompte,
                    p.nomPersonne,
                    p.prenomPersonne,
                    ENS.idEnseignant,
                    ETU.idEtudiant
                FROM AG_COMPTE c
                LEFT JOIN AG_PERSONNE p ON c.idSecuriteSocialPersonne = p.idSecuriteSocialPersonne
                LEFT JOIN AG_ENSEIGNANT ENS ON p.idSecuriteSocialPersonne = ENS.idSecuriteSocialPersonne
                LEFT JOIN AG_ETUDIANT ETU ON p.idSecuriteSocialPersonne = ETU.idSecuriteSocialPersonne
                WHERE c.nomUtilisateur = :login";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':login', $login);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            if ($user['roleCompte'] == 'Etudiant') {
                return ["success" => 0, "message" => "Acces interdit"];
            }
            
            if (password_verify($password, $user['motDePasse'])) {
                // On retire le mot de passe avant de renvoyer les données
                unset($user['motDePasse']);
                return ["success" => 1, "message" => "Connexion réussie", "user" => $user];
            } else {
                return ["success" => 0, "message" => "Mot de passe incorrect"];
            }
        } else {
            return ["success" => 0, "message" => "Utilisateur inexistant"];
        }
    }
}
?>