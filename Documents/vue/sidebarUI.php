<div class="side_bar_main_panel">
    <div class="side_bar_logo">
        <img src="assets/logo_red.png" alt="logo Paris-Saclay" draggable="false">
    </div>
    <div class="side_bar_text_info">
        <div class="side_bar_avatar_box">
            <?php if ($info_du_compte['avt'] != null) : ?>
                <img src="<?php echo $info_du_compte['avt']; ?>" alt="avt" draggable="false">
            <?php else: ?>
                <img src="assets/avt_null.png" alt="avt" draggable="false">
            <?php endif ?>
        </div>
        <div class="side_bar_text_content">
            <h3>Bienvenue !</h3>
            <p><?php echo "$nom $prenom"; ?></p>
        </div>
    </div>
    <div class="side_bar_button_navi">
        <div class="panel_button_nav">
            <?php 
                foreach ($list_button_navi as $nom => $lien) {
                    if ($nom == $pageCourant) {
                        echo "<a class=\"btn_nav_active\" href=\"index.php?page=$lien\">$nom</a>";
                    } else {
                        echo "<a href=\"index.php?page=$lien\">$nom</a>";
                    }
                }
            ?>
        </div>
    </div>
    <div class="side_bar_button_deconnexion">
        <form action="index.php?page=logout" method="POST">
            <button id="button_deconnexion" type="submit">Déconnexion</button>
        </form>
    </div>
</div>