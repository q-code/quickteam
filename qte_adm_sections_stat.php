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
require_once 'bin/qte_init.php';
if ( sUser::Role()!='A' ) die($L['R_admin']);
include Translate('@_adm.php');

// ---------
// INITIALISE
// ---------

$oVIP->selfurl = 'qte_adm_sections_stat.php';
$oVIP->selfname = '<span class="upper">'.$L['Adm_content'].'</span><br />'.$L['Sections'].'<br>'.$L['Update_stats'];
$oVIP->exiturl = 'qte_adm_sections.php';
$oVIP->exitname = '&laquo; '.$L['Sections'];

$arrDomains = GetDomains();
if ( count($arrDomains)>50 ) $warning='You have too much domains. Try to remove unused domains.';
$arrSections = GetSections('A',-2); // Optimisation: get all sections at once (grouped by domain)
$intSections = count($arrSections,COUNT_RECURSIVE) - count($arrDomains);
if ( $intSections>100 ) $warning='You have too much sections. Try to remove unused sections.';

// --------
// SUBMITTED
// --------

if ( isset($_POST['ok']) )
{
  $arr = array();
  foreach(array_keys($arrDomains) as $id)
  {
  if ( isset($_POST['domain'.$id]) ) $arr = array_merge($arr,$_POST['domain'.$id]);
  }
  if ( count($arr)>0 )
  {
  foreach($arr as $id) cSection::UpdateStats((int)$id);
  $_SESSION['pagedialog'] = 'O|'.L('Section',count($arr)).'. '.$L['S_update'];
  }
  else
  {
  $_SESSION['pagedialog'] = 'E|'.$L['E_nothing_selected'];
  }
}

// --------
// HTML START
// --------

$oHtml->scripts[] = '<script type="text/javascript" src="bin/js/qte_table.js"></script>';
$oHtml->scripts_jq[] ='
$(function() {
  $(".checkboxdomain").click(function() { qtCheckboxAll(this.id,this.id+"[]",true); }); // false  when no row hightlight
  $(".checkboxsection").click(function() { qtHighlight("tr_"+this.id,this.checked); }); // delete when no row hightlight
});
';

include APP.'_adm_inc_hd.php';

// count users by section (used to check last stats in each sections)
$arrCounts = array();
$oDB->Query('SELECT sid,count(userid) as countid FROM '.TABS2U.' GROUP BY sid');
while($row=$oDB->Getrow()) $arrCounts[(int)$row['sid']]=(int)$row['countid'];

$arr = memGet('sys_statuses');
echo '<form method="post" action="qte_adm_sections_stat.php">',PHP_EOL;
if ( $intSections>3 ) echo '<p style="margin:4px 0"><img src="admin/selection_up.gif" style="width:10px;height:10px;vertical-align:bottom;margin:2px 10px 0 15px"/>',$L['Selection'],': <input type="submit" class="small" name="ok" value="',$L['Update_stats'],'" /></p>',PHP_EOL;
echo '<table class="t-sec">
<tr class="tr">
<th>&nbsp;</th>
<th colspan="2" style="text-align:left">',$L['Domain'],'/',$L['Section'],'</th>
<th>',$L['Users'],'</th>
<th>',$arr['Z']['statusname'],' (',$L['Status'],' Z)</th>
</tr>
';

$i=0;
foreach($arrDomains as $intDomain=>$strDomain)
{
  if ( isset($arrSections[$intDomain]) ) {
  if ( count($arrSections[$intDomain])>0 ) {

    echo '<tr>',PHP_EOL;
    echo '<td class="colgroup"><input type="checkbox" id="domain'.$intDomain.'" class="checkboxdomain"',(count($arrSections[$intDomain])<3 ? ' style="display:none"' : ''),' /></td>',PHP_EOL;
    echo '<td class="colgroup" colspan="2">',$strDomain,'</td>',PHP_EOL;
    echo '<td class="colgroup" style="text-align:center">&nbsp;</td>',PHP_EOL;
    echo '<td class="colgroup" style="text-align:center">&nbsp;</td>',PHP_EOL;

    $i += 1;

    $j = 0;
    foreach($arrSections[$intDomain] as $intSecid=>$arrSection)
    {
      $oSEC = new cSection($arrSection);
      // update stats if required
      if ( isset($arrCounts[$intSecid]) ) {
      if ( $arrCounts[$intSecid]!=$oSEC->members ) {
        cSection::UpdateStats($intSecid);
        $oSEC->members=$arrCounts[$intSecid];
      }}

      echo '<tr class="rowlight" id="tr_section'.$oSEC->id.'">',PHP_EOL;
      echo '<td style="text-align:center"><input type="checkbox" class="checkboxsection" name="domain'.$intDomain.'[]" id="section'.$oSEC->id.'" value="'.$oSEC->id.'" onclick="qtCheckboxOne(\'domain'.$intDomain.'[]\',\'domain'.$intDomain.'\');" /></td>',PHP_EOL;
      echo '<td style="text-align:center"><label for="section'.$oSEC->id.'">',AsImg($_SESSION[QT]['skin_dir'].'/ico_section_'.$oSEC->type.'_'.$oSEC->status.'.gif','[+]',$L['Ico_section_'.$oSEC->type.'_'.$oSEC->status],'ico i-sec20'),'</label></td>';
      echo '<td><label for="section'.$oSEC->id.'"><span class="bold">',$oSEC->name,'</span> &middot; <span class="small">',$L['Section_type'][$oSEC->type],($oSEC->status=='1' ? '<span class="small"> ('.$L['Section_status'][1].')</span>' : ''),'</span></label></td>';
      echo '<td style="text-align:center">',$oSEC->members,'</td>',PHP_EOL;
      echo '<td style="text-align:center">',$oSEC->membersZ,'</td>',PHP_EOL;
      echo '</tr>',PHP_EOL;
    }

  }}
}
echo '</table>
<p style="margin:4px 0"><img src="admin/selection_down.gif" style="width:10px;height:10px;vertical-align:top;margin:2px 10px 0 15px"/>',$L['Selection'],': <input type="submit" class="small" name="ok" value="',$L['Update_stats'],'" /></p>
</form>
<p><a href="',$oVIP->exiturl,'">',$oVIP->exitname,'</a></p>
';

// --------
// HTML END
// --------

include APP.'_adm_inc_ft.php';