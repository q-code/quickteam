<?php // QuickTeam 3.0 build:20140608

define('APP', 'qte'); // application file prefix
if ( !isset($qte_root) ) $qte_root='';

// ---------------
// Connection config
// ---------------
require_once $qte_root.'bin/config.php';
if ( isset($qte_install) ) { define('QT','qte'.substr($qte_install,-1)); } else { define('QT','qte'); }

// ---------------
// System constants (CANNOT be changed by webmasters)
// ---------------
if ( !defined('PHP_VERSION_ID') ) { $version=explode('.',PHP_VERSION); define('PHP_VERSION_ID',($version[0]*10000+$version[1]*100+$version[2])); }
define('TABDOMAIN', $qte_prefix.'qtedomain');
define('TABSECTION', $qte_prefix.'qtesection');
define('TABUSER', $qte_prefix.'qteuser');
define('TABS2U', $qte_prefix.'qtes2u');
define('TABSTATUS', $qte_prefix.'qtestatus');
define('TABSETTING', $qte_prefix.'qtesetting');
define('TABCHILD', $qte_prefix.'qtechild');
define('TABINDEX', $qte_prefix.'qteindex');
define('TABDOC', $qte_prefix.'qtedoc');
define('TABLANG', $qte_prefix.'qtelang');
define('QTEVERSION', '3.0 build:20140608');
define('QSEL', ' selected="selected"');
define('QCHE', ' checked="checked"');
define('QDIS', ' disabled="disabled"');
define('S', '&nbsp;');
define('START', 1);
define('END', -1);
define('JQUERY_OFF', 'bin/js/jquery.min.js'); // jQuery resource when offline. This will be used if CDN (defined here after) is not possible.
define('JQUERYUI_OFF', 'bin/js/jquery-ui.min.js');
define('JQUERYUI_CSS_OFF', 'bin/css/jquery-ui/themes/base/jquery-ui.css');

// ---------------
// Interface constants (can be changed by webmasters)
// ---------------
define('QTE_CHANGE_USERNAME', true);         // allow users to change their username (login). False = only administrators can change the username.
define('QTE_SHOW_TIME',     true);           // show time in the bottom bar
define('QTE_SHOW_MODERATOR',true);           // show moderator in the bottom bar
define('QTE_SHOW_GOTOLIST', true);           // show gotolist in the bottom bar
define('QTE_SHOW_BIRTHDAYS',true);           // show users having birthday in the legend (if the field 'birthdate' is activated)
define('QTE_CRUMBTRAIL', ' &middot; ');      // crumbtrail separator (dont forget spaces)
define('QTE_CONVERT_AMP', false);            // save &amp; instead of &. Use TRUE to make &#0000; symbols NOT working.
define('QTE_DIR_DOC',$qte_root.'document/'); // directory of the document (with final /)
define('QTE_DIR_PIC',$qte_root.'picture/');  // directory of user's photo (with final /)
define('QTE_WEEKSTART', 1);                  // Start of the week (use code 1=monday,...,7=sunday)
define('QTE_JAVA_MAIL', true);               // Protect e-mail by a javascript
define('QTE_SEARCH_AGE', 13);                 // used as default search age (for example coppa limit is 13)
define('QTE_URLREWRITE', false);
// URL rewriting (for expert only):
// Rewriting url requires that your server is configured with following rule for the application folder: RewriteRule ^(.+)\.html(.*) qte_$1.php$2 [L]
// This can NOT be activated if you application folder contains html pages (they will not be accessible anymore when urlrewriting is acticated)

// -----------------
// JQUERY (this can be changed by webmaster)
// -----------------
// Content Delivery Network for jQuery and jQuery-UI. Using a CDN will increase performances.
// Possible CDN are: Google, Microsoft, jQuery-Media-Temple.
// You can also decide to use your local copy (in the bin/ directory) to avoid using a CDN.

define('JQUERY_CDN', '//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js');
  // define('JQUERY_CDN', '//ajax.aspnetcdn.com/ajax/jQuery/jquery-2.0.3.min.js');
  // define('JQUERY_CDN', '//code.jquery.com/jquery-2.0.3.min.js');
  // define('JQUERY_CDN', 'bin/js/jquery.min.js');

define('JQUERYUI_CDN', '//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js');
  // define('JQUERYUI_CDN', '//ajax.aspnetcdn.com/ajax/jquery.ui/1.10.3/jquery-ui.min.js');
  // define('JQUERYUI_CDN', '//code.jquery.com/ui/1.10.3/jquery-ui.js');
  // define('JQUERYUI_CDN', 'bin/js/jquery-ui.min.js');

define('JQUERYUI_CSS_CDN', '//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/themes/smoothness/jquery-ui.min.css');
  // define('JQUERYUI_CSS_CDN', '//ajax.aspnetcdn.com/ajax/jquery.ui/1.10.3/themes/smoothness/jquery-ui.min.css');
  // define('JQUERYUI_CSS_CDN', '//code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css');
  // define('JQUERYUI_CSS_CDN', 'bin/css/jquery-ui/themes/base/jquery-ui.css');

// ---------------
// Classes & functions
// ---------------
require_once 'bin/qt_lib_sys.php';
require_once 'bin/qt_lib_txt.php';
require_once 'bin/class/qt_class_db.php';
require_once 'bin/class/qt_abstracts.php';
require_once 'bin/class/qt_class_html.php';
require_once 'bin/class/qt_class_table.php';
require_once 'bin/class/qt_class_sys.php';
require_once 'bin/class/qt_class_fld.php';
require_once 'bin/class/qte_class_vip.php';
require_once 'bin/class/qte_class_user.php';
require_once 'bin/class/qte_class_sec.php';
require_once 'bin/class/qte_class_item.php';
require_once 'bin/qte_fn_base.php';
require_once 'bin/qte_fn_html.php';

// ---------------
//  Installation wizard (if file exists)
// ---------------
if ( !isset($qte_install) )
{
  if ( file_exists('install/index.php') )
  {
  echo 'QuickTeam ',QTEVERSION,' <a href="install/index.php">starting installation</a>...';
  echo '<meta http-equiv="REFRESH" content="1;url=install/index.php" />';
  exit;
  }
}
if ( empty($qte_install) )
{
  if ( file_exists('install/index.php') )
  {
  echo 'QuickTeam ',QTEVERSION,' <a href="install/index.php">starting installation</a>...';
  echo '<meta http-equiv="REFRESH" content="1;url=install/index.php" />';
  exit;
  }
}

// --------------
// Initialise Classes
// --------------
$oDB  = new cDB($qte_dbsystem,$qte_host,$qte_database,$qte_user,$qte_pwd,$qte_port,$qte_dsn);
if ( !empty($oDB->error) ) die ('<p><font color="red">Connection with database failed.<br />Please contact the webmaster for further information.</font></p><p>The webmaster must check that server is up and running, and that the settings in the config file are correct for the database.</p>');
$oVIP = new cVIP();

// ----------------
// Load system parameters (attention some parameters can be reserved, thus not loaded)
// ----------------
if ( !isset($_SESSION[QT]) || !isset($_SESSION[QT]['site_name']) ) GetParam(true);

// check major parameters

if ( empty($_SESSION[QT]['skin_dir']) || strpos($_SESSION[QT]['skin_dir'],'skin/')===false ) $_SESSION[QT]['skin_dir']=$qte_root.'skin/default';
if ( empty($_SESSION[QT]['language']) ) $_SESSION[QT]['language']='english';

// change language if required (by coockies or by the menu)

$str=QTiso();
if ( isset($_COOKIE[QT.'_cooklang']) ) $str=substr($_COOKIE[QT.'_cooklang'],0,2);
if ( isset($_GET['lx']) ) $str=substr($_GET['lx'],0,2);
if ( $str!=QTiso() && !empty($str) )
{
  include $qte_root.'bin/qte_lang.php';
  if ( array_key_exists($str,$arrLang) )
  {
    $_SESSION[QT]['language'] = $arrLang[$str][2];
    if ( isset($_COOKIE[QT.'_cooklang']) ) setcookie(QT.'_cooklang', $str, time()+60*60*24*100, '/');
    // unset dictionnaries
    $_SESSION['L'] = array();
    if ( isset($_SESSION[QT]['sys_domains']) ) unset($_SESSION[QT]['sys_domains']);
    if ( isset($_SESSION[QT]['sys_sections']) ) unset($_SESSION[QT]['sys_sections']);
    if ( isset($_SESSION[QT]['sys_statuses']) ) unset($_SESSION[QT]['sys_statuses']);
  }
  else
  {
    die('Wrong iso code language');
  }
}

// ----------------
// Initialise variable
// ----------------
$error = ''; // Required when server uses register_global_on
$warning = ''; // Required when server uses register_global_on
$arrExtData = array(); // Can be used by extensions

if ( !isset($_SESSION[QT]['viewmode']) ) $_SESSION[QT]['viewmode']='n';
if ( !isset($_SESSION[QT]['userlang']) ) $_SESSION[QT]['userlang']='1';
if ( !isset($_SESSION[QT]['lastcolumn']) ) $_SESSION[QT]['lastcolumn']='';
if ( !isset($_SESSION[QT]['cal_shownews']) ) $_SESSION[QT]['cal_shownews']=FALSE;
if ( !isset($_SESSION[QT]['cal_showall']) ) $_SESSION[QT]['cal_showall']=FALSE;

// ----------------
// Load dictionary
// ----------------
if ( !isset($_SESSION['L']) ) $_SESSION['L'] = array();

QTcheckL('index;domain;sec;secdesc;field;ffield');

include_once $qte_root.GetLang().'qte_main.php';

// ----------------
// Define types,statuses and initialise statistics
// ----------------
$oVIP->SetSys(); // must be at the end because uses language

// ----------------
// Default HTML settings
// ----------------
$oHtml = new cHtml();
$oHtml->file = 'qte';
$oHtml->html = '<html xmlns="http://www.w3.org/1999/xhtml" dir="'.QT_HTML_DIR.'" xml:lang="'.QT_HTML_LANG.'" lang="'.QT_HTML_LANG.'" class="no-js">';
$oHtml->title = $_SESSION[QT]['site_name'];
$oHtml->metas['charset'] = '<meta charset="'.QT_HTML_CHAR.'" />';
$oHtml->metas['description'] = '<meta name="description" content="QuickTeam" />';
$oHtml->metas['keywords'] = '<meta name="keywords" content="quickteam,users management,qt-cute,OpenSource" />';
$oHtml->metas['author'] = '<meta name="author" content="qt-cute.org" />';
$oHtml->metas['viewport'] = '<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=5" />';
$oHtml->links['icon'] = '<link rel="shortcut icon" href="'.$_SESSION[QT]['skin_dir'].'/qte_icon.ico" />';
$oHtml->links['cssBase'] = '<link rel="stylesheet" type="text/css" href="'.$_SESSION[QT]['skin_dir'].'/qt_base.css" />'; // attention qt_base
$oHtml->links['cssLayout'] = '<link rel="stylesheet" type="text/css" href="'.$_SESSION[QT]['skin_dir'].'/qte_layout.css" />';
$oHtml->links['css'] = '<link rel="stylesheet" type="text/css" href="'.$_SESSION[QT]['skin_dir'].'/qte_main.css" />';
$oHtml->scripts['base'] = '<script type="text/javascript" src="bin/js/qte_base.js"></script>';

// Coockie confirm

if ( $oVIP->coockieconfirm )
{
  $oVIP->exitname = $L['Continue'];
  include 'qte_inc_hd.php';
  $oHtml->Msgbox($L['Login'],'msgbox login');
  echo '<h2>'.L('Welcome').' '.sUser::Name().'</h2><p><a href="'.Href($oVIP->exiturl).'">'.$oVIP->exitname.'</a>&nbsp; &middot; &nbsp;<a href="'.Href('qte_login.php?a=out').'">'.sprintf(L('Welcome_not'),sUser::Name()).'</a></p>';
  $oHtml->Msgbox(END);
  include 'qte_inc_ft.php';
  exit;
}

// -----------------
//  Time setting (for PHP >=5.2)
// -----------------
if ( PHP_VERSION_ID>=50200 ) {
if ( isset($_SESSION[QT]['defaulttimezone']) ) {
if ( $_SESSION[QT]['defaulttimezone']!=='' ) {

date_default_timezone_set($_SESSION[QT]['defaulttimezone']);

}}}