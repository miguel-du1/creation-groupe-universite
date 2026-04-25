<?php 
class Modele {
    public function __construct($donnees = NULL) {
        if (!is_null($donnees)) {
            foreach ($donnees as $attribut => $valeur) {
                $this->set($attribut, $valeur);
            }
        }
    }

    public function get($attribut) {return $this->$attribut;}
    public function set($attribut, $valeur) {$this->$attribut = $valeur;}
}
?>