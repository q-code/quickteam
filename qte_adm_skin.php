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
* @copyright  2013 The PHP Group
* @version    3.0 build:20140608
*/

session_start();
require_once 'bin/qte_init.php';
include Translate('@_adm.php');

if ( sUser::Role()!='A' ) die($L['E_admin']);

// INITIALISE

$oVIP->selfurl = 'qte_adm_skin.php';
$oVIP->selfname = '<span class="upper">'.$L['Adm_settings'].'</span><br/>'.$L['Adm_layout'];

// --------
// SUBMITTED
// --------

if ( isset($_POST['ok']) )
{
  // check skin
  if ( empty($error) )
  {
    $_SESSION[QT]['skin_dir'] = $_POST['skin'];
    if ( !file_exists('skin/'.$_POST['skin'].'/qte_main.css') )
    {
    $error=$L['Section_skin'].' '.$L['E_invalid'].' (qte_main.css not found)';
    $_SESSION[QT]['skin_dir'] = 'default';
    }
  }

  // check banner/welcome/legend/home
  if ( empty($error) )
  {
    $_SESSION[QT]['skin_dir'] = 'skin/'.$_POST['skin'];
    $_SESSION[QT]['show_welcome'] = $_POST['welcome'];
    $_SESSION[QT]['show_legend'] = $_POST['legend'];
    $_SESSION[QT]['show_banner'] = $_POST['banner'];
    $_SESSION[QT]['home_menu'] = $_POST['home'];
    $_SESSION[QT]['section_descr'] = $_POST['section_descr'];
    $_SESSION[QT]['items_per_page'] = substr($_POST['items_per_page'],1);
  }

  // check homename
  if ( $_SESSION[QT]['home_menu']=='1' )
  {
    if ( empty($error) )
    {
      $str = $_POST['homename']; if ( get_magic_quotes_gpc() ) $str = stripslashes($str);
      $str = QTconv($str,'3',false);
      if ( !empty($str) ) { $_SESSION[QT]['home_name'] = $str; } else { $error = $L['Home_website_name'].S.$L['E_invalid']; }
    }
    if ( empty($error) )
    {
      $str = substr(trim($_POST['homeurl']),0,255);
      if ( !empty($str) ) { $_SESSION[QT]['home_url'] = $str; } else { $error = $L['Site_url'].': '.$L['E_invalid']; }
      if ( !preg_match('/^(http:\/\/|https:\/\/)/',$str) ) $warning = $L['Home_website_url'].': '.$L['E_missing_http'];
      $_SESSION[QT]['home_url'] = $str;
    }
  }

  // save value
  if ( empty($error) )
  {
    $oDB->Exec('UPDATE '.TABSETTING.' SET setting="'.$_SESSION[QT]['skin_dir'].'" WHERE param="skin_dir"');
    $oDB->Exec('UPDATE '.TABSETTING.' SET setting="'.$_SESSION[QT]['show_welcome'].'" WHERE param="show_welcome"');
    $oDB->Exec('UPDATE '.TABSETTING.' SET setting="'.$_SESSION[QT]['show_banner'].'" WHERE param="show_banner"');
    $oDB->Exec('UPDATE '.TABSETTING.' SET setting="'.$_SESSION[QT]['show_legend'].'" WHERE param="show_legend"');
    $oDB->Exec('UPDATE '.TABSETTING.' SET setting="'.$_SESSION[QT]['home_menu'].'" WHERE param="home_menu"');
    if ( $_SESSION[QT]['home_menu']=='1' )
    {
    $oDB->Exec('UPDATE '.TABSETTING.' SET setting="'.$_SESSION[QT]['home_name'].'" WHERE param="home_name"');
    $oDB->Exec('UPDATE '.TABSETTING.' SET setting="'.$_SESSION[QT]['home_url'].'" WHERE param="home_url"');
    }
    $oDB->Exec('UPDATE '.TABSETTING.' SET setting="'.$_SESSION[QT]['items_per_page'].'" WHERE param="items_per_page"');
    $oDB->Exec('UPDATE '.TABSETTING.' SET setting="'.$_SESSION[QT]['section_descr'].'" WHERE param="section_descr"');
  }
  // exit
  $_SESSION['pagedialog'] = (empty($error) ? 'O|'.$L['S_save'] : 'E|'.$error);
}

// --------
// HTML START
// --------

// WARNINGS

if ( !preg_match('/^(http:\/\/|https:\/\/)/',$_SESSION[QT]['home_url']) ) $warning = $L['Home_website_url'].': '.$L['E_missing_http'];

$oHtml->scripts[] = '
<script type="text/javascript">
function homedisabled(str)
{
  if (str=="0")
  {
  document.getElementById("homename").disabled=true;
  document.getElementById("homeurl").disabled=true;
  }
  else
  {
  document.getElementById("homename").disabled=false;
  document.getElementById("homeurl").disabled=false;
  }
  return;
}
</script>
';

include APP.'_adm_inc_hd.php';

// Read directory in language
$intHandle = opendir('skin');
$arrFiles = array();
while ( false!==($strFile = readdir($intHandle)) )
{
if ( $strFile!='.' && $strFile!='..' ) $arrFiles[$strFile]=ucfirst($strFile);
}
closedir($intHandle);
asort($arrFiles);

// Current skin
$strDfltskin = substr($_SESSION[QT]['skin_dir'],5);

// FORM

echo '<form method="post" action="',$oVIP->selfurl,'">
<h2 class="subtitle">',$L['Skin'],'</h2>
<table class="t-data">
<tr class="t-data" title="',$L['H_Board_skin'],'">
<td class="headfirst"><label for="skin">',$L['Board_skin'],'</label></td>
<td><select id="skin" name="skin" onchange="bEdited=true;">',QTasTag($arrFiles,$strDfltskin),'</select></td>
</tr>
<tr class="t-data" title="',$L['H_Show_banner'],'">
<td class="headfirst"><label for="banner">',$L['Show_banner'],'</label></td>
<td><select id="banner" name="banner" onchange="bEdited=true;">'.QTasTag(array(L('Show_banner0'),L('Show_banner1'),L('Show_banner2')),(int)$_SESSION[QT]['show_banner']).'</select></td>
</tr>
<tr class="t-data" title="',$L['H_Show_legend'],'">
<td class="headfirst"><label for="legend">',$L['Show_legend'],'</label></td>
<td>
<select id="legend" name="legend" onchange="bEdited=true;">',QTasTag(array($L['N'],$L['Y']),(int)$_SESSION[QT]['show_legend']),'</select>
</td>
</tr>
</table>
';
echo '<h2 class="subtitle">',$L['Layout'],'</h2>
<table class="t-data">
';
$arr = array('n10'=>'10','n20'=>'20','n30'=>'30','n40'=>'40','n50'=>'50','n100'=>'100');
echo '<tr class="t-data" title="',$L['H_Items_per_section_page'],'">
<td class="headfirst"><label for="items_per_page">',$L['Items_per_section_page'],'</label></td>
<td><select id="items_per_page" name="items_per_page" onchange="bEdited=true;">
',QTasTag($arr,'n'.$_SESSION[QT]['items_per_page'],array('format'=>'%s / '.strtolower($L['Page']))),'
</select></td>
</tr>
';
echo '<tr class="t-data" title="',$L['H_Show_welcome'],'">
<td class="headfirst"><label for="welcome">',$L['Show_welcome'],'</label></td>
<td><select id="welcome" name="welcome" onchange="bEdited=true;">
<option value="2"',($_SESSION[QT]['show_welcome']=='2' ? QSEL : ''),'>',$L['Y'],'</option>
<option value="0"',($_SESSION[QT]['show_welcome']=='0' ? QSEL : ''),'>',$L['N'],'</option>
<option value="1"',($_SESSION[QT]['show_welcome']=='1' ? QSEL : ''),'>',$L['While_unlogged'],'</option>
</select></td>
</tr>
<tr class="t-data" title="',$L['H_Repeat_section_description'],'">
<td class="headfirst"><label for="section_descr">',$L['Repeat_section_description'],'</label></td>
<td><select id="section_descr" name="section_descr" onchange="bEdited=true;">',QTasTag(array(L('Compact'),L('Normal')),(int)$_SESSION[QT]['section_descr']),'</select></td>
</tr>
</table>
';

echo '<h2 class="subtitle">',$L['Your_website'],'</h2>
<table class="t-data">
';
$str = QTconv($_SESSION[QT]['home_name'],'I');
echo '<tr class="t-data" title="',$L['H_Home_website_name'],'">
<td class="headfirst"><label for="home">',$L['Add_home'],'</label></td>
<td>
<select id="home" name="home" onchange="homedisabled(this.value); bEdited=true;">',QTasTag(array($L['N'],$L['Y']),(int)$_SESSION[QT]['home_menu']),'</select>
&nbsp;<input type="text" id="homename" name="homename" size="12" maxlength="24" value="',$str,'"',($_SESSION[QT]['home_menu']=='0' ? QDIS : ''),' onchange="bEdited=true;"/>',(strstr($str,'&amp;') ?  ' <span class="small">'.$_SESSION[QT]['home_name'].'</span>' : ''),'</td>
</tr>
<tr class="t-data" title="',$L['H_Website'],'">
<td class="headfirst"><label for="homeurl">',$L['Home_website_url'],'</label></td>
<td><input type="text" id="homeurl" name="homeurl" pattern="^(http://|https://).*" size="30" maxlength="100" value="',QTconv($_SESSION[QT]['home_url'],'I'),'"',($_SESSION[QT]['home_menu']=='0' ? QDIS : ''),' onchange="bEdited=true;"/></td>
</tr>
</table>
';

echo '<p style="margin:0 0 5px 0;text-align:center"><input type="submit" name="ok" value="',$L['Save'],'"/></p>
</form>
';

// HTML END

include APP.'_adm_inc_ft.php';