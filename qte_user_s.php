<?php // QuickTeam 3.0 build:20140608

if ( $_SESSION[QT]['editing'] ) if ( !sUser::IsStaff() ) $_SESSION[QT]['editing']=false;

// --------
// SUBMITTED
// --------

if ( isset($_POST['ok']) )
{
  // registration date
  if ( empty($error) )
  {
    if ( empty($_POST['regdate']) )
    {
      $oItem->firstdate = '0';
    }
    else
    {
      $str = QTdatestr(trim($_POST['regdate']),'Ymd','',false);
      if ( !is_string($str) ) $error = 'Registration '.$L['E_invalid'];
      if ( substr($str,0,6)=='Cannot' ) $error = 'Registration ('.trim($_POST['regdate']).') '.$L['E_invalid'];
      if ( empty($error) ) $oItem->firstdate = $str;
    }
  }

  // SAVE
  if ( empty($error) )
  {
    $oDB->Query('UPDATE '.TABUSER.' SET firstdate="'.$oItem->firstdate.'" WHERE id='.$id);
  }

  // --- SAVE CHILD INFO (if any) ---
  if ( empty($error) && isset($_POST['coppastatus']) )
  {
  	$strChilddate = '0';
  	$strParentdate = '0';
  	$strParentmail = '';

  	// Check admin
  	if ( $oItem->role=='A' && $_POST['coppastatus']!='0' ) $error = 'System administrator must have the coppa status Major';

  	// childdate
  	if ( empty($error) )
  	{
  		if ( !empty($_POST['birthdate']) )
  		{
  			$str = QTdatestr(trim($_POST['childdate']),'Ymd','',false);
  			if ( !is_string($str) ) $error = 'Child registration date ('.$_POST['childdate'].') '.$L['E_invalid'];
  			if ( substr($str,0,6)=='Cannot' ) $error = 'Child registration date ('.$_POST['childdate'].') '.$L['E_invalid'];
  			if ( empty($error) ) $strChilddate = $str;
  		}
  	}
  	// parentdate
  	if ( empty($error) )
  	{
  		if ( !empty($_POST['parentdate']) )
  		{
  			$str = QTdatestr(trim($_POST['parentdate']),'Ymd','',false);
  			if ( !is_string($str) ) $error = 'Parent agreement date ('.$_POST['childdate'].') '.$L['E_invalid'];
  			if ( substr($str,0,6)=='Cannot' ) $error = 'Parent agreement date ('.$_POST['childdate'].') '.$L['E_invalid'];
  			if ( empty($error) ) $strParentdate = $str;
  		}
  	}
  	// check emails
  	if ( empty($error) && !empty($_POST['parentmail']) )
  	{
  		$_POST['parentmail'] = str_replace(array(' ',';',',,'),',',$_POST['parentmail']);
  		$arrEmails = explode(',',$_POST['parentmail']);

  		if ( count($arrEmails)>5 ) $error = '5 '.L('emails').' '.L('maximum');
  		if ( empty($error) )
  		{
  			foreach ($arrEmails as $strEmail)
  			{
  				if ( !QTismail(trim($strEmail)) ) $error = L('email').' "'.$strEmail.'" '.L('e_invalid');
  			}
  		}
  		if ( empty($error) ) $strParentmail = implode(',',$arrEmails);
  	}

  	// save coppa
  	if ( empty($error) )
  	{
  		$oDB->Query('UPDATE '.TABCHILD.' SET childdate="'.$strChilddate.'",parentdate="'.$strParentdate.'",parentmail="'.$strParentmail.'" WHERE id='.$id);
  		$oDB->Query('UPDATE '.TABUSER.' SET children="'.$_POST['coppastatus'].'" WHERE id='.$id);
  	}
  }
  // --- END SAVE CHILD INFO (if any) ---

  // exit
  $_SESSION['pagedialog'] = (empty($error) ? 'O|'.$L['S_save'] : 'E|'.$error);

}

// --------
// HTML START
// --------

if ( $_SESSION[QT]['editing'] ) echo '<form method="post" action="',Href('qte_user.php'),'?id='.$id.'&amp;tt=',$tt,'">',PHP_EOL;

echo '<div class="pan-top">',(isset($L[strtoupper($tt).'Profile']) ? $L[strtoupper($tt).'Profile'] : $L['Profile']),'</div>
';

echo '<table class="t-data">
<tr><td class="headfirst">Id</td><td>',$id,'</td><td>';
if ( sUser::IsStaff() || sUser::Id()==$id ) echo '<a class="small" href="'.Href('qte_unregister.php').'?id=',$id,'">',$L['Unregister'],'...</a>';
echo '&nbsp;</td></tr>',PHP_EOL;

echo '<tr><td class="headfirst">',ObjTrans('field','username'),'</td><td>',$oItem->username,'&nbsp;</td>';
echo '<td>';
if ( sUser::Role()=='A' || (sUser::Id()==$id && QTE_CHANGE_USERNAME) ) {
if ( $id>1 ) {
  echo '<a class="small" href="'.Href('qte_user_rename.php').'?id=',$id,'">',$L['Change_name'],'...</a>';
}}
echo '&nbsp;</td></tr>',PHP_EOL;

echo '<tr>';
echo '<td class="headfirst">',$L['Password'],'</td>';
echo '<td>&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;</td>';
echo '<td>';
if ( sUser::Role()=='A' || sUser::Id()==$id ) {
  echo '<a class="small" href="'.Href('qte_user_pwd.php').'?id=',$id,'">',$L['Change_password'],'...</a>';
}
echo '&nbsp;</td></tr>',PHP_EOL;

echo '<tr>';
echo '<td class="headfirst">',$L['Secret_question'],'</td>';
echo '<td>',(empty($oItem->secret_a) ? $L['N'] : $L['Y']),'</td>';
echo '<td>';
if ( sUser::Role()=='A' || sUser::Id()==$id ) {
  echo '<a class="small" href="'.Href('qte_user_question.php').'?id=',$id,'">',$L['Secret_question'],'...</a>';
}
echo '&nbsp;</td></tr>',PHP_EOL;

echo '<tr>';
echo '<td class="headfirst">',$L['Picture'],'</td>';
echo '<td>',(empty($oItem->picture) ? $L['None'] : $L['Y']),'</td>';
echo '<td>';
if ( sUser::Role()=='A' || sUser::Id()==$id ) {
  echo '<a class="small" href="'.Href('qte_user_img.php').'?id=',$id,'">',$L['Change_picture'],'...</a>';
}
echo '&nbsp;</td></tr>',PHP_EOL;

echo '<tr>';
echo '<td class="headfirst">System role</td>';
echo '<td>',$L['Userrole_'.$oItem->role],'</td>';
echo '<td>';
if ( sUser::IsStaff() ) {
if ( $id>1 ) {
  echo '<a class="small" href="'.Href('qte_change.php').'?a=userrole&amp;u=',$id,'">',$L['Change_role'],'...</a>';
}}
echo '&nbsp;</td></tr>',PHP_EOL;

echo '<tr><td class="headfirst">Registration</td>';
if ( $_SESSION[QT]['editing'] )
{
  if ( empty($oItem->firstdate) ) { $str = ''; } else { $str = substr($oItem->firstdate,0,4).'-'.substr($oItem->firstdate,4,2).'-'.substr($oItem->firstdate,6,2); }
  echo '<td><input class="profile" type="date" id="regdate" name="regdate" size="11" maxlength="11" value="',$str,'" onchange="bEdited=true;"/> <a href="#" onclick="document.getElementById(\'regdate\').value=\'',date('Y-m-d'),'\';"><img src="',$_SESSION[QT]['skin_dir'],'/ico_date.gif" alt="today" title="',$L['dateSQL']['Today'],'" style="vertical-align:bottom"/></a> <span class="small">',$L['H_Date'],'</span></td>';
  echo '<td>&nbsp;</td>';
}
else
{
  echo '<td>',(empty($oItem->firstdate) ? '&nbsp;' : QTdatestr($oItem->firstdate,'$','',false)),'</td><td>&nbsp;</td>';
}
echo '</tr>',PHP_EOL;

// Index statistics

if ( sUser::IsStaff() )
{
  $arrIndex = array();
  $oDB->Query('SELECT ufield,ukey FROM '.TABINDEX.' WHERE userid='.$id);
  $i=0;
  while( $row = $oDB->Getrow() )
  {
    $arrIndex[] = '('.$row['ufield'].') '.strtolower($row['ukey']);
    if ( $i>30 ) { $arrIndex[] = '...'; break; }
    $i++  ;
  }
  echo '<tr><td class="headfirst">Index keys</td>';
  if ( $_SESSION[QT]['editing'] && sUser::IsStaff() )
  {
    echo '<td>',count($arrIndex);
    if ( count($arrIndex)>0 ) echo ' <select class="small">',QTasTag($arrIndex),'</select>';
    echo '</td><td><a class="small" href="'.Href('qte_change.php').'?a=dropindex&amp;u=',$id,'">',$L['Delete'],'...</a> | <a class="small" href="'.Href('qte_change.php').'?a=makeindex&amp;u=',$id,'">Index...</a></td>';
  }
  else
  {
    echo '<td>',count($arrIndex);
    if ( count($arrIndex)>0 ) echo ' <select class="small">',QTasTag($arrIndex),'</select>';
    echo '</td><td>&nbsp;</td>';
  }
  echo '</tr>',PHP_EOL;
}

echo '</table>
';

// COPPA CHILD

if ( $oItem->coppa!=0 && $_SESSION[QT]['register_coppa']=='1' && sUser::IsStaff() )
{
	echo '<br/><div class="pan-top">Coppa</div>',PHP_EOL;
	echo '<table class="t-data">',PHP_EOL;

	echo '<tr>';
	echo '<td class="headfirst">',$L['Coppa_status'],'</td>';
	echo '<td>';
	if ( $_SESSION[QT]['editing'] )
	{
		echo '<select name="coppastatus" size="1" onchange="bEdited=true;">',QTasTag($L['Coppa_child'],$oItem->coppa),'</select>';
	}
	else
	{
		echo $L['Coppa_child'][$oItem->coppa];
	}
	echo '</td>';
	echo '</tr>',PHP_EOL;

	$oDB->Query('SELECT * FROM '.TABCHILD.' WHERE id='.$id);
	$row = $oDB->Getrow();

	echo '<tr>';
	echo '<td class="headfirst">',$L['Coppa_request_date'],'</td>';
	if ( $_SESSION[QT]['editing'] )
	{
		if ( empty($row['childdate']) ) { $str = ''; } else { $str = substr($row['childdate'],0,4).'-'.substr($row['childdate'],4,2).'-'.substr($row['childdate'],6,2); }
		echo '<td><input type="date" id="childdate" name="childdate" size="11" maxlength="11" value="',$str,'" onchange="bEdited=true;"/> <a href="#" onclick="document.getElementById(\'childdate\').value=\'',date('Y-m-d'),'\';"><img src="',$_SESSION[QT]['skin_dir'],'/ico_date.gif" alt="today" title="',$L['dateSQL']['Today'],'" style="vertical-align:bottom"/></a> <span class="small">',$L['H_Date'],'</span></td>';
	}
	else
	{
		echo '<td>',(!empty($row['childdate']) ? QTdatestr($row['childdate'],'$','',false) : ''),'</td>';
	}
	echo '</tr>',PHP_EOL;

	echo '<tr>';
	echo '<td class="headfirst">',$L['Coppa_agreement_date'],'</td>';
	if ( $_SESSION[QT]['editing'] )
	{
		if ( empty($row['parentdate']) ) { $str = ''; } else { $str = substr($row['parentdate'],0,4).'-'.substr($row['parentdate'],4,2).'-'.substr($row['parentdate'],6,2); }
		echo '<td><input type="date" id="parentdate" name="parentdate" size="11" maxlength="11" value="',$str,'" onchange="bEdited=true;"/> <a href="#" onclick="document.getElementById(\'parentdate\').value=\'',date('Y-m-d'),'\';"><img src="',$_SESSION[QT]['skin_dir'],'/ico_date.gif" alt="today" title="',$L['dateSQL']['Today'],'" style="vertical-align:bottom"/></a> <span class="small">',$L['H_Date'],'</span></td>';
	}
	else
	{
		echo '<td>',(!empty($row['parentdate']) ? QTdatestr($row['parentdate'],'$','',false) : ''),'</td>';
	}
	echo '</tr>',PHP_EOL;

	echo '<tr>';
	echo '<td class="headfirst">',$L['Parent_mail'],'</td>';
	echo '<td>';
	if ( $_SESSION[QT]['editing'] )
	{
		echo '<input class="small" type="email" id="parentmail" name="parentmail" size="40" maxlength="255" value="',$row['parentmail'],'" onchange="bEdited=true;"/>';
	}
	else
	{
		echo '<a href="mailto:',$row['parentmail'],'" class="small">',$row['parentmail'],'</a>';
	}
	echo '</td>';
	echo '</tr>',PHP_EOL;
	echo '</table>',PHP_EOL;
}

// End form

if ( $_SESSION[QT]['editing'] )
{
  echo '<p class="save"><input type="hidden" name="id" value="',$id,'"/><input type="submit" name="ok" value="',$L['Save'],'"/></p>';
}

if ( $_SESSION[QT]['editing'] ) echo '</form>';