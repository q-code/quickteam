<?php

// QT 3.0 build:20130817

session_start();
require_once 'bin/qte_init.php';
if ( sUser::Role()!='A' ) die('Access denied');
$oVIP->selfurl=APP.'_adm_const.php';
$oVIP->selfname='PHP constants';

function ConstantToString($str)
{
  if ( is_string($str) ) return '"'.htmlentities($str).'"';
  if ( is_bool($str) ) return ($str ? 'TRUE' : 'FALSE');
  if ( is_array($str) ) return 'array of '.count($str).' values';
  if ( is_null($str) ) return '(null)';
  return $str;
}

// HTML start

include Translate(APP.'_adm.php');
include APP.'_adm_inc_hd.php';

// CONSTANT

$arr = get_defined_constants(true); if ( isset($arr['user']) ) $arr = $arr['user']; // userdefined constants

// Prepare table template

$table = new cTable('','t-data');
$table->row = new cTableRow('','t-data');
$table->td[0] = new cTableData('','','headfirst'); $table->td[0]->Add('style','width:200px;');
$table->td[1] = new cTableData();

// Show constants

echo '<p>Here are the major constants. To have a full list of constants see the file /bin/'.APP.'_init.php.</p>';

echo $table->Start().PHP_EOL;
$table->SetTDcontent( array('QT', ConstantToString(constant('QT'))) );
echo $table->GetTDrow().PHP_EOL;

foreach($arr as $key=>$str)
{
  if ( substr($key,0,3)==strtoupper(APP) )
  {
    $table->SetTDcontent( array($key, ConstantToString($str)) );
    echo $table->GetTDrow().PHP_EOL;
  }
}
echo $table->End(true).PHP_EOL;

// Show DB parameters

echo '<p>Here are the database connection parameters (except passwords)</p>';

echo $table->Start().PHP_EOL;
$table->td[0] = new cTableData('','','headfirst'); $table->td[0]->Add('style','width:200px;');
$table->td[1] = new cTableData();
foreach(array('dbsystem','host','database','prefix','user','port','install') as $str)
{
  $str = APP.'_'.$str;
  $table->SetTDcontent( array('$'.$str, (isset($$str) ? ConstantToString($$str) : '&nbsp;')) );
  echo $table->GetTDrow().PHP_EOL;
}
echo $table->End(true,true,true).PHP_EOL;

include APP.'_adm_inc_ft.php';