<?php

// QuickTeam 3.0 build:20140608

session_start();

if ( isset($_GET['language']) ) $_SESSION['qte_setup_lang']=$_GET['language'];
if ( !isset($_SESSION['qte_setup_lang']) ) $_SESSION['qte_setup_lang']='en';
if ( !file_exists('qte_lang_'.$_SESSION['qte_setup_lang'].'.php') ) $_SESSION['qte_setup_lang']='en';

include 'qte_lang_'.$_SESSION['qte_setup_lang'].'.php';
include '../bin/config.php';

$strAppl = 'QuickTeam';
$strPrevUrl = 'qte_setup.php';
$strNextUrl = 'qte_setup_2.php';
$strPrevLabel= $L['Back'];
$strNextLabel= $L['Next'];
$strError = '';

// --------
// HTML START
// --------

include 'qte_setup_hd.php';

echo '
<table>
<tr>
<td width="475" style="padding:0px;vertical-align:top">';

// --------
// SUBMITTED
// --------

if ( isset($_POST['ok']) )
{
  include '../bin/class/qt_class_db.php';

  $qte_dbsystem = strip_tags(trim($_POST['qte_dbsystem']));
  $qte_host     = strip_tags(trim($_POST['qte_host']));
  $qte_database = strip_tags(trim($_POST['qte_database']));
  $qte_prefix   = strip_tags(trim($_POST['qte_prefix']));
  $qte_user     = strip_tags(trim($_POST['qte_user']));
  $qte_pwd      = strip_tags(trim($_POST['qte_pwd']));
  $str = strip_tags(trim($_POST['qte_dbo_login']));
  if ( $str!='') $_SESSION['qte_dbologin'] = $str;
  $str = strip_tags(trim($_POST['qte_dbo_pswrd']));
  if ( $str!='') $_SESSION['qte_dbopwd'] = $str;

  // Test Connection

  if ( isset($_SESSION['qte_dbologin']) )
  {
    $oDB = new cDB($qte_dbsystem,$qte_host,$qte_database,$_SESSION['qte_dbologin'],$_SESSION['qte_dbopwd']);
  }
  else
  {
    $oDB = new cDB($qte_dbsystem,$qte_host,$qte_database,$qte_user,$qte_pwd);
  }

  if ( empty($oDB->error) )
  {
    echo '<div class="setup_ok">',$L['S_connect'],'</div>';
  }
  else
  {
    echo '<div class="setup_err">',sprintf ($L['E_connect'],$qte_database,$qte_host),'</div>';
  }

  // Save Connection

  $strFilename = '../bin/config.php';
  $content = '<?php
  $qte_dbsystem = "'.$qte_dbsystem.'";
  $qte_host = "'.$qte_host.'";
  $qte_database = "'.$qte_database.'";
  $qte_prefix = "'.$qte_prefix.'";
  $qte_user = "'.$qte_user.'";
  $qte_pwd = "'.$qte_pwd.'";
  $qte_install = "'.date('Y-m-d').'";';

  if (!is_writable($strFilename)) $strError="Impossible to write into the file [$strFilename].";
  if ( empty($strError) )
  {
  if (!$handle = fopen($strFilename, 'w')) $strError="Impossible to open the file [$strFilename].";
  }
  if ( empty($strError) )
  {
  if ( fwrite($handle, $content)===FALSE ) $strError="Impossible to write into the file [$strFilename].";
  fclose($handle);
  }

  // End message
  if ( empty($strError) )
  {
    echo '<div class="setup_ok">',$L['S_save'],'</div>';
  }
  else
  {
    echo '<div class="setup_err">',$strError,$L['E_save'],'</div>';
  }
}

// --------
// FORM
// --------

echo '<form method="post" name="install" action="qte_setup_1.php">
<h2>',$L['Connection_db'],'</h2>
<table class="t-conn">
<tr>
<td>',$L['Database_type'],'</td>
<td><select name="qte_dbsystem">
<optgroup label="PDO">
<option value="pdo.mysql"',($qte_dbsystem=='pdo.mysql' ? ' selected="selected"' : ''),'>MySQL 5 or next</option>
<option value="pdo.sqlsrv"',($qte_dbsystem=='pdo.sqlsrv' ? ' selected="selected"' : ''),'>SQL sever (or Express)</option>
<option value="pdo.pg"',($qte_dbsystem=='pdo.pg' ? ' selected="selected"' : ''),'>PostgreSQL</option>
</optgroup>
<optgroup label="Legacy">
<option value="mysql"',($qte_dbsystem=='mysql' ? ' selected="selected"' : ''),'>MySQL 5 or next</option>
<option value="sqlsrv"',($qte_dbsystem=='sqlsrv' ? ' selected="selected"' : ''),'>SQL server (or Express)</option>
<option value="pg"'.($qte_dbsystem=='pg' ? 'selected="selected"' : ''),'>PostgreSQL</option>
<option value="ibase"'.($qte_dbsystem=='ibase' ? 'selected="selected"' : ''),'>FireBird</option>
<option value="sqlite"'.($qte_dbsystem=='sqlite' ? 'selected="selected"' : ''),'>SQLite</option>
<option value="db2"',($qte_dbsystem=='db2' ? ' selected="selected"' : ''),'>IBM DB2</option>
<option value="oci"',($qte_dbsystem=='oci' ? ' selected="selected"' : ''),'>Oracle</option>
</optgroup>
</select></td>
</tr>
';
echo '<tr>
<td>',$L['Database_host'],'</td>
<td>
<input type="text" name="qte_host" value="',$qte_host,'" size="30" maxlength="250"/>
</td>
</tr>
<tr>
<td>',$L['Database_name'],'</td>
<td><input type="text" name="qte_database" value="',$qte_database,'" size="15" maxlength="100"/></td>
</tr>
<tr>
<td>',$L['Table_prefix'],'</td>
<td><input type="text" name="qte_prefix" value="',$qte_prefix,'" size="15" maxlength="100"/></td>
</tr>
<tr>
<td>',$L['Database_user'],'</td>
<td>
<input type="text" name="qte_user" value="',$qte_user,'" size="15" maxlength="100"/>
<input type="password" name="qte_pwd" value="',$qte_pwd,'" size="15" maxlength="100"/>
</td>
</tr>
<tr>
<td colspan="2" style="border-top:solid 1px #cccccc"><span class="small">',$L['Htablecreator'],'</span></td>
</tr>
<tr>
<td style="border-bottom:solid 1px #cccccc">Table creator (user/password)</td>
<td style="border-bottom:solid 1px #cccccc">
<input type="text" name="qte_dbo_login" value="',(isset($_SESSION['qte_dbologin']) ? $_SESSION['qte_dbologin'] : ''),'" size="15" maxlength="100"/>
<input type="password" name="qte_dbo_pswrd" value="',(isset($_SESSION['qte_dbopwd']) ? $_SESSION['qte_dbopwd'] : ''),'" size="15" maxlength="100"/>
</td>
</tr>
<tr>
<td colspan="2" style="padding:10px;text-align:center"><input class="submit" type="submit" name="ok" value="',$L['Save'],'" onclick="this.style.visibility=\'hidden\';"/></td>
</tr>
</table>
</form>
<span class="small">',$L['Upgrade'],'</span>';

echo '
</td>
<td class="hidden" style="vertical-align:top"><div class="setup_help">',$L['Help_1'],'</div></td>
</tr>
</table>
';

// --------
// HTML END
// --------

include 'qte_setup_ft.php';