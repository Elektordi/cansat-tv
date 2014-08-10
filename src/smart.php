<?php
include "common.php";
if(!installed()) die("ERROR");


?>
<html>
<head>
    <title>CanSat T.V. - Smartphone</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
</head>
<body>
    <h1 align="center">CanSat T.V.<br/>Interface simplifiée</h1>
    <hr/>
    <?php
    if($_SERVER['REQUEST_METHOD']=='POST') {
        if(isset($_POST['clic'])) {
            updateDb('projets', 'etat', $_POST['etat_suivant'], $_POST['rowid']);
        } elseif(isset($_POST['retour'])) {
            updateDb('projets', 'etat', $_POST['etat_precedant'], $_POST['rowid']);
        } elseif(isset($_POST['largueur'])) {
            updateDb('projets', 'largueur', $_POST['largueur'], $_POST['rowid']);
        }
        syncAffichage();
    }
    $aff = getAffichage(60);
    $projs = getProjets();
    $clubs = getClubs();
    $limite = time()-$delai_fin;
    ?>
    <table border="1" align="center" width="75%">
        <tr>
            <th>Nom projet</th>
            <th>Etat</th>
            <th>Largueur</th>
        </tr>
        <?php
        $etats = getEtats();
        foreach($aff as $l) {
            $l = array_merge($l, $projs[$l['projet']]);
            $color = "white";
            if($l['fin'] && $l['fin'] < $limite) $color="lightgrey";
            ?>
            <tr style="background-color: <?php echo $color; ?>;">
                <td><?php echo $l['nom']; ?></td>
                <td><form method="post" style="margin-bottom: 0px;"><input type="hidden" name="rowid" value="<?php echo $l['rowid']; ?>"/><?php
                echo $l['etat'].' - ';
                if($l['etat']!='') $k = array_search($l['etat'], $etats);
                else $k=0;
                
                if($k===false) {
                    echo '<font color="red">Êtat "'.$l['etat'].'" inconnu.</font>';
                } else {
                    if($k>0) {
                        echo '<input type="submit" name="retour" value="&lt;- '.$etats[$k-1].'" onclick="return confirm(\'Revenir en arrière ?\');" /><input type="hidden" name="etat_precedant" value="'.$etats[$k-1].'" />'; 
                    }
                    if($k<count($etats)-1) {
                        echo '<input type="submit" name="clic" value="-&gt; '.$etats[$k+1].'" /><input type="hidden" name="etat_suivant" value="'.$etats[$k+1].'" />'; 
                    } else {
                        echo '<font color="green">OK</font>';
                    }
                }
                ?></form></td>
                <td><form method="post" style="margin-bottom: 0px;"><input type="hidden" name="rowid" value="<?php echo $l['rowid']; ?>" /><select name="largueur" onchange="this.form.submit();">
                    <?php $largueurs = getLargueurs();
                    foreach($largueurs as $e) {
                        echo "<option value='".$e."' ".($e==$l['largueur']?"selected='1'":'').">".$e."</option>";
                    } ?>
                </select></form></td>
            </tr>
            <?php
        }
        ?>
    </table>
    <?php    
    closedb();
    ?>
    <p align="center"><input type="button" value="Resyncroniser" onclick="document.location='smart.php';"/></p>
    <hr/>
    <p align="right"><a href=".">Retour au menu...</a></p>
</body>
</html>
