<form action="index.php?page=supprimerEtuPromo" id="form_supprimmer_etudiant_promo" method="POST">
    <?php 
        if (isset($_GET['groupe'])) {
            echo "<input type=\"hidden\" name=\"page\" value=\"groupeRes\">";
            echo "<input type=\"hidden\" name=\"promo\" value=\"{$_GET['promo']}\">";
            echo "<input type=\"hidden\" name=\"groupe\" value=\"{$_GET['groupe']}\">";
        } else {
            echo "<input type=\"hidden\" name=\"page\" value=\"promotionRes\">";
            echo "<input type=\"hidden\" name=\"promo\" value=\"{$_GET['promo']}\">";
        }
        echo "<input type=\"hidden\" name=\"etudiant\" value=\"{$_GET['etudiant']}\">";
    ?>
    <button type="submit" class="button">
        <span class="button__text">Désinscrire l’étudiant de la promotion <?php echo $info_du_compte_etu_res['promotion'] ?></span>
        <span class="button__icon"><i class="fi fi-br-delete-user"></i></span>
    </button>
</form>

