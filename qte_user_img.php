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
require 'bin/qte_init.php';
if ( $_SESSION[QT]['picture']=='0' ) die(Error(10));
if ( !sUser::CanView('U') ) die(Error(11));
$id = -1; QThttpvar('id','int'); if ( $id<0 ) die('Missing parameter id...');
if ( sUser::Role()!='A' ) { if (sUser::Id()!=$id) die($L['R_user']); }

// --------
// INITIALISE
// --------

include Translate(APP.'_reg.php');

if ( !isset($_SESSION['temp_key']) ) $_SESSION['temp_key']= "";
if ( !isset($_SESSION['temp_ext']) ) $_SESSION['temp_ext']= "";

$oVIP->selfurl = 'qte_user_img.php';
$oVIP->selfname = $L['Change_picture'];
$oVIP->exiturl = Href('qte_user.php').'?id='.$id.'&amp;tt=s';
$oVIP->exitname = $L['Profile'];

$upload_subdir = TargetDir(APPCST('_DIR_PIC'),$id);
$upload_path = APPCST('_DIR_PIC').$upload_subdir; // The path to where the image will be saved
$large_image_location = $upload_path.'src'.$id.'_'.$_SESSION['temp_key'].$_SESSION['temp_ext'];
$thumb_image_location = $upload_path.$id.$_SESSION['temp_ext'];

// Save (and notify if coppa)
function saveThumbnail($id,$str)
{
  global $oDB; $oDB->Exec('UPDATE '.TABUSER.' SET picture="'.$str.'" WHERE id='.$id);

  if ( $_SESSION[QT]['register_coppa']=='1' )
  {
    global $oItem;
    if ( $oItem->coppa!=0 )
    {
    include 'bin/class/qt_class_smtp.php';
    $strSubject = $_SESSION[QT]['site_name'].' - New picture';
    $strFile = GetLang().'mail_img_coppa.php';
    if ( file_exists($strFile) ) include $strFile;
    if ( empty($strMessage) ) $strMessage = "We inform you that your children (%s) has changed his/her picture on the board {$_SESSION[QT]['site_name']}.";
    $strMessage = sprintf($strMessage,$_POST['name']);
    $strMessage = wordwrap($strMessage,70,"\r\n");
    QTmail($oItem->parentmail,$strSubject,strMessage);
    }
  }
}

// --------
// SUBMITTED for Exit
// --------

if ( isset($_POST['exit']) )
{
  if ( file_exists($large_image_location) ) unlink($large_image_location);
  unset($_SESSION['temp_key']);
  $oHtml->Redirect($oVIP->exiturl);
}

// --------
// INITIALISE image and repository object
// --------

$oItem = new cItem($id);

$photo = (empty($oItem->picture) ? '' : QTE_DIR_PIC.$oItem->picture); // Current photo (Can be empty)
if ( !empty($photo) && !file_exists($photo) ) $photo='';

$strUserPlaceholder = $_SESSION[QT]['skin_dir'].'/user.gif'; // when photo is empty shows the placeholder
$photolabel = $oItem->firstname.'<br />'.$oItem->lastname;

$max_file = 3;           // Maximum file size in MB
$max_width = 650;        // Max width allowed for the large image
$thumb_max_width = 150;  // Above this value, the crop tool will start
$thumb_max_height = 150; // Above this value, the crop tool will start
$thumb_width = 100;      // Width of thumbnail image
$thumb_height = 100;     // Height of thumbnail image
$strMimetypes = 'image/pjpeg,image/jpeg,image/jpg';
if ( strpos($_SESSION[QT]['picture'],'gif')!==FALSE) $strMimetypes.=',image/gif';
if ( strpos($_SESSION[QT]['picture'],'png')!==FALSE) $strMimetypes.=',image/png,image/x-png';

//Check to see if any images with the same name already exist
$large_photo_exists = ''; if ( file_exists($large_image_location) ) $large_photo_exists = "<img src=\"".$large_image_location."\" alt=\"Large Image\"/>";

// --------
// SUBMITTED for Delete
// --------

if ( isset($_POST['del']) )
{
  if ( file_exists($large_image_location) ) unlink($large_image_location);
  if ( file_exists(QTE_DIR_PIC.$oItem->picture) ) unlink(QTE_DIR_PIC.$oItem->picture);
  $oDB->Exec('UPDATE '.TABUSER.' SET picture="0" WHERE id='.$id);
  unset($_SESSION['temp_key']);
  $_SESSION['pagedialog'] = (empty($error) ? 'O|'.$L['S_delete'] : 'E|'.$error);
  $oHtml->Redirect($oVIP->exiturl);
}

// --------
// PAGE
// --------

include 'qte_upload_img.php';