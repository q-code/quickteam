<?php

// This page is a sample that implements the login capabilities of QuickTeam.
// This page uses the function qte_web_login() located in the QuickTeam file bin/qte_fn_web_login.php
// to log in (or log out) the user.
// qte_web_login() function is described at the end of this page...

session_start();

// ----------
// MANDATORY PARAMETERS
// ----------
// $qte_root is the directory of the QuickTeam application (with final /)
//   example: $qte_root = 'quickteam/';
// $exit_url is the page to be displayed after this process
//   example: $exit_url = 'index.html';

$qte_root = 'quickteam/';
$exit_url = 'index.html';

// ----------
// MAIN FUNCTION IS INCLUDED 
// ----------

include $qte_root.'bin/qte_fn_web_login.php'; 

// ----------
// WEB PAGE BEGINS HERE
// ----------

echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" xml:lang="en" lang="en">
<head>
<title>Test login</title>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1"/>
</head>

<body>
';

// ----------
// Following part is executed when a logout is requested
// ----------
// To request a logout, you can make a link to this page and add 'logout' as a url parameter
// example: <a href="test_login.php?logout">Log out</a>

if ( isset($_GET['logout']) )
{
  qte_web_login('logout');
  echo '<p>You are disconnected..</p>';    
}

// ----------
// Default process
// ----------
// The login form is displayed when user is not yet logged.
// When the user submits his username and password,
// the same process is executed, but the form is not displayed

if ( !qte_web_login('logged') )
{
  //
  // If the user did not submit his username and password, the form is displayed
  //
  if ( !isset($_POST['usr']) || !isset($_POST['pwd']) )
  {
  
    echo '
    <form method="post" action="test_login.php">
    <p>Username <input type="text" name="usr" size="20" maxlenght="24"/></p>
    <p>Password <input type="password" name="pwd" size="20" maxlenght="24"/> <input type="submit" name="submit" value="Log in"></p>
    </form>
    ';

  }
  else
  {
  
    $strUsr = trim($_POST['usr']); if ( get_magic_quotes_gpc() ) $strUsr = stripslashes($strUsr);
    $strPwd = trim($_POST['pwd']);
    $strUsr = strip_tags($strUsr);
    $strPwd = strip_tags($strPwd);
    
    //
    // Here is the login request, using the main function.
    // Put the team id as second argument to also check that this user is member of this specific team.
    //

    $isvalid = qte_web_login('login',-1,$strUsr,$strPwd,$qte_root);
    
    if ( $isvalid )
    {
      echo '<p>Access granted...</p>';
      // Uncomment the folling line to make a automatic redirection to your exit page
      // echo '<meta http-equiv="REFRESH" content="0;url=',$exit_url,'">';
    }
    else
    {
      echo '<p>Access denied... <a href="',$qte_root,'test_login.php">Try again</a></p>';
    }
    
  }
}
else
{
  echo '<p>You are already logged in... <a href="',$qte_root,'test_login.php?logout">log out</a></p>';
}

// ----------
// EXIT PAGE
// ----------

echo '
<p><a href="',$exit_url,'">Back to the homepage</a></p>
</body>
</html>';

// ----------
// WEB PAGE ENDS HERE
// ----------

/* 

HERE IS THE DOCUMENTATION OF THE FUNCTION qte_web_login()
THIS FUNCTION IS PART OF THE QUICKTEAM APPLICATION
AND CAN BE INCLUDED FROM THE FILE bin/qte_fn_web_login.php

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
If a user is registered in QuickTeam but is not (or not yet) member of this specific team,
this login function will return FALSE.

Note:
This option allows you to manage access to parts of website (or to applications)
by simply creating Teams in the QuickTeam application.

PARAMETRES
----------
@ $strAction is the requested action. Possible values are:
  'logged' to test if the user is already logged in.
  'login'  to connect the user
  'logout' to disconnect the user
@ $intSection is the optional Team id (see main option). To skip the option, use -1.
@ $strUsr is the username. 
@ $strPwd is the password (not yet encrypted).
@ $qte_root is the installation directory of the QuickTeam application:
  in most case, the QuickTeam application is installed in a subdirectory of your website
  (i.e. quickteam/)

*/