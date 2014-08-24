<?php

/**
* PHP version 5
*
* LICENSE: This source file is subject to version 3.0 of the PHP license
* that is available through the world-wide-web at the following URI:
* http://www.php.net/license.  If you did not receive a copy of
* the PHP License and are unable to obtain it through the web, please
* send a note to license@php.net so we can mail you a copy immediately.
*
* @package    QuickTeam
* @author     Philippe Vandenberghe <info@qt-cute.org>
* @copyright  2014 The PHP Group
* @version    3.0 build:20140608
*/

session_start();
require 'bin/qte_init.php';
include Translate(APP.'_adm.php');

if ( sUser::Role()!='A' ) die($L['E_admin']);

// INITIALISE

$oVIP->selfurl = 'qte_adm_site.php';
$oVIP->selfname = '<span class="upper">'.$L['Adm_info'].'</span><br/>'.$L['Adm_general'];

// --------
// SUBMITTED
// --------

if ( isset($_POST['ok']) )
{
  // check sitename
  $str = $_POST['sitename']; if ( get_magic_quotes_gpc() ) $str = stripslashes($str);
  $str = substr(QTconv($str,'3',false),0,64);
  if ( !empty($str) ) { $_SESSION[QT]['site_name'] = $str; } else { $error = $L['Site_name'].' '.$L['E_invalid']; }

  // check siteurl
  if ( empty($error) )
  {
    $str = QTconv($_POST['siteurl'],'2');
    if ( substr($str,-1,1)=='/' ) $str = substr($str,0,-1);
    if ( !empty($str) ) { $_SESSION[QT]['site_url'] = $str; } else { $error = $L['Site_url'].': '.$L['E_invalid']; }
    if ( !preg_match('/^(http:\/\/|https:\/\/)/',$str) ) $warning = $L['Site_url'].': '.$L['E_missing_http'];
  }

  // check indexname
  if ( empty($error) )
  {
    $str = $_POST['title']; if ( get_magic_quotes_gpc() ) $str = stripslashes($str);
    $str = substr(QTconv($str,'3',false),0,64);
    if ( !empty($str) ) { $_SESSION[QT]['index_name'] = $str; } else { $error = $L['Name_of_index'].' '.$L['E_invalid']; }
  }

  // check adminemail
  if ( empty($error) )
  {
    $str = trim($_POST['adminmail']);
    if ( QTismail($str) ) { $_SESSION[QT]['admin_email'] = $str; } else { $error = $L['Adm_e_mail'].' ['.$str.'] '.$L['E_invalid']; }
  }

  // check others
  if ( empty($error) )
  {
    $_SESSION[QT]['use_smtp'] = $_POST['smtp'];
    if ( $_SESSION[QT]['use_smtp']=='1' )
    {
    $_SESSION[QT]['smtp_host'] = $_POST['smtphost'];
    $_SESSION[QT]['smtp_port'] = $_POST['smtpport'];
    $_SESSION[QT]['smtp_username'] = $_POST['smtpusr'];
    $_SESSION[QT]['smtp_password'] = $_POST['smtppwd'];
    if ( empty($_SESSION[QT]['smtp_host']) ) $error = 'Smtp host '.$L['E_invalid'];
    }
  }

  // save value
  if ( empty($error) )
  {
    $oDB->Exec('UPDATE '.TABSETTING.' SET setting="'.addslashes($_SESSION[QT]['site_name']).'" WHERE param="site_name"');
    $oDB->Exec('UPDATE '.TABSETTING.' SET setting="'.$_SESSION[QT]['site_url'].'"WHERE param="site_url"');
    $oDB->Exec('UPDATE '.TABSETTING.' SET setting="'.addslashes($_SESSION[QT]['index_name']).'" WHERE param="index_name"');
    $oDB->Exec('UPDATE '.TABSETTING.' SET setting="'.$_SESSION[QT]['admin_email'].'" WHERE param="admin_email"');
    $oDB->Exec('UPDATE '.TABSETTING.' SET setting="'.$_SESSION[QT]['use_smtp'].'" WHERE param="use_smtp"');
    if ( $_SESSION[QT]['smtp_host']=='1' )
    {
    $oDB->Exec('DELETE FROM '.TABSETTING.' WHERE param="smtp_host" OR param="smtp_port" OR param="smtp_username" OR param="smtp_password"');
    $oDB->Exec('INSERT INTO '.TABSETTING.' VALUES ("smtp_host","'.$_SESSION[QT]['smtp_host'].'","1")');
    $oDB->Exec('INSERT INTO '.TABSETTING.' VALUES ("smtp_port","'.$_SESSION[QT]['smtp_port'].'","1")');
    $oDB->Exec('INSERT INTO '.TABSETTING.' VALUES ("smtp_username","'.$_SESSION[QT]['smtp_username'].'","1")');
    $oDB->Exec('INSERT INTO '.TABSETTING.' VALUES ("smtp_password","'.$_SESSION[QT]['smtp_password'].'","1")');
    }
    $str = trim($_POST['adminfax']); if ( get_magic_quotes_gpc() ) $str = stripslashes($str);
      $str = QTconv($str,'3',false);
      if ( strlen($str)>255 ) $str = substr($str,0,255);
      $oDB->Exec('UPDATE '.TABSETTING.' SET setting="'.addslashes($str).'" WHERE param="admin_fax"');
      $_SESSION[QT]['admin_fax'] = $str;
    $str = trim($_POST['adminname']); if ( get_magic_quotes_gpc() ) $str = stripslashes($str);
      $str = QTconv($str,'3',false);
      if ( strlen($str)>255 ) $str = substr($str,0,255);
      $oDB->Exec('UPDATE '.TABSETTING.' SET setting="'.addslashes($str).'" WHERE param="admin_name"');
      $_SESSION[QT]['admin_name'] = $str;
    $str = trim($_POST['adminaddr']); if ( get_magic_quotes_gpc() ) $str = stripslashes($str);
      $str = QTconv($str,'3',false);
      if ( strlen($str)>255 ) $str = substr($str,0,255);
      $oDB->Exec('UPDATE '.TABSETTING.' SET setting="'.addslashes($str).'" WHERE param="admin_addr"');
      $_SESSION[QT]['admin_addr'] = $str;


    // save translations

    cLang::Delete('index','i');
    foreach($_POST as $strKey=>$str)
    {
    	if ( substr($strKey,0,1)=='T' && !empty($str) )
     	{
      $oGP = new cGetPost($str);
    	cLang::Add('index',substr($strKey,1),'i',$oGP->e);
    	}
    }

    // register lang

    $_SESSION['L']['index'] = cLang::Get('index',QTiso());
  }

  // exit
  $_SESSION['pagedialog'] = (empty($error) ? 'O|'.$L['S_save'] : 'E|'.$error);

}

if ( !preg_match('/^(http:\/\/|https:\/\/)/',$_SESSION[QT]['site_url']) ) $warning = $L['Site_url'].': '.$L['E_missing_http'];

// --------
// HTML START
// --------

$oHtml->scripts[] = '<script type="text/javascript">
function smtpdisabled(str)
{
if (str=="0")
{
document.getElementById("smtphost").disabled=true;
document.getElementById("smtpport").disabled=true;
document.getElementById("smtpusr").disabled=true;
document.getElementById("smtppwd").disabled=true;
}
else
{
document.getElementById("smtphost").disabled=false;
document.getElementById("smtpport").disabled=false;
document.getElementById("smtpusr").disabled=false;
document.getElementById("smtppwd").disabled=false;
}
return null;
}
function PassInLink()
{
strHost = document.getElementById("smtphost").value;
strPort = document.getElementById("smtpport").value;
strUser = document.getElementById("smtpusr").value;
strPass = document.getElementById("smtppwd").value;
document.getElementById("smtplink").href="qte_ext_smtp.php?h=" + strHost + "&amp;p=" + strPort + "&amp;u=" + strUser + "&amp;w=" + strPass;
document.getElementById("smtplink").target="_blank";
return null;
}
</script>
';

include APP.'_adm_inc_hd.php';

// FORM

echo '<form method="post" action="',$oVIP->selfurl,'">
<h2 class="subtitle">',$L['General_site'],'</h2>
<table class="t-data">
';
$str = QTconv($_SESSION[QT]['site_name'],'I');
echo '<tr class="t-data" title="',$L['H_Site_name'],'">
<td class="headfirst"><label for="sitename">',$L['Site_name'],'</label></td>
<td><input required type="text" id="sitename" name="sitename" size="50" maxlength="64" value="',$str,'" onchange="bEdited=true;"/>',(strstr($str,'&amp;') ? ' <span class="small">'.$_SESSION[QT]['site_name'].'</span>' : ''),'</td>
</tr>
';
echo '<tr class="t-data" title="',$L['H_Site_url'],'">
<td class="headfirst"><label for="siteurl">',$L['Site_url'],'</label></td>
<td><input required type="url" id="siteurl" name="siteurl" pattern="^(http://|https://).*" size="50" maxlength="255" value="',$_SESSION[QT]['site_url'],'" onchange="bEdited=true;"/></td>
</tr>
';
$str = QTconv($_SESSION[QT]['index_name'],'I');
echo '<tr class="t-data" title="',$L['H_Name_of_index'],'">
<td class="headfirst"><label for="title">',$L['Name_of_index'],'</label></td>
<td>
<input required type="text" id="title" name="title" size="50" maxlength="64" value="',$str,'" style="background-color:#FFFF99" onchange="bEdited=true;"/>',(strstr($str,'&amp;') ? ' <span class="small">'.$_SESSION[QT]['index_name'].'</span>' : ''),'</td>
</tr>
<tr class="t-data">
<td class="headfirst">',$L['Name_of_index'],'<br/>',$L['Translations'],' *</td>
<td>
<table>';
$arrTrans = cLang::Get('index','*','i');
include 'bin/qte_lang.php'; // this creates $arrLang
foreach($arrLang as $strIso=>$arr)
{
  $str = '';
  if ( isset($arrTrans[$strIso]) ) {
  if ( !empty($arrTrans[$strIso]) ) {
    $str = QTconv($arrTrans[$strIso],'I');
  }}
  echo '
  <tr>
  <td style="width:25px"><span title="',$arr[1],'">',$arr[0],'</span></td>
  <td><input class="small" title="',$L['Name_of_index'],' (',$strIso,')" type="text" id="T',$strIso,'" name="T',$strIso,'" size="45" maxlength="64" value="',$str,'" onchange="bEdited=true;"/>',(strstr($str,'&amp;') ?  ' <span class="small">'.$arrTrans[$strIso].'</span>' : ''),'</td>
  </tr>
  ';
}
echo '</table>
</td>
</tr>
<tr class="t-data">
<td class="blanko" colspan="2">* <span class="small">',sprintf($L['E_no_translation'],$_SESSION[QT]['index_name']),'</span></td>
</tr>
</table>
';
echo '<h2 class="subtitle">',$L['Contact'],'</h2>
<table class="t-data">
';
echo '<tr class="t-data" title="',$L['H_Admin_e_mail'],'">
<td class="headfirst"><label for="adminmail">',$L['Adm_e_mail'],'</label></td>
<td><input required type="email" id="adminmail" name="adminmail" size="50" maxlength="255" value="',$_SESSION[QT]['admin_email'],'" onchange="bEdited=true;"/></td>
</tr>
';
$str = QTconv($_SESSION[QT]['admin_fax'],'I');
echo '<tr class="t-data" title="',$L['H_Admin_fax'],'">
<td class="headfirst"><label for="adminfax">',$L['Adm_fax'],'</label></td>
<td><input type="text" id="adminfax" name="adminfax" size="50" maxlength="255" value="',$str,'" onchange="bEdited=true;"/>',(strstr($str,'&amp;') ?  ' <span class="small">'.$_SESSION[QT]['admin_fax'].'</span>' : ''),'</td>
</tr>
';
$str = QTconv($_SESSION[QT]['admin_name'],'I');
echo '<tr class="t-data" title="',$L['Adm_name'],'">
<td class="headfirst"><label for="adminname">',$L['Adm_name'],'</label></td>
<td><input type="text" id="adminname" name="adminname" size="50" maxlength="255" value="',$str,'" onchange="bEdited=true;"/>',(strstr($str,'&amp;') ?  ' <span class="small">'.$_SESSION[QT]['admin_name'].'</span>' : ''),'</td>
</tr>
';
$str = QTconv($_SESSION[QT]['admin_addr'],'I');
echo '<tr class="t-data" title="',$L['Adm_addr'],'">
<td class="headfirst"><label for="adminaddr">',$L['Adm_addr'],'</label></td>
<td><input type="text" id="adminaddr" name="adminaddr" size="50" maxlength="255" value="',$str,'" onchange="bEdited=true;"/>',(strstr($str,'&amp;') ?  ' <span class="small">'.$_SESSION[QT]['admin_addr'].'</span>' : ''),'</td>
</tr>
</table>
';
echo '<h2 class="subtitle">',$L['Email_settings'],'</h2>
<table class="t-data">
';
echo '<tr class="t-data" title="',$L['H_Use_smtp'],'">
<td class="headfirst"><label for="smtp">',$L['Use_smtp'],'</label></td>
<td><select id="smtp" name="smtp" onchange="smtpdisabled(this.value); bEdited=true;">',QTasTag(array($L['N'],$L['Y']),(int)$_SESSION[QT]['use_smtp']),'</select></td>
</tr>
';
echo '<tr class="t-data" title="',$L['H_Use_smtp'],'">
<td class="headfirst"><label for="smtphost">Smtp host</label></td>
<td>
<input type="text" id="smtphost" name="smtphost" size="28" maxlength="64" value="',$_SESSION[QT]['smtp_host'],'"'.($_SESSION[QT]['use_smtp']=='0' ? QDIS : '').' onchange="bEdited=true;"/>
 port <input type="text" id="smtpport" name="smtpport" size="4" maxlength="6" value="',(isset($_SESSION[QT]['smtp_port']) ? $_SESSION[QT]['smtp_port'] : '25'),'"'.($_SESSION[QT]['use_smtp']=='0' ? QDIS : '').' onchange="bEdited=true;"/>
</td>
</tr>
';
echo '<tr class="t-data" title="',$L['H_Use_smtp'],'">
<td class="headfirst"><label for="smtpusr">Smtp username</label></td>
<td><input type="text" id="smtpusr" name="smtpusr" size="28" maxlength="64" value="',$_SESSION[QT]['smtp_username'],'"'.($_SESSION[QT]['use_smtp']=='0' ? QDIS : '').' onchange="bEdited=true;"/></td>
</tr>
';
echo '<tr class="t-data" title="',$L['H_Use_smtp'],'">
<td class="headfirst"><label for="smtppwd">Smtp password</label></td>
<td><input type="text" id="smtppwd" name="smtppwd" size="28" maxlength="64" value="',$_SESSION[QT]['smtp_password'],'"'.($_SESSION[QT]['use_smtp']=='0' ? QDIS : '').' onchange="bEdited=true;"/> <a id="smtplink" href="qte_ext_smtp.php" onclick="PassInLink()">test smtp</a></td>
</tr>
</table>
';
echo '
<p style="margin:0 0 5px 0;text-align:center"><input type="submit" name="ok" value="',$L['Save'],'"/></p>
</form>
';

// HTML END

include APP.'_adm_inc_ft.php';