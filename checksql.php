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
* @package    QTE
* @author     Philippe Vandenberghe <info@qt-cute.org>
* @copyright  2014 The PHP Group
* @version    3.0 build:20140608
*/

session_start();
require 'bin/qte_init.php';
if ( !sUser::CanView('A') ) { $oHtml->PageMsg(11); return; }

$q = '';   // query

QThttpvar('q','str',false); // do not strip tags because <> can be used in the sql


// --------
// HTML START
// --------

$oVIP->arrJava=null;
include 'qte_inc_hd.php';


// Dataset (form)

echo '<form id="form_q" name="form_q" method="post" action="checksql.php">
<textarea id="q" name="q" cols="100"/>'.$q.'</textarea>
<input type="submit" name="ok" value="query"/>
';

echo '
</form>
<br/>
';

// ---------
// SUBMITTED
// ---------

if ( isset($_POST['ok']) )
{

  $q = str_replace('TABDOMAIN',$qte_prefix.'qtedomain',$q);
  $q = str_replace('TABSECTION',$qte_prefix.'qtesection',$q);
  $q = str_replace('TABUSER',$qte_prefix.'qteuser',$q);
  $q = str_replace('TABS2U',$qte_prefix.'qtes2u',$q);
  $q = str_replace('TABCHILD',$qte_prefix.'qtechild',$q);
  $q = str_replace('TABDOC',$qte_prefix.'qtedoc',$q);

  $oDB->Query( $q );

  echo '<table class="hidden">'.PHP_EOL;
  $i=0;
  while($row=$oDB->Getrow())
  {
    if ( $i==0 ) printf( '<tr class="hidden"><td>%s</td></tr>',implode('</td><td>',array_keys($row)) );
    printf( '<tr class="hidden"><td>%s</td></tr>',implode('</td><td>',$row) );
    if ( $i>100 ) break;
    ++$i;
  }
  echo '</table>',PHP_EOL;

}

// ---------
// HTML END
// ---------

include 'qte_inc_ft.php';