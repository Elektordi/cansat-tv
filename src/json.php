<?php
include "common.php";
if(!installed()) die();

$out = array();
$projs = getProjets();
$clubs = getClubs();
$aff = getAffichage($delai_fin);

$i = 1;
foreach($aff as $a) {
    $p = $projs[$a['projet']];
    $p['club'] = $clubs[$p['club']];

    if($p['etat']=='') $p['etat'] = "En attente";
    if($p['largueur']=='') $p['largueur'] = "Inconnu";
    
    $out[$i++] = $p;
    if($i==5) break;
}

$data = array( 'projets' => $out, 'ok' => 1, 'lock' => getConfig('lock', 0), 'fini' => (count($out)==0) );

closedb();
echo json_encode($data);

?>
