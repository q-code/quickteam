<?php // QuickTeam 3.0 build:20140608

/*

This function allows you making a login from an external page (for example, your website).
The page 'test_login.php' is an example that implements this function.

PRINCIPLE
---------
This function checks if the username and password is valid
(i.e. username and password correspond to a registered user in QuickTeam).
The function returns TRUE when logging is successfull, and FALSE otherwise.

MAIN OPTION
-----------
When a username/password is valid, you can optionnaly also check that
this user is a member of one specific team (in QuickTeam)
If a user is registered in QuickTeam but is not (or not yet) member of this team,
this login function will return FALSE.

Note:
This option, allows you to manage access to parts of website (or to applications)
by simply creating Teams in the QuickTeam application.

PARAMETRES
----------
@ $strAction is the requested action. Possible values are:
  'logged' to test if the user is already logged in.
  'login'  to connect the user
  'logout' to disconnect the user
@ $intSection is the optional Team id (see main option). To skip the option, use -1.
@ $strUsr is username.
@ $strPwd is password (not yet encrypted).
@ $qte_root is the installation directory of the QuickTeam application:
  in most case, the QuickTeam application is installed in a subdirectory of your website
  (i.e. quickteam/)

*/


function qte_web_login($strAction='',$intSection=-1,$strUsr='',$strPwd='',$qte_root='')
{
  switch($strAction)
  {

  case 'logged':

    if ( !isset($_SESSION['qte_usr_auth']) ) return false;
    $bReturn = false;
    if ( $_SESSION['qte_usr_auth']=='yes' ) $bReturn = true;
    if ( $bReturn && $intSection>=0 )
    {
      // query
      require $qte_root.'bin/config.php';
      require $qte_root.'bin/class/qt_class_db.php';
      $oDB = new cDB($qte_dbsystem,$qte_host,$qte_database,$qte_user,$qte_pwd);
      if ( !empty($oDB->error) ) die ('<p style="color:red">Connection with database failed.<br />Check that server is up and running.<br />Check that the settings in the file <b>bin/config.php</b> are correct for your database.</p>');
      $oDB->Query('SELECT count(userid) as countid  FROM '.$qte_prefix.'qtes2u WHERE userid='.$_SESSION['qte_usr_id'].' AND sid='.$intSection);
      $row=$oDB->Getrow();
      if ( $row['countid']==0 ) $bReturn=false;
    }
    Return $bReturn;
    break;

  case 'logout':

    if ( isset($_SESSION['qte_usr_auth']) ) unset($_SESSION['qte_usr_auth']);
    for($i=1;$i<=9;++$i) if ( isset($_SESSION['qte'.$i.'_usr_auth']) ) unset($_SESSION['qte'.$i.'_usr_auth']);
    break;

  case 'login':

    $bReturn = false;

    // query
    require $qte_root.'bin/config.php';
    require $qte_root.'bin/class/qt_class_db.php';
    $oDB = new cDB($qte_dbsystem,$qte_host,$qte_database,$qte_user,$qte_pwd);
    if ( !empty($oDB->error) ) die ('<p style="color:red">Connection with database failed.<br />Check that server is up and running.<br />Check that the settings in the file <b>bin/config.php</b> are correct for your database.</p>');

    // check user exists
    $oDB->Query('SELECT count(*) as countid FROM '.$qte_prefix.'qteuser WHERE username="'.$strUsr.'" AND pwd="'.sha1($strPwd).'"');
    $row=$oDB->Getrow();
    if ( $row['countid']==1 ) $bReturn=true;

    // read user
    if ( $bReturn )
    {
    $oDB->Query('SELECT id,username,role,children FROM '.$qte_prefix.'qteuser WHERE username="'.$strUsr.'"');
    $arrUser = $oDB->Getrow();
    }

    // check user is in group
    if ( $bReturn && $intSection>=0 )
    {
      $oDB->Query('SELECT count(userid) as countid  FROM '.$qte_prefix.'qtes2u WHERE userid='.$arrUser['id'].' AND sid='.$intSection);
      $row=$oDB->Getrow();
      if ( $row['countid']==0 ) $bReturn=false;
    }

    // register user
    if ( $bReturn )
    {
      $_SESSION['qte_usr_auth'] = 'yes';
      $_SESSION['qte_usr_id']   = $arrUser['id'];
      $_SESSION['qte_usr_name'] = $arrUser['username'];
      $_SESSION['qte_usr_role'] = $arrUser['role'];
      $_SESSION['qte_usr_child'] = $arrUser['children'];
    }

    Return $bReturn;
    break;

  default:

    die('Undefined action...');
    break;

  }

}