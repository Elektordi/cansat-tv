<?php
include "common.php";
?>
<html>
<head><title>CanSat T.V.</title></head>
<?php if (preg_match('/(Android|iPhone|iPad|Touch|Nintendo 3DS)/', $_SERVER['HTTP_USER_AGENT'])) { ?>
<script>

if(confirm("Aller sur l'interface simplifié pour smartphone ?")) {
    document.location = "smart.php";
}

</script>
<?php } ?>
<body>
    <p align="center"><img src="images/vignette.jpg"/></p>
    <h1 align="center">CanSat T.V.</h1>
    <hr/>
    <?php
    if(installed()) {
    ?>
    <p align="center"><a href="screen.php">Écran de diffusion</a></p>
    <p align="center"><a href="admin.php">Interface de contrôle</a></p>
    <p align="center"><a href="smart.php">Contrôle simplifié</a></p>
    <?php 
    } else {
    ?>
    <p align="center"><a href="install.php">Initialiser le logiciel</a></p>
    <?php 
    }
    ?>
    <hr/>
    <p><i>CanSat T.V. version 0.5 - Sous license MIT - <a href="https://github.com/Elektordi/cansat-tv" target="_blank">Fork me on Github</a></i></p>
    <p><i>Développé par Guillaume Genty, Association Planète Sciences.</i></p>
</body>
</html>
