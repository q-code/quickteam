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
 * @package    QuickTeam team
 * @author     Philippe Vandenberghe <info@qt-cute.org>
 * @copyright  2014 The PHP Group
 * @version    3.0 build:20140608
 */

session_start();
require_once 'bin/qte_init.php';
$oHtml->links['css'] = '<link rel="stylesheet" type="text/css" href="'.$_SESSION[QT]['skin_dir'].'/qte_index.css" />';
$oVIP->selfurl = 'qte_index.php';

// --------
// SECURITY
// --------

if ( $_SESSION[QT]['board_offline']=='1' ) { EchoPage(99); return; }
if ( $_SESSION[QT]['visitor_right']<1 && sUser::Role()=='V' ) { $oHtml->PageMsg(11); return; }

// --------
// INITIALIZE
// --------

if ( isset($_SESSION[QT]['section']) ) unset($_SESSION[QT]['section']); // previous section
$arrSections = GetSections(sUser::Role(),-2); // Get all sections at once (grouped by domain)

// --------
// HTML START
// --------

$oHtml->scripts_jq[] = '
$(function() {
  $( "tr.wayin" ).click(function() {
    if ( this.id.indexOf("wayin_")==0 )
    {
      var s = this.id.substr(6);
      var doc = document.getElementById("wayout_"+s);
      if ( doc ) window.location.assign(doc.href);
    }
  })
  .css("cursor","pointer");
});
';

include 'qte_inc_hd.php';

// --------
// DOMAIN / SECTIONS
// --------

$table = new cTable('','t-sec');
$table->th[0] = new cTableHead('&nbsp;','','c-icon');
$table->th[1] = new cTableHead('&nbsp;','','c-section');
$table->th[2] = new cTableHead('&nbsp;','','c-logo');
$table->th[3] = new cTableHead($L['Users'],'','c-items');
$table->td[0] = new cTableData('','','c-icon');
$table->td[1] = new cTableData('','','c-section');
$table->td[2] = new cTableData('','','c-logo');
$table->td[3] = new cTableData('','','c-items');

$intDom = 0;
$intSec = 0;

foreach(memGet('sys_domains') as $intDomid=>$strDomtitle)
{
	if ( isset($arrSections[$intDomid]) ) {
  if ( count($arrSections[$intDomid])>0 ) {

    $intDom++;
    if ( $intDom>1 ) echo '<div class="dom-sep"></div>',PHP_EOL;
    echo '<!-- domain ',$intDomid,': ',$strDomtitle,' -->',PHP_EOL;
    $table->row = new cTableRow('', 't-sec');
    echo $table->Start().PHP_EOL;
    echo '<thead>',PHP_EOL;
    $table->th[1]->content = $strDomtitle;
    echo $table->GetTHrow().PHP_EOL;
    echo '</thead>',PHP_EOL;
    echo '<tbody>',PHP_EOL;

    $strAlt='r1';

    foreach($arrSections[$intDomid] as $intSection=>$arrSection)
    {
      $intSec++;
      $oSEC = new cSection($arrSection,(isset($arrLastPostId[$intSection]) ? $arrLastPostId[$intSection] : false)); //use query optimisation
      $table->row = new cTableRow('wayin_'.$oSEC->id, 't-sec '.$strAlt.' hover wayin');
      $table->td[0]->content = AsImg($oSEC->GetIcon(),'F',$L['Ico_section_'.$oSEC->type.'_'.$oSEC->status],'i-sec','',Href('qte_section.php?s='.$oSEC->id));
      $table->td[1]->content = '<p class="section"><a id="wayout_'.$oSEC->id.'" class="section" href="'.Href('qte_section.php?s='.$oSEC->id).'">'.$oSEC->name.'</a></p>'.(empty($oSEC->descr) ? '' : '<p class="sectiondesc">'.$oSEC->descr.'</p>');
      $str = $oSEC->GetLogo();
      $table->td[2]->content = (empty($str) ? '&nbsp;' : AsImg($str,'logo',$oSEC->name,'section','','qte_section.php?s='.$oSEC->id));
      $table->td[3]->content = $oSEC->members;
      echo $table->GetTDrow().PHP_EOL;
      if ( $strAlt=='r1' ) { $strAlt='r2'; } else { $strAlt='r1'; }
    }
    echo '</tbody>',PHP_EOL;
    echo '</table>',PHP_EOL;

  }}
}

// No public section

if ( $intSec==0 ) echo '<p>',( sUser::Role()=='V' ? $L['E_no_public_section'] : $L['E_no_visible_section'] ),'</p>';

// --------
// HTML END
// --------

if ( isset($oSEC) ) unset($oSEC);
include 'qte_inc_ft.php';