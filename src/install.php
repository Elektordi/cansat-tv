<?php
include "common.php";
if(installed()) die("INSTALLATION DEJA REALISEE - POUR REINSTALLER MERCI DE SUPPRIMER LA BASE ($dbfile)");

$ok = " <b>[&nbsp;<font color=\"green\">OK</font>&nbsp;]</b>";
$ko = " <b>[&nbsp;<font color=\"red\">KO</font>&nbsp;]</b>";

?>
<html>
<head><title>CanSat TV SETUP</title></head>
<body>
<p align="right"><a href=".">Retour au menu...</a></p>
<p>Installation en cours:</p>
<?php

if(function_exists('phpversion')) $vp = phpversion();

if(isset($vp) && $vp) {
    if(version_compare($vp, '5.2', '>')) {
        echo "<p>PHP version $vp présent $ok</p>";
    } else {
        echo "<p>PHP version $vp or version 2 nécessaire $ko</p>";
    }
} else {
     die("<p>PHP version trop ancienne $ko</p></body></html>");
}


if(function_exists('sqlite_libversion')) $vs = sqlite_libversion();

if(isset($vs) && $vs) {
    if(preg_match("/^2\./", $vs)) {
        echo "<p>SQLite version $vs présent $ok</p>";
    } else {
        echo "<p>SQLite version $vs or version 2 nécessaire $ko</p>";
    }
} else {
     die("<p>SQLite absent $ko</p></body></html>");
}

$dbhandle = @sqlite_open($dbfile, 0666, $error);
if (!$dbhandle) die("<p>Impossible de créer $dbfile ($error) $ko</p></body></html>");
echo "<p>Base $dbfile crée $ok</p>";

$stm = "CREATE TABLE config(key TEXT PRIMARY KEY, value TEXT NOT NULL)";
$q = @sqlite_exec($dbhandle, $stm, $error);
if (!$q) die("<p>Impossible de créer la table 'config' ($error) $ko</p></body></html>");
echo "<p>Table 'config' crée $ok</p>";

$stm = "CREATE TABLE projets(nom TEXT NOT NULL, club INTEGER NOT NULL, etat TEXT, categorie TEXT, vitesse TEXT, largueur TEXT, freq TEXT, missions TEXT, image TEXT)";
$q = @sqlite_exec($dbhandle, $stm, $error);
if (!$q) die("<p>Impossible de créer la table 'projets' ($error) $ko</p></body></html>");
echo "<p>Table 'projets' crée $ok</p>";

$stm = "CREATE TABLE clubs(nom TEXT NOT NULL, ville TEXT, pays TEXT)";
$q = @sqlite_exec($dbhandle, $stm, $error);
if (!$q) die("<p>Impossible de créer la table 'clubs' ($error) $ko</p></body></html>");
echo "<p>Table 'clubs' crée $ok</p>";

$stm = "CREATE TABLE affichage(projet INTEGER NOT NULL, ordre INTEGER NOT NULL, fin INTEGER)";
$q = @sqlite_exec($dbhandle, $stm, $error);
if (!$q) die("<p>Impossible de créer la table 'affichage' ($error) $ko</p></body></html>");
echo "<p>Table 'affichage' crée $ok</p>";

sqlite_close($dbhandle);

//$_SESSION['cansat_admin'] = 1;

?>
<h3>Installation réussie !</h3>
<p><a href="admin.php?mode=config">Continuer...</a></p>
</body></html>
