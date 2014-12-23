<?php // QuickTeam 3.0 build:20141222

if ( $_SESSION[QT]['editing'] )
{
  if ( !sUser::IsStaff() && $_SESSION[QT]['member_right']!='2' ) $_SESSION[QT]['editing']=false;
}

// --------
// SUBMITTED
// --------

if ( isset($_POST['ok']) )
{
  if ( empty($error) && isset($_POST['teamid1']) ) $oItem->teamid1 = ( get_magic_quotes_gpc() ? stripslashes(strip_tags($_POST['teamid1'])) : strip_tags($_POST['teamid1']) );
  if ( empty($error) && isset($_POST['teamid2']) ) $oItem->teamid2 = ( get_magic_quotes_gpc() ? stripslashes(strip_tags($_POST['teamid2'])) : strip_tags($_POST['teamid2']) );
  if ( empty($error) && isset($_POST['teamrole1']) ) $oItem->teamrole1 = ( get_magic_quotes_gpc() ? stripslashes(strip_tags($_POST['teamrole1'])) : strip_tags($_POST['teamrole1']) );
  if ( empty($error) && isset($_POST['teamrole2']) ) $oItem->teamrole2 = ( get_magic_quotes_gpc() ? stripslashes(strip_tags($_POST['teamrole2'])) : strip_tags($_POST['teamrole2']) );
  if ( empty($error) && isset($_POST['teamvalue1']) ) $oItem->teamvalue1 = ( is_numeric($_POST['teamvalue1']) ? strip_tags($_POST['teamvalue1']) : '');
  if ( empty($error) && isset($_POST['teamvalue2']) ) $oItem->teamvalue2 = ( is_numeric($_POST['teamvalue2']) ? strip_tags($_POST['teamvalue2']) : '');
  if ( empty($error) && isset($_POST['teamflag1']) ) $oItem->teamflag1 = ( get_magic_quotes_gpc() ? stripslashes(strip_tags($_POST['teamflag1'])) : strip_tags($_POST['teamflag1']) );
  if ( empty($error) && isset($_POST['teamflag2']) ) $oItem->teamflag2 = ( get_magic_quotes_gpc() ? stripslashes(strip_tags($_POST['teamflag2'])) : strip_tags($_POST['teamflag2']) );
  if ( empty($error) && isset($_POST['descr']) ) $oItem->descr = ( get_magic_quotes_gpc() ? stripslashes(strip_tags($_POST['descr'])) : strip_tags($_POST['descr']) );
  if ( empty($error) && isset($_POST['teamdate1']) )
  {
    if ( empty($_POST['teamdate1']) )
    {
      $oItem->teamdate1 = '0';
    }
    else
    {
      $str = QTdatestr(trim($_POST['teamdate1']),'Ymd','',false);
      if ( !is_string($str) ) $error = ObjTrans('field','teamdate1').' ('.$_POST['teamdate1'].') '.$L['E_invalid'];
      if ( substr($str,0,6)=='Cannot' ) $error = ObjTrans('field','teamdate1').' ('.$_POST['teamdate1'].') '.$L['E_invalid'];
      // futur checking
      switch(ObjTrans('ffield','teamdate1'))
      {
      case '0': if ( !QTisvaliddate($str,true,true) ) $error = ObjTrans('field','teamdate1').' ('.$_POST['teamdate1'].') '.$L['E_invalid'];
      case '1': if ( $str>intval(date('Ymd')) ) $error = $L['No_future'][1].' '.ObjTrans('field','teamdate1').' ('.$_POST['teamdate1'].') '.$L['E_invalid'];
      case '2': if ( !QTisvaliddate($str,true,false) ) $error = $L['No_future'][2].' '.ObjTrans('field','teamdate1').' ('.$_POST['teamdate1'].') '.$L['E_invalid'];
      }
      if ( empty($error) ) $oItem->teamdate1 = $str;
    }
  }
  if ( empty($error) && isset($_POST['teamdate2']) )
  {
    if ( empty($_POST['teamdate2']) )
    {
      $oItem->teamdate2 = '0';
    }
    else
    {
      $str = QTdatestr(trim($_POST['teamdate2']),'Ymd','',false);
      if ( !is_string($str) ) $error = ObjTrans('field','teamdate2').' ('.$_POST['teamdate2'].') '.$L['E_invalid'];
      if ( substr($str,0,6)=='Cannot' ) $error = ObjTrans('field','teamdate2').' ('.$_POST['teamdate2'].') '.$L['E_invalid'];
      // futur checking
      switch(ObjTrans('ffield','teamdate2'))
      {
      case '0': if ( !QTisvaliddate($str,true,true) ) $error = ObjTrans('field','teamdate2').' ('.$_POST['teamdate2'].') '.$L['E_invalid'];
      case '1': if ( $str>intval(date('Ymd')) ) $error = $L['No_future'][1].' '.ObjTrans('field','teamdate2').' ('.$_POST['teamdate2'].') '.$L['E_invalid'];
      case '2': if ( !QTisvaliddate($str,true,false) ) $error = $L['No_future'][2].' '.ObjTrans('field','teamdate2').' ('.$_POST['teamdate2'].') '.$L['E_invalid'];
      }
      if ( empty($error) ) $oItem->teamdate2 = $str;
    }
  }

  // update
  if ( empty($error) )
  {
    $oItem->privacy = str_replace('descr;','',$oItem->privacy);
    if ( isset($_POST['hiddendescr']) ) $oItem->privacy .= 'descr;';

    // save changes
    $oDB->Exec('UPDATE '.TABUSER.' SET teamid1="'.$oItem->teamid1.'",teamid2="'.$oItem->teamid2.'",teamvalue1='.($oItem->teamvalue1=='' ? 'NULL' : $oItem->teamvalue1).',teamvalue2='.($oItem->teamvalue2=='' ? 'NULL' : $oItem->teamvalue2).',teamrole1="'.$oItem->teamrole1.'",teamrole2="'.$oItem->teamrole2.'",teamflag1="'.$oItem->teamflag1.'",teamflag2="'.$oItem->teamflag2.'",teamdate1="'.$oItem->teamdate1.'",teamdate2="'.$oItem->teamdate2.'",descr="'.$oItem->descr.'",privacy="'.$oItem->privacy.'" WHERE id='.$id);

    // update index
    $oItem->SaveKeywords($oItem->GetKeywords(GetFields('index_t')));

    // exit
    $strInfo = $L['S_save'];
  }
}

// --------
// HTML START
// --------

echo '<div class="pan-top">',(isset($L[strtoupper($tt).'Profile']) ? $L[strtoupper($tt).'Profile'] : $L['Profile']),'</div>
';

if ( $_SESSION[QT]['editing'] ) echo '<form method="post" action="',Href('qte_user.php'),'?id='.$id.'&amp;tt=',$tt,'">',PHP_EOL;

echo '<table class="t-data">',PHP_EOL;

// -------
foreach($arrFLD as $strField=>$oFLD) {
// -------

echo '<tr>',PHP_EOL;

// default display

$strCol2 = '&nbsp;';
$strCol3 = '&nbsp;';

if ( $_SESSION[QT]['editing'] )
{
$strCol2 = InputFormat($strField,$oItem->$strField);
}
else
{
if ( $oItem->$strField!='' && $oItem->$strField!=null ) $strCol2 = AsFormat(AsList($oItem->$strField),$oFLD->format);
}

// special display

switch(substr($strField,0,-1))
{
case 'teamrole': if ( $_SESSION[QT]['editing'] ) $strCol2 = InputFormat($strField,$oItem->$strField,true); break;
case 'teamflag': if ( $_SESSION[QT]['editing'] ) $strCol2 = InputFormat($strField,$oItem->$strField,true); break;
case 'teamdate':
  if ( $_SESSION[QT]['editing'] )
  {
  if ( empty($oItem->$strField) ) { $str = ''; } else { $str = substr($oItem->$strField,0,4).'-'.substr($oItem->$strField,4,2).'-'.substr($oItem->$strField,6,2); }
  $strCol2 = '<input class="profile" type="text" id="'.$strField.'" name="'.$strField.'" size="10" maxlength="10" value="'.$str.'" onchange="bEdited=true;"/> <a href="#" onclick="document.getElementById(\'teamdate1\').value=\''.date('Y-m-d').'\';"><img src="'.$_SESSION[QT]['skin_dir'].'/ico_date.gif" alt="today" title="'.$L['dateSQL']['Today'].'" style="vertical-align:bottom"/></a> <span class="small">'.$L['H_Date'].'</span>';
  }
  else
  {
  $strCol2 = ( empty($oItem->$strField) ? '&nbsp;' : QTdatestr($oItem->$strField,'$','') );
  }
  break;
case 'teamvalue':
  if ( !$_SESSION[QT]['editing'] )
  {
    if ( !is_numeric($oItem->$strField) ) $strCol2 = '&nbsp;';
  }
  break;
case 'desc':
  if ( $_SESSION[QT]['editing'] )
  {
    $strCol2 = '<textarea class="profile" id="'.$strField.'" name="'.$strField.'" rows="4" cols="32" onchange="bEdited=true;">'.strip_tags($oItem->$strField).'</textarea>';
    $strCol3 = '<input type="checkbox" id="hiddendescr" name="hiddendescr"'.(strstr($oItem->privacy,'descr') ? QCHE : '').' onchange="bEdited=true;"/><label for="hiddendescr" title="'.$L['Hidden_info'].'">'.$L['Hidden'].'</label>';
  }
  break;
}

echo '<td class="headfirst">',$oFLD->name,'</td>',PHP_EOL;
echo '<td>',$strCol2,'</td>',PHP_EOL;
echo '<td style="text-align:right">',$strCol3,'</td>',PHP_EOL;
echo '</tr>',PHP_EOL;

// -------
}
// -------

echo '
</table>
';
if ( $_SESSION[QT]['editing'] )
{
  echo '<p class="save">';
  if ( !empty($error) ) echo '<span class="error">',$error,'</span> ';
  if ( empty($error) && !empty($warning) ) echo '<span class="warning">',$warning,'</span> ';
  if ( empty($error) && isset($strInfo) ) echo '<span id="infomessage">',$strInfo,'</span> ';
  echo '<input type="hidden" name="id" value="',$id,'"/><input type="submit" name="ok" value="',$L['Save'],'"/></p>';
}

if ( $_SESSION[QT]['editing'] ) echo '</form>';