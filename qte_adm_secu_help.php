<?php

// QuickTeam version 3.0 build:20140608

session_start();
require_once 'bin/qte_init.php';
if ( sUser::Role()!='A' ) die($L['E_admin']);

$oHtml->links = array('<link rel="shortcut icon" href="admin/qte_icon.ico" />',
		'<link rel="stylesheet" type="text/css" href="admin/qt_base.css" />',
		'<link rel="stylesheet" type="text/css" href="admin/qte_main.css" />');
$oHtml->scripts = array();
echo $oHtml->Head();
echo $oHtml->Body();

$oHtml->Msgbox($L['Help'],array('style'=>'width:650px'));
include Translate('@_secu_help.php');
$oHtml->Msgbox(END);

echo $oHtml->End();