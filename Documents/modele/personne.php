<?php 
abstract class Personne {
    // Attributs
    protected $idSecuriteSocialPersonne;
    protected $nomPersonne;
    protected $prenomPersonne;
    protected $emailPersonne;
    protected $telPersonne;
    protected $naissancePersonne;
    protected $sexePersonne;
    protected $etatCivilPersonne;
    protected $adrPersonne;

    // Constructeur
    public function __construct(
        $id = null,
        $nom = null,
        $prenom = null,
        $email = null,
        $telephone = null,
        $naissance = null,
        $sexe = null,
        $etatCivil = null,
        $adresse = null
    ) {
        if ($id !== null) $this->idSecuriteSocialPersonne = $id;
        if ($nom !== null) $this->nomPersonne = $nom;
        if ($prenom !== null) $this->prenomPersonne = $prenom;
        if ($email !== null) $this->emailPersonne = $email;
        if ($telephone !== null) $this->telPersonne = $telephone;
        if ($naissance !== null) $this->naissancePersonne = $naissance;
        if ($sexe !== null) $this->sexePersonne = $sexe;
        if ($etatCivil !== null) $this->etatCivilPersonne = $etatCivil;
        if ($adresse !== null) $this->adrPersonne = $adresse;
    }

    public function get($attribut) {return $this->$attribut;}
    public function set($attribut, $valeur) {$this->$attribut = $valeur;}
}
?>