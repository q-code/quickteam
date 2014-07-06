<?php // v3.0 build:20140608

ob_start();

if ( isset($_GET['view']) ) $_SESSION[QT]['viewmode'] = (strtolower($_GET['view'])=='c' ? 'c' : 'n');

// Page message

if ( !empty($_SESSION['pagedialog']) )
{
  if ( empty($oVIP->msg->text) ) $oVIP->msg->FromString($_SESSION['pagedialog']);
  $oHtml->scripts_jq[] = '$(function() {
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

// check LangMenu condition

$arrLangMenu = array(); //$strLangMenu = '';
if ( $_SESSION[QT]['userlang']=='1' )
{
  if ( file_exists('bin/qte_lang.php') )
  {
    include 'bin/qte_lang.php';
    foreach ($arrLang as $strKey => $arrDef)
    {
    $arrLangMenu[] = '<a href="'.Href().'?'.GetUri('lx').'&amp;lx='.$strKey.'"'.(isset($arrDef[1]) ? ' title="'.$arrDef[1].'"' : '').' class="'.($_SESSION[QT]['show_banner']=='0' ? 'langmenu nobanner' : 'langmenu banner').'">'.$arrDef[0].'</a>';
    }
  }
  else
  {
    $arrLangMenu[] = '<span class="small">missing file:bin/qte_lang.php</span>';
  }
}
if ( sUser::Id()>0 )
{
  if ( empty($_SESSION[QT.'_usr_info']) ) $_SESSION[QT.'_usr_info'] = array('',L('Userrole_V'));
  $strLangMenu = AsImg($_SESSION[QT]['skin_dir'].'/ico_user_p_1.gif','::',$L['User'],'username').'&nbsp;<a class="username" href="'.Href('qte_user.php').'?id='.sUser::Id().'">'.UserFirstLastName($_SESSION[QT.'_usr_info'],' ',sUser::Name()).'</a>';
}
else
{
  $strLangMenu = AsImg($_SESSION[QT]['skin_dir'].'/ico_user_p_0.gif','::',$L['User'],'username').'&nbsp;<span class="username">'.L('Userrole_V').'</span>';
}
if ( count($arrLangMenu)>1 ) $strLangMenu .= ' | '.implode(' ',$arrLangMenu);

$strLangMenu = '<div class="langmenu">'.$strLangMenu.'</div>'.PHP_EOL;

// check welcome
$bWelcome = true;

if ( in_array($oVIP->selfurl,array('qte_register.php','qte_form_reg.php','qte_change.php')) ) $bWelcome = false;
if ( $_SESSION[QT]['show_welcome']=='0' ) $bWelcome = false;
if ( $_SESSION[QT]['show_welcome']=='1' && sUser::Auth() ) $bWelcome = false;
if ( $_SESSION[QT]['board_offline']=='1' ) $bWelcome = false;
if ( !file_exists(Translate('sys_welcome.txt')) ) $bWelcome = false;

$oHtml->title = (empty($oVIP->selfname) ? '' : $oVIP->selfname.' - ').$oHtml->title;

// --------
// HTML START
// --------

echo $oHtml->Head();
echo $oHtml->Body(array('onload'=>(isset($strBodyAddOnload) ? $strBodyAddOnload : null),'onunload'=>(isset($strBodyAddOnunload) ? $strBodyAddOnunload : null)));

echo cHtml::Page(START);

// MENU

$arrMenus = array();

// keys are:
// 'h' in header,
// 'f' in footer,
// 'n' name,
// 'u' url,
// 's' selected with url's,
// 'secondary' define if class secondary can be applied to header menu

if ( $_SESSION[QT]['home_menu']=='1' && !empty($_SESSION[QT]['home_url']) )
{
$arrMenus[]=array('h'=>true, 'f'=>true, 'n'=>$_SESSION[QT]['home_name'], 'u'=>$_SESSION[QT]['home_url']);
}
$arrMenus[]=array('h'=>false,'f'=>true, 'n'=>L('Legal'), 'u'=>'qte_privacy.php');
//$arrMenus[]=array('h'=>false,'f'=>true, 'n'=>L('FAQ'), 'u'=>'qte_faq.php');
$arrMenus[]=array('h'=>true, 'f'=>false,'n'=>ObjTrans('index','i',$_SESSION[QT]['index_name']), 'u'=>'qte_index.php', 's'=>'qte_index.php qte_section.php qte_calendar.php qte_privacy.php', 'secondary'=>true);
$arrMenus[]=array('h'=>true, 'f'=>true, 'n'=>L('Search'), 'u'=>( $_SESSION[QT]['board_offline']=='1' || (sUser::Role()=='V' && $_SESSION[QT]['visitor_right']<3) ? '' : 'qte_search.php'),'s'=>'qte_search.php');
if ( cVIP::CanViewStats() )
{
$arrMenus[]=array('h'=>false,'f'=>true,'n'=>L('Statistics'), 'u'=>($_SESSION[QT]['board_offline']=='1' ? '' : 'qte_stats.php'));
}
if ( sUser::Auth() )
{
$arrMenus[]=array('h'=>true, 'f'=>true,'n'=>L('Profile'), 'u'=>($_SESSION[QT]['board_offline']=='1' ? '' : 'qte_user.php?id='.sUser::Id()), 's'=>'qte_user.php qte_user_img.php.php qte_user_sign.php qte_user_pwd.php', 'secondary'=>true);
$arrMenus[]=array('h'=>true, 'f'=>true,'n'=>L('Logout'), 'u'=>'qte_login.php?a=out');
}
else
{
$arrMenus[]=array('h'=>true, 'f'=>true,'n'=>L('Register'),'u'=>($_SESSION[QT]['board_offline']=='1' ? '' : 'qte_user_new.php'), 's'=>'qte_register.php qte_form_reg.php', 'secondary'=>true);
$arrMenus[]=array('h'=>true, 'f'=>true,'n'=>L('Login'),'u'=>'qte_login.php', 's'=>'qte_login.php qte_reset_pwd.php');
}

$strMenus = '
<!-- menu -->
<div class="menu'.($bWelcome ? ' withwelcome' : '').'">
<ul class="inline">
';
foreach($arrMenus as $arrMenu) {
  if ( $arrMenu['h'] ) {
    if ( !isset($arrMenu['s']) ) $arrMenu['s']=' '.$arrMenu['u'];
    //if ( !isset($arrMenu['i']) ) $arrMenu['i']=' '.$arrMenu['u'];
    if ( empty($arrMenu['u']) )
    {
      $strMenus .= '<li class="inactif'.(isset($arrMenu['secondary']) ? ' secondary' : '').'">'.$arrMenu['n'].'</li>'.PHP_EOL;
    }
    else
    {
      $strMenus .= '<li'.(isset($arrMenu['secondary']) ? ' class="secondary"' : '').(strstr($arrMenu['s'],$oVIP->selfurl) ? ' id="menuactif"' : '').'><a href="'.Href($arrMenu['u']).'">'.$arrMenu['n'].'</a></li>'.PHP_EOL;
    }
  }
}
$strMenus .='</ul>
</div>
';

// show banner and menu

switch($_SESSION[QT]['show_banner'])
{
  case '0': EchoBanner('',$strLangMenu,'','nobanner'); echo $strMenus; break;
  case '1': EchoBanner($_SESSION[QT]['skin_dir'].'/qte_logo.gif',$strLangMenu); echo $strMenus; break;
  case '2': EchoBanner($_SESSION[QT]['skin_dir'].'/qte_logo.gif',$strLangMenu,$strMenus); break;
}

// WELCOME

if ( $bWelcome )
{
echo '
<!-- welcome -->
<div class="welcome">';
include Translate('sys_welcome.txt');
echo '</div>
';
}

// MAIN

echo '
<!-- BODY -->
<div class="body">
';

echo '
<!-- top bar -->
<div class="bodyhd">
<div class="bodyhdleft"><a class="body_hd" href="',Href('qte_index.php'),'"',($oVIP->selfurl=='qte_index.php' ? ' onclick="return false;"' : ''),'>',ObjTrans('index','i',$_SESSION[QT]['index_name']),'</a>';
if ( isset($_SESSION[QT]['section']) && $_SESSION[QT]['section']>=0 )
{
	if ( !isset($q) ) $q='';
  echo QTE_CRUMBTRAIL,'<a class="body_hd" href="',Href('qte_section.php'),'?s=',$_SESSION[QT]['section'],'"',($oVIP->selfurl=='qte_section.php' && empty($q) ? ' onclick="return false;"' : ''),'>',ObjTrans('sec','s'.$_SESSION[QT]['section'],(isset($oSEC) ? $oSEC->name : $_SESSION[QT]['sys_sections'][$_SESSION[QT]['section']])),'</a>';
}

echo '</div>
<div class="bodyhdright">';

switch($oVIP->selfurl)
{
case 'qte_index.php':
  $strURI = GetUri('view');
  if ( $_SESSION[QT]['viewmode']=='c' )
  {
  echo '<a href="',Href('qte_index.php'),'?',$strURI,'&amp;view=n"><img class="ico i-modes" src="',$_SESSION[QT]['skin_dir'],'/ico_view_n.gif" title="',$L['Ico_view_n'],'" alt="N"/></a>';
  }
  else
  {
  echo '<a href="',Href('qte_index.php'),'?',$strURI,'&amp;view=c"><img class="ico i-modes" src="',$_SESSION[QT]['skin_dir'],'/ico_view_c.gif" title="',$L['Ico_view_c'],'" alt="C"/></a>';
  }
  break;
case 'qte_section.php':
  $strURI = GetUri('view');
  if ( !empty($_SESSION[QT]['picture']) )
  {
    if ( $_SESSION[QT]['viewmode']=='c' )
    {
    echo '<a href="',Href('qte_section.php'),'?',$strURI,'&amp;view=n"><img class="ico i-modes" src="',$_SESSION[QT]['skin_dir'],'/ico_view_n.gif" title="',$L['Ico_view_n'],'" alt="N"/></a>';
    }
    else
    {
    echo '<a href="',Href('qte_section.php'),'?',$strURI,'&amp;view=c"><img class="ico i-modes" src="',$_SESSION[QT]['skin_dir'],'/ico_view_c.gif" title="',$L['Ico_view_c'],'" alt="C"/></a>';
    }
  }
  break;
case 'qte_calendar.php':
  $i = (isset($s) ? (int)$s : -1);
  if ( $i<0 && isset($_SESSION[QT]['section']) && $_SESSION[QT]['section']>=0 ) $i=$_SESSION[QT]['section'];
  if ( $i>=0 ) echo '<a href="',Href('qte_section.php'),'?s=',$i,'"><img class="ico i-modes" src="',$_SESSION[QT]['skin_dir'],'/ico_view_f_n.gif" title="',$L['Ico_view_f_n'],'" alt="Tab"/></a>';
  break;
case 'qte_stats.php':
  $strURI = GetUri('view');
  break;
}

echo '</div>
</div>
';

// MAIN CONTENT

echo '
<!-- main content -->
<div class="bodyct pg-'.cSYS::PageCode($oVIP->selfurl).' view-'.$_SESSION[QT]['viewmode'].'">

';