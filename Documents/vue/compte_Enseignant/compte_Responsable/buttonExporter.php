<form action="index.php" id="form_exporter_info_etudiant" method="GET">
    <?php 
        if (isset($_GET['groupe'])) {
            echo "<input type=\"hidden\" name=\"page\" value=\"profilEtuRes\">";
            echo "<input type=\"hidden\" name=\"promo\" value=\"{$_GET['promo']}\">";
            echo "<input type=\"hidden\" name=\"groupe\" value=\"{$_GET['groupe']}\">";
            echo "<input type=\"hidden\" name=\"etudiant\" value=\"{$_GET['etudiant']}\">";
        } else {
            echo "<input type=\"hidden\" name=\"page\" value=\"profilEtuRes\">";
            echo "<input type=\"hidden\" name=\"promo\" value=\"{$_GET['promo']}\">";
            echo "<input type=\"hidden\" name=\"etudiant\" value=\"{$_GET['etudiant']}\">";
        }
        if (isset($_GET['edit']) && $_GET['edit'] == 'false') echo "<input type=\"hidden\" name=\"edit\" value=\"true\">";
        if (isset($_GET['edit']) && $_GET['edit'] == 'true') echo "<input type=\"hidden\" name=\"edit\" value=\"false\">";    
        echo "<input type=\"hidden\" name=\"action\" value=\"exporter\">";
    ?>
    <button type="submit" id="button_exporter_info_etudiant">
        Exporter<i id="icon_exporter_info_etudiant" class="fi fi-br-file-export"></i>
    </button>
</form>