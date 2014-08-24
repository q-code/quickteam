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
 * @copyright  2013 The PHP Group
 * @version    3.0 build:20140608
 */

session_start();
require 'bin/qte_init.php';
if ( sUser::Role()!='A' ) die($L['E_admin']);
$id = -1;
QThttpvar('id','int'); if ( $id<0 ) die('Missing parameter id...');

// --------
// INITIALISE
// --------

include Translate(APP.'_reg.php');

if ( !isset($_SESSION['temp_key']) ) $_SESSION['temp_key']='';
if ( !isset($_SESSION['temp_ext']) ) $_SESSION['temp_ext']='';

$oVIP->selfurl = 'qte_adm_section_img.php';
$oVIP->selfname = $L['Change_picture'];
$oVIP->exiturl = 'qte_adm_section.php?tt=1&s='.$id;

$upload_path = QTE_DIR_DOC.'section/'; // The path to where the image will be saved
$large_image_location = $upload_path.'src'.$id.'_'.$_SESSION['temp_key'].$_SESSION['temp_ext'];
$thumb_image_location = $upload_path.$id.$_SESSION['temp_ext'];

if ( isset($_POST['exit']) )
{
  // Exit request code is placed before as following object initialisation is not required in case of exiting
  if ( file_exists($large_image_location) ) unlink($large_image_location);
  unset($_SESSION['temp_key']);
  $oHtml->Redirect($oVIP->exiturl);
}

$oSEC = new cSection($id);

if ( empty($oSEC->d_logo) )
{
if ( file_exists('document/section/'.$id.'.gif') ) $oSEC->d_logo = $id.'.gif'; // section-logo directory is 'document/section' (even if DIR_DOC is changed)
if ( file_exists('document/section/'.$id.'.jpg') ) $oSEC->d_logo = $id.'.jpg';
if ( file_exists('document/section/'.$id.'.png') ) $oSEC->d_logo = $id.'.png';
if ( file_exists('document/section/'.$id.'.jpeg') ) $oSEC->d_logo = $id.'.jpeg';
}

$max_file = 2;       // Maximum file size in MB
$max_width = 650;    // Display width for the large image (image can be larger)
$thumb_max_width  = (defined('QTE_SECTIONLOGO_WIDTH') ? QTE_SECTIONLOGO_WIDTH+25 : 100); // Above this value, the crop tool will start
$thumb_max_height = (defined('QTE_SECTIONLOGO_HEIGHT') ? QTE_SECTIONLOGO_HEIGHT+25 : 100); // Above this value, the crop tool will start
$thumb_width      = (defined('QTE_SECTIONLOGO_WIDTH') ? QTE_SECTIONLOGO_WIDTH : 75);  // Width of thumbnail image (75px)
$thumb_height     = (defined('QTE_SECTIONLOGO_HEIGHT') ? QTE_SECTIONLOGO_HEIGHT : 75); // Height of thumbnail image (75px)
$strMimetypes = 'image/pjpeg,image/jpeg,image/jpg,image/gif,image/png,image/x-png';
$photo = QTE_DIR_DOC.'section/'.$oSEC->d_logo; // Current photo (Can be empty)
if ( !file_exists($photo) ) $photo='';
$photolabel = '';

//Check to see if any images with the same name already exist
$large_photo_exists = ''; if ( file_exists($large_image_location) ) $large_photo_exists = '<img src="'.$large_image_location.'" alt="Large image"/>';

function saveThumbnail($id,$str)
{
  global $oSEC;
  $oSEC->ChangeOption('logo',$id.$_SESSION['temp_ext']);
}

// --------
// SUBMITTED FOR DELETE
// --------

if ( isset($_POST['del']) )
{
  if ( !empty($oSEC->d_logo) && file_exists(QTE_DIR_DOC.'section/'.$oSEC->d_logo) ) unlink(QTE_DIR_DOC.'section/'.$oSEC->d_logo);
  if ( file_exists($large_image_location) ) unlink($large_image_location);
  $oSEC->ChangeOption('logo','0');
  unset($_SESSION['temp_key']);
  $oVIP->selfname = $L['Picture'];
  $_SESSION['pagedialog'] = 'O|'.$L['S_delete'];
  $oHtml->Redirect($oVIP->exiturl);
}

// --------
// PAGE
// --------

$oHtml->links = array();
$oHtml->links[] = '<link rel="shortcut icon" href="admin/qte_icon.ico" />';
$oHtml->links[] = '<link rel="stylesheet" type="text/css" href="admin/qt_base.css" /><link rel="stylesheet" type="text/css" href="admin/qte_main.css" />';

include 'qte_upload_img.php';