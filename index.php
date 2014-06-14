<?php

include 'qte_index.php';

if ( isset($_GET['debugsql']) ) 
{
  if ( $_GET['debugsql']=='0' ) { unset($_SESSION['QTdebugsql']); } else { $_SESSION['QTdebugsql']=true; var_dump($_SESSION['QTdebugsql']); }
}