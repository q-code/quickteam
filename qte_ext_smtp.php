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
* @package    QT
* @author     Philippe Vandenberghe <info@qt-cute.org>
* @copyright  2014 The PHP Group
* @version    3.0 build:20140608
*/

session_start();
require_once 'bin/qte_init.php';
if ( sUser::Role()!='A' ) die('Access is restricted to administrators only');

// INITIALISE

include 'bin/class/qt_class_smtp.php';

$oVIP->selfurl = 'qte_ext_smtp.php';
$oVIP->selfname = 'SMTP test';

if ( isset($_GET['h']) ) $_SESSION[QT]['smtp_host'] = strip_tags($_GET['h']);
if ( isset($_GET['p']) ) $_SESSION[QT]['smtp_port'] = strip_tags($_GET['p']);
if ( isset($_GET['u']) ) $_SESSION[QT]['smtp_username'] = strip_tags($_GET['u']);
if ( isset($_GET['w']) ) $_SESSION[QT]['smtp_password'] = strip_tags($_GET['w']);

// --------
// SUBMITTED
// --------

if ( isset($_POST['ok']) )
{
  // register value used
  $_SESSION[QT]['smtp_host'] = $_POST['smtphost'];
  $_SESSION[QT]['smtp_port'] = $_POST['smtpport'];
  $_SESSION[QT]['smtp_username'] = $_POST['smtpusr'];
  $_SESSION[QT]['smtp_password'] = $_POST['smtppwd'];
  if ( !QTismail($_POST['mailto']) ) die($L['Email'].' '.Error(1));

  // send mail
  QTmail($_POST['mailto'],$_POST['subject'],$_POST['message'],'From:'.$_SESSION[QT]['admin_email'],'1');

  // exit
  $oVIP->exiturl = 'qte_ext_smtp.php';
  $oVIP->exitname = 'SMTP test';
  $oHtml->PageMsgAdm(NULL,'Process completed...<br /><br />If you have changed the smtp settings during the test, go to the Administration page and SAVE your new settings!');
}

// --------
// HTML START
// --------

$oHtml->scripts = array();
include APP.'_adm_inc_hd.php';

// CONTENT

echo '<br />
<form method="post" action="',Href(),'">
<h2 class="subtitle">Application time zone</h2>
<table class="t-data">
<tr>
<td class="headfirst" style="width:200px;"><label for="smtphost">Host</label></td>
<td><input type="text" id="smtphost" name="smtphost" size="30" maxlength="64" value="',$_SESSION[QT]['smtp_host'],'" /> <span class="small">Use prefix to activate SSL or TLS connection e.g.</span> <span class="small" style="color:#4444ff">ssl://smtp.domain.com</span></td>
</tr>
<tr>
<td class="headfirst"><label for="smtphost">Port</label></td>
<td>
<input type="text" id="smtpport" name="smtpport" size="5" maxlength="6" value="',(isset($_SESSION[QT]['smtp_port']) ? $_SESSION[QT]['smtp_port'] : '25'),'" />
</td>
</tr>
<tr>
<td class="headfirst"><label for="smtpusr">Username</label></td>
<td><input type="text" id="smtpusr" name="smtpusr" size="30" maxlength="64" value="',$_SESSION[QT]['smtp_username'],'" /></td>
</tr>
<tr>
<td class="headfirst"><label for="smtppwd">Password</label></td>
<td><input type="text" id="smtppwd" name="smtppwd" size="30" maxlength="64" value="',$_SESSION[QT]['smtp_password'],'" /></td>
</tr>
</table>
';
echo '<h2 class="subtitle">Application time zone</h2>
<table class="t-data">
<tr>
<td class="headfirst"><label for="mailto">SEND TO</label></td>
<td><input type="text" id="mailto" name="mailto" size="30" maxlength="64" value="" /></td>
</tr>
<tr>
<td class="headfirst">From</td>
<td>',$_SESSION[QT]['admin_email'],'</td>
</tr>
<tr>
<td class="headfirst"><label for="subject">Subject</label></td>
<td><input type="text" id="subject" name="subject" size="30" maxlength="64" value="Test smtp" /></td>
</tr>
<tr>
<td class="headfirst"><label for="message">Message</label></td>
<td><input type="text" id="message" name="message" size="30" maxlength="64" value="Test mail send by smtp server" /></td>
</tr>
<tr>
</table>
';
echo '<p style="margin:0 0 5px 0;text-align:center"><input type="submit" name="ok" value="',L('Send'),'"/></p>
</form>
';

echo '<br />
<div class="scrollmessage">
<h2>Setting examples</h2>
<p class="bold">Example for gmail</p>
<p>
Host <span style="color:#4444ff">tls://smtp.gmail.com</span><br />
Port <span style="color:#4444ff">587</span><br />
Username <span style="color:#4444ff">yourusername@gmail.com</span><br />
Password <span style="color:#4444ff">your google account password</span><br />
<br />
<span class="small">Note: using ssl or tls requires that your webhost opens these transport sockets in the php configuration. When this is not possible or if the test failled, you can use standard mail function (in the administration page Site & contact, don\'t use external smtp server).</span>
</p>
';
echo '<p class="bold">Example for pop3 instead of smtp</p>
<p>
Host <span style="color:#4444ff">pop3.yourdomain.com</span><br />
Port <span style="color:#4444ff">110</span><br />
Username <span style="color:#4444ff">your username</span><br />
Password <span style="color:#4444ff">your password</span><br />
</p>
</div>
';

// HTML END

include APP.'_adm_inc_ft.php';