<?php
include "common.php";
if(!installed()) die("ERROR");

$mode = "direct";
if(isset($_GET['mode'])) $mode = $_GET['mode'];

?>
<html>
<head><title>CanSat T.V. - Interface de contrôle</title></head>
<style>
.active {
    font-weight:bold;
}
</style>
<body>
    <p align="center"><img src="images/vignette.jpg"/></p>
    <h1 align="center">CanSat T.V. - Interface de contrôle</h1>
    <p align="center"> -
        <a href="admin.php?mode=direct" <?php if($mode=='direct') echo "class='active'"; ?>>En direct</a> -
        <a href="admin.php?mode=projets" <?php if($mode=='projets') echo "class='active'"; ?>>Liste des projets</a> -
        <a href="admin.php?mode=clubs" <?php if($mode=='clubs') echo "class='active'"; ?>>Liste des clubs</a> -
        <a href="admin.php?mode=config" <?php if($mode=='config') echo "class='active'"; ?>>Options</a> -
    </p>
    <hr/>
    <?php
    if($mode=='direct') {
        if(isset($_GET['sync'])) syncAffichage(); // TEST 
        if(isset($_GET['bouge'])) {
            querydb("UPDATE affichage SET ordre=".($_GET['ordre']+0)." WHERE projet=".($_GET['bouge']+0));
            syncAffichage();
        }
        if($_SERVER['REQUEST_METHOD']=='POST') {
            if(isset($_POST['clic'])) {
                updateDb('projets', 'etat', $_POST['etat_suivant'], $_POST['rowid']);
            } elseif(isset($_POST['etat'])) {
                updateDb('projets', 'etat', $_POST['etat'], $_POST['rowid']);
            } elseif(isset($_POST['largueur'])) {
                updateDb('projets', 'largueur', $_POST['largueur'], $_POST['rowid']);
            }
            syncAffichage();
        }
        $aff = getAffichage();
        $projs = getProjets();
        $clubs = getClubs();
        $limite = time()-$delai_fin;
        ?>
        <p align="center">Contrôle en direct de l'affichage:</p>
        <p align="center"><i>Les lignes en gris sont terminés depuis plus de <?php echo $delai_fin/60; ?> minute(s) et ont disparues de l'affichage public.</i></p>
        <table border="1" align="center" width="75%">
            <tr>
                <th>Nom projet</th>
                <th>Club</th>
                <th>Freq.</th>
                <th>Missions</th>
                <th>Etat</th>
                <th>Largueur</th>
                <th>Ordre</th>
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
                    <td><?php echo $clubs[$l['club']]['nom']; ?></td>
                    <td><?php echo $l['freq']; ?></td>
                    <td><?php echo nl2br($l['missions']); ?></td>
                    <td><form method="post" style="margin-bottom: 0px;"><input type="hidden" name="rowid" value="<?php echo $l['rowid']; ?>"/><select name="etat">
                        <?php $etats = getEtats();
                        foreach($etats as $e) {
                            echo "<option value='".$e."' ".($e==$l['etat']?"selected='1'":'').">".$e."</option>";
                        } ?>
                    </select><input type="submit" value="OK"> - <?php
                    if($l['etat']!='') $k = array_search($l['etat'], $etats);
                    else $k=0;
                    
                    if($k===false) {
                        echo '<font color="red">Êtat "'.$l['etat'].'" inconnu.</font>';
                    } elseif($k<count($etats)-1) {
                        echo '<input type="submit" name="clic" value="-&gt; '.$etats[$k+1].'" /><input type="hidden" name="etat_suivant" value="'.$etats[$k+1].'" />'; 
                    } else {
                        echo '<font color="green">OK</font>';
                    }
                    ?></form></td>
                    <td><form method="post" style="margin-bottom: 0px;"><input type="hidden" name="rowid" value="<?php echo $l['rowid']; ?>"/><select name="largueur">
                        <?php $largueurs = getLargueurs();
                        foreach($largueurs as $e) {
                            echo "<option value='".$e."' ".($e==$l['largueur']?"selected='1'":'').">".$e."</option>";
                        } ?>
                    </select><input type="submit" value="OK"></form></td>
                    <td width="100"><a href="admin.php?mode=direct&bouge=<?php echo $l['rowid']; ?>&ordre=<?php echo $l['ordre']-15; ?>">Haut</a> / <a href="admin.php?mode=direct&bouge=<?php echo $l['rowid']; ?>&ordre=<?php echo $l['ordre']+15; ?>">Bas</a></td>
                </tr>
                <?php
            }
            ?>
        </table>
        <p align="center"><input type="button" value="Resyncroniser cette page" onclick="document.location='admin.php?mode=direct';"/></p>
        <?php    
    } else if($mode=='projets') { 
        if(isset($_GET['delete'])) {
            deleteDb('projets', $_GET['delete']);
        }
        if($_SERVER['REQUEST_METHOD']=='POST') {
            replaceDb('projets', array($_POST['nom'], $_POST['club'], $_POST['etat'], $_POST['categorie'], $_POST['vitesse'], $_POST['largueur'], $_POST['freq'], $_POST['missions'], $_POST['image']), $_POST['rowid']);
            syncAffichage();
        }
        $e = getProjets();
        $clubs = getClubs();
        ?>
        <table border="1" align="center" width="75%">
            <tr>
                <th>ID</th>
                <th>Nom projet</th>
                <th>Club</th>
                <th>Etat</th>
                <th>Catégorie</th>
                <th>Vitesse</th>
                <th>Largueur</th>
                <th>Freq</th>
                <th>Missions</th>
                <th>Image</th>
            </tr>
            <?php
            foreach($e as $l) {
                $color='white';
                if(isset($_GET['edit']) && $_GET['edit']==$l['rowid']) {
                    $le = $l;
                    $color = 'red';
                }
                
                ?>
                <tr style="background-color: <?php echo $color; ?>;">
                    <td><?php echo $l['rowid']; ?></td>
                    <td><?php echo $l['nom']; ?></td>
                    <td><?php echo $clubs[$l['club']]['nom']; ?></td>
                    <td><?php echo $l['etat']; ?></td>
                    <td><?php echo $l['categorie']; ?></td>
                    <td><?php echo $l['vitesse']; ?></td>
                    <td><?php echo $l['largueur']; ?></td>
                    <td><?php echo $l['freq']; ?></td>
                    <td><?php echo nl2br($l['missions']); ?></td>
                    <td><img height="50" src="equipes/<?php echo $l['image']; ?>"/></td>
                    <td width="100px"><a href="admin.php?mode=projets&edit=<?php echo $l['rowid']; ?>">Editer</a> / <a href="admin.php?mode=projets&delete=<?php echo $l['rowid']; ?>" onclick="return confirm('Confirmer suppression ?');">Suppr.</a></td>
                </tr>
                <?php
            }
            ?>
        </table>
        <hr/>
        <h3>Ajouter / Éditer projet:</h3>
        <form method="post">
            <p>Nom du projet: <input type="text" name="nom" value="<?php if($le) echo h($le['nom']); ?>" /></p>
            <p>Club: <select name="club">
                <?php foreach($clubs as $c) {
                    echo "<option value='".$c['rowid']."' ".($c['rowid']==$le['club']?"selected='1'":'').">".$c['nom']."</option>";
                } ?>
            </select></p>
            <p>Etat: <input type="text" name="etat" value="<?php if($le) echo h($le['etat']); ?>" /> (A laisser vide de base)</p>
            <p>Categorie: <input type="text" name="categorie" value="<?php if($le) echo h($le['categorie']); ?>" /></p>
            <p>Vitesse: <input type="text" name="vitesse" value="<?php if($le) echo h($le['vitesse']); ?>" /></p>
            <p>Largueur: <input type="text" name="largueur" value="<?php if($le) echo h($le['largueur']); ?>" /> (A laisser vide de base)</p>
            <p>Freq: <input type="text" name="freq" value="<?php if($le) echo h($le['freq']); ?>" /></p>
            <p>Missions: <textarea name="missions"><?php if($le) echo h($le['missions']); ?></textarea></p>
            <p>Image: <select name="image" onchange="preview.src='equipes/'+this.options[this.selectedIndex].value;">
                <option value=''>Aucune image</option>
                <?php
                $files = scandir('equipes/');
                foreach($files as $f) {
                    if(substr($f,0,1)=='.') continue;
                    echo "<option value='".$f."' ".($f==$le['image']?"selected='1'":'').">".$f."</option>";
                } ?>
            </select></p>
            <p><img src="images/blank.gif" name="preview"/></p>
            <p><input type="hidden" name="rowid" value="<?php if($le) echo h($le['rowid']); ?>"/><input type="submit" value="Enregistrer"/></p>
        </form>
        <?php
    } elseif($mode=='clubs') {
        if($_SERVER['REQUEST_METHOD']=='POST') {
            replaceDb('clubs', array($_POST['nom'], $_POST['ville'], $_POST['pays']), $_POST['rowid']);
        }
        $e = getClubs();
        ?>
        <table border="1" align="center" width="75%">
            <tr>
                <th>ID</th>
                <th>Nom club</th>
                <th>Ville d'origine</th>
                <th>Pays d'origine</th>
            </tr>
            <?php
            foreach($e as $l) {
                $color='white';
                if(isset($_GET['edit']) && $_GET['edit']==$l['rowid']) {
                    $le = $l;
                    $color = 'red';
                }
                
                ?>
                <tr style="background-color: <?php echo $color; ?>;">
                    <td><?php echo $l['rowid']; ?></td>
                    <td><?php echo $l['nom']; ?></td>
                    <td><?php echo $l['ville']; ?></td>
                    <td><?php echo $l['pays']; ?></td>
                    <td width="50px"><a href="admin.php?mode=clubs&edit=<?php echo $l['rowid']; ?>">Editer</a></td>
                </tr>
                <?php
            }
            ?>
        </table>
        <hr/>
        <h3>Ajouter / Éditer club:</h3>
        <form method="post">
            <p>Nom du club: <input type="text" name="nom" value="<?php if($le) echo h($le['nom']); ?>" /></p>
            <p>Ville: <input type="text" name="ville" value="<?php if($le) echo h($le['ville']); ?>" /></p>
            <p>Pays: <input type="text" name="pays" value="<?php if($le) echo h($le['pays']); ?>" /></p>
            <p><input type="hidden" name="rowid" value="<?php if($le) echo h($le['rowid']); ?>"/><input type="submit" value="Enregistrer"/></p>
        </form>
        <?php
    } elseif($mode=='config') {
        if($_SERVER['REQUEST_METHOD']=='POST') {
            foreach($_POST as $k => $v) {
                setConfig($k, $v);
            }
            echo "<p><b>Configuration enregistrée.</b></p>";
        }
        ?>
        <form method="post">
            <p>Nom de la rencontre: <input type="text" name="title" value="<?php echo h(getConfig('title')); ?>" /></p>
            <p>Liste des largueurs (séparés par des virgules): <input type="text" name="largueurs" value="<?php echo h(getConfig('largueurs')); ?>" /></p>
            <p><input type="submit" value="Valider"/></p>
        </form>
        <?php
    }
    closedb();
    ?>
    <hr/>
    <p align="right"><a href=".">Retour au menu...</a></p>
</body>
</html>
