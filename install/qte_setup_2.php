<?php

// QuickTeam 3.0 build:20140608

session_start();

if ( !isset($_SESSION['qte_setup_lang']) ) $_SESSION['qte_setup_lang']='en';

include 'qte_lang_'.$_SESSION['qte_setup_lang'].'.php';
include '../bin/config.php'; if ( $qte_dbsystem=='sqlite' ) $qte_database = '../'.$qte_database;

$strAppl     = 'QuickTeam';
$strPrevUrl  = 'qte_setup_1.php';
$strNextUrl  = 'qte_setup_3.php';
$strPrevLabel= $L['Back'];
$strNextLabel= $L['Next'];

// --------
// HTML START
// --------

include 'qte_setup_hd.php';

if ( isset($_POST['ok']) )
{
  include '../bin/class/qt_class_db.php';
  include '../bin/qte_fn_base.php';

  if ( isset($_SESSION['qte_dbopwd']) )
  {
  $qte_user = $_SESSION['qte_dbologin'];
  $qte_pwd = $_SESSION['qte_dbopwd'];
  }

  define('TABLANG', $qte_prefix.'qtelang');
  define('TABDOMAIN', $qte_prefix.'qtedomain');
  define('TABSECTION', $qte_prefix.'qtesection');
  define('TABUSER', $qte_prefix.'qteuser');
  define('TABCHILD', $qte_prefix.'qtechild');
  define('TABS2U', $qte_prefix.'qtes2u');
  define('TABSTATUS', $qte_prefix.'qtestatus');
  define('TABSETTING', $qte_prefix.'qtesetting');
  define('TABINDEX', $qte_prefix.'qteindex');
  define('TABDOC', $qte_prefix.'qtedoc');
  
  $oDB = new cDB($qte_dbsystem,$qte_host,$qte_database,$qte_user,$qte_pwd);
  if ( !empty($oDB->error) ) die ('<p class="error">Connection with database failed.<br/>Please contact the webmaster for further information.</p><p>The webmaster must check that server is up and running, and that the settings in the config file are correct for the database.</p>');

  if ( empty($oDB->error) )
  {
    // Install the tables

    $strTable = TABLANG;
    echo "A) {$L['Installation']} LANG... ";
    include 'qte_setup_lang.php';
    echo $L['Done'],'<br />';

    $strTable = TABSETTING;
    echo "B) {$L['Installation']} SETTING... ";
    include 'qte_setup_setting.php';
    echo $L['Done'],', ',$L['Default_setting'],'<br />';

    $strTable = TABDOMAIN;
    echo "C) {$L['Installation']} DOMAIN... ";
    include 'qte_setup_domain.php';
    echo $L['Done'],', ',$L['Default_domain'],'<br />';

    $strTable = TABSECTION;
    echo "D) {$L['Installation']} TEAM... ";
    include 'qte_setup_section.php';
    echo $L['Done'],', ',$L['Default_section'],'<br />';

    $strTable = TABUSER;
    echo "E) {$L['Installation']} USER... ";
    include 'qte_setup_user.php';
    echo $L['Done'],', ',$L['Default_user'],'<br />';

    $strTable = TABCHILD;
    echo "F) {$L['Installation']} CHILD... ";
    include 'qte_setup_child.php';
    echo $L['Done'],'<br />';

    $strTable = TABS2U;
    echo "G) {$L['Installation']} S2U... ";
    include 'qte_setup_s2u.php';
    echo $L['Done'],'<br />';

    $strTable = TABSTATUS;
    echo "H) {$L['Installation']} STATUS... ";
    include 'qte_setup_status.php';
    echo $L['Done'],', ',$L['Default_status'],'<br />';

    $strTable = TABINDEX;
    echo "I) {$L['Installation']} INDEX... ";
    include 'qte_setup_index.php';
    echo $L['Done'],'<br />';

    $strTable = TABDOC;
    echo "J) {$L['Installation']} DOC... ";
    include 'qte_setup_doc.php';
    echo $L['Done'],'<br />';

    if ($result==FALSE)
    {
      echo '<div class="setup_err">',sprintf ($L['E_install'],$strTable,$qte_database,$qte_user),'</div>';
    }
    else
    {
      echo '<div class="setup_ok">',$L['S_install'],'</div>';
      $_SESSION['qteInstalled'] = true;
      // save the url
      $strURL = ( empty($_SERVER['SERVER_HTTPS']) ? "http://" : "https://" ).$_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
      $strURL = substr($strURL,0,-24);
      $oDB->Exec('UPDATE '.TABSETTING.' SET setting="'.$strURL.'" WHERE param="site_url"');
    }
  }
  else
  {
    echo '<div class="setup_err">',sprintf ($L['E_connect'],$qte_database,$qte_host),'</div>';
  }

}
else
{
  echo '
  <h2>',$L['Install_db'],'</h2>
  <table>
  <tr valign="top">
  <td width="475" style="padding:5px">
  <form method="post" name="install" action="qte_setup_2.php" >
  <p class="small">',$L['Upgrade2'],'</p>
  <p><input class="submit" type="submit" name="ok" value="',sprintf($L['Create_tables'],$qte_database),'" onclick="this.style.visibility=\'hidden\';"/></p>
  </form>
  </td>
  <td><div class="setup_help">',$L['Help_2'],'</div></td>
  </tr>
  </table>
  ';
}

// --------
// HTML END
// --------

include 'qte_setup_ft.php';