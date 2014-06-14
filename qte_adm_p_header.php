<?php

// QuickTeam 3.0 build:20140608

ob_start();

$bShowtoc = false;
if ( substr($oVIP->selfurl,0,7)=='qte_adm' || substr($oVIP->selfurl,0,5)=='qtem_' ) $bShowtoc=true;

$oHtml->links['icon'] = '<link rel="shortcut icon" href="admin/qte_icon.ico" />';
$oHtml->links['cssBase'] = '<link rel="stylesheet" type="text/css" href="admin/qt_base.css" />'; // attention qt_base
unset($oHtml->links['cssLayout']);
$oHtml->links['css'] = '<link rel="stylesheet" type="text/css" href="admin/qte_main.css" />';
$oHtml->scripts[] = '<script type="text/javascript">var e0 = '.(isset($L['E_editing']) ? '"'.$L['E_editing'].'"' : '0').';</script>';

// Page message

if ( !empty($_SESSION['pagedialog']) )
{
  if ( empty($oVIP->msg->text) ) $oVIP->msg->FromString($_SESSION['pagedialog']);
  $oHtml->scripts_jq[] = '
  $(function() {
    var doc = document.getElementById("pagedialog");
    if ( doc )
    {
    doc.innerHTML = "<img src=\"bin/css/pagedialog_'.$oVIP->msg->type.'.png\" alt=\"+\" class=\"pagedialog\"/>'.$oVIP->msg->text.'";
    doc.className = "absolute_'.$oVIP->msg->type.'";
    $("#pagedialog").fadeIn(500).delay(2000).fadeOut(800);
    }
  });
  ';
  $oVIP->msg->Clear();
}

echo $oHtml->Head();
echo $oHtml->Body(array('onload'=>(isset($strBodyAddOnload) ? $strBodyAddOnload : null),'onunload'=>(isset($strBodyAddOnunload) ? $strBodyAddOnunload : null)));

echo '
<div class="banner"><img class="logo" id="toc_logo" src="admin/qte_logo.gif" style="border-width:0" alt="QuickTicket" title="QuickTicket" /></div>

<!-- MENU/PAGE -->
<table class="pg-layout">
<tr>
<td style="',($bShowtoc ? 'width:170px;' : 'width:1px;'),'">
';

if ( $bShowtoc )
{

echo '
<!-- TOC -->
<div class="menu">
';

$arrLangMenu = array();
if ( file_exists('bin/qte_lang.php') )
{
  include 'bin/qte_lang.php';
  $strURI = GetUri('lx');
  foreach($arrLang as $strKey=>$arrDef)
  {
  $arrLangMenu[] = '<a href="'.Href().'?'.$strURI.'&amp;lx='.$strKey.'" title="'.$arrDef[1].'" onclick="return qtEdited(bEdited,e0);">'.$arrDef[0].'</a>';
  }
}
else
{
  $arrLangMenu[] = '<span class="small">missing file:bin/qte_lang.php</span>';
}

echo '<p class="language">',implode(' &middot; ',$arrLangMenu),'</p>
';

$str = 'return qtEdited(bEdited,e0);';
//echo '<div class="header">',strtoupper($L['Administration']),'</div>';
echo '
<div class="group">
<p class="group">',L('Info'),'</p>
<a class="item'.($oVIP->selfurl=='qte_adm_index.php' ? ' actif' : '').'" href="qte_adm_index.php" onclick="'.($oVIP->selfurl=='qte_adm_index.php' ? 'return false;' : $str).'">',$L['Adm_status'],'</a>
<a class="item'.($oVIP->selfurl=='qte_adm_site.php' ? ' actif' : '').'" href="qte_adm_site.php" onclick="'.($oVIP->selfurl=='qte_adm_site.php' ? 'return false;' : $str).'">',$L['Adm_general'],'</a>
</div>
<div class="group">
<p class="group">',L('Adm_settings'),'</p>
<a class="item'.($oVIP->selfurl=='qte_adm_region.php' ? ' actif' : '').'" href="qte_adm_region.php" onclick="'.($oVIP->selfurl=='qte_adm_region.php' ? 'return false;' : $str).'">',$L['Adm_region'],'</a>
<a class="item'.($oVIP->selfurl=='qte_adm_skin.php' ? ' actif' : '').'" href="qte_adm_skin.php" onclick="'.($oVIP->selfurl=='qte_adm_skin.php' ? 'return false;' : $str).'">',$L['Adm_layout'],'</a>
<a class="item'.($oVIP->selfurl=='qte_adm_secu.php' ? ' actif' : '').'" href="qte_adm_secu.php" onclick="'.($oVIP->selfurl=='qte_adm_secu.php' ? 'return false;' : $str).'">',$L['Adm_security'],'</a>
<a class="item'.($oVIP->selfurl=='qte_adm_fields.php' ? ' actif' : '').'" href="qte_adm_fields.php" onclick="'.($oVIP->selfurl=='qte_adm_fields.php' ? 'return false;' : $str).'">',$L['Field_man'],'</a>
</div>
<div class="group">
<p class="group">',L('Adm_content'),'</p>
<a class="item'.($oVIP->selfurl=='qte_adm_sections.php' ? ' actif' : '').'" href="qte_adm_sections.php" onclick="'.($oVIP->selfurl=='qte_adm_sections.php' ? 'return false;' : $str).'">',$L['Sections'],'</a>
<a class="item'.($oVIP->selfurl=='qte_adm_users.php' ? ' actif' : '').'" href="qte_adm_users.php" onclick="'.($oVIP->selfurl=='qte_adm_users.php' ? 'return false;' : $str).'">',$L['Users'],'</a>
<a class="item'.($oVIP->selfurl=='qte_adm_statuses.php' ? ' actif' : '').'" href="qte_adm_statuses.php" onclick="'.($oVIP->selfurl=='qte_adm_statuses.php' ? 'return false;' : $str).'">',$L['Statuses'],'</a>
</div>
<div class="group">
<p class="group">',L('Adm_modules'),'</p>
';

// search modules
$arrModules = GetParam(false,'param LIKE "module%"');
if ( count($arrModules)>0 )
{
  foreach($arrModules as $strKey=>$strValue)
  {
  $strKey = str_replace('module_','',$strKey);
  echo '<a class="item'.($oVIP->selfurl=='qtem_'.$strKey.'_adm.php' ? ' actif' : '').'" href="qtem_',$strKey,'_adm.php" onclick="return qtEdited(bEdited,e0);">',$strValue,'</a>',PHP_EOL;
  }
}
echo '<p class="item"><a href="qte_adm_module.php?a=add" onclick="return qtEdited(bEdited,e0);">[',L('Add'),']</a> &middot; <a href="qte_adm_module.php?a=rem" onclick="return warningedited(bEdited,e0);">[',$L['Remove'],']</a></p>
</div>
<div class="footer"><a id="exit" href="qte_index.php" target="_top" onclick="return qtEdited(bEdited,e0);">',L('Exit'),'</a></div>
</div>
';
}

echo '</td>
';
// --------------
// END TABLE OF CONTENT
// --------------

echo '
<td style="padding-left:10px">
<!-- END MENU/PAGE -->
';

echo cHtml::Page(START);

// Title (and help frame)

echo '<div style="width:300px; margin-bottom:20px"><h1>',$oVIP->selfname,'</h1>';
if ( isset($strPageversion) ) echo '<p class="small">',$strPageversion,'</p>';
if ( !empty($error) ) echo '<p id="infomessage" class="error">',$error,'</p>';
if ( empty($error) && !empty($warning) ) echo '<p id="warningmessage" class="warning">',$warning,'</p>';
if ( empty($error) && isset($strInfo) ) echo '<p id="infomessage" style="color:#007F11"><b>',$strInfo,'</b></p>';
echo '</div>
';

if ( file_exists(Translate($oVIP->selfurl.'.txt')) )
{
  echo '<div class="hlp_box">',PHP_EOL;
  echo '<div class="hlp_head">',L('Help'),'</div>',PHP_EOL;
  echo '<div class="hlp_body">'; include Translate($oVIP->selfurl.'.txt'); echo '</div>',PHP_EOL;
  echo '</div>',PHP_EOL;
}