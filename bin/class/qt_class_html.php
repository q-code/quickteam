<?php

/*
class cGetPost is used to handled forms values (e.g. title)

class cHtml is used to build html basic tags <head> and <body>
->Head()     Returns the <head></head> block including the properties
->Body($arr) Returns the <body> including the $arr attributes: supported key-values are id,class,style,title,onload,onunload
->End()      Returns the </body></html>
->Redirect   Perform html redirection
->MsgBox     Show
*/

// ======

class cGetPost
{

public $s = ''; // source string (from the form)
public $t = ''; // trimmed and stripslashed string
public $e = ''; // encoded string (convert <>"' but not &)
public function __construct($str,$size=0,$encode='Q A L R')
{
  $this->s = $str;
  $this->t = trim((get_magic_quotes_gpc() ? stripslashes($str) : $str));
  $this->e = QTencode($this->t,$encode);
  if ( $size>0 ) $this->e = substr($this->e,0,$size);
}

}

// ======

class cHtml
{

public $dtd = '<!DOCTYPE html>'; // default is html 5
public $html = '<html>'; // can be use to include xml attributes (see constructor)
public $title = '';
public $metas = array(); // List of meta declarations. Recommandation: Use the meta 'name' as array key to void double metas when adding a new meta
public $links = array();
public $scripts = array();
public $scripts_end = array();
public $scripts_jq = array();

// ------

function Head()
{

// check loader in case of jquery
if ( !empty($this->scripts_jq) && !isset($this->scripts['jquery']) ) $this->scripts['jquery']=$this->GetJqueryLoader();

// load scripts
$str = implode(PHP_EOL,$this->metas).PHP_EOL.implode(PHP_EOL,$this->links).PHP_EOL.implode(PHP_EOL,$this->scripts);
if ( !empty($this->scripts_jq) )
{
$str .= '
<script type="text/javascript">
function initJQ()
{
'.implode(PHP_EOL,$this->scripts_jq).'
}
</script>';
}

return $this->dtd.PHP_EOL.$this->html.PHP_EOL.'<head>'.PHP_EOL.'<title>'.$this->title.'</title>'.PHP_EOL.$str.PHP_EOL.'</head>'.PHP_EOL;

}

// ------

function Body($arr=array())
{

return '<body'.(isset($arr['id']) ? ' id="'.$arr['id'].'"' : '').(isset($arr['class']) ? ' class="'.$arr['class'].'"' : '').(isset($arr['style']) ? ' style="'.$arr['style'].'"' : '').(isset($arr['title']) ? ' title="'.$arr['title'].'"' : '').(isset($arr['onload']) ? ' onload="'.$arr['onload'].'"' : '').(isset($arr['onunload']) ? ' onunload="'.$arr['onunload'].'"' : '').">\r\n";

}

// ------

function End()
{

return implode(PHP_EOL,$this->scripts_end).PHP_EOL.'</body>'.PHP_EOL.'</html>';

}

// ------

public static function Page($i=1,$extraclass='')
{

switch($i)
{
case 1:  return PHP_EOL.'<!-- START PAGE -->'.PHP_EOL.'<div class="page'.(empty($extraclass) ? '' : ' '.$extraclass).'">'.PHP_EOL.PHP_EOL; break;
case -1: return PHP_EOL.'<!-- END PAGE -->'.PHP_EOL.'</div><div id="pagedialog"></div>'.PHP_EOL.PHP_EOL; break;
default: die('Unknown Html->page value');
}

}

// ------

function Redirect($u='index.php',$s='Continue')
{

if ( headers_sent() )
{
echo '<a href="'.$u.'">',$s,'</a><meta http-equiv="REFRESH" content="0;url='.$u.'">';
}
else
{
header('Location: '.str_replace('&amp;','&',$u));
}
exit;

}

// --------

function CloseWindow()
{

echo '<p><a href="#" onclick="window.close();">'.$L['Close'].' [x]</a></p>
<script type="text/javascript">window.close();</script>
';
exit;

}

// --------

function Msgbox($strTitle='',$arrBox=array(),$arrBoxTitle=array(),$arrBoxBody=array())
{

if ( $strTitle===END ) { echo '</div>',PHP_EOL,'</div>',PHP_EOL; return; }
if ( is_string($arrBox) )
{
  $arr = explode(',',$arrBox);
  $arrBox=array();
  $arrBoxTitle=array();
  $arrBoxBody=array();
  if ( isset($arr[0]) ) $arrBox['class']=$arr[0];
  if ( isset($arr[1]) ) $arrBoxTitle['class']=$arr[1];
  if ( isset($arr[2]) ) $arrBoxBody['class']=$arr[2];
}
if ( !isset($arrBox['class']) )      $arrBox['class']='msgbox';
if ( !isset($arrBoxTitle['class']) ) $arrBoxTitle['class']='msgboxtitle';
if ( !isset($arrBoxBody['class']) )  $arrBoxBody['class']='msgboxbody';
echo '<div'.(isset($arrBox['id']) ? ' id="'.$arrBox['id'].'"' : '').(isset($arrBox['class']) ? ' class="'.$arrBox['class'].'"' : '').(isset($arrBox['style']) ? ' style="'.$arrBox['style'].'"' : '').(isset($arrBox['title']) ? ' title="'.$arrBox['title'].'"' : '').'>'.PHP_EOL;
echo '<div'.(isset($arrBoxTitle['id']) ? ' id="'.$arrBoxTitle['id'].'"' : '').(isset($arrBoxTitle['class']) ? ' class="'.$arrBoxTitle['class'].'"' : '').(isset($arrBoxTitle['style']) ? ' style="'.$arrBoxTitle['style'].'"' : '').(isset($arrBoxTitle['title']) ? ' title="'.$arrBoxTitle['title'].'"' : '').'>'.$strTitle.'</div>'.PHP_EOL;
echo '<div'.(isset($arrBoxBody['id']) ? ' id="'.$arrBoxBody['id'].'"' : '').(isset($arrBoxBody['class']) ? ' class="'.$arrBoxBody['class'].'"' : '').(isset($arrBoxBody['style']) ? ' style="'.$arrBoxBody['style'].'"' : '').(isset($arrBoxBody['title']) ? ' title="'.$arrBoxBody['title'].'"' : '').'>';

}

// --------
// PageBox is replaced by PageMsg()
function PageBox($strTitle,$strMessage='Access denied',$strSkin='admin',$intTime=0,$strWidth='300px',$strTitleId='msgboxtitle',$strBodyId='msgbox') { $this->PageMsg($strTitle,$strMessage,$intTime,$strWidth,$strTitleId,$strBodyId,'',$strSkin); }
function PageMsgAdm($strTitle,$strMessage='Access denied',$intTime=0,$strWidth='300px',$strTitleId='msgboxtitle',$strBodyId='msgbox',$root='') { $this->PageMsg($strTitle,$strMessage,$intTime,$strWidth,$strTitleId,$strBodyId,$root,'admin'); }
function PageMsg($strTitle,$strMessage='Access denied',$intTime=0,$strWidth='300px',$strTitleId='msgboxtitle',$strBodyId='msgbox',$root='',$strSkin='')
{

global $oVIP;
if ( empty($strTitle) ) $strTitle = $oVIP->selfname;
if ( empty($strSkin) && !empty($_SESSION[QT]['skin_dir']) ) $strSkin = $_SESSION[QT]['skin_dir'];
if ( empty($strSkin) ) $strSkin = $root.'admin';

$this->links   = array();
$this->links[] = '<link rel="shortcut icon" href="'.$strSkin.'/'.APP.'_icon.ico" />';
$this->links[] = '<link rel="stylesheet" type="text/css" href="'.$strSkin.'/qt_base.css" />';
$this->links[] = '<link rel="stylesheet" type="text/css" href="'.$strSkin.'/'.APP.'_layout.css" />';
$this->links[] = '<link rel="prev" id="exiturl" href="'.$oVIP->exiturl.'" />';

echo $this->Head();
echo $this->Body();
echo cHtml::Page(1,'pagemsg');

// in case of error code
if ( is_int($strTitle) )
{
  $e = $strTitle;
  if ( $e==99 )
  {
  $strFile = $root.Translate('sys_offline.txt',false);
  if ( file_exists($strFile) ) { $strMessage=file_get_contents($strFile); } else { $strMessage=Error(10); }
  }
  else
  {
  $strMessage=Error($e);
  }
  $strTitle = '!';
}
// display message box
$this->Msgbox($strTitle,array('style'=>'width:'.$strWidth),array('id'=>$strTitleId),array('id'=>$strBodyId));
echo $strMessage,'
<p><a id="exiturl" href="',Href($oVIP->exiturl),'">',$oVIP->exitname,'</a></p>';
$this->Msgbox(-1);
echo cHtml::Page(-1);

if ( $intTime>0 )
{
echo '
<script type="text/javascript">if ( document.getElementById("exiturl") ) setTimeout(\'window.location=document.getElementById("exiturl").href\',',($intTime*1000),');</script>
';
}

echo $this->End();
exit;

}

// ------

public function GetJqueryLoader()
{

return '<script type="text/javascript" src="bin/js/modernizr.custom.js"></script>
<script type="text/javascript">
Modernizr.load([
{
both:["'.JQUERY_CDN.'","'.JQUERYUI_CDN.'","'.JQUERYUI_CSS_CDN.'"],
complete:function(){if(!window.jQuery){Modernizr.load([{both:["'.JQUERY_OFF.'","'.JQUERYUI_OFF.'","'.JQUERYUI_CSS_OFF.'"],complete:function(){initJQ();}}]);}else{initJQ();}
}
}]);
</script>';

}

// ------

public function DropJquery()
{

if ( isset($this->scripts['jquery']) ) unset($this->scripts['jquery']);
if ( isset($this->scripts['jqueryui']) ) unset($this->scripts['jqueryui']);
if ( isset($this->links['jqueryui']) ) unset($this->links['jqueryui']);

}

// ------

}