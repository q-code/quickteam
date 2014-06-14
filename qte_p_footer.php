<?php

// QuickTicket 3.0 build:20140608

// BODY END

echo '
</div>
';

// LINE END

$bSectionlist = false;
if ( $oVIP->selfurl!='qte_index.php' ) {
if ( QTE_SHOW_GOTOLIST ) {
if ( count($_SESSION[QT]['sys_sections'])>1 ) {
  $bSectionlist = true;
}}}

echo '
<!-- bottom bar -->
<div class="bodyft">
<div class="bodyftleft">';
if ( QTE_SHOW_TIME )
{
  echo gmdate($_SESSION[QT]['formattime'], time() + 3600*($_SESSION[QT]['time_zone']));
  if ( $_SESSION[QT]['show_time_zone']=='1' )
  {
    echo ' (gmt';
    if ( $_SESSION[QT]['time_zone']>0 ) echo '+',$_SESSION[QT]['time_zone'];
    if ( $_SESSION[QT]['time_zone']<0 ) echo $_SESSION[QT]['time_zone'];
    echo ')';
  }
}
if ( isset($oSEC) )
{
  if ( QTE_SHOW_MODERATOR ) echo ' &middot; ',$L['Section_moderator'],': <a href="',Href('qte_user.php?id='.$oSEC->modid),'">',$oSEC->modname,'</a>';
}

echo '</div>
<div class="bodyftright">';
if ( $bSectionlist ) echo '<label for="jumpto">',$L['Goto'],'&nbsp;</label><select id="jumpto" name="s" size="1" onchange="window.location=\''.Href('qte_section.php').'?s=\'+this.value;"><option value="-1">&nbsp;</option>',Sectionlist(),'</select>';
echo '</div>
</div>
<!-- END BODY -->
</div>
';

// --------
// INFO & LEGEND
// --------

if ( $_SESSION[QT]['board_offline']!='1' ) {
if ( $_SESSION[QT]['show_legend']=='1' ) {
if ( in_array($oVIP->selfurl,array('index.php','qte_index.php','qte_section.php','qte_find.php','qte_calendar.php')) ) {

echo '
<!-- Legend -->
<table class="info">
<tr>
<td>
<div class="infobox stat">
<h1>',$L['Information'],'</h1>
';

// section info

if ( isset($oSEC) )
{
  echo ObjTrans('sec',"s$s",$oSEC->name),':<br />';

  if ( $_SESSION[QT]['show_Z'] )
  {
    echo '&bull; ',L('User',$oSEC->members),'<br /><br />';
  }
  else
  {
    echo '&bull; ',L('User',$oSEC->members);
    echo '<br />&bull; ',(isset($oVIP->statuses['Z']['statusname']) ? $oVIP->statuses['Z']['statusname'] : 'Not member'),': ',($oSEC->membersZ==0 ? strtolower($L['None']) : $oSEC->membersZ),'<br /><br />';
  }

}

// application info

echo ObjTrans('index','i',$_SESSION[QT]['index_name']),':<br />';
if ( isset($_SESSION[QT]['sys_members']) )
{
echo '&bull; ',L('User',$_SESSION[QT]['sys_members']);
}

// new user info

if ( isset($_SESSION[QT]['sys_states']['newuserid']) ) {
if ( !empty($_SESSION[QT]['sys_states']['newuserdate']) ) {
if ( DateAdd($_SESSION[QT]['sys_states']['newuserdate'],30,'day')>Date('Ymd') ) {
echo '<br /><br />',$L['Welcome_to'],'<a class="small" href="',Href('qte_user.php?id='.$_SESSION[QT]['sys_states']['newuserid']),'">',$_SESSION[QT]['sys_states']['newusername'],'</a>';
}}}

// birthday

if ( QTE_SHOW_BIRTHDAYS ) {
if ( isset($_SESSION[QT]['sys_members']) ) {
if ( $_SESSION[QT]['sys_members']>1 ) {
if ( strpos($_SESSION[QT]['fields_u'],'birthdate')!==FALSE ) {

  switch($oDB->type)
  {
  // Select month
  case 'mysql4':
  case 'mysql': $oDB->Query('SELECT id,username,firstname,lastname FROM '.TABUSER.' WHERE SUBSTRING(birthdate,5,4)="'.substr(DateAdd(Date('Ymd'),+1,'day'),4,4).'" OR SUBSTRING(birthdate,5,4)="'.Date('md').'"'); break;
  case 'sqlsrv':
  case 'mssql': $oDB->Query('SELECT id,username,firstname,lastname FROM '.TABUSER.' WHERE SUBSTRING(birthdate,5,4)="'.substr(DateAdd(Date('Ymd'),+1,'day'),4,4).'" OR SUBSTRING(birthdate,5,4)="'.Date('md').'"'); break;
  case 'pg':    $oDB->Query('SELECT id,username,firstname,lastname FROM '.TABUSER.' WHERE SUBSTRING(birthdate,5,4)="'.substr(DateAdd(Date('Ymd'),+1,'day'),4,4).'" OR SUBSTRING(birthdate,5,4)="'.Date('md').'"'); break;
  case 'ibase': $oDB->Query('SELECT id,username,firstname,lastname FROM '.TABUSER.' WHERE SUBSTRING(birthdate FROM 5 FOR 4)="'.substr(DateAdd(Date('Ymd'),+1,'day'),4,4).'" OR SUBSTRING(birthdate FROM 5 FOR 4)="'.Date('md').'"'); break;
  case 'sqlite': $oDB->Query('SELECT id,username,firstname,lastname FROM '.TABUSER.' WHERE SUBSTR(birthdate,5,4)="'.substr(DateAdd(Date('Ymd'),+1,'day'),4,4).'" OR SUBSTR(birthdate,5,4)="'.Date('md').'"'); break;
  case 'db2':   $oDB->Query('SELECT id,username,firstname,lastname FROM '.TABUSER.' WHERE SUBSTR(birthdate,5,4)="'.substr(DateAdd(Date('Ymd'),+1,'day'),4,4).'" OR SUBSTR(birthdate,5,4)="'.Date('md').'"'); break;
  case 'oci':   $oDB->Query('SELECT id,username,firstname,lastname FROM '.TABUSER.' WHERE SUBSTR(birthdate,5,4)="'.substr(DateAdd(Date('Ymd'),+1,'day'),4,4).'" OR SUBSTR(birthdate,5,4)="'.Date('md').'"'); break;
  default: die('Unknown db type '.$oDB->type);
  }
  $arr = array();
  while($row=$oDB->Getrow())
  {
    if ( empty($row['lastname']) ) $row['lastname']='('.$row['username'].')';
    $arr[] = '<a class="small" href="'.Href('qte_user.php').'?id='.$row['id'].'">'.(empty($row['firstname']) ? '' : $row['firstname'].' ').$row['lastname'].'</a>';
    if ( count($arr)>4 ) break;
  }
  if ( !empty($arr) ) echo '<br /><br />',$L['Happy_birthday'],implode(', ',$arr);

}}}}

echo '</div>',PHP_EOL;
echo '</td>',PHP_EOL;
echo '<td>',PHP_EOL;
if ( isset($strDetailLegend) )
{
echo '<div class="infobox details"><h1>',$L['Details'],'</h1>',PHP_EOL;
echo $strDetailLegend;
echo '</div>',PHP_EOL;
}
echo '</td>',PHP_EOL;
echo '<td>',PHP_EOL;
echo '<div class="infobox legend"><h1>',$L['Legend'],'</h1>',PHP_EOL;
if ( in_array($oVIP->selfurl,array('index.php','qte_index.php')) )
{
  echo AsImg($_SESSION[QT]['skin_dir'].'/ico_section_0_0.gif','[+]',$L['Ico_section_0_0'],'ico i-sec'),S,$L['Ico_section_0_0'],'<br />',PHP_EOL;
  echo AsImg($_SESSION[QT]['skin_dir'].'/ico_section_0_1.gif','[+]',$L['Ico_section_0_1'],'ico i-sec'),S,$L['Ico_section_0_1'],'<br/>',PHP_EOL;
}
else
{
  foreach ($oVIP->statuses as $strKey=>$arr)
  {
  echo AsImg($_SESSION[QT]['skin_dir'].'/'.$oVIP->statuses[$strKey]['icon'],$strKey,$arr['statusname'],'ico i-status'),S,$arr['statusname'],'<br />',PHP_EOL;
  }
}
echo '</div>',PHP_EOL;
echo '</td>',PHP_EOL;
echo '</tr>',PHP_EOL;
echo '</table>',PHP_EOL;

}}}

// --------
// COPYRIGHT
// --------

// MODULE RSS
if ( $_SESSION[QT]['board_offline']!='1' ) {
if ( UseModule('rss') ) {
if ( $_SESSION[QT]['m_rss']=='1' ) {
if ( sUser::Role()!='V' || sUser::Role().substr($_SESSION[QT]['m_rss_conf'],0,1)=='VV' ) {
if ( $oVIP->selfurl!='qtem_rss.php' ) {
  $arrMenus[]=array('h'=>false,'f'=>true, 'n'=>'<img src="admin/rss.gif" width="34" height="14" style="vertical-align:bottom;border-width:0" alt="rss" title="RSS" />', 'u'=>'qtem_rss.php');
}}}}}

echo '
<!-- footer -->
<div class="footer">
<div class="footerleft">';
$i=0;
foreach($arrMenus as $arrMenu) {
if ( $arrMenu['f'] ) {
  if ( !isset($arrMenu['s']) ) $arrMenu['s']=$arrMenu['u'];
  if ( $i!=0 ) echo ' &middot; ';
  $i++;
  if ( empty($arrMenu['u']) )
  {
  echo $arrMenu['n'];
  }
  else
  {
  echo '<a href="',Href($arrMenu['u']),'"',(strstr($arrMenu['s'],$oVIP->selfurl) ? ' onclick="return false;"' : ''),'>',$arrMenu['n'],'</a>';
  }
}}
if ( sUser::Role()=='A' ) echo ' &middot; <a href="',Href('qte_adm_index.php'),'">['.L('Administration').']</a>';

echo '</div>
<div class="footerright">powered by <a href="http://www.qt-cute.org">QT-cute</a> <span title="',QTEVERSION,'">v',substr(QTEVERSION,0,3),'</span></div>
</div>
';

// END PAGE CONTROL

echo cHtml::Page(END);

// HTML END

if ( isset($oDB->stats) )
{
  $oDB->stats['end'] = (float)(vsprintf('%d.%06d', gettimeofday()));
  echo '<br/>&nbsp;',$oDB->stats['num'],' queries in ',round($oDB->stats['end']-$oDB->stats['start'],4),' sec';
}

echo $oHtml->End();

ob_end_flush();