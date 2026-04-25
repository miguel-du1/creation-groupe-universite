<?php
require_once 'modele/etudiant.php';
require_once 'modele/groupe.php';
require_once 'modele/promotion.php';

class Mode3 {
    
    const NB_ETUDIANTS_MIN_GROUPE = 20;
    const NB_ETUDIANTS_MAX_GROUPE = 25;
    const NB_FEMME_MIN_GROUPE = 5;

    private static array $code;
    public static function initCode() { if (!isset(self::$code)) self::$code = range('A', 'Z'); }

    // ------------------------------------------------- //
    // ------ FUNCTION POUR ALGO GLOUTON --------------- //
    // ------------------------------------------------- //

    static function chercherEtudiantsParcours(array $listE, string $parcours): array {
        $listECherche = [];
        foreach ($listE as $etudiant) {
            if ($parcours === $etudiant->get("parcoursEtudiant")) {
                $listECherche[] = $etudiant;
            }
        }
        return $listECherche;
    }

    static function nbFemmesListe(array $listE): int {
        $compterFemmes = 0;
        foreach ($listE as $etudiant) {
            if ($etudiant->get("sexePersonne") === "F") {
                $compterFemmes++;
            }
        }
        return $compterFemmes;
    }

    // ------------------------------------------------- //
    // ------ FUNCTION POUR ALGO BRUTE FORCE ----------- //
    // ------------------------------------------------- //

    public static function organiserGroupesParHasard(array $listE) {
        self::initCode();

        $nbGroupesCrees = 1; // Minimum
        $nbEtudiantTotal = count($listE);
        $listGroupsCrees = [];

        // Si la liste donnee n'a pas suffisament d'etudiant -> return un groupe exceptionel
        if ($nbEtudiantTotal < self::NB_ETUDIANTS_MIN_GROUPE) {
            $groupeCree = new Groupe(self::$code[$nbGroupesCrees - 1] ?? 'A', $nbGroupesCrees - 1);
            foreach ($listE as $etudiant) {
                $groupeCree->ajouteEtudiant($etudiant, self::NB_ETUDIANTS_MAX_GROUPE);
            }
            $listGroupsCrees[] = $groupeCree;
            return $listGroupsCrees;
        }

        // Calculer le nombre de groupes crees minimum | maximum
        $nbGroupesMin = (int) ceil($nbEtudiantTotal / self::NB_ETUDIANTS_MAX_GROUPE);
        $nbGroupesMax = intdiv($nbEtudiantTotal, self::NB_ETUDIANTS_MIN_GROUPE);

        // Si le minimum est plus grand que le maximum -> return les groupes exceptionels
        if ($nbGroupesMin > $nbGroupesMax) {

            $nbTotalGroups = $nbGroupesMax;

            if ($nbTotalGroups <= 0) {
                $groupeCree = new Groupe(self::$code[0] ?? 'A', 0);
                foreach ($listE as $etudiant) {
                    $groupeCree->ajouteEtudiant($etudiant, self::NB_ETUDIANTS_MAX_GROUPE);
                }
                $listGroupsCrees[] = $groupeCree;
                return $listGroupsCrees;
            }

            $index = 0;

            // Créer des groupes de taille minimale
            for ($g = 0; $g < $nbTotalGroups; $g++) {
                $groupeCree = new Groupe(self::$code[$g] ?? chr(ord('A') + $g) , $g);
                for ($k = 0; $k < self::NB_ETUDIANTS_MIN_GROUPE && $index < $nbEtudiantTotal; $k++) {
                    $groupeCree->ajouteEtudiant($listE[$index], self::NB_ETUDIANTS_MAX_GROUPE);
                    $index++;
                }
                $listGroupsCrees[] = $groupeCree;
            }

            // Le reste
            if ($index < $nbEtudiantTotal) {
                $groupeExcep = new Groupe(self::$code[$nbTotalGroups] ?? chr(ord('A') + $nbTotalGroups), $nbTotalGroups);
                while ($index < $nbEtudiantTotal) {
                    $groupeExcep->ajouteEtudiant($listE[$index], self::NB_ETUDIANTS_MAX_GROUPE);
                    $index++;
                }
                $listGroupsCrees[] = $groupeExcep;
            }

            return $listGroupsCrees;
        }

        // Choisir aléatoirement un nombre de groupes valide
        $nbGroups = random_int($nbGroupesMin, $nbGroupesMax);

        // Initialiser chaque groupe avec minSize personnes
        $sizes = array_fill(0, $nbGroups, self::NB_ETUDIANTS_MIN_GROUPE);

        $restant = $nbEtudiantTotal - self::NB_ETUDIANTS_MIN_GROUPE * $nbGroups;

        // Répartir le reste dans les groupes, sans dépasser maxSize
        $i = 0;
        while ($restant > 0) {
            if ($sizes[$i] < self::NB_ETUDIANTS_MAX_GROUPE) {
                $sizes[$i]++;
                $restant--;
            }
            $i = ($i + 1) % $nbGroups;
        }

        // Créer des groupes selon les tailles
        $index = 0;
        for ($g = 0; $g < count($sizes); $g++) {
            $size = $sizes[$g];
            $groupeCree = new Groupe(self::$code[$g] ?? chr(ord('A') + $g) , $g);
            for ($k = 0; $k < $size && $index < $nbEtudiantTotal; $k++) {
                $groupeCree->ajouteEtudiant($listE[$index], self::NB_ETUDIANTS_MAX_GROUPE);
                $index++;
            }
            $listGroupsCrees[] = $groupeCree;
        }

        return $listGroupsCrees;
    }

    /**
     * Fonction pour verifier le nombre de femmes minimum
     * @param Groupe[] $listeGroupes
     */
    public static function validGenderConstraint(array $listeGroupes) {
        foreach ($listeGroupes as $groupe) {
            $femaleCount = 0;
            foreach ($groupe->get("listEtudiants") as $etudiant) {
                if ($etudiant->get('sexePersonne') === 'F') {
                    $femaleCount++;
                }
            }
            if (!($femaleCount === 0 || $femaleCount >= self::NB_FEMME_MIN_GROUPE)) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param Groupe[] $listeGroupes
     */
    public static function imbalanceScore(array $listeGroupes) {
        if (empty($listeGroupes)) return INF;

        $minAvg = INF;
        $maxAvg = -INF;

        foreach ($listeGroupes as $groupe) {
            if ($groupe->get("nbEtudiant") === 0) return INF;

            $total = 0.0;
            foreach ($groupe->get("listEtudiants") as $etudiant) {
                $total += (float) $etudiant->getNote();
            }
            $avg = $total / $groupe->get("nbEtudiant");

            if ($avg < $minAvg) $minAvg = $avg;
            if ($avg > $maxAvg) $maxAvg = $avg;
        }

        return $maxAvg - $minAvg;
    }

    /**
     * @param Etudiant[] $listE
     * @return Groupe[]
     */
    public static function constituerBruteForce(array $listE): array {
        self::initCode();

        $bestGroups = [];
        $bestScore = INF;

        for ($iter = 0; $iter < 1000; $iter++) {
            // Mélanger aléatoirement la liste des étudiants
            $shuffled = $listE;
            shuffle($shuffled);

            // Répartir aléatoirement en groupes de taille comprise entre minSize et maxSize
            $groupes = self::organiserGroupesParHasard($shuffled);

            // Vérifier la condition pour les filles
            if (!self::validGenderConstraint($groupes)) {
                continue;
            }

            // Calculer l’écart de score
            $score = self::imbalanceScore($groupes);

            if ($score < $bestScore) {
                $bestScore = $score;
                $bestGroups = $groupes;
            }
        }

        return $bestGroups;
    }

    // ------------------------------------------------- //
    // ------------------- GLOUTON --------------------- //
    // ------------------------------------------------- //

    /** @param Etudiant[] $listE @return Groupe[] */
    public static function constituerGlouton1(array $listE): array
    {
        self::initCode();

        $listGConstitues = [];

        // --- PREPARER POUR ALGO --- //
        $nbFemmePromo   = self::nbFemmesListe($listE);
        $nbHommePromo   = count($listE) - $nbFemmePromo;
        $nbEtudiantPromo = count($listE);

        $nbGroupePrevu = 0;
        $typeDeGroupeAlgoChoisi = 0; // 20 ou 25
        $nbEtudiantRestantAlgo = 0;

        if (($nbEtudiantPromo % self::NB_ETUDIANTS_MAX_GROUPE) >= self::NB_ETUDIANTS_MIN_GROUPE
            || ($nbEtudiantPromo % self::NB_ETUDIANTS_MAX_GROUPE) === 0) {

            $nbGroupePrevu = intdiv($nbEtudiantPromo, self::NB_ETUDIANTS_MAX_GROUPE);
            $typeDeGroupeAlgoChoisi = self::NB_ETUDIANTS_MAX_GROUPE;

            if (($nbEtudiantPromo % self::NB_ETUDIANTS_MAX_GROUPE) >= self::NB_ETUDIANTS_MIN_GROUPE) {
                $nbGroupePrevu++;
                $nbEtudiantRestantAlgo = $nbEtudiantPromo % self::NB_ETUDIANTS_MAX_GROUPE;
            }
        } else {
            $nbGroupePrevu = intdiv($nbEtudiantPromo, self::NB_ETUDIANTS_MIN_GROUPE);
            $typeDeGroupeAlgoChoisi = self::NB_ETUDIANTS_MIN_GROUPE;
            $nbEtudiantRestantAlgo  = $nbEtudiantPromo % self::NB_ETUDIANTS_MIN_GROUPE;
        }

        // femmes par groupe
        $nbGroupeAyantFemme = intdiv($nbFemmePromo, self::NB_FEMME_MIN_GROUPE);
        $nbGroupeHommeUnique = 0;
        $nbFemmeMinParGroupe = self::NB_FEMME_MIN_GROUPE;

        if ($nbGroupeAyantFemme >= $nbGroupePrevu) {
            $nbGroupeAyantFemme = $nbGroupePrevu;
            $nbFemmeMinParGroupe = intdiv($nbFemmePromo, $nbGroupePrevu);
        } else {
            $nbGroupeHommeUnique = $nbGroupePrevu - $nbGroupeAyantFemme;
        }

        $nbFemmePasDansUnGroupe = $nbFemmePromo - ($nbGroupeAyantFemme * $nbFemmeMinParGroupe);

        // tránh chia 0
        $nbFemmeMaxParGroupe = $nbFemmeMinParGroupe;
        if ($nbGroupeAyantFemme > 0) {
            $nbFemmeAjouterTraiterRestant = $nbFemmePasDansUnGroupe / $nbGroupeAyantFemme;
            $differentFemmeMax = (int) ceil($nbFemmeAjouterTraiterRestant);
            $nbFemmeMaxParGroupe = $nbFemmeMinParGroupe + $differentFemmeMax;
        }

        // Creer les groupes
        $totalGroupeCrees = $nbGroupeAyantFemme + $nbGroupeHommeUnique;
        for ($i = 0; $i < $totalGroupeCrees; $i++) {
            $listGConstitues[] = new Groupe(self::$code[$i] ?? chr(ord('A') + $i) , $i);
        }

        $differentEtudiantMax = 0;
        if ($totalGroupeCrees > 0) {
            $nbEtudiantAjouterTraiterRestant = $nbEtudiantRestantAlgo / $totalGroupeCrees;
            $differentEtudiantMax = (int) ceil($nbEtudiantAjouterTraiterRestant);
        }

        $nbEtudiantMaxParGroupe = $typeDeGroupeAlgoChoisi;
        if ($typeDeGroupeAlgoChoisi === self::NB_ETUDIANTS_MIN_GROUPE) {
            $nbEtudiantMaxParGroupe = $typeDeGroupeAlgoChoisi + $differentEtudiantMax;
        }
        if ($typeDeGroupeAlgoChoisi === self::NB_ETUDIANTS_MAX_GROUPE) {
            $nbEtudiantMaxParGroupe = self::NB_ETUDIANTS_MAX_GROUPE;
        }

        $nbHommePasDansUnGroupe = $nbHommePromo - (
            (($typeDeGroupeAlgoChoisi - $nbFemmeMaxParGroupe) * $nbFemmePasDansUnGroupe)
            + (($typeDeGroupeAlgoChoisi - $nbFemmeMinParGroupe) * ($totalGroupeCrees - $nbFemmePasDansUnGroupe))
        );
        if ($nbHommePasDansUnGroupe < 0) $nbHommePasDansUnGroupe = 0;

        // Filtrer les étudiants selon chaque parcours
        $groupeCourant = 0;
        $nbFemmePromoRestant = $nbFemmePromo;
        $nbHommePromoRestant = $nbHommePromo;
        $nbEtudiantPromoRestant = $nbEtudiantPromo;

        $traiterFemmeRestant = false;

        foreach (Promotion::LIST_PARCOURS as $parcour) {
            $listEtudiantParParcours = self::chercherEtudiantsParcours($listE, (string)$parcour);

            foreach ($listEtudiantParParcours as $etudiant) {

                if ($nbFemmePromoRestant <= $nbFemmePasDansUnGroupe) $traiterFemmeRestant = true;

                // --- FEMME --- //
                $groupeAjouteFemme = $groupeCourant;

                if ($etudiant->get('sexePersonne') === "F") {

                    // condition autorisation femme
                    $condAutoriseFemme = false;
                    if ($nbGroupeAyantFemme <= 0) {
                        // pas de groupe "ayant femme" => on traite comme homme-only groups, on ajoute où possible
                        $condAutoriseFemme = true;
                    } else {
                        if ($traiterFemmeRestant) {
                            $condAutoriseFemme = $listGConstitues[$groupeAjouteFemme]->getNbFemmeGroupe() < $nbFemmeMaxParGroupe;
                        } else {
                            $condAutoriseFemme = $listGConstitues[$groupeAjouteFemme]->getNbFemmeGroupe() < $nbFemmeMinParGroupe;
                        }
                    }

                    while (!($condAutoriseFemme && $listGConstitues[$groupeAjouteFemme]->peutAjouter(self::NB_ETUDIANTS_MAX_GROUPE))) {

                        if ($nbGroupeAyantFemme > 0) {
                            if ($groupeAjouteFemme >= $nbGroupeAyantFemme - 1) $groupeAjouteFemme = 0;
                            else $groupeAjouteFemme++;
                        } else {
                            if ($groupeAjouteFemme >= $totalGroupeCrees - 1) $groupeAjouteFemme = 0;
                            else $groupeAjouteFemme++;
                        }

                        if ($nbGroupeAyantFemme <= 0) {
                            $condAutoriseFemme = true;
                        } else {
                            if ($traiterFemmeRestant) {
                                $condAutoriseFemme = $listGConstitues[$groupeAjouteFemme]->getNbFemmeGroupe() < $nbFemmeMaxParGroupe;
                            } else {
                                $condAutoriseFemme = $listGConstitues[$groupeAjouteFemme]->getNbFemmeGroupe() < $nbFemmeMinParGroupe;
                            }
                        }
                    }

                    $listGConstitues[$groupeAjouteFemme]->ajouteEtudiant($etudiant, self::NB_ETUDIANTS_MAX_GROUPE);
                    $nbFemmePromoRestant--;
                    $nbEtudiantPromoRestant--;
                    continue;
                }

                // --- HOMME --- //
                $groupeAjouteHomme = $groupeCourant;

                $ajouterJusqua = $typeDeGroupeAlgoChoisi;
                if ($nbHommePromoRestant <= $nbEtudiantRestantAlgo) $ajouterJusqua = $nbEtudiantMaxParGroupe;

                $cond1 = false;
                if ($groupeAjouteHomme < $nbGroupeAyantFemme) {
                    if ($groupeAjouteHomme < $nbFemmePasDansUnGroupe) {
                        $cond1 = $listGConstitues[$groupeAjouteHomme]->getNbHommeGroupe() + 1 <= $ajouterJusqua - $nbFemmeMaxParGroupe;
                    } else {
                        $cond1 = $listGConstitues[$groupeAjouteHomme]->getNbHommeGroupe() + 1 <= $ajouterJusqua - $nbFemmeMinParGroupe;
                    }
                } else {
                    $cond1 = $listGConstitues[$groupeAjouteHomme]->getNbHommeGroupe() + 1 <= $typeDeGroupeAlgoChoisi;
                }

                $cond2 = false;
                if ($nbFemmePromoRestant === 0) {
                    $cond2 = $listGConstitues[$groupeAjouteHomme]->get("nbEtudiant") + 1 <= $typeDeGroupeAlgoChoisi;
                }

                $cond3 = false;
                if ($nbHommePromoRestant <= $nbHommePasDansUnGroupe) {
                    if ($groupeAjouteHomme < $nbFemmePasDansUnGroupe) {
                        $cond3 = $listGConstitues[$groupeAjouteHomme]->getNbHommeGroupe() + 1 <= $ajouterJusqua - $nbFemmeMaxParGroupe;
                    } else {
                        $cond3 = $listGConstitues[$groupeAjouteHomme]->getNbHommeGroupe() + 1 <= $ajouterJusqua - $nbFemmeMinParGroupe;
                    }
                }

                $condAutoriseHomme = ($cond1 || $cond2 || $cond3);

                while (!($condAutoriseHomme && $listGConstitues[$groupeAjouteHomme]->peutAjouter(self::NB_ETUDIANTS_MAX_GROUPE))) {

                    if ($groupeAjouteHomme === $totalGroupeCrees - 1) $groupeAjouteHomme = 0;
                    else $groupeAjouteHomme++;

                    // update conditions
                    if ($groupeAjouteHomme < $nbGroupeAyantFemme) {
                        if ($groupeAjouteHomme < $nbFemmePasDansUnGroupe) {
                            $cond1 = $listGConstitues[$groupeAjouteHomme]->getNbHommeGroupe() + 1 <= $ajouterJusqua - $nbFemmeMaxParGroupe;
                        } else {
                            $cond1 = $listGConstitues[$groupeAjouteHomme]->getNbHommeGroupe() + 1 <= $ajouterJusqua - $nbFemmeMinParGroupe;
                        }
                    } else {
                        $cond1 = $listGConstitues[$groupeAjouteHomme]->getNbHommeGroupe() + 1 <= $ajouterJusqua;
                    }

                    if ($nbFemmePromoRestant === 0) {
                        $cond2 = $listGConstitues[$groupeAjouteHomme]->get("nbEtudiant") + 1 <= $typeDeGroupeAlgoChoisi;
                    } else {
                        $cond2 = false;
                    }

                    if ($nbHommePromoRestant <= $nbHommePasDansUnGroupe) {
                        if ($groupeAjouteHomme < $nbFemmePasDansUnGroupe) {
                            $cond3 = $listGConstitues[$groupeAjouteHomme]->getNbHommeGroupe() + 1 <= $ajouterJusqua - $nbFemmeMaxParGroupe;
                        } else {
                            $cond3 = $listGConstitues[$groupeAjouteHomme]->getNbHommeGroupe() + 1 <= $ajouterJusqua - $nbFemmeMinParGroupe;
                        }
                    } else {
                        $cond3 = false;
                    }

                    $condAutoriseHomme = ($cond1 || $cond2 || $cond3);
                }

                $listGConstitues[$groupeAjouteHomme]->ajouteEtudiant($etudiant, self::NB_ETUDIANTS_MAX_GROUPE);
                $nbHommePromoRestant--;
                $nbEtudiantPromoRestant--;
            }
        }

        return $listGConstitues;
    }

    /** @param Etudiant[] $listE @return Groupe[] */
    public static function constituerGlouton2(array $listE, $type_G, $semestre_G,) {
        self::initCode();

        // --- SEPARER PAR LES LISTES INDEPENDANTES --- //
        $listGConstitues = [];

        $listHommePromo = [];
        $listFemmePromo = [];

        foreach (Promotion::LIST_PARCOURS as $parcour) {
            $listEtudiantParParcours = self::chercherEtudiantsParcours($listE, (string)$parcour);
            foreach ($listEtudiantParParcours as $etudiant) {
                if ($etudiant->get("sexePersonne") === "F") {
                    $listFemmePromo[] = $etudiant;
                    continue;
                }
                $listHommePromo[] = $etudiant;
            }
        }

        // --- PREPARER POUR ALGO --- //
        $nbHommePromo = count($listHommePromo);
        $nbFemmePromo = count($listFemmePromo);
        $nbEtudiantPromo = $nbHommePromo + $nbFemmePromo;

        $nbGFemmePlusGrandPromo = false;

        $typeGroupeAlgoChoisi = self::NB_ETUDIANTS_MAX_GROUPE;
        $nbGroupePrevu = intdiv($nbEtudiantPromo, self::NB_ETUDIANTS_MAX_GROUPE);

        if (($nbEtudiantPromo % self::NB_ETUDIANTS_MAX_GROUPE) >= 20) {
            $nbGroupePrevu++;
        } else {
            $nbGroupePrevu = intdiv($nbEtudiantPromo, self::NB_ETUDIANTS_MIN_GROUPE);
            $typeGroupeAlgoChoisi = self::NB_ETUDIANTS_MIN_GROUPE;
        }

        $nbGroupeAyantFemme = intdiv($nbFemmePromo, self::NB_FEMME_MIN_GROUPE);
        if ($nbGroupeAyantFemme >= $nbGroupePrevu) {
            $nbGFemmePlusGrandPromo = true;
            $nbGroupeAyantFemme = $nbGroupePrevu;
        }

        $nbMinFemmeDansUnGroupe = self::NB_FEMME_MIN_GROUPE;
        $nbMaxFemmeDansUnGroupe = $nbMinFemmeDansUnGroupe;

        if (!$nbGFemmePlusGrandPromo) {
            $nbMinFemmeDansUnGroupe = self::NB_FEMME_MIN_GROUPE;
        } else {
            $nbMinFemmeDansUnGroupe = intdiv($nbFemmePromo, $nbGroupePrevu);
        }

        // femmes restantes
        if (!$nbGFemmePlusGrandPromo) {
            $nbFemmePasDansGroupe = $nbFemmePromo - ($nbGroupeAyantFemme * self::NB_FEMME_MIN_GROUPE);
        } else {
            $nbFemmePasDansGroupe = $nbFemmePromo - ($nbMinFemmeDansUnGroupe * $nbGroupeAyantFemme);
        }

        if ($nbGroupeAyantFemme > 0 && ($nbFemmePasDansGroupe % $nbGroupeAyantFemme) > 0) {
            $nbMaxFemmeDansUnGroupe = $nbMinFemmeDansUnGroupe + (int) ceil($nbFemmePasDansGroupe / $nbGroupeAyantFemme);
        }

        // hommes dans groupes "ayant femme"
        $nbHommeDansGroupe = $nbGroupeAyantFemme * (self::NB_ETUDIANTS_MIN_GROUPE - self::NB_FEMME_MIN_GROUPE) - $nbFemmePasDansGroupe;
        if ($nbHommeDansGroupe >= $nbHommePromo) $nbHommeDansGroupe = $nbHommePromo;

        $nbHommePasDansGroupe = $nbHommePromo - $nbHommeDansGroupe;

        $nbPlaceLibres = $nbGroupeAyantFemme * (self::NB_ETUDIANTS_MAX_GROUPE - self::NB_ETUDIANTS_MIN_GROUPE);
        $nbGroupeHommeUnique = 0;
        $nbHommeRestant = $nbHommePasDansGroupe;

        if ($nbHommePasDansGroupe > $nbPlaceLibres) {
            $nbGroupeCrees = 1;
            while (true) {
                if (($nbHommePasDansGroupe - (self::NB_ETUDIANTS_MAX_GROUPE * $nbGroupeCrees)) <= $nbPlaceLibres) {
                    $nbGroupeHommeUnique = $nbGroupeCrees;
                    $nbHommeRestant = ($nbHommePasDansGroupe - (self::NB_ETUDIANTS_MAX_GROUPE * $nbGroupeCrees));
                    break;
                }
                $nbGroupeCrees++;
            }
        }

        // --- COMMENCER A CONSTITUER --- //
        $nbGroupeTotal = $nbGroupeAyantFemme + $nbGroupeHommeUnique;
        for ($i = 0; $i < $nbGroupeTotal; $i++) {
            $listGConstitues[] = new Groupe(
                "TP",
                $semestre_G,
                self::$code[$i] ?? chr(ord('A') + $i),
                $i
            );
        }

        // --- FEMME --- //
        $groupeCourrant = 0;
        $compterEtudant = 0;

        foreach ($listFemmePromo as $etudiant) {

            $nbFemmeAjoute = ($groupeCourrant < $nbFemmePasDansGroupe) ? $nbMaxFemmeDansUnGroupe : $nbMinFemmeDansUnGroupe;

            if ($compterEtudant === $nbFemmeAjoute) {
                $compterEtudant = 0;
                $groupeCourrant++;
            }

            if ($groupeCourrant === $nbGroupeAyantFemme) {
                $groupeCourrant = 0;
                $nbFemmeAjoute = 1;
            }

            $listGConstitues[$groupeCourrant]->ajouteEtudiant($etudiant, self::NB_ETUDIANTS_MAX_GROUPE);
            $compterEtudant++;
        }

        // --- HOMME --- //
        $groupeCourrant = 0;
        $compterEtudant = 0;

        foreach ($listHommePromo as $etudiant) {

            if ($groupeCourrant < $nbFemmePasDansGroupe) {
                $nbHommeAjoute = $typeGroupeAlgoChoisi - $nbMaxFemmeDansUnGroupe;
            } elseif ($groupeCourrant < $nbGroupeAyantFemme) {
                $nbHommeAjoute = $typeGroupeAlgoChoisi - $nbMinFemmeDansUnGroupe;
            } else {
                $nbHommeAjoute = $typeGroupeAlgoChoisi;
            }

            if ($compterEtudant === $nbHommeAjoute) {
                $compterEtudant = 0;
                if ($groupeCourrant === $nbGroupeTotal - 1) $groupeCourrant = 0;
                else $groupeCourrant++;
            }

            while (!$listGConstitues[$groupeCourrant]->peutAjouter(self::NB_ETUDIANTS_MAX_GROUPE)) {
                if ($groupeCourrant === $nbGroupeTotal - 1) $groupeCourrant = 0;
                else $groupeCourrant++;
            }

            $listGConstitues[$groupeCourrant]->ajouteEtudiant($etudiant, self::NB_ETUDIANTS_MAX_GROUPE);
            $compterEtudant++;
        }

        return $listGConstitues;
    }
}
?>