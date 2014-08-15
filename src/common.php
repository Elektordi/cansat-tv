<?php

$delai_fin = 30;

$dbfile = 'db/cansat.db';
$dbhandle = null;

header("Content-Type: text/html; charset=utf-8");
ini_set('default_charset', 'UTF-8');
//session_start();

function installed() {
    global $dbfile;
    return file_exists($dbfile);
}

function querydb($q) {
    global $dbhandle, $dbfile;
    if(!$dbhandle) $dbhandle = new SQLite3($dbfile);
    if(!$dbhandle) die();
    //var_dump($q);
    $r = $dbhandle->query($q);
    
    if(!$r) die(); //sqlite_error_string
    return $r;
}

function closedb() {
    global $dbhandle;
    if(!$dbhandle) return;
    $dbhandle->close();
}

function getProjets() {
    $liste = array();
    $r = querydb("SELECT rowid,* FROM projets;");
    while($a = $r->fetchArray(SQLITE3_ASSOC)) {
        $liste[$a['rowid']] = $a;
    }
    return $liste;
}

function getClubs() {
    $liste = array();
    $r = querydb("SELECT rowid,* FROM clubs;");
    while($a = $r->fetchArray(SQLITE3_ASSOC)) {
        $liste[$a['rowid']] = $a;
    }
    return $liste;
}

function getAffichage($marge = null) {
    $liste = array();
    $where = 1;
    if($marge!==null) $where = "fin > ".(time()-$marge);
    $r = querydb("SELECT rowid,* FROM affichage WHERE $where OR fin=0 ORDER BY ordre ASC;");
    while($a = $r->fetchArray(SQLITE3_ASSOC)) {
        $liste[$a['rowid']] = $a;
    }
    return $liste;
}

function syncAffichage() {
    $aa = getAffichage();
    $pa = getProjets();
    $ea = getEtats();
    $i = 100;
    $out = array();
    foreach($aa as $a) {
        if(!isset($pa[$a['projet']])) continue;
        $p = $pa[$a['projet']];
        unset($pa[$a['projet']]);
        $fin = 0;
        $k = array_search($p['etat'], $ea);
        if($k==count($ea)-1){
            if($a['fin']) {
                $fin = $a['fin'];
            } else {
               $fin = time(); 
            }
        }
        $out[]= '('.$p['rowid'].','.$i.','.$fin.')';
        $i+=10;
    }
    foreach($pa as $p) {
        $out[]= '('.$p['rowid'].','.$i.',0)';
        $i+=10;
    }

    querydb("BEGIN EXCLUSIVE TRANSACTION");
    querydb("DELETE FROM affichage");
    foreach($out as $q) querydb("INSERT INTO affichage VALUES ".$q.";");
    querydb("COMMIT TRANSACTION");
}

function getLargueurs() {
    return array_merge(array('En attente'), explode(',', getConfig('largueurs')));
}

function getEtats() {
    return array('En attente', 'Préparation', 'Intégré dans largueur', 'Montée aérostat', 'Prêt pour largage', 'Récupération', 'Mission terminée');
}

function getEtatsStyle() {
    return array('att', 'jaune', 'jaune', 'rouge', 'rouge blink', 'vert blink', 'vert');
}

function getEtatsCrit() {
    return array(0,0,0,1,1,0,0);
}

function replaceDb($table, $fields, $id=null) {
    if($id) {
        $r = querydb("PRAGMA table_info($table);");
        if(!$r) return;
        $q = "UPDATE $table SET ";
        $i=0;
        while($a = $r->fetchArray(SQLITE3_ASSOC)) {
            if(isset($fields[$a['name']])) $f = $fields[$a['name']];
            else $f = $fields[$i];
    
            if(empty($f)) {
                $f = 'NULL';
            } elseif(!is_numeric($f)) {
                $f = "'".SQLite3::escapeString($f)."'";
            }
    
            if($i) $q.=', ';
            $q.= $a['name']." = $f";
            $i++;
        }
        $q.= " WHERE rowid = ".($id+0).";";
        querydb($q);
    } else {
        $fields2 = array();
        foreach($fields as $f) {
            if($f===null) {
                $fields2[] = 'NULL';
            } elseif(is_numeric($f)) {
                $fields2[] = $f;
            } else {
                $fields2[] = "'".SQLite3::escapeString($f)."'";
            }
        }
        querydb("INSERT OR REPLACE INTO $table VALUES(".implode(",", $fields2).");");
    }
}

function deleteDb($table, $id=null) {
    querydb("DELETE FROM $table WHERE rowid=".($id+0).";");
}

function updateDb($table, $field, $value, $id) {
    if(empty($value)) {
        $value = 'NULL';
    } elseif(!is_numeric($value)) {
        $value = "'".SQLite3::escapeString($value)."'";
    }
    querydb("UPDATE $table SET $field = $value WHERE rowid=".($id+0).";");
}

function getConfig($key, $default=null) {
    $r = querydb("SELECT value FROM config WHERE key LIKE '".SQLite3::escapeString($key)."';");
    $a = $r->fetchArray(SQLITE3_ASSOC);
    if(!$a) return $default;
    return $a['value'];
}

function setConfig($key, $value) {
    replaceDb('config', array($key, $value));
    return;
}

function h($str) {
    return htmlentities($str, ENT_COMPAT | ENT_HTML401, 'UTF-8');
}

?>
