<?php
$strPageMenu='';

switch($oVIP->selfurl)
{

// ----------
case 'qte_section.php':
// ----------

if ( sUser::IsStaff() )
{

// Moderator Actions

$strPageMenu .= '
<div class="options" id="options">
<form method="post" action="'.$oVIP->selfurl.'?'.GetUri().'" id="Maction">
<p>'.L('Userrole_'.sUser::Role()).' <select name="Maction" class="small" onchange="document.getElementById(\'action_ok\').click();">
<option value="">&nbsp;</option>
<optgroup label="Action">
';
if ( $oSEC->status==1 && sUser::Role()=='M' )
{
$strPageMenu .= '<option value="add" disabled="disabled">'.$L['User_man'].'...</option>'.PHP_EOL;
}
else
{
$strPageMenu .= '<option value="add">'.$L['User_man'].'...</option>'.PHP_EOL;
}
if ( sUser::Role()=='A' ) $strPageMenu .= '<option value="new">'.$L['User_add'].'...</option>';
$strPageMenu .= '
<option value="email">'.$L['Emails'].'...</option>
</optgroup>
<optgroup label="Option">
<option value="show_Z"'.($_SESSION[QT]['show_Z'] ? ' class="bold"' : '').'>'.$L['Show'].': '.$oVIP->statuses['Z']['statusname'].'</option>
<option value="hide_Z"'.(!$_SESSION[QT]['show_Z'] ? ' class="bold"' : '').'>'.$L['Hide'].': '.$oVIP->statuses['Z']['statusname'].'</option>
</optgroup>
</select>';

// show infocolumn if not in a query and table is not empty
if ( empty($q) && !empty($intCount) )
{
  $strPageMenu .= ' '.$L['Last_column'].'&nbsp;<select id="infofield" name="infofield" class="small" onchange="document.getElementById(\'action_ok\').click();">'.PHP_EOL;
  // get the usable field list
  $arrDisabled = GetFLDs($oSEC->forder,false,true); // this gets the already-ACTIVE fields (as fieldkey) out of the section fields (to be greyed out)
  // get current infofield (if exist)
  $strInfofield=''; if ( isset($_SESSION[QT]['infofield']) ) $strInfofield=$_SESSION[QT]['infofield'];
  // get list of fields
  $arr = array('0'=>'('.$L['None'].')');
  $arr = $arr + GetFLDnames(GetFLDs('status_i;fullname'));
  $strPageMenu .= QTasOption($arr,$strInfofield,array(),$arrDisabled);
  $strPageMenu .= '<optgroup label="'.L('Fields_personal').'">'.PHP_EOL;
  $arr = GetFLDnames(GetFLDs('title;firstname;midname;lastname;alias;picture;address;phones;emails;emails_i;www;birthdate;age;nationality;sexe'));
  $strPageMenu .= QTasOption($arr,$strInfofield,array(),$arrDisabled);
  $strPageMenu .= '</optgroup>'.PHP_EOL;
  $strPageMenu .= '<optgroup label="'.L('Fields_team').'">'.PHP_EOL;
  $arr = GetFLDnames(GetFLDs('teamid1;teamid2;teamrole1;teamrole2;teamdate1;teamdate2;teamvalue1;teamvalue2;teamflag1;teamflag2;descr'));
  $strPageMenu .= QTasOption($arr,$strInfofield,array(),$arrDisabled);
  $strPageMenu .= '</optgroup>'.PHP_EOL;
  $strPageMenu .= '<optgroup label="'.L('Fields_system').'">'.PHP_EOL;
  $arr = GetFLDnames(GetFLDs('id;username;role;status;children;firstdate'));
  $strPageMenu .= QTasOption($arr,$strInfofield,array(),$arrDisabled);
  $strPageMenu .= '</optgroup>'.PHP_EOL;
  $strPageMenu .= '</select>'.PHP_EOL;
}

$strPageMenu .=  '<input type="submit" name="Mok" value="'.$L['Ok'].'" class="small" id="action_ok"/>
</p>
</form>
</div>
<div class="showoptions" onclick="showoptions();" title="'.L('My_preferences').'"></div>
<script type="text/javascript">
var doc = document;
doc.getElementById("options").style.display="none";
doc.getElementById("action_ok").style.display="none";
function showoptions()
{
  var doc = document.getElementById("options");
  if ( doc ) doc.style.display=(doc.style.display!="block" ? "block" : "none");
}
</script>

';

}

break;

// ----------
case 'qte_adm_users.php':
if ( sUser::IsStaff() ) {
// ----------

  // SUBMITTED for add

  if ( isset($_POST['add']) )
  {
    // check
    if ( empty($error) )
    {
      $str = $_POST['title']; if ( get_magic_quotes_gpc() ) $str = stripslashes($str);
      $str = QTconv($str,'U');
      if ( !QTislogin($str) ) $error = $L['Username'].' '.$L['E_invalid'];
      $strTitle = $str;
      if ( sUser::IsUser($strTitle) ) $error = $L['Username'].' '.$L['E_already_used'];
    }
    if ( empty($error) )
    {
      $str = $_POST['pass']; if ( get_magic_quotes_gpc() ) $str = stripslashes($str);
      $str = QTconv($str,'U');
      if ( !QTispassword($str) ) $error = $L['Password'].' '.$L['E_invalid'];
      $strNewpwd = $str;
    }
    if ( empty($error) )
    {
      $str = $_POST['mail']; if ( get_magic_quotes_gpc() ) $str = stripslashes($str);
      if ( !QTismail($str) ) $error = $L['Email'].' '.$L['E_invalid'];
      $strMail = $str;
    }

    // add the user
    if ( empty($error) )
    {
      $newid = sUser::AddUser(htmlspecialchars($strTitle,ENT_QUOTES),$strNewpwd,$strMail,'U','0'); // return false in case of error
      if ( $newid )
      {
      if ( isset($_GET['s']) )  $intSec = (int)strip_tags($_GET['s']);
      if ( isset($_POST['s']) ) $intSec = (int)strip_tags($_POST['s']);
      if ( !isset($intSec) || !is_int($intSec) ) $intSec=0;
      cItem::InSection($intSec,'add',$newid);
      }
      else
      {
      $error=true;
      }
    }
    if ( empty($error) )
    {
      // Unregister global sys (will be recomputed on next page)
      Unset($_SESSION[QT]['sys_members']);
      Unset($_SESSION[QT]['sys_newuserid']);

      // send email
      if ( isset($_POST['notify']) )
      {
        include 'bin/class/qt_class_smtp.php';
        $strSubject='Welcome';
        $strFile = GetLang().'mail_registred.php';
        if ( file_exists($strFile) ) include $strFile;
        if ( empty($strMessage) ) $strMessage='Please find here after your login and password to access the board '.$_SESSION[QT]['site_name'].PHP_EOL.'Login: %s\nPassword: %s';
        $strMessage = sprintf($strMessage,$strTitle,$strNewpwd);
        $strMessage = wordwrap($strMessage,70,"\r\n");
        QTmail($strMail,$strSubject,$strMessage);
      }

      // exit
      unset($_POST['pass']);
      if ( isset($newid) && $newid>0 ) $_POST['cid'] = $newid; // request to check the last created user
      $_SESSION['pagedialog'] = 'O|'.$L['Register_completed'];
    }
    else
    {
      $_SESSION['pagedialog'] = 'E|Unable to create the user';
    }
  }

  $strPageMenu .= '<a onclick="ToggleForm(!IsFormVisible()); return false;" href="#">'.L('User_add').'...</a>';
  $oHtml->scripts[] = '<script type="text/javascript">
  function ValidateForm(theForm)
  {
    if (theForm.title.value.length==0) { alert(qtHtmldecode("'.$L['Missing'].': '.$L['Username'].'")); return false; }
    if (theForm.pass.value.length==0) { alert(qtHtmldecode("'.$L['Missing'].': '.$L['Password'].'")); return false; }
    if (theForm.mail.value.length==0) { alert(qtHtmldecode("'.$L['Missing'].': '.$L['Email'].'")); return false; }
    return null;
  }

  function IsFormVisible()
  {
    if (document.getElementById("adduser"))
    {
    if ( document.getElementById("adduser").style.display=="none" ) return false;
    }
    return true;
  }
  function ToggleForm(i)
  {
    if ( i )
    {
    document.getElementById("adduser").style.display="block";
    }
    else
    {
    document.getElementById("adduser").style.display="none";
    }
  }
  </script>';
  $oHtml->scripts_jq[] = '
  $(function() {
    $("#title").blur(function() {
      $.post("qte_j_exists.php",
        {f:"username",v:$("#title").val(),e1:"'.sprintf(L('E_char_min'),4).'",e2:"'.L('E_already_used').'"},
        function(data) { if ( data.length>0 ) document.getElementById("formerror").innerHTML=data; }
        );
      });
  });
  ';

  if ( !isset($_POST['title']) && isset($_GET['title']) ) $_POST['title']=urldecode($_GET['title']);
  if ( !isset($_POST['mail']) && isset($_GET['mail']) ) $_POST['mail']=$_GET['mail'];
  $strUserform  = '
  <form id="adduser" style="margin:5px 0 15px 0" method="post" action="'.$oVIP->selfurl.'" onsubmit="return ValidateForm(this);">
  <table class="t-data">
  <tr class="t-data"><td class="headfirst">'.$L['Role'].'</td><td><select name="role" size="1"><option value="U"'.QSEL.'>'.$L['Userrole_U'].'</option><option value="M">'.$L['Userrole_M'].'</option>'.(sUser::Role()=='A' ? '<option value="A">'.$L['Userrole_A'].'</option>' : '').'</select></td></tr>
  <tr class="t-data"><td class="headfirst">'.$L['Username'].'</td><td><input id="title" name="title" type="text" size="15" maxlength="24" value="'.(isset($_POST['title']) ? $_POST['title'] : '').'" onfocus="document.getElementById(\'formerror\').innerHTML=\'\';" /></td></tr>
  <tr class="t-data"><td class="headfirst">'.$L['Password'].'</td><td><input id="pass" name="pass" type="text" size="15" maxlength="32"  value="'.(isset($_POST['pass']) ? $_POST['pass'] : '').'" /></td></tr>
  <tr class="t-data"><td class="headfirst">'.$L['Email'].'</td><td><input id="mail" name="mail" type="email" size="30" maxlength="255"  value="'.(isset($_POST['mail']) ? $_POST['mail'] : '').'" /></td></tr>
  <tr class="t-data"><td class="headfirst" colspan="2"><span id="formerror" class="error">'.(empty($error) ? '' : $error).'</span> <input id="notify" name="notify" type="checkbox" /> <label for="notify">'.$L['Send'].' '.L('email').'</label>&nbsp; <input type="submit" id="add" name="add" value="'.$L['Add'].'" /></td></tr>
  </table>
  </form>
  <script type="text/javascript">ToggleForm('.( isset($_POST['add']) || isset($_GET['add']) ? 'true' : 'false' ).');</script>';

  }
  break;

}