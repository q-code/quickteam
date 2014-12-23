<?php // QuickTeam 3.0 build:20141222

// FUNCTION (can also get section by domain)

function GetMembership($id,$bByDomain=false)
{
  $arr = array();
  global $oDB;
  $oDB->Query('SELECT s.id,s.title,s.stats,s.options,s.type,s.domainid FROM '.TABSECTION.' s INNER JOIN '.TABS2U.' l  ON l.sid=s.id WHERE l.userid='.$id.' ORDER BY l.issuedate DESC');
  while ( $row=$oDB->Getrow() )
  {
    if ( $bByDomain )
    {
    if ( $row['type']=='0' ) $arr[(int)$row['domainid']][(int)$row['id']] = $row;
    if ( $row['type']!='0' && sUser::IsStaff() ) $arr[(int)$row['domainid']][(int)$row['id']] = $row;
    }
    else
    {
    if ( $row['type']=='0' ) $arr[(int)$row['id']] = $row;
    if ( $row['type']!='0' && sUser::IsStaff() ) $arr[(int)$row['id']] = $row;
    }
  }
  return $arr;
}
function CountMembership($id)
{
  global $oDB;
  $oDB->Query('SELECT count(*) as countid FROM '.TABS2U.' WHERE userid='.$id);
  $row=$oDB->Getrow();
  return (int)$row['countid'];
}

// INITIALIZE

if ( $_SESSION[QT]['editing'] )
{
  if ( !sUser::IsStaff() ) $_SESSION[QT]['editing']=false;
}

$intMembership = CountMembership($id);
$intDom = 0;
$intSec = 0;

// --------
// SUBMITTED
// --------

if ( isset($_POST['ok']) )
{
  $bEdited = false;
  if ( !isset($_POST['id'])  ) die('Missing id');
  $id = (int)$_POST['id'];

  // -- REM FROM TEAM --

  if ( isset($_POST['delteam']) ) {
  if ( $_POST['delteam']!='no' ) {
    // update del team
    if ( $_POST['delteam']=='all' )
    {
      $arrMembership = GetMembership($id); // list sections that MUST have statistics updated after the delete
      $oDB->Exec('DELETE FROM '.TABS2U.' WHERE sid<>0 AND userid='.$id);
      foreach(array_keys($arrMembership) as $intId) { if ( $intId>0 ) cSection::UpdateStats($intId); }
      $bEdited = true;
    }
    else
    {
      cItem::InSection($_POST['delteam'],'rem',$id);
      cSection::UpdateStats((int)$_POST['delteam']);
      $bEdited = true;
    }
  }}

  // -- ADD TO TEAM --

  if ( isset($_POST['addteam']) ) {
  if ( $_POST['addteam']!='no' ) {
    // update add team
    cItem::InSection($_POST['addteam'],'add',$id);
    cSection::UpdateStats((int)$_POST['addteam']);
    $bEdited = true;
  }}

  // -- exit --

  if ( $bEdited ) $strInfo = $L['S_save'];
}

// --------
// HTML START
// --------

echo '<div class="pan-top">',(isset($L[strtoupper($tt).'Profile']) ? $L[strtoupper($tt).'Profile'] : $L['Profile']),'</div>
';

if ( !empty($error) ) echo '<span class="error">',$error,'</span>',PHP_EOL;

if ( $_SESSION[QT]['editing'] )
{
  $arrMembership = GetMembership($id);
  echo '<div class="editform"><form method="post" action="',Href('qte_user.php'),'?id='.$id.'&amp;tt=',$tt,'">',PHP_EOL;
  echo '<table>',PHP_EOL;
  echo '<tr>';
  echo '<td class="headfirst">',$L['User_section_add'],'</td>',PHP_EOL;
  echo '<td><select name="addteam" id="addteam" size="1" onchange="bEdited=true;"><option value="no"> </option>',Sectionlist(-1,array(),array_keys($arrMembership)),'</select></td>',PHP_EOL;
  echo '<td rowspan="2"><p class="save"><input type="hidden" name="id" value="',$id,'"/><input type="submit" name="ok" value="',$L['Save'],'"/></p></td>';
  echo '</tr>',PHP_EOL;
  echo '<tr>';
  echo '<td class="headfirst">',$L['User_section_del'],'</td>',PHP_EOL;
  echo '<td><select name="delteam" id="delteam" size="1" onchange="bEdited=true;"><option value="no"> </option><option value="all">('.$L['All'].')</option>',QTasTag(QTarrget($arrMembership)),'</select></td>',PHP_EOL;
  echo '</tr>',PHP_EOL;
  echo '</table>',PHP_EOL;
  echo '</form></div>';
}

// -----------
if ( $intMembership>0 ) {
// -----------

$arrSections = GetMembership($id,true);
foreach(memGet('sys_domains') as $intDomid=>$strDomtitle)
{
  if ( isset($arrSections[$intDomid]) ) {
  if ( count($arrSections[$intDomid])>0 ) {

    ++$intDom;
    if ( $intDom>1 ) echo '<div class="dom-sep"></div>',PHP_EOL;
    echo '<table class="t-data section">',PHP_EOL;
    echo '<tr><th>',$strDomtitle,'</th></tr>',PHP_EOL;

    foreach($arrSections[$intDomid] as $intSection=>$row)
    {
      ++$intSec;
      $oSEC = new cSection($row);
      if ( $oSEC->type!=0 ) $oSEC->name .= ' '.AsImg($_SESSION[QT]['skin_dir'].'/ico_section_'.$oSEC->type.'_'.$oSEC->status.'.gif','[+]',$L['Ico_section_'.$oSEC->type.'_'.$oSEC->status],'ico','width:25px');
      $oSEC->descr .= (empty($oSEC->descr) ? '' : '<br />').'<a href="'.Href('qte_section.php').'?s='.$oSEC->id.'" class="small">'.L('User',$oSEC->members).'</a>';
      echo '<tr>';
      echo '<td>';
      $oSEC->ShowInfo('sectioninfo-right','sectioninfo','sectiondesc','qte_section.php?s='.$oSEC->id);
      echo '</td>',PHP_EOL;
      echo '</tr>',PHP_EOL;
    }

    echo '</table>',PHP_EOL;

  }}
}

// -----------
}
// -----------

if ( $intSec==0 ) { $table = new cTable('t1','t-user'); $table->th[] = new cTableHead('&nbsp;'); echo $table->GetEmptyTable('<p style="margin-left:10px;margin-right:10px">'.L('E_no_membership').'</p>',true,'','r1'); }
if ( $intSec<$intMembership ) echo '<p class="small">'.L('E_private_membership').'</p>';