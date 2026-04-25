<?php
class Etudiant
{
    private $conn;
    private $table_name = "AG_ETUDIANT";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getAll($idPromotion = null)
    {
        $query = "SELECT 
                    e.idEtudiant, 
                    COALESCE(p.nomPersonne, 'Inconnu') as nomPersonne, 
                    COALESCE(p.prenomPersonne, '') as prenomPersonne, 
                    COALESCE(g.codeGroupe, '-') as codeGroupe,
                    e.idPromotion
                  FROM " . $this->table_name . " e
                  LEFT JOIN AG_PERSONNE p ON e.idSecuriteSocialPersonne = p.idSecuriteSocialPersonne
                  LEFT JOIN AG_GROUPE g ON e.idGroupe = g.idGroupe";

        if ($idPromotion) {
            $query .= " WHERE e.idPromotion = ?";
            $stm = $this->conn->prepare($query);
            $stm->execute([$idPromotion]);
        } else {
            $stm = $this->conn->prepare($query);
            $stm->execute();
        }

        return $stm->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOne($id)
    {
        $query = "SELECT 
                    e.idEtudiant, 
                    e.parcoursEtudiant,
                    e.typeBac,
                    e.statutAcademique,
                    e.formationAnglais,
                    e.apprentissageEtudiant,
                    p.nomPersonne, 
                    p.prenomPersonne, 
                    p.emailPersonne,
                    p.telPersonne,
                    p.naissancePersonne,
                    p.sexePersonne,
                    p.etatCivilPersonne,
                    p.adrPersonne,
                    pay.nomPays,
                    g.codeGroupe,
                    pro.anneePromotion,
                    pro.idPromotion
                  FROM " . $this->table_name . " e
                  LEFT JOIN AG_PERSONNE p ON e.idSecuriteSocialPersonne = p.idSecuriteSocialPersonne
                  LEFT JOIN AG_PAYS pay ON p.idPays = pay.idPays
                  LEFT JOIN AG_GROUPE g ON e.idGroupe = g.idGroupe
                  LEFT JOIN AG_PROMOTION pro ON e.idPromotion = pro.idPromotion
                  WHERE e.idEtudiant = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $data)
    {
        // On construit la requête dynamiquement en fonction des champs envoyés
        $fields = [];
        $values = [];

        // Liste des champs autorisés à la modification
        $allowed = [
            'idGroupe', 
            'formationAnglais', 
            'parcoursEtudiant', 
            'typeBac', 
            'statutAcademique', 
            'apprentissageEtudiant',
            'idPromotion'
        ];

        foreach ($data as $key => $val) {
            if (in_array($key, $allowed)) {
                $fields[] = "$key = ?";
                $values[] = $val;
            }
        }

        if (empty($fields)) {
            return false;
        }

        $values[] = $id;
        $sql = "UPDATE " . $this->table_name . " SET " . implode(', ', $fields) . " WHERE idEtudiant = ?";

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($values);
    }
}
?>