<?php // QuickTeam 3.0 build:20140608

// --------
// HIGH LEVEL
// --------

function GetSetting($key='',$default='',$db=true)
{
  // This returns a string (can be empty string if key not found and no default)
  if ( empty($key) || !is_string($key) || !is_string($default) ) die('GetSetting: wrong type for key or default');

  // Read from session
  if ( isset($_SESSION[QT][$key]) ) return $_SESSION[QT][$key];

  // Read from database
  if ( $db )
  {
  global $oDB;
  $oDB->Query('SELECT setting FROM '.TABSETTING.' WHERE param="'.$key.'"');
  $row=$oDB->Getrow();
  if ( isset($row['setting']) ) { $_SESSION[QT][$key] = (string)$row['setting']; return $_SESSION[QT][$key]; }
  }

  // Uses default
  return $default;
}

// --------

function GetParam($bRegister=false,$strWhere='')
{
  global $oDB;
  $arrParam = array();
  $oDB->Query('SELECT param,setting FROM '.TABSETTING.(empty($strWhere) ? '' : ' WHERE '.$strWhere) );
  while ($row = $oDB->Getrow())
  {
  $arrParam[$row['param']]=strval($row['setting']);
  if ( $bRegister ) $_SESSION[QT][$row['param']]=strval($row['setting']);
  }
  return $arrParam;
}

// --------

function EmptyFloat($i)
{
  // Return true when $i is empty or a value starting with '0.000000'
  if ( empty($i) ) return true;
  if ( !is_string($i) && !is_float($i) && !is_int($i) ) die('EmptyFloat: Invalid argument #1, must be a float, int or string');
  if ( substr((string)$i,0,8)=='0.000000' ) return true;
  return false;
}

// --------

function ObjTrans($strType,$strId,$generate=true,$intMax=0,$strTrunc='...')
{
  // This function returns the translation of the objid
  // When translation is not defined, the function can generate it ($generate=true or a string)
  // When $generate is true (no string) the most appropriate replacement is generated
  // When $intMax>1, the text is truncated to intMax characters and the $strTrunc is added.

  $str = '';
  if ( isset($_SESSION['L'][$strType][$strId]) ) $str = $_SESSION['L'][$strType][$strId];
  if ( empty($str) && $generate )
  {
    switch($strType)
    {
    case 'index': $str = (is_string($generate) ? $generate : GetSetting('index_name','Index')); break;
    case 'domain':
    case 'sec':
    case 'field':
    case 'tab': $str = (is_string($generate) ? $generate : ucfirst($strType.' '.$strId)); break;
    case 'ffield': $str = (is_string($generate) ? $generate : '%s'); break;
    case 'secdesc':
    case 'tabdesc': $str = (is_string($generate) ? $generate : ''); break;
    default: $str = (is_string($generate) ? $generate : ucfirst(str_replace('_',' ',$strId))); break;
    }
  }
  if ( $intMax>1 && isset($str[$intMax]) ) return substr($str,0,$intMax).$strTrunc;
  return $str;
}

// --------
// Basic SQL
// --------

function LimitSQL($strState,$strOrder,$intStart=0,$intLength=50)
{
	global $oDB;
	$strOrder = trim($strOrder); if ( substr($strOrder,-3,3)!='ASC' && substr($strOrder,-4,4)!='DESC' ) $strOrder .= ' ASC';
	switch($oDB->type)
	{
    case 'pdo.mysql': return 'SELECT '.$strState.' ORDER BY '.$strOrder.' LIMIT '.$intStart.','.$intLength; break;
		case 'pdo.sqlsrv':
    case 'sqlsrv': return ($intStart==0 ? "SELECT TOP $intLength $strState ORDER BY $strOrder" : "WITH OrderedRows AS (SELECT ROW_NUMBER() OVER (ORDER BY $strOrder) AS RowNumber, $strState) SELECT * FROM OrderedRows WHERE RowNumber BETWEEN ".($intStart+1)." AND ".($intStart+$intLength)); break;
    case 'pdo.pg':
    case 'pg': return "SELECT $strState ORDER BY $strOrder LIMIT $intLength OFFSET $intStart"; break;
    case 'pdo.ibase':
    case 'ibase': return "SELECT FIRST $intLength SKIP $intStart $strState ORDER BY $strOrder"; break;
    case 'pdo.sqlite':
    case 'sqlite': return "SELECT $strState ORDER BY $strOrder LIMIT $intLength OFFSET $intStart"; break;
    case 'pdo.db2':
    case 'db2': return ($intStart==0 ? "SELECT $strState ORDER BY $strOrder FETCH FIRST $intLength ROWS ONLY" : "SELECT * FROM (SELECT ROW_NUMBER() OVER() AS RN, $strState) AS cols WHERE RN BETWEEN ($intStart+1) AND ($intStart+1+$intLength)"); break;
    case 'pdo.oci':
    case 'oci': return ($intStart==0 ? "SELECT * FROM (SELECT $strState ORDER BY $strOrder) WHERE ROWNUM<$intLength" : "SELECT * FROM (SELECT a.*, rownum RN FROM (SELECT $strState ORDER BY $strOrder) a WHERE rownum<$intStart+1+$intLength) WHERE rn>=$intStart"); break;
    default: return 'SELECT '.$strState.' ORDER BY '.$strOrder.' LIMIT '.$intStart.','.$intLength; break;
	}
}

function FirstCharCase($strField,$strCase='u',$len=1)
{
	global $oDB;
	switch($oDB->type)
	{
    case 'pdo.mysql':
			if ( $strCase=='u' ) return "UPPER(LEFT($strField,$len))";
      if ( $strCase=='l' ) return "LOWER(LEFT($strField,$len))";
			if ( $strCase=='a-z' ) return "UPPER($strField) NOT REGEXP '^[A-Z]'";
			break;
    case 'pdo.sqlsrv':
		case 'sqlsrv':
      if ( $strCase=='u' ) return "UPPER(LEFT($strField,$len))";
      if ( $strCase=='l' ) return "LOWER(LEFT($strField,$len))";
			if ( $strCase=='a-z' ) return "(ASCII(UPPER(LEFT($strField,1)))<65 OR ASCII(UPPER(LEFT($strField,1)))>90)";
			break;
    case 'pdo.pg':
    case 'pg':
      if ( $strCase=='u' ) return "UPPER(SUBSTRING($strField,1,$len))";
      if ( $strCase=='l' ) return "LOWER(SUBSTRING($strField,1,$len))";
			if ( $strCase=='a-z' ) return "UPPER($strField) !~ '^[A-Z]'";
			break;
    case 'pdo.ibase':
    case 'ibase':
      if ( $strCase=='u' ) return "UPPER(SUBSTRING($strField FROM 1 FOR $len))";
      if ( $strCase=='l' ) return "LOWER(SUBSTRING($strField FROM 1 FOR $len))";
			if ( $strCase=='a-z' ) return "(UPPER(SUBSTRING($strField FROM 1 FOR 1))<'A' OR UPPER(SUBSTRING($strField FROM 1 FOR 1))>'Z')";
			break;
    case 'pdo.sqlite':
    case 'sqlite':
      if ( $strCase=='u' ) return "UPPER(SUBSTR($strField,1,$len))";
      if ( $strCase=='l' ) return "LOWER(SUBSTR($strField,1,$len))";
			if ( $strCase=='a-z' ) return "(UPPER(SUBSTR($strField,1,1))<'A' OR UPPER(SUBSTR($strField,1,1))>'Z')";
			break;
    case 'pdo.oci':
    case 'pdo.db2':
    case 'oci':
		case 'db2':
      if ( $strCase=='u' ) return "UPPER(SUBSTR($strField,1,$len))";
      if ( $strCase=='l' ) return "LOWER(SUBSTR($strField,1,$len))";
			if ( $strCase=='a-z' ) return "(ASCII(UPPER(SUBSTR($strField,1,1)))<65 OR ASCII(UPPER(SUBSTR($strField,1,1)))>90)";
			break;
		default:
      if ( $strCase=='u' ) return "UPPER(LEFT($strField,$len))";
      if ( $strCase=='l' ) return "LOWER(LEFT($strField,$len))";
      if ( $strCase=='a-z' ) return "UPPER($strField) NOT REGEXP '^[A-Z]'";
      break;
	}
}

function SqlDateCondition($strDate='',$strField='firstpostdate',$intLength=4,$strComp='=')
{
  // Creates a where close for a date field. strDate can be an integer or the string 'old' (5 years or more)
  global $oDB;
  if ( $strDate==='old' ) { $strDate = '<"'.(Date('Y')-3).'"'; } else { $strDate = $strComp.'"'.$strDate.'"'; }
  switch($oDB->type)
  {
  case 'pdo.mysql': return 'LEFT('.$strField.','.$intLength.')'.$strDate; break;
  case 'pdo.pg':
  case 'pg': return 'SUBSTRING('.$strField.',1,'.$intLength.')'.$strDate; break;
  case 'pdo.ibase':
  case 'ibase': return 'SUBSTRING('.$strField.' FROM 1 FOR '.$intLength.')'.$strDate; break;
  case 'pdo.sqlite':
  case 'sqlite':
  case 'pdo.db2':
  case 'db2':
  case 'pdo.oci':
  case 'oci': return 'SUBSTR('.$strField.',1,'.$intLength.')'.$strDate; break;
  default: return 'LEFT('.$strField.','.$intLength.')'.$strDate;
  }
}

// --------
// COMMON FUNCTIONS
// --------

function explodeall($sep=';,',$str,$max=null,$bClean=true,$bTrim=true)
{
  // Same as explode but using each character in $sep as separator
  // Note: before exploding, double separators can be removed with $bClean and $str can be trimmed with $bTrim
  // Note: the separators can be complex (several characters) if $sep is defined as an array of separators
  if ( is_string($sep) ) $sep = str_split($sep);
  if ( !is_array($sep) || !is_string($str) || !is_bool($bClean) || !is_bool($bTrim) ) die('explodeall: invalid argument');
  if ( count($sep)==1 ) return (isset($max) ? explode($sep[0],$str,$max) : explode($sep[0],$str) );

  if ( $bTrim ) $str = trim($str);
  $str = str_replace($sep, $sep[0], $str); // all separators are translated to primary separator
  if ( $bClean ) while ( strpos($str,$sep[0].$sep[0])!==false ) $str = str_replace($sep[0].$sep[0],$sep[0],$str); // remove duplicate separator
  return (isset($max) ? explode($sep[0],$str,$max) : explode($sep[0],$str) );
}

// --------

function AsEmailText($str,$strId='mail-',$strIdN='',$bLink=true,$bHash=true,$arrProp=array())
{
  QTargs('AsEmailText',array($str,$strId,$strIdN,$bLink,$bHash,$arrProp),array('str','str','str','boo','boo','arr'));
  // arrProp can includes class, style, title
  if ( !QTismail($str) ) return $str;

  if ( $bLink )
  {
    if ( $bHash )
    {
    $arr = explode('@',$str,2);
    $strJava='<script type="text/javascript">document.getElementById("'.$strId.$strIdN.'").href="mailto:'.$arr[0].'"+"@"+"'.$arr[1].'";document.getElementById("'.$strId.$strIdN.'").innerHTML="'.$arr[0].'"+"@"+"'.$arr[1].'";</script>';
    $str = '';
    }
    return '<a id="'.$strId.$strIdN.'" href="mailto:'.$str.'"'.(isset($arrProp['title']) ? ' title="'.$arrProp['title'].'"' : '').(isset($arrProp['class']) ? ' class="'.$arrProp['class'].'"' : '').(isset($arrProp['style']) ? ' style="'.$arrProp['style'].'"' : '').(isset($arrProp['target']) ? ' target="'.$arrProp['target'].'"' : '').'>'.$str.'</a>'.(isset($strJava) ? $strJava: '');
  }
  else
  {
    if ( $bHash )
    {
    $arr = explode('@',$str,2);
    $strJava='<script type="text/javascript">document.getElementById("'.$strId.$strIdN.'").innerHTML="'.$arr[0].'"+"@"+"'.$arr[1].'";</script>';
    $str = '';
    }
    return '<span id="'.$strId.$strIdN.'"'.(isset($arrProp['title']) ? ' title="'.$arrProp['title'].'"' : '').(isset($arrProp['class']) ? ' class="'.$arrProp['class'].'"' : '').(isset($arrProp['style']) ? ' style="'.$arrProp['style'].'"' : '').(isset($arrProp['target']) ? ' target="'.$arrProp['target'].'"' : '').'>'.$str.'</span>'.(isset($strJava) ? $strJava: '');
  }
}
function AsEmailImage($str,$strId='mail-',$strIdN='',$bLink=true,$bHash=true,$arrProp=array(),$root='')
{
  QTargs('AsEmailImage',array($str,$strId,$strIdN,$bLink,$bHash,$arrProp,$root),array('str','str','str','boo','boo','arr','str'));
  // arrProp can includes class, style, title
  if ( !QTismail($str) ) return '';

  if ( $bLink )
  {
    if ( $bHash )
    {
    $arr = explode('@',$str,2);
    $strJava='<script type="text/javascript">document.getElementById("'.$strId.$strIdN.'").href="mailto:'.$arr[0].'"+"@"+"'.$arr[1].'";if (document.getElementById("img'.$strId.$strIdN.'")) document.getElementById("img'.$strId.$strIdN.'").title="'.$arr[0].'"+"@"+"'.$arr[1].'";</script>';
    $str = '';
    }
    return '<a id="'.$strId.$strIdN.'" href="mailto:'.$str.'"'.(isset($arrProp['title']) ? ' title="'.$arrProp['title'].'"' : '').(isset($arrProp['class']) ? ' class="'.$arrProp['class'].'"' : '').(isset($arrProp['style']) ? ' style="'.$arrProp['style'].'"' : '').(isset($arrProp['target']) ? ' target="'.$arrProp['target'].'"' : '').'><img id="img'.$strId.$strIdN.'" src="'.$root.$_SESSION[QT]['skin_dir'].'/ico_user_e_1.gif" alt="email" title="'.$str.'" /></a>'.(isset($strJava) ? $strJava: '');
  }
  else
  {
    if ( $bHash )
    {
    $arr = explode('@',$str,2);
    $strJava='<script type="text/javascript">document.getElementById("'.$strId.$strIdN.'").href="mailto:'.$arr[0].'"+"@"+"'.$arr[1].'";if (document.getElementById("img'.$strId.$strIdN.'")) document.getElementById("img'.$strId.'").title="'.$arr[0].'"+"@"+"'.$arr[1].'";</script>';
    $str = '';
    }
    return '<img id="img'.$strId.$strIdN.'" src="'.$root.$_SESSION[QT]['skin_dir'].'/ico_user_e_1.gif" alt="email" title="'.$str.'" />';
  }
}
function AsEmailsTxt($strEmails,$sep=' ',$strId='mail-',$bLink=true,$bHash=true,$intMax=0,$strEmpty='&nbsp;',$arrProp=array())
{
  if ( empty($strEmails) || !is_string($strEmails) ) return $strEmpty;
  // get list of Emails (and remove duplicate mails)
  $arrEmails = array_unique(explodeall(';, ',$strEmails));
  if ( $intMax>0 ) $arrEmails = array_slice($arrEmails,0,$intMax);
  // render emails
  $arr = array();
  foreach ($arrEmails as $i=>$str)
  {
    $arr[]= AsEmailText($str,(string)$strId,(string)$i,$bLink,$bHash,$arrProp);
  }
  return implode($sep,$arr);
}
function AsEmailsImg($strEmails,$sep='',$strId='mail-',$bLink=true,$bHash=true,$intMax=0,$strEmpty='&nbsp;',$arrProp=array(),$root='')
{
  if ( empty($strEmails) || !is_string($strEmails) ) return $strEmpty;
  // get list of Emails (and remove duplicate mails)
  $arrEmails = array_unique(explodeall(';, ',$strEmails));
  if ( $intMax>0 ) $arrEmails = array_slice($arrEmails,0,$intMax);
  // render emails
  $arr = array();
  foreach ($arrEmails as $i=>$str)
  {
    $arr[]= AsEmailImg($str,(string)$strId,(string)$i,$bLink,$bHash,$arrProp,$root);
  }
  return implode($sep,$arr);
}

// --------

function AsFormat($arr='',$strFormat='',$strSep='<br/>')
{
  if ( !is_array($arr) ) $arr = array($arr);
  if ( empty($strFormat) || $strFormat==='%s' ) return implode($strSep,$arr);

  foreach($arr as $strKey=>$strValue)
  {
  if ( $strValue==='' ) continue;
  if ( strstr($strFormat,',') ) continue;
  if ( strstr($strFormat,' ; ') ) continue;
  $arr[$strKey] = sprintf($strFormat,$strValue);
  }

  return implode($strSep,$arr);
}

// --------

function AsImg($strSrc='',$strAlt='',$strTitle='',$strClass='',$strStyle='',$strHref='',$strId='')
{
  QTargs('AsImg',array($strSrc,$strAlt,$strClass,$strStyle,$strHref,$strId));
  if ( empty($strSrc) ) return '';
  $strSrc  = '<img'.(empty($strId) ? '' : ' id="'.$strId.'"').' src="'.$strSrc.'" alt="'.(empty($strAlt) ? '' : QTconv($strAlt)).'" title="'.(empty($strTitle) ? '' : QTconv($strTitle)).'"'.(empty($strClass) ? '' : ' class="'.$strClass.'"').(empty($strStyle) ? '' : ' style="'.$strStyle.'"').'/>';
  if ( empty($strHref) ) { return $strSrc; } else { return '<a href="'.Href($strHref).'">'.$strSrc.'</a>' ; }
}

function AsImgPopup($strId='',$strSrc='',$strAlt='',$strTitle='',$strClass='',$strStyle='',$strHref='')
{
	QTargs('AsImg',array($strId,$strSrc,$strAlt,$strClass,$strStyle,$strHref));
	if ( empty($strSrc) ) return '';
	return '<img class="popup clickable" id="popup_'.$strId.'" src="'.$strSrc.'" style="display:none" onclick="qtHide(this.id);" alt="(image not found)"/><img'.(empty($strId) ? '' : ' id="'.$strId.'"').' src="'.$strSrc.'" alt="'.(empty($strAlt) ? '' : QTconv($strAlt)).'" title="'.(empty($strTitle) ? '' : QTconv($strTitle)).'" class="clickable'.(empty($strClass) ? '' : ' '.$strClass).'"'.(empty($strStyle) ? '' : ' style="'.$strStyle.'"').' onclick="qtPopupImage(this,\''.(empty($strHref) ? '' : $strHref).'\');"/>';
}

function AsImgBox($strSrc='',$strClass='',$strStyle='',$strCaption='',$strHref='')
{
  QTargs('AsImgBox',array($strSrc,$strClass,$strStyle,$strCaption,$strHref));

  if ( !empty($strHref) ) $strCaption = '<a href="'.Href($strHref).'" class="small">'.$strCaption.'</a>';
  return '<div'.(empty($strClass) ? '' : ' class="'.$strClass.'"').(empty($strStyle) ? '' : ' style="'.$strStyle.'"').'>'.$strSrc.(empty($strCaption) ? '' : '<p class="imgcaption">'.$strCaption.'</p>').'</div>';
}

// --------

function UserFirstLastName($oItem,$sep=' ',$alt='unknown',$size=32,$eol='...',$bHtmlEntities=true)
{
	// $oItem be a cItem object, an array
	$firstname ='';
	$lastname = '';
	if ( is_a($oItem,'cItem') )
	{
		$firstname = trim($oItem->firstname);
	  $lastname = trim($oItem->lastname);
	}
	if ( is_array($oItem) )
	{
	  if ( !empty($oItem[0]) ) $firstname = trim($oItem[0]);
	  if ( !empty($oItem[1]) ) $lastname = trim($oItem[1]);
	  if ( !empty($oItem['firstname']) ) $firstname = trim($oItem['firstname']);
	  if ( !empty($oItem['lastname']) ) $lastname = trim($oItem['lastname']);
	}

	// format
	if ( $bHtmlEntities ) { $firstname = htmlentities($firstname); $lastname = htmlentities($lastname);}
	// return Fist+Last name. If empty $alt allows returning formatted Username.
	$str = trim($firstname.$sep.$lastname);
	if ( empty($str) && !empty($alt) ) $str = '('.$alt.')';
	if ( strlen($str)>$size ) $str = substr($str,0,$size).$eol;
	return $str;
}

function UserPicture($oItem,$Url=false,$bNullImage=true,$class="userpicture")
{
	if ( empty($oItem->picture) )
	{
		$str = ($bNullImage ? $_SESSION[QT]['skin_dir'].'/user.gif' : '<br/>');
	}
	else
	{
		$str = QTE_DIR_PIC.$oItem->picture;
	}
	$str = '<img class="'.$class.'" src="'.$str.'" title="'.QTconv(UserFirstLastName($oItem)).'" alt="(user)">';
	if ( !empty($Url) ) $str = '<a href="'.$Url.'">'.$str.'</a>';
	return $str;
}

function AsImgBoxUser($oItem,$add='',$bNullImage=true,$bEditUrl=false)
{
  if (empty($oItem->lastname) ) $oItem->lastname=$oItem->username;
  $strCaption = QTconv(trim($oItem->firstname),'5');
  $strCaption .= (empty($strCaption) ? '' : '<br />').QTconv($oItem->lastname,'5');
  switch($add)
  {
  case 'username':
    $strCaption .= '<br />('.QTconv(trim($oItem->username),'5').')';
    break;
  case 'status':
    $arr = memGet('sys_statuses');
    if ( isset($arr[$oItem->status]['statusname']) ) $strCaption .= '<br />('.QTconv($arr[$oItem->status]['statusname'],'5').')';
    break;
  default:
    if ( !empty($add) ) '<br />('.$strCaption .= QTconv(trim($add),'5').')';
  }
  if ( $bEditUrl )
  {
    global $oVIP, $L;
    if ( empty($oItem->picture) && sUser::Id()==$oItem->id ) $strCaption .= '<br /><br /><a class="small" href="'.Href('qte_user_img.php').'?id='.$oItem->id.'">'.$L['My_picture'].'</a>';
  }
  if ( empty($oItem->picture) )
  {
  	$strUserImage = ($bNullImage ? '<img src="'.$_SESSION[QT]['skin_dir'].'/user.gif" title="'.$oItem->firstname.'" alt="(user)">' : '<br/>');
  }
  else
  {
  	$strUserImage = AsImg(QTE_DIR_PIC.$oItem->picture,'',$oItem->firstname);
  }
  return AsImgBox($strUserImage,'','',$strCaption);
}

// -------

function AsList($str,$bFirst=false,$strSep=',',$intMax=9,$strMoreIndicator='...',$bTrim=true)
{
  // Convert a string into a list [array].
  // Can generale a list with only the first element and optionally the '...' indicator as second element.
  // With default options, the list will be truncated to 9 elements and the '...' indicator will added as the 10th (if more items exist).
  // Use $strMoreIndicator='' to drop the 'more indicator' as last added item.
  // If $str does not include the separator $strSep, the result will be a list of 1 element ($str).
  // To ensure backward compatibility the separator ' ; ' is ALSO considered as a separator.

  QTargs('AsList',array($str,$bFirst,$strSep),array('str','boo','str'));
  if ( $strSep!=' ; ' ) $str = str_replace(' ; ',$strSep,$str); // to ensure compatibility with version 2.x
  if ( strstr($str,$strSep) )
  {
    $arr = explode($strSep,$str);

    if ( $bFirst ) $intMax=1;
    if ( count($arr)>$intMax )
    {
      $arr = array_slice($arr, 0, $intMax);
      if ( !empty($strMoreIndicator) ) $arr[] = $strMoreIndicator;
    }
  }
  else
  {
    $arr = array($str);
  }
  if ( $bTrim ) $arr = array_map('trim',$arr);
  return $arr;
  // TIPS: check if it's usefull to also remove duplicates on this returned result
}

// --------

function AsUrl($str,$bLink=true,$arrProp=array())
{
  QTargs('AsUrl',array($str,$bLink,$arrProp),array('str','boo','arr'));
  // arrProp can includes class, style, target, title and label
  if ( empty($str) ) return '';
  if ( $bLink )
  {
  return '<a href="'.$str.'"'.(isset($arrProp['title']) ? ' title="'.$arrProp['title'].'"' : '').(isset($arrProp['class']) ? ' class="'.$arrProp['class'].'"' : '').(isset($arrProp['style']) ? ' style="'.$arrProp['style'].'"' : '').(isset($arrProp['target']) ? ' target="'.$arrProp['target'].'"' : '').'>'.(isset($arrProp['label']) ? $arrProp['label'] : $str).'</a>';
  }
  else
  {
  return '<span'.(isset($arrProp['title']) ? ' title="'.$arrProp['title'].'"' : '').(isset($arrProp['class']) ? ' class="'.$arrProp['class'].'"' : '').(isset($arrProp['style']) ? ' style="'.$arrProp['style'].'"' : '').(isset($arrProp['target']) ? ' target="'.$arrProp['target'].'"' : '').'>'.(isset($arrProp['label']) ? $arrProp['label'] : $str).'</span>';
  }
}

// --------

function DateAdd($d='0',$i=-1,$str='year')
{
   if ( $d=='0' ) die('DateAdd: Argument #1 must be a string');
   if ( !is_string($d) ) die('DateAdd: Argument #1 must be a string');
   if ( !is_int($i) ) die('DateAdd: Argument #2 must be an integer');
   if ( !is_string($str) ) die('DateAdd: Argument #3 must be a string');
   $intY = intval(substr($d,0,4));
   $intM = intval(substr($d,4,2));
   $intD = intval(substr($d,6,2));
   switch($str)
   {
   case 'year': $intY += $i; break;
   case 'month': $intM += $i; break;
   case 'day': $intD += $i; break;
   }
   if ( in_array($intM,array(1,3,5,7,8,10,12)) && $intD>31 ) { ++$intM; $intD -= 31; }
   if ( in_array($intM,array(4,6,9,11)) && $intD>30 ) { ++$intM; $intD -= 30; }
   if ( $intD<1 ) { --$intM; $intD += 30; }
   if ( $intM>12 ) { ++$intY; $intM -= 12; }
   if ( $intM<1 ) { --$intY; $intM += 12; }
   if ( $intM==2 && $intD>28 ) { ++$intM; $intD -= 28; }
   return strval($intY*10000+$intM*100+$intD).(strlen($d)>8 ? substr($d,8) : '');
}

// -------

function DropHttp($str)
{
  $str = str_replace('https://','',$str);
  return str_replace('http://','',$str);
}

// --------

function FieldQuote($strValue,$strTable,$strField)
{
  // Returns a quoted value, except for these fields:
  if ( $strTable==TABUSER && $strField=='id' ) return ($strValue==='' || is_null($strValue) ? 'NULL' : $strValue);
  if ( $strTable==TABDATA && $strField=='id' ) return ($strValue==='' || is_null($strValue) ? 'NULL' : $strValue);
  if ( $strTable==TABSECTION && in_array($strField,array('id','domainid','vorder','modid')) ) return ($strValue==='' || is_null($strValue) ? 'NULL' : $strValue);
  if ( $strTable==TABDOMAIN && in_array($strField,array('id','vorder')) ) return ($strValue==='' || is_null($strValue) ? 'NULL' : $strValue);
  if ( $strTable==TABCHILD && $strField=='id' ) return ($strValue==='' || is_null($strValue) ? 'NULL' : $strValue);
  if ( $strTable==TABDOC && $strField=='id' ) return ($strValue==='' || is_null($strValue) ? 'NULL' : $strValue);
  if ( $strTable==TABINDEX && $strField=='userid' ) return ($strValue==='' || is_null($strValue) ? 'NULL' : $strValue);
  if ( $strTable==TABS2U && in_array($strField,array('sid','userid')) ) return ($strValue==='' || is_null($strValue) ? 'NULL' : $strValue);
  if ( $strTable==TABSTATUS && $strField=='id' ) return ($strValue==='' || is_null($strValue) ? 'NULL' : $strValue);
  if ( empty($strValue) ) {
  if ( in_array($strField,array('birthday','docdate','eventdate','fielddate','firstdate','firstpostdate','issuedate','lastdate','lastpostdate','modifdate','statusdate','wisheddate')) ) {
    return '"0"';
  }}
  return '"'.$strValue.'"';
}

// --------

function FormatUsers($arrUsers,$n='u',$bStatus=false)
{
  // create a new array (same key) with formatted lastname/firstname/username (according to the format $n)
  // $bStatus to append the status (with;)
  $arrResult = array();
  foreach ($arrUsers as $key=>$arr)
  {
    if ( !isset($arr['username']) ) $arr['username']=$key;
    if ( !isset($arr['lastname']) ) $arr['lastname']=$arr['username'];
    if ( !isset($arr['firstname']) ) $arr['firstname']='';
    if ( $bStatus && !isset($arr['status']) ) $arr['status']='0';
    switch($n)
    {
    case 'lf':  $arrResult[$key]=$arr['lastname'].' '.$arr['firstname']; if ( empty($arrResult[$key]) ) $arrResult[$key]=$arr['username']; break;
    case 'fl':  $arrResult[$key]=$arr['firstname'].' '.$arr['lastname']; if ( empty($arrResult[$key]) ) $arrResult[$key]=$arr['username']; break;
    case 'lfu': $arrResult[$key]=trim($arr['lastname'].' '.$arr['firstname']).' ('.$arr['username'].')'; break;
    case 'flu': $arrResult[$key]=trim($arr['firstname'].' '.$arr['lastname']).' ('.$arr['username'].')'; break;
    case 'ulf': $arrResult[$key]=$arr['username'].' ('.trim($arr['lastname'].' '.$arr['firstname']).')'; break;
    case 'ufl': $arrResult[$key]=$arr['username'].' ('.trim($arr['firstname'].' '.$arr['lastname']).')'; break;
    default: $arrResult[$key]=$arr['username']; break;
    }
    if ( $bStatus) $arrResult[$key] .= ';'.$arr['status'];
  }
  asort($arrResult);
  return $arrResult;
}

// --------
// Returns an array of [key] id, [value] title

function GetDomains()
{
  global $oDB;
  $arr = array();
  $oDB->Query('SELECT id,title FROM '.TABDOMAIN.' ORDER BY vorder');
  while ( $row = $oDB->Getrow() )
  {
    $arr[$row['id']] = $row['title'];
  }
  // search translation
  $arrL = cLang::Get('domain',QTiso(),'*');
  if ( count($arrL)>0)
  {
    foreach ($arr as $id => $str)
    {
      if ( array_key_exists('d'.$id,$arrL) )
      {
      if ( !empty($arrL['d'.$id]) ) $arr[$id]=$arrL['d'.$id];
      }
    }
  }
  return $arr;
}

// --------

function GetSections($strRole='V',$intDomain=-1,$arrReject=array(),$strExtra='',$strOrder='d.vorder,s.vorder')
{
  // Returns an array of sections. The format is $arrSection[sectionid] = array of section info (all fields from the database)
  // Use $intDomain to get sections in this domain only.
  // $intDomain=-1 returns sections in all domains.
  // $intDomain=-2 returns sections grouped by domain (see definition in SectionsByDomain)
  if ( is_int($arrReject) || is_string($arrReject) ) $arrReject = array((int)$arrReject);
  QTargs( 'GetSections',array($strRole,$intDomain,$arrReject,$strExtra,$strOrder),array('str','int','arr','str','str') );
  if ( $intDomain>=0 ) { $strWhere = 's.domainid='.$intDomain; } else { $strWhere = 's.domainid>=0'; }
  if ( $strRole==='V' || $strRole==='U' ) $strWhere .= ' AND s.type<>"1"';
  if ( !empty($strExtra) ) $strWhere .= ' AND '.$strExtra;
  $arrSections = array();
  
  global $oDB;
  $oDB->Query('SELECT s.* FROM '.TABSECTION.' s INNER JOIN '.TABDOMAIN.' d ON s.domainid=d.id WHERE '.$strWhere.' ORDER BY '.$strOrder);
  while($row=$oDB->Getrow())
  {
    $arr = array();
    $id = (int)$row['id'];
    // if reject
    if ( in_array($id,$arrReject,true) ) continue;
    // section create
    $arr[$id] = $row;
    // search translation
    $str = ObjTrans('sec','s'.$id,false);
    if ( !empty($str) ) $arr[$id]['title']=$str;
    // compile sections
    if ( $intDomain==-2 )
    {
    $arrSections = SectionsByDomain($strRole,$arr);
    }
    else
    {
    $arrSections[$id] = $arr[$id];
    }
  }
  return $arrSections;
}

// --------

function SectionsByDomain($role='',$arrSections=array())
{
  // Returns an array of domains+sections. The format is $arr[domainid][sectionid] = array of section info 
  // Role is used to hide the protected sections.
  // If an argument is empty, uses the current role/list of sections. 
  // When a domain don't have sections, it is not returned ($arr[domainid] is not set). 
  $arr = array();
  if ( empty($arrSections) ) $arrSections = memGet('sys_sections');  
  if ( empty($role) ) $role = sUser::Role();
  foreach($arrSections as $id=>$arrSection)
  {
    if ( ($role==='V' || $role==='U') && isset($arrSection['type']) && $arrSection['type']==='1' ) continue;
    $arr[(int)$arrSection['domainid']][$id] = $arrSection;
  }
  return $arr;
}

// --------

function GetFields($str)
{
  if ( $str=='index_p' ) return array('username','address','phones','emails','www','title','firstname','midname','lastname','alias','birthdate','nationality','sexe','age');
  if ( $str=='index_t' ) return array('teamid1','teamid2','teamrole1','teamrole2','teamdate1','teamdate2','teamvalue1','teamvalue2','teamflag1','teamflag2','descr');
  if ( $str=='index_s') return array('id','status','role');
  if ( $str=='all') return array_merge(GetFields('index_p'),GetFields('index_t'),GetFields('index_s'));
  return array();
}

// --------
// Returns an array of [key] section id, [value] section title

function GetSectionTitles($strRole='V',$intDomain=-1,$intReject=-1,$strExtra='')
{
  QTargs('GetSectionTitles',array($strRole,$intDomain,$intReject,$strExtra),array('str','int','int','str'));

  if ( $intDomain>=0 ) { $strWhere = 'domainid='.$intDomain; } else { $strWhere = 'domainid>=0'; }
  if ( $strRole=='V' || $strRole=='U' ) $strWhere .= ' AND type<>"1"';
  if ( !empty($strExtra) ) $strWhere .= ' AND '.$strExtra;

  global $oDB,$oVIP;
  $arr = array();

  $oDB->Query('SELECT id,title FROM '.TABSECTION.' WHERE '.$strWhere.' ORDER BY vorder' );
  while ( $row = $oDB->Getrow() )
  {
    $id = (int)$row['id'];
    $arr[$id] = $row['title'];
    // search translation (without auto generate)
    $str = ObjTrans('sec',$id,false);
    if ( !empty($str) ) $arr[$id] = $str;
  }

  // if reject
  if ( $intReject>=0 && isset($arr[$intReject]) ) unset($arr[$intReject]);

  return $arr;
}

// --------

function GetFLDs($arr,$bActive=true,$bGetIdOnly=false)
{
  // Returns an array with cFLD (from names in $arr) that are ACTIVES
  // Use $bActive=false to get all cFLD (including INACTIVES)
  // Use $GetIdOnly to get the field key instead of the cFLD objects
  if ( is_string($arr) ) $arr = explode(';',$arr);
  if ( !is_array($arr) ) die('GetFLDs: arg #1 must be an array');
  if ( count($arr)==0 ) die('GetFLDs: arg #1 must be an array');
  $arrFLD = array();
  foreach($arr as $strKey)
  {
    $oFLD = new cFLD($strKey,ucfirst($strKey));
    if ( !$oFLD->on && $bActive ) continue;
    $arrFLD[$strKey]=($bGetIdOnly ? $strKey : $oFLD);
  }
  return $arrFLD;
}

// --------

function GetFLDnames($arrFLD,$bIcon=true)
{
  // Return a list of $oFLD->name. Using $bIcon: when the key ends with "_i" it adds ' icon' to the name.
  if ( !is_array($arrFLD) ) die('GetFLDnames: arg #1 must be an array');
  $arr = array();
  foreach($arrFLD as $strKey=>$oFLD)
  {
    if ( empty($oFLD->name) ) { $arr[$strKey]=$strKey; } else { $arr[$strKey]=$oFLD->name.($bIcon && substr($strKey,-2,2)=='_i' ? ' icon' : ''); }
  }
  return $arr;
}

// --------

function GetUri($reject=array())
{
  // Returns the URI not urldecoded as a string
  // $reject to remove parametres from the URI (can be csv)
  if ( !is_array($reject) ) $reject=explode(',',$reject);
  $arr = QTexplodeUri(null,false);
  foreach($reject as $key) $arr = QTarradd($arr,trim($key)); // null value to remove the key
  return QTimplodeUri($arr,false);
}

// --------

function GetUsers($strRole='all',$intSection=-1,$strStatus='all',$intStart=0,$intLength=100,$intMax=100)
{
  // Returns an array of (max 500) users id/lastname+firstname
  // number = returns 1 user [id][name]
  // 'all' = returns all users (default)
  // 'A'   = returns administrators
  // 'M'   = returns staff members (+Admin)
  // 'M-'  = returns staff members (-Admin)
  // 'U'   = returns users (-Admins -moderators)
  // 'SC'  = returns sleeping children
  // Use $intLength<1 to return all users (up to $intMax)

  global $oDB;
  $arrUsers = array();
  $strQ = '';
  if ( $intSection<0 )
  {
    switch($strRole)
    {
    case 'all': $strQ = 'WHERE u.id>0'; break;
    case 'A':   $strQ = 'WHERE u.id>0 AND u.role="A"'; break;
    case 'U':   $strQ = 'WHERE u.id>0 AND u.role="U"'; break;
    case 'M':   $strQ = 'WHERE u.id>0 AND (u.role="A" OR u.role="M")'; break;
    case 'M-':  $strQ = 'WHERE u.id>0 AND u.role="M"'; break;
    case 'SC':  $strQ = 'WHERE u.id>0 AND u.children=2'; break;
    case 'lost':$strQ = 'LEFT JOIN '.TABS2U.' l ON l.userid=u.id WHERE u.id>0 AND l.userid IS NULL'; break;
    default: die('GetUsers: undefined option '.$strRole);
    }
  }
  else
  {
    switch($strRole)
    {
    case 'all': $strQ = 'INNER JOIN '.TABS2U.' l ON l.userid=u.id WHERE l.userid>0 AND l.sid='.$intSection; break;
    case 'A':   $strQ = 'INNER JOIN '.TABS2U.' l ON l.userid=u.id WHERE l.userid>0 AND l.sid='.$intSection.' AND u.role="A"'; break;
    case 'U':   $strQ = 'INNER JOIN '.TABS2U.' l ON l.userid=u.id WHERE l.userid>0 AND l.sid='.$intSection.' AND u.role="U"'; break;
    case 'M':   $strQ = 'INNER JOIN '.TABS2U.' l ON l.userid=u.id WHERE l.userid>0 AND l.sid='.$intSection.' AND (u.role="M" OR u.role="A")'; break;
    case 'M-':  $strQ = 'INNER JOIN '.TABS2U.' l ON l.userid=u.id WHERE l.userid>0 AND l.sid='.$intSection.' AND u.role="M"'; break;
    case 'SC':  $strQ = 'INNER JOIN '.TABS2U.' l ON l.userid=u.id WHERE l.userid>0 AND l.sid='.$intSection.' AND u.children=2'; break;
    case 'lost':$strQ = 'LEFT JOIN '.TABS2U.' l ON l.userid=u.id WHERE u.id>0 AND l.userid IS NULL'; break;
    default: die('GetUsers: undefined option '.$strRole);
    }
  }

  // add status
  if ( is_string($strStatus) && strtolower($strStatus)!=='all' && $strStatus!=='*' && $strStatus!=='' ) $strQ .= ' AND u.status="'.$strStatus.'"';

  // query
  $oDB->Query( LimitSQL('u.id,u.username,u.firstname,u.lastname,u.status,u.picture FROM '.TABUSER.' u '.$strQ,'u.lastname ASC, u.firstname ASC',$intStart,$intLength) );
  while ($row=$oDB->Getrow())
  {
    $arrUsers[(int)$row['id']]=$row;
    if ( count($arrUsers)>$intMax ) break;
  }
  return $arrUsers;
}

// ------------

function InvalidUpload($arrFile=array(),$strExtensions='',$strMimes='',$intSize=0,$intWidth=0,$intHeight=0)
{
   // For the uploaded document ($arrFile), this function returns (as string):
   // '' (empty string) if it matches with all conditions (see parameters)
   // An error message if not, and unlink the uploaded document.
   //
   // @$arrFile: The uploaded document ($_FILES['fieldname']).
   // @$strExtensions: List of valid extensions (as string). Empty to skip.
   // @$strMimes: List of valid mimetypes (as string). Empty to skip
   // @$intSize: Maximum file size (kb). 0 to skip.
   // @$intWidth: Maximum image width (pixels). 0 to skip.
   // @$intHeight: Maximum image width (pixels). 0 to skip.

  // check arguments
  if ( is_array($strExtensions) ) $strExtensions=implode(';',$strExtensions);
  if ( is_array($strMimes) ) $strMimes=implode(';',$strMimes);

  if ( !is_array($arrFile) ) die('CheckUpload: argument #1 must be an array');
  if ( !is_string($strExtensions) ) die('CheckUpload: argument #2 must be a string');
  if ( !is_string($strMimes) ) die('CheckUpload: argument #3 must be a string');
  if ( !is_int($intSize) ) die('CheckUpload: argument #4 must be an integer');
  if ( !is_int($intWidth) ) die('CheckUpload: argument #5 must be an integer');
  if ( !is_int($intHeight) ) die('CheckUpload: argument #6 must be an integer');

  global $L;

  // check load

  if ( !is_uploaded_file($arrFile['tmp_name']) )
  {
    unlink($arrFile['tmp_name']);
    return 'You id not upload a file!';
  }

  // check size (kb)

  if ( $intSize>0 ) {
  if ( $arrFile['size'] > ($intSize*1024+16) ) {
    unlink($arrFile['tmp_name']);
    return $L['E_file_size'].' (&lt;'.$intSize.' Kb)';
  }}

  // check extension

  if ( !empty($strExtensions) )
  {
    $ext = strtolower(substr($arrFile['name'],strrpos($arrFile['name'], '.')+1));
    if ( strpos($strExtensions,$ext)===FALSE )
    {
    unlink($arrFile['tmp_name']);
    return 'Format ['.substr($arrFile['name'],-4,4).'] not supported... Use '.str_replace(';',' ',$strExtensions);
    }
  }

  // check mimetype

  if ( !empty($strMimes) ) {
  if ( strpos(strtolower($strMimes),strtolower($arrFile['type']))===FALSE ) {
    unlink($arrFile['tmp_name']);
    return 'Format ['.$arrFile['type'].'] not supported... Use '.str_replace(';',' ',$strMimes);
  }}

  // check size (pixels)

  if ( $intWidth>0 || $intHeight>0 )
  {
    $size = getimagesize($arrFile['tmp_name']);
    if ( $intWidth>0 ) {
    if ( $size[0] > $intWidth ) {
      unlink($arrFile['tmp_name']);
      return $intWidth.'x'.$intHeight.' '.$L['E_pixels_max'];
    }}
    if ( $intHeight>0 ) {
    if ( $size[1] > $intHeight ) {
      unlink($arrFile['tmp_name']);
      return $intWidth.'x'.$intHeight.' '.$L['E_pixels_max'];
    }}
  }

  return '';
}

// --------
// MakePager
// Return FALSE if pager not needed

function MakePager($uri,$count,$intPagesize=50,$currentpage=1)
{
  $arrUri = parse_url($uri);
  $arrArg = array();
  $arrNew = array();
  $uri = Href($arrUri['path']);
  if ( isset($arrUri['query'])) $arrArg = explode('&',str_replace('&amp;','&',$arrUri['query']));

  foreach($arrArg as $strValue)
  {
    if ( substr($strValue,0,4)=='page' ) continue;
    $arrNew[]=$strValue;
  }
  $arg = implode('&amp;',$arrNew);

  $strPages='';
  $firstpage='';
  $lastpage='';
  $top = ceil($count/$intPagesize);
  if ( $currentpage<5 )
  {
    $arrPages=array(1,2,3,4,5);
  }
  elseif ( $currentpage==$top )
  {
    $arrPages=array($currentpage-4,$currentpage-3,$currentpage-2,$currentpage-1,$currentpage);
  }
  else
  {
    $arrPages=array($currentpage-2,$currentpage-1,$currentpage,$currentpage+1,$currentpage+2);
  }

  // pages
  foreach($arrPages as $page)
  {
    if ( $count>$intPagesize && $page>=1 && $page<=$top )
    {
    $strPages .= ' '.($currentpage==$page ? '<b>'.$page.'</b>' : '<a href="'.$uri.'?'.$arg.'&amp;page='.$page.'">'.$page.'</a>');
    }
  }
  // extreme
  if ( $count>($intPagesize*5) )
  {
    global $L;
    if ( $arrPages[0]>1 ) $firstpage = ' <a href="'.$uri.'?'.$arg.'&amp;page=1" title="'.$L['First'].'">&laquo;</a>';
    if ( $arrPages[4]<$top ) $lastpage = ' <a href="'.$uri.'?'.$arg.'&amp;page='.$top.'" title="'.$L['Last'].': '.$top.'">&raquo;</a>';
  }
  return $firstpage.$strPages.$lastpage;
}


// --------

function ReadValues($str='')
{
  $arrValues = array();
  if ( empty($str) ) return $arrValues;
  $arr = explode(';',$str);
  foreach ($arr as $str)
  {
    $a = explode('=',$str);
    if ( count($a)==2 ) $arrValues[$a[0]]=$a[1];
  }
  return $arrValues;
}

// --------

function TargetDir($strRoot='',$intId=0)
{
  // This check if a directory/subdirectory is available for an Id
  // Returns dir/subdir (without the root)
  // Returns '' if not available

  $strDir = '';
  $intDir = ($intId>0 ? floor($intId/1000) : 0);
  if ( is_dir($strRoot.strval($intDir).'000') )
  {
    $strDir = strval($intDir).'000/';
    $intSDir = $intId-($intDir*1000);
    $intSDir = ($intSDir>0 ? floor($intSDir/100) : 0);
    if ( is_dir($strRoot.$strDir.strval($intDir).strval($intSDir).'00') ) $strDir .= strval($intDir).strval($intSDir).'00/';
  }
  return $strDir;
}

// --------

function ToCsv($str,$strSep=';',$strEnc='"',$strSepAlt=',',$strEncAlt="'")
{
  // Converts a value ($str) to a csv text with final separator [;]. A string is enclosed by ["].
  // When $str contains the separator or the encloser character, they are replaced by the alternates ($strSepAlt,$strEncAlt)
  // TIP: $strSep empty (or "\r\n") to generate a end-line value
  if ( is_int($str) || is_float($str) ) return $str.$strSep;
  if ( $str==='' || is_null($str) ) return $strEnc.$strEnc.$strSep;
  $str = str_replace('&nbsp;',' ',$str);
  $str = str_replace("\r\n",' ',$str);
  $str = html_entity_decode($str,ENT_QUOTES,'UTF-8');
  $str = str_replace($strSep,$strSepAlt,$str);
  $str = str_replace($strEnc,$strEncAlt,$str);
  return $strEnc.$str.$strEnc.$strSep;
}

// --------

function UpdateSectionStats($intS=-1,$arrValues=array())
{
  // Check

  if ( !is_int($intS) || $intS<0) die('UpdateSectionStats: Wrong id');

  // Process (provided values are not recomputed)

  if ( !isset($arrValues['members']) )  $arrValues['members']  = cSection::CountItems($intS,null); //SectionCount('members',$intS);
  if ( !isset($arrValues['membersZ']) ) $arrValues['membersZ'] = cSection::CountItems($intS,'Z'); //SectionCount('membersZ',$intS);
  foreach($arrValues as $strKey=>$strValue) { $arrValues[$strKey]=$strKey.'='.$strValue; }

  // Save

  global $oDB;
  $oDB->Exec('UPDATE '.TABSECTION.' SET stats="'.implode(';',$arrValues).'" WHERE id='.$intS );

  // Return the stat line

  return implode(';',$arrValues);
}

// --------

function UseModule($strName)
{
  QTargs('UseModule',array($strName));
  $mod = GetSetting('module_'.$strName,'',false);
  if ( empty($mod) ) return false; // check only in session variable
  return true;
}