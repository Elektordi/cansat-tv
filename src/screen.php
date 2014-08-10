<?php
include "common.php";
if(!installed()) die("ERROR");

?>
<html><head><title>CanSat-TV</title>
<style>

body {
    background: black;
    margin: 0px;
    overflow: hidden;
}

.content {
    position: relative;
    width: 1024px;
    height: 768px;
    margin: auto;
    background: #000042;
    background-image:url('images/cansat_template.png');
    color: #FFFFFF;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.sync-err {
    position: absolute;
    bottom: 10px;
    left: 10px;
    font-family: monospace;
    text-align: right;
    color: red;
    font-weight: bold;
    text-decoration: blink;
}

.page-temp {
    position: absolute;
    top: 600px;
    width: 100%;
    font-size: 64px;
    font-weight:bold;
    font-family: arial;
    text-align: center;
}

.titre {
    position: absolute;
    left: 200px;
    top: 40px;
    width: 500px;
    font-size: 64px;
    font-weight:bold;
    color: #c8ffff;
    font-family: arial;
    text-align: center;
}

.photo {
    position: absolute;
    left: 50px;
    top: 220px;
    width: 200px;
    height: 200px;
    border: 2px solid #FFFFFF;
    box-shadow: 1px 1px 12px #FFFFFF;
    -moz-border-radius: 15px;
    border-radius: 15px;
    overflow: hidden;
    background: #FFFFFF;
}

.club {
    position: absolute;
    left: 300px;
    top: 220px;
    font-size: 64px;
    
}

.projet {
    position: absolute;
    left: 300px;
    top: 290px;
    font-size: 64px;
}

.pays {
    position: absolute;
    left: 300px;
    top: 360px;
    font-size: 64px;
}

.etat {
    position: absolute;
    left: 0px;
    top: 470px;
    width: 100%;
    text-align: center;
    font-size: 64px;
    text-transform: uppercase;
}

.etat-att {
    color: #808080;
}

.etat-vert {
    color: #00FF00;
}

.etat-jaune {
    color: #FFFF00;
}

.etat-rouge {
    color: #FF0000;
}

.blink {
    text-decoration: blink;
}

.ville {
    position: absolute;
    left: 50px;
    top: 580px;
    font-size: 28px;
}

.vitesse {
    position: absolute;
    left: 50px;
    top: 620px;
    font-size: 28px;
}

.largueur {
    position: absolute;
    left: 50px;
    top: 660px;
    font-size: 28px;
}

.categorie {
    position: absolute;
    left: 50px;
    top: 700px;
    font-size: 28px;
}

.missions {
    position: absolute;
    left: 580px;
    top: 580px;
    font-size: 28px;
}

.missions ul {
    margin-top: 0px;
}

.liste {
    position: absolute;
    left: 20px;
    top: 170px;
    width: 984px;
    max-height: 580px;
    border-collapse: collapse;
}

.liste th {    
    color: #FFFFFF;
    font-size: 28px;
}

.liste td {    
    color: #FFFFFF;
    font-size: 28px;
    border: 3px solid rgba(255, 255, 255, .5);
    vertical-align: middle;
    padding-left: 5px;
    padding-right: 5px;
    padding-top: 0px;
    padding-bottom: 0px;
    max-height: 100px;
}

.liste td div {
    max-height: 100px;
    overflow: hidden;
    margin-top: 0px;
    margin-bottom: 0px;
    text-align: center;
}

td.c_photo {
    width: 100px;
    background: #FFFFFF;
    padding-left: 0px !important;
    padding-right: 0px !important;
    margin-left: 0px !important;
    margin-right: 0px !important;
}

td.c_club {
    max-width: 300px;
}

td.c_projet {
    max-width: 300px;
}

td.c_pays {
    max-width: 100px;
}

td.c_etat {
    max-width: 150px;
    text-align: center;
    text-transform: uppercase;
}

</style>
<script src="js/jquery-1.9.1.min.js" type="text/javascript"></script>
<script>

var page = 'temp';
var delay = 0;
var conf = jQuery.parseJSON("<?php echo addslashes(json_encode(array('etats' => getEtats(), 'style' => getEtatsStyle(), 'crit' => getEtatsCrit()))); ?>");

function color(etat) {
    var i;
    for(i=0;i<conf.etats.length;i++) {
        if(conf.etats[i] == etat) {
            return conf.style[i];
        }
    }
    return "att";
}

function tick() {
    $.ajax({
        url: "json.php",
        cache: false,
    })
    .error(function( json ) {
        $( ".sync-err" ).show();
    })
    .done(function( json ) {
        data = jQuery.parseJSON(json);
        if(data.ok) {
            $( ".sync-err" ).hide();
        } else {
            $( ".sync-err" ).show();
            return;
        }
        
        if(data.fini) {
                $( ".page-temp" ).html("Campagne terminée !");
                if(page!="temp") {
                    $( ".page-"+page ).fadeOut(1000);
                    $( ".page-temp" ).delay(1000).fadeIn(1000);
                    page='temp';
                }
                return;
        }
        
        for(i=1;i<=5;i++) {
            if(data.projets[i]) {
                $("#c_"+i).show();
                $("#c_"+i+"_photo").children("img").attr("src", "equipes/"+data.projets[i].image);
                $("#c_"+i+"_club").html(data.projets[i].club.nom);
                $("#c_"+i+"_projet").html(data.projets[i].nom);
                $("#c_"+i+"_pays").html(data.projets[i].club.pays);
                $("#c_"+i+"_etat").html("<span class=\"etat-"+color(data.projets[i].etat)+"\">"+data.projets[i].etat+"</span>");
            } else {
                $("#c_"+i).hide();
            }
        }
        
        critical = 0;
        for(i=0;i<conf.etats.length;i++) {
            if(conf.etats[i] == data.projets[1].etat) {
                critical = conf.crit[i];
                break;
            }
        }
        
        //if(page!='fiche' || critical) {
            $("#var-img").attr("src", "equipes/"+data.projets[1].image);
            $("#var-club").html(data.projets[1].club.nom);
            $("#var-projet").html(data.projets[1].nom);
            $("#var-pays").html(data.projets[1].club.pays);
            $("#var-etat").html("<span class=\"etat-"+color(data.projets[1].etat)+"\">"+data.projets[1].etat+"</span>");
            $("#var-ville").html(data.projets[1].club.ville);
            $("#var-vitesse").html(data.projets[1].vitesse);
            $("#var-largueur").html(data.projets[1].largueur);
            $("#var-categorie").html(data.projets[1].categorie);
            $("#var-missions").html("<li>"+data.projets[1].missions.replace(/\n/g,"</li><li>")+"</li>");
        //}
        
        if(critical) {
            if(page!='fiche') {
                $( ".page-"+page ).fadeOut(1000);
                $( ".page-fiche" ).delay(1000).fadeIn(1000);
                page='fiche';
                delay = 5;
            }
            return;
        }

        if(delay==0) {
            $( ".page-"+page ).fadeOut(1000);
            if(page=='temp') {
                $( ".page-liste" ).delay(1000).fadeIn(1000);
                page= 'liste';
            } else if(page=='liste') {
                $( ".page-fiche" ).delay(1000).fadeIn(1000);
                page='fiche';
            } else if(page=='fiche') {
                $( ".page-liste" ).delay(1000).fadeIn(1000);
                page='liste';
            }
            delay = 10;
        }
        delay--;
        
    });
}

window.setInterval("tick()",1000);

</script>
</head><body>

    <div class="content">
        <div class="sync-err">Sync lost!</div>
        <div class="titre"><?php echo getConfig('title'); ?></div>
        <div class="page-temp">Connexion en cours...</div>
        <div class="page-fiche" style="display: none;">
            <div class="photo"><img src="images/blank.gif" width="200" id="var-img"/></div>
            <div class="club">Club: <span id="var-club">???</span></div>
            <div class="projet">Projet: <span id="var-projet">???</span></div>
            <div class="pays">Pays: <span id="var-pays">???</span></div>
            <div class="etat" id="var-etat"><span class="etat-att">???</span></div>
            <div class="ville">Ville d'origine: <span id="var-ville">???</span></div>
            <div class="vitesse">Vitesse de descente: <span id="var-vitesse">???</span> m/s</div>
            <div class="largueur">Largueur: <span id="var-largueur">???</span></div>
            <div class="categorie">Catégorie: <span id="var-categorie">???</span></div>
            <div class="missions">Missions à réaliser: <br/>
                <ul><span id="var-missions">
                    <li>???</li>
                </span></ul>
            </div>
        </div>
        <div class="page-liste" style="display: none;">
            <table class="liste">
                <tr>
                    <th class="c_photo"><!-- Photo --></th>
                    <th class="c_club">Club</th>
                    <th class="c_projet">Projet</th>
                    <th class="c_pays">Pays</th>
                    <th class="c_etat">État</th>
                </tr>
                <tr id="c_1">
                    <td class="c_photo"><div id="c_1_photo"><img src="images/blank.gif" width="100"/></div></td>
                    <td class="c_club"><div id="c_1_club">???</div></td>
                    <td class="c_projet"><div id="c_1_projet">??? 1</div></td>
                    <td class="c_pays"><div id="c_1_pays">???</div></td>
                    <td class="c_etat"><div id="c_1_etat"><span class="etat-vol">???</span></div></td>
                </tr>
                <tr id="c_2">
                    <td class="c_photo"><div id="c_2_photo"><img src="images/blank.gif" width="100"/></div></td>
                    <td class="c_club"><div id="c_2_club">???</div></td>
                    <td class="c_projet"><div id="c_2_projet">??? 2</div></td>
                    <td class="c_pays"><div id="c_2_pays">???</div></td>
                    <td class="c_etat"><div id="c_2_etat"><span class="etat-att">???</span></div></td>
                </tr>
                <tr id="c_3">
                    <td class="c_photo"><div id="c_3_photo"><img src="images/blank.gif" width="100"/></div></td>
                    <td class="c_club"><div id="c_3_club">???</div></td>
                    <td class="c_projet"><div id="c_3_projet">??? 3</div></td>
                    <td class="c_pays"><div id="c_3_pays">???</div></td>
                    <td class="c_etat"><div id="c_3_etat"><span class="etat-att">???</span></div></td>
                </tr>
                <tr id="c_4">
                    <td class="c_photo"><div id="c_4_photo"><img src="images/blank.gif" width="100"/></div></td>
                    <td class="c_club"><div id="c_4_club">???</div></td>
                    <td class="c_projet"><div id="c_4_projet">??? 4</div></td>
                    <td class="c_pays"><div id="c_4_pays">???</div></td>
                    <td class="c_etat"><div id="c_4_etat"><span class="etat-att">???</span></div></td>
                </tr>
                <tr id="c_5">
                    <td class="c_photo"><div id="c_5_photo"><img src="images/blank.gif" width="100"/></div></td>
                    <td class="c_club"><div id="c_5_club">???</div></td>
                    <td class="c_projet"><div id="c_5_projet">??? 5</div></td>
                    <td class="c_pays"><div id="c_5_pays">???</div></td>
                    <td class="c_etat"><div id="c_5_etat"><span class="etat-att">???</span></div></td>
                </tr>
            </table>
        </div>
    </div>    
</body></html>
<?php closedb(); ?>
