<?php

// QuickTeam 3.0 build:20140608

session_start();
$strAppl = 'QuickTeam';
if ( !isset($_SESSION['boardmail']) ) $_SESSION['boardmail']='';
if ( !isset($_SESSION['qte_setup_lang']) ) $_SESSION['qte_setup_lang']='en';

$arrLangs = array();
$arrLangs['en'] = 'English';
if ( file_exists('qte_lang_fr.php') ) $arrLangs['fr'] = 'Fran&ccedil;ais';
if ( file_exists('qte_lang_nl.php') ) $arrLangs['nl'] = 'Nederlands';
if ( file_exists('qte_lang_it.php') ) $arrLangs['it'] = 'Italiano';
if ( file_exists('qte_lang_es.php') ) $arrLangs['es'] = 'Espa&ntilde;ol';
if ( file_exists('qte_lang_de.php') ) $arrLangs['de'] = 'Deutsche';
if ( file_exists('qte_lang_pt.php') ) $arrLangs['pt'] = 'Portuguese';

include 'qte_lang_'.$_SESSION['qte_setup_lang'].'.php';

// --------
// Basic check
// --------

  $bFolder = false;
  if ( is_dir('../document') ) {
  if ( is_dir('../document/section') ) {
  if ( is_readable('../document/section') ) {
  if ( is_writable('../document/section') ) {
    $bFolder=true;
  }}}}

  $bConfig = false;
  if ( file_exists('../bin/config.php') ) {
  if ( is_readable('../bin/config.php') ) {
  if ( is_writable('../bin/config.php') ) {
    $bConfig=true;
  }}}

// --------
// Html start
// --------

include 'qte_setup_hd.php';

if ( $bFolder && $bConfig )
{
  echo '<h1>',$strAppl,'</h1>';
  echo '<h2>Language ?</h2>';
  echo '
  <form method="get" action="qte_setup_1.php">
  <select name="language" size="1">';
  foreach ($arrLangs as $strKey => $strLang)
  {
  echo '<option value="',$strKey,'"',($_SESSION['qte_setup_lang']==$strKey ? ' selected="selected"' : ''),'>',$strLang,'</option>';
  }
  echo '</select>
  <input type="submit" name="ok" value="Ok"/>
  </form>
  ';
}
else
{
  if ( !$bFolder )
  {
  echo '<h2>[EN] Before install</h2>';
  echo '<p>The directory <b>document/section/</b> is not writable (or does not exist). Please make this folder writable before starting installation (e.g. with a FTP client, set folder attributes to chmod 777)</p>';
  echo '<h2 style="color:#000099;background-color:inherit">[FR] Avant d\'installer</h2>';
  echo '<p style="color:#000099;background-color:inherit">Le r&eacute;pertoire <b>document/section/</b> n\'est pas inscriptible (ou n\'existe pas). Veillez rendre ce r&eacute;pertoire inscriptible avant de lancer l\'installation (ex. avec un logiciel de FTP changez les attributs de s&eacute;curit&eacute; de ce dossier en chmod 777)</p>';
  echo '<h2 style="color:#000099;background-color:inherit">[NL] Voor installatie</h2>';
  echo '<p style="color:#000099;background-color:inherit">De map <b>document/section/</b> is read-only (of bestaat niet). U moet dit map inschrijfbaar maken (ex. met een FTP software u kan de veiligheid attributen naar chmod 777 veranderen)</p>';
  }
  if ( !$bConfig )
  {
  echo '<h2>[EN] Before install</h2>';
  echo '<p>The configuration file <b>bin/config.php</b> is not writable. Please make this file writable before starting installation (e.g. with a FTP client, set the file attributes to chmod 777)</p>';
  echo '<h2 style="color:#000099;background-color:inherit">[FR] Avant d\'installer</h2>';
  echo '<p style="color:#000099;background-color:inherit">Le fichier <b>bin/config.php</b> n\'est pas inscriptible. Veillez rendre ce fichier inscriptible avant de lancer l\'installation (ex. avec un logiciel de FTP changer les attributs de securit&eacute; de ce fichier en chmod 777)</p>';
  echo '<h2 style="color:#000099;background-color:inherit">[NL] Voor installatie</h2>';
  echo '<p style="color:#000099;background-color:inherit">De file <b>bin/config.php</b> is read-only. U moet dit inschrijfbaar maken (bvb. met een FTP software u kan de veiligheid attributen naar chmod 777 veranderen)</p>';
  }
}

// --------
// HTML END
// --------

include 'qte_setup_ft.php';