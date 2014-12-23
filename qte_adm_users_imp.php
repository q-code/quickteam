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
 * @version    3.0 build:20141222
 */

session_start();
require 'bin/qte_init.php';
include Translate(APP.'_adm.php');

if ( sUser::Role()!='A' ) die($L['R_admin']);

// ---------
// INITIALISE
// ---------

$strTitle   = '';
$strDelimit = ',';
$strEnclose = '"';
$strSkip    = 'N';

$oVIP->selfurl = 'qte_adm_users_imp.php';
$oVIP->selfname = '<span class="upper">'.$L['Adm_content'].'</span><br />'.$L['Users'].'<br />'.$L['Users_import_csv'];
$oVIP->exiturl = 'qte_adm_users.php';
$oVIP->exitname = '&laquo;&nbsp;'.$L['User_man'];

// --------
// SUBMITTED
// --------

if ( isset($_POST['ok']) )
{
  // Check uploaded document

  $error = InvalidUpload($_FILES['title'],'csv,txt,text','',500);

  // Check form value

  if ( empty($error) )
  {
    $strDelimit = trim($_POST['delimit']);
    if ( isset($_POST['skip']) ) $strSkip='Y';
    if ( empty($strDelimit) ) $error=$L['Separator'].' '.Error(1);
    if ( strlen($strDelimit)!=1 ) $error=$L['Separator'].' '.Error(1);
    if ( preg_match('/[0-9A-Za-z]/',$strDelimit) ) $error=$L['Separator'].' '.Error(1);
    $strStatus = 'Z'; if ( isset($_POST['status']) ) $strStatus = substr($_POST['status'],0,1);
  }

  // Read file

  if ( empty($error) )
  {
    if ( $handle = fopen($_FILES['title']['tmp_name'],'r') )
    {
      $i = 0;
      $intCountUser = 0;
      $intNextUser = $oDB->Nextid(TABUSER);
      while( ($row=fgetcsv($handle,500,$strDelimit))!==FALSE )
      {
        ++$i;
        if ( $strSkip=='Y' && $i==1 ) continue;
        if ( count($row)==1 ) continue;
        if ( count($row)==4 )
        {
          $strRole = 'U'; if ( $row[0]=='A' || $row[0]=='M' || $row[0]=='a' || $row[0]=='m') $strRole=strtoupper($row[0]);
          $strLog = trim($row[1]); if ( !empty($strLog) ) $strLog=utf8_decode($strLog);
          $strPwd = trim($row[2]);
          if ( substr($strPwd,0,3)=='SHA' || substr($strPwd,0,3)=='sha' ) $strPwd = sha1($strPwd);
          if ( empty($strPwd) ) $strPwd=sha1($strLog);
          $strMail = $row[3];
          // insert
          if ( !empty($strLog) )
          {
            if ( $oDB->Exec('INSERT INTO '.TABUSER.' (id,role,username,lastname,pwd,status,emails,children,firstdate) VALUES ('.$intNextUser.',"'.$strRole.'","'.$strLog.'","'.$strLog.'","'.$strPwd.'","'.$strStatus.'","'.$strMail.'","0","'.date('Ymd').'")') )
            {
              $oDB->Exec('INSERT INTO '.TABS2U.' (sid,userid,issuedate) VALUES ('.$_POST['section'].','.$intNextUser.',"'.date('Ymd').'")');
              ++$intNextUser;
              ++$intCountUser;
            }
            else
            {
              echo ' - Cannot insert a new user with username ',$strLog,'<br />';
            }
          }
        }
        else
        {
          $error='Number of parameters ('.count($row).') not matching in line '.$i;
        }
      }
    }
    fclose($handle);
    // Unregister global sys (will be recomputed on next page)
    if ( $intCountUser>0 ) cSection::UpdateStats((int)$_POST['section']);
    Unset($_SESSION[QT]['sys_states']);
  }

  // End message

  if ( empty($error) )
  {
    unlink($_FILES['title']['tmp_name']);
    $oVIP->selfname = $L['Users_import_csv'];
    if ( $intCountUser==0 )
    {
    $oHtml->PageMsgAdm(NULL, 'No user inserted... Check the file and check that you don\'t have duplicate usernames.');
    }
    else
    {
    $oHtml->PageMsgAdm(NULL, $intCountUser.' '.$L['Users'].'<br />'.$L['S_insert'],0,'400px');
    }
  }
}

// --------
// HTML START
// --------

$oHtml->scripts[] ='<script type="text/javascript">
function ValidateForm(theForm)
{
  if (theForm.title.value.length==0) { alert(qtHtmldecode("'.$L['E_mandatory'].': File")); return false; }
  if (theForm.delimit.value.length==0) { alert(qtHtmldecode("'.$L['E_mandatory'].': '.$L['Separator'].'")); return false; }
  return null;
}
</script>
';

$oHtml->scripts_end[] = '<script type="text/javascript">
// drag info
var doc = document.getElementById("draganddrop");
if (doc)
{
if (navigator.userAgent.toLowerCase().indexOf("firefox") != -1) doc.style.display="inline";
if (navigator.userAgent.toLowerCase().indexOf("opera") != -1) doc.style.display="inline";
if (navigator.userAgent.toLowerCase().indexOf("chrome") != -1) doc.style.display="inline";
}
</script>
';

include APP.'_adm_inc_hd.php';

echo '<form method="post" action="',$oVIP->selfurl,'" enctype="multipart/form-data" onsubmit="return ValidateForm(this);">
<input type="hidden" name="maxsize" value="5242880"/>
';
echo '<h2 class="subtitle">',L('File'),'</h2>
<table class="t-data">
<tr>
<td class="headfirst" style="width:200px"><label for="title">CSV file</label></td>
<td><input type="file" id="title" name="title" size="32" value="',$strTitle,'"/> <span class="small" id="draganddrop" style="display:none">(',L('or_drop_file'),')</span></td>
</tr>
</table>
';
echo '<h2 class="subtitle">',L('Settings'),'</h2>
<table class="t-data">
<tr>
<td class="headfirst"><label for="delimit">',L('Separator'),'</label></td>
<td><input type="text" id="delimit" name="delimit" size="1" maxlength="5" value="',$strDelimit,'"/></td>
</tr>
<tr>
<td class="headfirst">',L('First_line'),'</td>
<td><input type="checkbox" id="skip" name="skip"',($strSkip=='Y' ? QCHE : ''),'/><label for="skip">',$L['Skip_first_line'],'</label></td>
</tr>
<tr>
<td class="headfirst">',L('Status'),'</td>
<td> <select name="status" size="1">',QTasTag(memGet('sys_statuses'),'Z'),'</select></td>
</tr>
<tr>
<td class="headfirst">',L('Destination'),'</td>
<td> <select name="section" size="1">',Sectionlist(0),'</select></td>
</tr>
</table>
<p style="margin:0 0 5px 0;text-align:center"><input type="submit" name="submit" value="',L('Ok'),'" /></p>
</form>
';

// HTML END

include APP.'_adm_inc_ft.php';