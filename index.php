<?php

include 'qte_index.php';

if ( isset($_GET['debugsql']) ) 
{
  if ( $_GET['debugsql']==='0' ) { unset($_SESSION['QTdebugsql']); } else { $_SESSION['QTdebugsql']=true; var_dump($_SESSION['QTdebugsql']); }
}
if ( isset($_GET['statsql']) ) 
{
  if ( $_GET['statsql']==='0' ) { unset($_SESSION['QTstatsql']); } else { $_SESSION['QTstatsql']=true; var_dump($_SESSION['QTstatsql']); }
}
//!!
if ( isset($_GET['memunset']) ) 
{
  memUnset('sys_domains');
  memUnset('sys_sections'); // store all sections
  memUnset('sys_statuses');
  memUnset('sys_members');
  memUnset('sys_states');
  echo ' memunset ';
}