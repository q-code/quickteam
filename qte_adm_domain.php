<?php

/**
* PHP versions 5
*
* LICENSE: This source file is subject to version 3.0 of the PHP license
* that is available through the world-wide-web at the following URI:
* http://www.php.net/license. If you did not receive a copy of
* the PHP License and are unable to obtain it through the web, please
* send a note to license@php.net so we can mail you a copy immediately.
*
* @author     Philippe Vandenberghe <info@qt-cute.org>
* @copyright  2013 The PHP Group
* @version    3.0 build:20141222
*/

session_start();
require 'bin/qte_init.php';
if ( sUser::Role()!='A' ) die(Error(13));
require 'bin/class/qte_class_dom.php';
include Translate(APP.'_adm.php');

// INITIALISE

$d = -1; QThttpvar('d','int'); if ( $d<0 ) die('Missing argument d');

$oDOM = new cDomain($d);

$oVIP->selfurl = 'qte_adm_domain.php';
$oVIP->selfname = '<span class="upper">'.$L['Adm_content'].'</span><br />'.$L['Domain_upd'];
$oVIP->exiturl = 'qte_adm_sections.php';
$oVIP->exitname = '&laquo; '.$L['Sections'];

// --------
// SUBMITTED
// --------

if ( isset($_POST['ok']) )
{
  $str = trim($_POST['title']); if ( get_magic_quotes_gpc() ) $str = stripslashes($str);
  $str = QTconv($str,'3',QTE_CONVERT_AMP,false);
  if ( $str=='' ) $error = $L['Title'].' '.$L['E_invalid'];

  // Save name and translations

  if ( empty($error) )
  {
    $r = $oDOM->Rename($str); // Returns false in case of db error
    if ( !$r ) $error = $oDB->error;
    cLang::Delete('domain','d'.$d);
  }
  if ( empty($error) )
  {
    foreach($_POST as $strKey=>$strTranslation)
    {
      if ( substr($strKey,0,1)=='T' )
      {
        $strTranslation = trim($strTranslation);
        if ( !empty($strTranslation) )
        {
        if ( get_magic_quotes_gpc() ) $strTranslation = stripslashes($strTranslation);
        cLang::Add('domain', substr($strKey,1), 'd'.$d, addslashes(QTconv($strTranslation,'5')));
        }
      }
    }
  }

  // Error handler

  $_SESSION['pagedialog'] = (empty($error) ? 'O|'.$L['S_save'] : 'E|'.$error);
  if ( empty($error) )
  {
    Unset($_SESSION['L']['domain']); // Clear session to reloard names
    $oHtml->Redirect($oVIP->exiturl); // Exit
  }
}

// --------
// HTML START
// --------

include APP.'_adm_inc_hd.php';

echo '
<form method="post" action="',$oVIP->selfurl,'">
<table class="t-data" width="500">
';
$str = QTconv($oDOM->title,'I');
echo '<tr class="t-data">
<td class="headfirst" style="width:100px;"><label for="title">',$L['Title'],'</label></td>
<td><input required type="text" id="title" name="title" size="32" maxlength="64" value="',$str,'" onchange="bEdited=true;" />',(strstr($str,'&amp;') ?  ' <span class="small">'.$oDOM->title.'</span>' : ''),'</td>
</tr>
';
echo '<tr class="t-data">
<td class="headfirst">',$L['Translations'],'</td>
<td colspan="2">
<p class="help">',sprintf($L['E_no_translation'],$oDOM->title),'</p>
<table>';
$arrTrans = cLang::Get('domain','*','d'.$d);
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
  <td style="width:30px"><span title="',$arr[1],'">',$arr[0],'</span></td>
  <td><input class="small" title="',$L['Domain'],' (',$strIso,')" type="text" id="T',$strIso,'" name="T',$strIso,'" size="32" maxlength="64" value="',$str,'" onchange="bEdited=true;" />',(strstr($str,'&amp;') ?  ' <span class="small">'.$arrTrans[$strIso].'</span>' : ''),'</td>
  </tr>
  ';
}
echo '</table>
</td>
</tr>
</table>
';
echo '<p style="margin:0 0 5px 0;text-align:center"><input type="hidden" name="d" value="',$d,'"/><input type="submit" name="ok" value="',$L['Save'],'"/></p>
</form>
<p><a href="',$oVIP->exiturl,'">',$oVIP->exitname,'</a></p>
';

// HTML END

include APP.'_adm_inc_ft.php';