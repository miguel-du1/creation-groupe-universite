<?php 
class Compte {

    // Attributs
    protected $id;
    protected $nom;

    // Constructeur
    function __construct ($id=null, $nom=null) {
        $this->id = $id;
        $this->nom = $nom;
    }

    // Methodes
    static public function getDetailInfoDuCompteEtu ($idCompte) {
        require_once("config/connexion.php");
        Connexion::connect();
        $resultat = Connexion::pdo()->prepare("
            SELECT 
                C.path_avt AS avt,
                C.nomUtilisateur AS username,
                P.idSecuriteSocialPersonne AS numSocial,
                P.nomPersonne AS nom,
                P.prenomPersonne AS prenom,
                DATE_FORMAT(P.naissancePersonne, '%d/%m/%Y') AS dateNaissance,
                P.sexePersonne AS sexe,
                P.etatCivilPersonne AS etatCivil,
                PA.nomPays AS nationalite,
                P.telPersonne AS phone,
                P.emailPersonne AS email,
                P.adrPersonne AS address,
                PM.anneePromotion AS promotion,
                G.codeGroupe AS groupe,
                E.idEtudiant,
                E.typeBac,
                E.parcoursEtudiant,
                E.apprentissageEtudiant,
                E.formationAnglais,
                E.statutAcademique
            FROM AG_COMPTE C
            INNER JOIN AG_PERSONNE P ON C.idSecuriteSocialPersonne = P.idSecuriteSocialPersonne
            INNER JOIN AG_ETUDIANT E ON P.idSecuriteSocialPersonne = E.idSecuriteSocialPersonne
            LEFT JOIN AG_PAYS PA ON P.idPays = PA.idPays
            LEFT JOIN AG_PROMOTION PM ON E.idPromotion = PM.idPromotion
            LEFT JOIN AG_GROUPE G ON E.idGroupe = G.idGroupe
            WHERE C.idCompte = ?
        ");
        $resultat->execute([$idCompte]);
        $row = $resultat->fetch(PDO::FETCH_ASSOC);
        $info_du_compte = ($row !== false) ? $row : null;
        return $info_du_compte;
    }

    static public function getIdUserParIdEtudiant ($idEtudiant) {
        require_once("config/connexion.php");
        Connexion::connect();
        $resultat = Connexion::pdo()->prepare("
            SELECT idCompte
            FROM AG_ETUDIANT E
            INNER JOIN AG_COMPTE C ON E.idSecuriteSocialPersonne = C.idSecuriteSocialPersonne
            WHERE idEtudiant = ?
        ");
        $resultat->execute([$idEtudiant]);
        $row = $resultat->fetch(PDO::FETCH_ASSOC);
        $id_user = ($row !== false) ? $row : null;
        return $id_user;
    }

    static public function getDetailInfoDuCompteEns ($idCompte) {
        require_once("config/connexion.php");
        Connexion::connect();
        $resultat = Connexion::pdo()->prepare("
            SELECT 
                C.path_avt AS avt,
                C.nomUtilisateur AS username,
                C.roleCompte AS role_compte,
                P.nomPersonne AS nom,
                P.prenomPersonne AS prenom,
                DATE_FORMAT(P.naissancePersonne, '%d/%m/%Y') AS dateNaissance,
                P.sexePersonne AS sexe,
                P.etatCivilPersonne AS etatCivil,
                P.idSecuriteSocialPersonne AS numSocial,
                PA.nomPays AS nationalite,
                P.telPersonne AS phone,
                P.emailPersonne AS email,
                P.adrPersonne AS address,
                E.idEnseignant,
                E.departement,
                M.nomMatiere AS responsable
            FROM AG_COMPTE C
            INNER JOIN AG_PERSONNE P ON C.idSecuriteSocialPersonne = P.idSecuriteSocialPersonne
            INNER JOIN AG_ENSEIGNANT E ON P.idSecuriteSocialPersonne = E.idSecuriteSocialPersonne
            LEFT JOIN AG_PAYS PA ON P.idPays = PA.idPays
            LEFT JOIN AG_MATIERE M ON E.idMatiere = M.idMatiere
            WHERE C.idCompte = ?
        ");
        $resultat->execute([$idCompte]);
        $row = $resultat->fetch(PDO::FETCH_ASSOC);
        $info_du_compte = ($row !== false) ? $row : null;
        return $info_du_compte;
    }

    static public function getListeMatiereEnseignees ($idCompte) {
        require_once("config/connexion.php");
        Connexion::connect();
        $resultat = Connexion::pdo()->prepare("
            SELECT M.nomMatiere
            FROM AG_COMPTE C
            INNER JOIN AG_PERSONNE P ON C.idSecuriteSocialPersonne = P.idSecuriteSocialPersonne
            INNER JOIN AG_ENSEIGNANT E ON P.idSecuriteSocialPersonne = E.idSecuriteSocialPersonne
            LEFT JOIN AG_MATIERE_ENSEIGNANT ME ON E.idEnseignant = ME.idEnseignant
            INNER JOIN AG_MATIERE M ON ME.idMatiere = M.idMatiere
            WHERE C.idCompte = ?
        ");
        $resultat->execute([$idCompte]);
        $rows = $resultat->fetchAll(PDO::FETCH_ASSOC);
        $liste = ($rows !== false) ? $rows : null;
        return $liste;
    }

    static public function getListeCompteEnsei ($idCompte) {
        require_once("config/connexion.php");
        Connexion::connect();
        $resultat = Connexion::pdo()->prepare("
            SELECT 
                C.idCompte,
                P.nomPersonne as nom,
                P.prenomPersonne as prenom,
                C.roleCompte,
                C.nomUtilisateur as username
            FROM AG_COMPTE C
            INNER JOIN AG_PERSONNE P ON C.idSecuriteSocialPersonne = P.idSecuriteSocialPersonne
            WHERE roleCompte != 'Etudiant'
            ORDER BY roleCompte, nomUtilisateur
        ");
        $resultat->execute();
        $rows = $resultat->fetchAll(PDO::FETCH_ASSOC);
        $liste = ($rows !== false) ? $rows : null;
        return $liste;
    }
}
?>