<?php // QuickTeam 3.0 build:20140608

// --------
// SUBMITTED
// --------

if ( isset($_POST['ok']) )
{
  // generic

  foreach ($arrFLD as $strField=>$oFLD)
  {
    if ( empty($error) && isset($_POST[$strField]) )
    {
    if ( get_magic_quotes_gpc() ) { $str = stripslashes($_POST[$strField]); } else { $str = $_POST[$strField]; }
    $oItem->$strField = QTconv($str,'3',QTE_CONVERT_AMP); if ( strlen($oItem->$strField)>255) $oItem->$strField = substr($oItem->$strField,255);
    }
  }

  // birthdate

  if ( empty($error) )
  {
    if ( empty($_POST['birthdate']) )
    {
      $oItem->birthdate = '0';
    }
    else
    {
      $str = QTdatestr(trim($_POST['birthdate']),'Ymd','',false);
      if ( !is_string($str) ) $error = ObjTrans('field','birthdate').' ('.$_POST['birthdate'].') '.$L['E_invalid'];
      if ( substr($str,0,6)=='Cannot' ) $error = ObjTrans('field','birthdate').' ('.$_POST['birthdate'].') '.$L['E_invalid'];
      // futur checking
      switch(ObjTrans('ffield','birthdate'))
      {
      case '0': if ( !QTisvaliddate($str,true,true) ) $error = ObjTrans('field','birthdate').' ('.$_POST['birthdate'].') '.$L['E_invalid'];
      case '1': if ( $str>intval(date('Ymd')) ) $error = $L['No_future'][1].' '.ObjTrans('field','birthdate').' ('.$_POST['birthdate'].') '.$L['E_invalid'];
      case '2': if ( !QTisvaliddate($str,true,false) ) $error = $L['No_future'][2].' '.ObjTrans('field','birthdate').' ('.$_POST['birthdate'].') '.$L['E_invalid'];
      }
      if ( empty($error) ) $oItem->birthdate = $str;
      if ( !empty($oItem->birthdate) ) $oItem->SetAge();
    }
  }

  // check www

  if ( empty($error) && isset($_POST['www']) )
  {
    $str = trim($_POST['www']);  if ( get_magic_quotes_gpc() ) $str = stripslashes($str);
    $str = QTconv($str,'2'); if ( strlen($str)>255) $str = substr($str,0,255);
    if ( !empty($oItem->www) && substr($oItem->www,0,4)!='http' ) { $oItem->www=''; $error=ObjTrans('field','www').' '.$L['E_invalid']; }
    if ( $oItem->www=='http://' || $oItem->www=='https://' ) $oItem->www='';
  }

  // check emails

  if ( empty($error) && !empty($_POST['emails']) )
  {
    $_POST['emails'] = str_replace(array(' ',';',',,'),',',$_POST['emails']);
    $arrEmails = explode(',',$_POST['emails']);

    if ( count($arrEmails)>5 ) $error = '5 '.L('emails').' '.L('maximum');
    if ( empty($error) )
    {
      foreach ($arrEmails as $strEmail)
      {
      if ( !QTismail(trim($strEmail)) ) $error = L('email').' "'.$strEmail.'" '.L('e_invalid');
      }
    }
    if ( empty($error) ) $oItem->emails = implode(',',$arrEmails);
  }

  // privacy

  if ( empty($error) )
  {
    foreach(array('address','emails','phones','www','coord') as $strKey)
    {
    $oItem->privacy = str_replace($strKey.';','',$oItem->privacy);
    if ( isset($_POST['hidden'.$strKey]) ) $oItem->privacy .= $strKey.';';
    }
  }

  // coord

  if ( empty($error) && isset($_POST['coord']) )
  {
    if ( get_magic_quotes_gpc() ) $_POST['coord'] = stripslashes($_POST['coord']);
    if ( !empty($_POST['coord']) )
    {
    $_POST['coord'] = QTstr2yx($_POST['coord']);
    if ($_POST['coord']===FALSE ) $error='Invalid coordinate format';
    }
  }

  // SAVE
  if ( empty($error) )
  {
    include 'bin/class/qt_class_smtp.php';

    $strUpdate = '';
    foreach ($arrFLD as $strField=>$oFLD)
    {
      $strUpdate .= $strField.'="'.$oItem->$strField.'",';
    }
    $strUpdate .= 'privacy="'.$oItem->privacy.'"';
    $oDB->Exec('UPDATE '.TABUSER.' SET '.$strUpdate.' WHERE id='.$id);

    if ( $bMap ) {
    if ( isset($_POST['coord']) ) {
			cItem::SetCoord( $id, (empty($_POST['coord']) ? null : $_POST['coord']) ); // null if coord is removed
			$oItem->x = null;
			$oItem->y = null;
			if ( !empty($_POST['coord']) ) { $oItem->x = QTgetx($_POST['coord']); $oItem->y = QTgety($_POST['coord']); }
    }}

    // check moderator name
    if ( $oItem->role=='A' || $oItem->role=='M' )
    {
      $strNewname = $oItem->firstname.' '.$oItem->lastname;
      if ( $strOldname!=$strNewname ) $oDB->Exec('UPDATE '.TABSECTION.' SET modname="'.$strNewname.'" WHERE modid='.$id);
    }

    // check fullname
    $oItem->SetFullname();

    // update index
    $oItem->SaveKeywords($oItem->GetKeywords(GetFields('index_p')));

    // parent warning if coppa (and if edited by the kid himself)
    if ( $oItem->coppa != '0' && $_SESSION[QT]['register_coppa']=='1' && sUser::Id()==$id )
    {
      $oDB->Query('SELECT parentmail FROM '.TABCHILD.' WHERE id='.$id);
      $row = $oDB->Getrow();
      $strSubject = $_SESSION[QT]['site_name'].' - Profile updated';
      $strFile = GetLang().'mail_profile_coppa.php';
      if ( file_exists($strFile) ) include $strFile;
      if ( empty($strMessage) ) $strMessage="Your children (login: %s) has modified his/her profile on the board {$_SESSION[QT]['site_name']}.";
      $strMessage = sprintf($strMessage, $oItem->username);
      $strMessage = wordwrap($strMessage,70,"\r\n");
      QTmail($row['parentmail'],$strSubject,$strMessage);
    }
  }

  // exit
  $_SESSION['pagedialog'] = (empty($error) ? 'O|'.$L['S_save'] : 'E|'.$error);

}

if ( $bMap && !QTgemptycoord($oItem) )
{
	$strPname = QTconv($oItem->fullname,'U');
	$strPlink = '<a class="small" href="http://maps.google.com?q='.floatval($oItem->y).','.floatval($oItem->x).'+('.urlencode($strPname).')&z='.$_SESSION[QT]['m_map_gzoom'].'" title="'.L('map_In_google').'" target="_blank">[G]</a>';
	$strPinfo = '<span class="bold">Lat: '.QTdd2dms(floatval($oItem->y)).' <br />Lon: '.QTdd2dms(floatval($oItem->x)).'</span><br /><span class="small">DD: '.round(floatval($oItem->y),8).', '.round(floatval($oItem->x),8).'</span> '.$strPlink;
	$oMapPoint = new cMapPoint(floatval($oItem->y),floatval($oItem->x),$strPname,$strPinfo);
	if ( isset($_SESSION[QT]['m_map']['U']['icon']) )        $oMapPoint->icon        = $_SESSION[QT]['m_map']['U']['icon'];
	if ( isset($_SESSION[QT]['m_map']['U']['shadow']) )      $oMapPoint->shadow      = $_SESSION[QT]['m_map']['U']['shadow'];
	if ( isset($_SESSION[QT]['m_map']['U']['printicon']) )   $oMapPoint->printicon   = $_SESSION[QT]['m_map']['U']['printicon'];
	if ( isset($_SESSION[QT]['m_map']['U']['printshadow']) ) $oMapPoint->printshadow = $_SESSION[QT]['m_map']['U']['printshadow'];
	$arrExtData[$id] = $oMapPoint;
}

// --------
// HTML START
// --------

echo '<div class="pan-top">',(isset($L[strtoupper($tt).'Profile']) ? $L[strtoupper($tt).'Profile'] : $L['Profile']),'</div>
';

// -- QUERY USER --

$strBirth_y = '';
$strBirth_m = '';
$strBirth_d = '';
if ( !empty($oItem->birthdate) )
{
  $strBirth_y = intval(substr(strval($oItem->birthdate),0,4));
  $strBirth_m = intval(substr(strval($oItem->birthdate),4,2));
  $strBirth_d = intval(substr(strval($oItem->birthdate),6,2));
}

// -- DISPLAY PROFILE --

if ( $_SESSION[QT]['editing'] ) echo '<form method="post" action="',Href('qte_user.php'),'?id='.$id.'&amp;tt=',$tt,'">',PHP_EOL;
echo '<table class="t-data">',PHP_EOL;

// -------
foreach($arrFLD as $strField=>$oFLD) {
// -------

$strCol2 = '&nbsp;';
$strCol3 = '&nbsp;';

echo '<tr>',PHP_EOL;

// default display

if ( $_SESSION[QT]['editing'] )
{
  $strCol2 = InputFormat($strField,$oItem->$strField);
}
else
{
  if ( !empty($oItem->$strField) ) $strCol2 = AsFormat(AsList($oItem->$strField),$oFLD->format);
}

// special display

switch($strField)
{
case 'title':
  if ( $_SESSION[QT]['editing'] ) $strCol2 = InputFormat($strField,$oItem->$strField,true);
  break;

case 'lastname':
  if ( !$_SESSION[QT]['editing'] ) $strCol2 = '<span class="bold">'.(empty($oItem->$strField) ? '('.L('unknown').')' : $strCol2).'</span>' ;
  break;

case 'status':
  $arr = memGet('sys_statuses');
  if ( $_SESSION[QT]['editing'] && sUser::IsStaff() )
  {
  $strCol2 = '<select class="profile" id="status" name="status" onchange="bEdited=true; SetStatusIcon(this.value);">'.QTasTag($arr,$oItem->status).'</select> ';
  foreach($arr as $key=>$arr) $strCol2 .= AsImg($_SESSION[QT]['skin_dir'].'/'.$arr['icon'],'',$arr['statusname'],'ico i-status hiddenicon','display:none','','statusicon_'.$key);
  }
  else
  {
  $strCol2 = (isset($arr[$oItem->status]) ? $arr[$oItem->status]['statusname'].'&nbsp;'.AsImg($_SESSION[QT]['skin_dir'].'/'.$arr[$oItem->status]['icon'],'['.$oItem->status.']',$arr[$oItem->status]['statusdesc'],'ico i-status') : '(unknown status)');
  }
  break;

case 'birthdate':
  if ( $_SESSION[QT]['editing'] )
  {
    if ( empty($oItem->birthdate) ) { $str = ''; } else { $str = substr($oItem->birthdate,0,4).'-'.substr($oItem->birthdate,4,2).'-'.substr($oItem->birthdate,6,2); }
    $strCol2 = '<input class="profile" type="date" id="birthdate" name="birthdate" size="11" maxlength="11" value="'.$str.'" onchange="bEdited=true;"/> <a href="#" onclick="document.getElementById(\'birthdate\').value=\''.date('Y-m-d').'\';"><img src="'.$_SESSION[QT]['skin_dir'].'/ico_date.gif" alt="today" title="'.$L['dateSQL']['Today'].'" style="vertical-align:bottom"/></a> <span class="small">'.$L['H_Date'].'</span>';
    if ( $_SESSION[QT]['register_coppa']=='1' && sUser::IsStaff() && $oItem->id>1)
    {
      if ( $oItem->age<13 && $oItem->coppa==0 ) $strCol3 = '<a class="small" href="'.Href('qte_change.php').'?a=userchild&amp;u='.$id.'&amp;v=1">'.$L['Coppa_apply'].'</a>';
      if ( $oItem->coppa!=0 ) $strCol3 = '<a class="small" href="'.Href('qte_change.php').'?a=userchild&amp;u='.$id.'&amp;v=0">'.$L['Coppa_notapply'].'</a>';
    }
  }
  else
  {
    $strCol2 = (empty($oItem->birthdate) ? S : QTdatestr($oItem->birthdate,'$','',false));
    if ( !empty($oItem->age) ) $strCol2 .= ' ('.$oItem->age.')';
  }
  break;

case 'address':
  if ( $_SESSION[QT]['editing'] )
  {
    $strCol2 = InputFormat($strField,strip_tags($oItem->$strField),false,40,255);
    $strCol3 = '<input type="checkbox" id="hidden'.$strField.'" name="hidden'.$strField.'"'.(strstr($oItem->privacy,$strField) ? QCHE : '').' onchange="bEdited=true;"/> <label for="hidden'.$strField.'" title="'.$L['Hidden_info'].'">'.$L['Hidden'].'</label>';
  }
  else
  {
    if ( sUser::IsStaff() || sUser::Id()==$id ) {
    if ( strstr($oItem->privacy,$strField) ) {
    $strCol3 = '<span class="small" title="'.$L['Hidden_info'].'">'.$L['Hidden'].'</span>';
    }}
  }
  break;
case 'phones':
  if ( $_SESSION[QT]['editing'] )
  {
    $strCol2 = InputFormat($strField,strip_tags($oItem->$strField),false,40,255);
    $strCol3 = '<input type="checkbox" id="hidden'.$strField.'" name="hidden'.$strField.'"'.(strstr($oItem->privacy,$strField) ? QCHE : '').' onchange="bEdited=true;"/> <label for="hidden'.$strField.'" title="'.$L['Hidden_info'].'">'.$L['Hidden'].'</label>';
  }
  else
  {
    if ( sUser::IsStaff() || sUser::Id()==$id ) {
    if ( strstr($oItem->privacy,$strField) ) {
    $strCol3 = '<span class="small" title="'.$L['Hidden_info'].'">'.$L['Hidden'].'</span>';
    }}
  }
  break;
case 'emails':
  if ( $_SESSION[QT]['editing'] )
  {
    $strCol2 = InputFormat($strField,strip_tags($oItem->$strField),false,40,255);
    $strCol3 = '<input type="checkbox" id="hidden'.$strField.'" name="hidden'.$strField.'"'.(strstr($oItem->privacy,$strField) ? QCHE : '').' onchange="bEdited=true;"/> <label for="hidden'.$strField.'" title="'.$L['Hidden_info'].'">'.$L['Hidden'].'</label>';
  }
  else
  {
    $strCol2 = AsEmailsTxt($oItem->emails,'<br/>');    
    if ( sUser::IsStaff() || sUser::Id()==$id ) {
    if ( strstr($oItem->privacy,$strField) ) {
    $strCol3 = '<span class="small" title="'.$L['Hidden_info'].'">'.$L['Hidden'].'</span>';
    }}
  }
  break;
case 'www':
  if ( $_SESSION[QT]['editing'] )
  {
    $strCol2 = InputFormat($strField,(empty($oItem->www) ? 'http://' : $oItem->www),false,40,255);
    $strCol3 = '<input type="checkbox" id="hidden'.$strField.'" name="hidden'.$strField.'"'.(strstr($oItem->privacy,$strField) ? QCHE : '').' onchange="bEdited=true;"/> <label for="hidden'.$strField.'" title="'.$L['Hidden_info'].'">'.$L['Hidden'].'</label>';
  }
  else
  {
    if ( !empty($oItem->www) )
    {
    $arr = AsList($oItem->www);
    foreach($arr as $intKey=>$strValue) $arr[$intKey] = AsUrl(trim($strValue),true,array('class'=>'small','target'=>'_blank','label'=>DropHttp($strValue)));
    $strCol2 = implode('<br />',$arr);
    }
    if ( sUser::IsStaff() || sUser::Id()==$id ) {
    if ( strstr($oItem->privacy,$strField) ) {
    $strCol3 = '<span class="small" title="'.$L['Hidden_info'].'">'.$L['Hidden'].'</span>';
    }}
  }
  break;

case 'nationality':
  if ( $_SESSION[QT]['editing'] )
  {
    $strCol2 = InputFormat($strField,$oItem->$strField,true);
  }
  break;

case 'sexe':
  if ( $_SESSION[QT]['editing'] )
  {
    $strCol2 = InputFormat($strField,$oItem->$strField,true);
  }
  break;
}

echo '<td class="headfirst">',ObjTrans('field',$strField),'</td>',PHP_EOL;
echo '<td>',$strCol2,'</td>',PHP_EOL;
echo '<td style="text-align:right">',$strCol3,'</td>',PHP_EOL;
echo '</tr>',PHP_EOL;

// -------
}
// -------

if ( $bMap )
{
  $strCol1 = $L['Coord'];
  $strCol2 = S;
  $strCol3 = S;
  if ( $_SESSION[QT]['editing'] )
  {
    $strCol2 = '<input type="text" class="small" id="yx" name="coord" size="40" value="'.(!empty($oItem->y) ? $oItem->y.','.$oItem->x : '').'"/> <span class="small">'.$L['Coord_latlon'].'</span>';
    $strCol3 = '<input type="checkbox" id="hiddencoord" name="hiddencoord"'.(strstr($oItem->privacy,'coord') ? QCHE : '').' onchange="bEdited=true;"/> <label for="hiddencoord" title="'.$L['Hidden_info'].'">'.$L['Hidden'].'</label>';
  }
  else
  {
    if ( !empty($oItem->y) && !empty($oItem->x) ) $strCol2 = QTdd2dms(floatval($oItem->y)).', '.QTdd2dms(floatval($oItem->x)).' '.$L['Coord_latlon'].' <span class="small disabled">DD '.round(floatval($oItem->y),8).','.round(floatval($oItem->y),8).'</span>'.(isset($strPlink) ? S.$strPlink : S);
    if ( sUser::IsStaff() || sUser::Id()==$id ) {
    if ( strstr($oItem->privacy,'coord') ) {
    $strCol3 = '<span class="small" title="'.$L['Hidden_info'].'">'.$L['Hidden'].'</span>';
    }}
  }
  echo '<tr>',PHP_EOL;
  echo '<td class="headfirst">',$strCol1,'</td>',PHP_EOL;
  echo '<td>',$strCol2,'</td>',PHP_EOL;
  echo '<td style="text-align:right">',$strCol3,'</td>',PHP_EOL;
  echo '</tr>',PHP_EOL;
}

echo '</table>',PHP_EOL;

if ( $_SESSION[QT]['editing'] ) echo '<p class="save"><input type="hidden" name="id" value="',$id,'"/><input type="submit" name="ok" value="',$L['Save'],'"/></p>',PHP_EOL.'</form>'.PHP_EOL;

// java add-on when editing status
if ( $_SESSION[QT]['editing'] && sUser::IsStaff() )
{
echo '
<script type="text/javascript">
function SetStatusIcon(id)
{
  var doc = document;
  var icons = doc.getElementsByClassName("hiddenicon");
  for(var i=0; i<icons.length; i++) icons[i].style.display="none";
  if ( doc.getElementById("statusicon_"+id) ) doc.getElementById("statusicon_"+id).style.display="inline";
}
SetStatusIcon("',$oItem->status,'"); // make visible the current status (before using select)
</script>
';
}