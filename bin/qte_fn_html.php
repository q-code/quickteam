<?php // QuickTeam 3.0 build:20140612

function Sectionlist($selected=-1,$arrReject=array(),$arrDisabled=array(),$strAll='')
{
  // attention $selected is type-sensitive (uses '*' or [int])
  // if $strAll is not empty, the list includes in first position an 'all' option having the value '*' and the label $strAll
  if ( is_int($arrReject) || is_string($arrReject) ) $arrReject = array((int)$arrReject);
  if ( is_int($arrDisabled) || is_string($arrDisabled) ) $arrDisabled = array((int)$arrDisabled);
  QTargs('Sectionlist',array($arrReject,$arrDisabled,$strAll),array('arr','arr','str'));

  $str = '';
  $arrSections = memGet('sys_sections');

  if ( !empty($strAll) ) $str ='<option value="*"'.($selected==='*' ? QSEL : '').(in_array('*',$arrDisabled,true) ? ' disabled="disabled"': '').'>'.(strlen($strAll)>20 ? substr($strAll,0,19).'...' : $strAll).'</option>';

  if ( is_array($arrSections) && isset($arrSections[0]) )
  {

    // reject
    $arr = arrSections;
    if ( count($arrReject)>0 ) { foreach($arrReject as $id) if ( isset($arr[$id]) ) unset($arr[$id]); }

    // format
    $arrDomains = memGet('sys_domains');
    if ( count($arr)>3 && count($arrDomains)>1 )
    {
      $arr = GetSections(sUser::Role(),-2,$arrReject);// get all sections at once (grouped by domain)
      foreach ($arrDomains as $intDom=>$strDom)
      {
        if ( isset($arr[$intDom]) ) {
        if ( count($arr[$intDom])>0 ) {
          $str .= '<optgroup label="'.(strlen($strDom)>20 ? substr($strDom,0,19).'...' : $strDom).'">';
          foreach($arr[$intDom] as $id=>$row) $str .= '<option value="'.$id.'"'.($id===$selected ? QSEL : '').(in_array($id,$arrDisabled,true) ? ' disabled="disabled"': '').'>'.(strlen($row['title'])>20 ? substr($row['title'],0,19).'...' : $row['title']).'</option>';
          $str .= '</optgroup>';
        }}
      }
    }
    else
    {
      foreach($arr as $id=>$strSection) $str .= '<option value="'.$id.'"'.($id===$selected ? QSEL : '').(in_array($id,$arrDisabled,true) ? ' disabled="disabled"': '').'>'.$strSection.'</option>';
    }

  }
  return $str;
}

// --------

function EchoPage($content='Page not defined')
{
if ( !is_string($content) && !is_int($content) ) die('EchoPage: invalid argument');

global $oVIP,$oHtml,$L;
$oVIP->selfurl='qte_index.php';
$oVIP->exiturl='qte_index.php';
include 'qte_inc_hd.php';

if ( is_int($content) )
{
  $oHtml->Msgbox('!');
  if ( $content===99 )
  {
    $content = Translate('sys_offline.txt',false);
    if ( file_exists($content) ) { include $content; } else { echo Error(99); }
  }
  else
  {
    echo Error($content);
  }
  $oHtml->Msgbox(END);
}
else
{
  echo $content;
}

include 'qte_inc_ft.php';
exit;

}

// --------

function EchoBanner($logo='',$langMenu='',$mainMenu='',$class='banner')
{
  echo '<!-- banner -->',PHP_EOL;
  echo '<div class="',$class,'">',PHP_EOL;
  if ( !empty($logo) ) echo '<img class="logo" src="',$logo,'" alt="',$_SESSION[QT]['site_name'],'" title="',$_SESSION[QT]['site_name'],'"/>',PHP_EOL;
  if ( !empty($langMenu) ) echo $langMenu,PHP_EOL;
  if ( !empty($mainMenu) ) echo $mainMenu,PHP_EOL;
  echo '</div>',PHP_EOL;
}

// --------

function HtmlLettres($baseUrl='',$strGroup='all',$strAll='All',$strClass='lettres',$strTitle='',$intSize=1,$bFilterForm=true)
{
  // $strGroup is the current group, $strAll is the label of the 'all' group
  if ( empty($baseUrl) ) $baseUrl=Href();
  if ( !strpos($baseUrl,'?') ) $baseUrl .='?';
  $or = ' '.L('or').' ';
  switch($intSize)
  {
  case 1: $arr = explode('.','A.B.C.D.E.F.G.H.I.J.K.L.M.N.O.P.Q.R.S.T.U.V.W.X.Y.Z'); break;
  case 2: $arr = explode('.','A|B.C|D.E|F.G|H.I|J.K|L.M|N.O|P.Q|R.S|T.U|V.W|X.Y|Z'); break;
  case 3: $arr = explode('.','A|B|C.D|E|F.G|H|I.J|K|L.M|N|O.P|Q|R.S|T|U.V|W.X|Y|Z'); break;
  case 4: $arr = explode('.','A|B|C|D.E|F|G|H.I|J|K|L.M|N|O|P.Q|R|S|T.U|V|W.X|Y|Z'); break;
  }
  $str = '<a class="primary"'.($strGroup==='all' ? ' id="active"' : '').' href="'.($strGroup==='all' ? 'javascript:void(0)' : $baseUrl.'&amp;group=all').'">'.$strAll.'</a>';
  foreach($arr as $g)
  {
  $str .= '<a'.($strGroup===$g ? ' id="active"' : '').' href="'.($strGroup===$g ? 'javascript:void(0)' : $baseUrl.'&amp;group='.$g).'"'.( empty($strTitle) ? '' : ' title="'.$strTitle.str_replace('|',$or,$g).'"' ).'>'.str_replace('|','',$g).'</a>';
  }
  $str .= '<a class="primary"'.($strGroup==='0' ? ' id="active"' : '').' href="'.($strGroup==='0' ? 'javascript:void(0)' : $baseUrl.'&amp;group=0').'"'.( empty($strTitle) ? '' : ' title="'.$strTitle.L('Other_char').'"' ).'>#</a>';

  $strGroups  = '<div class="'.$strClass.'">';
  $strGroups .= '<span class="label">'.L('Show').'</span>'.$str;
  if ( $bFilterForm )   $strGroups .= '<form method="get" action="'.$baseUrl.'"><input required type="text" class="'.$strClass.'" value="'.($strGroup=='all' || in_array($strGroup,$arr) ? '' : $strGroup).'" name="group" maxlength="7" title="'.$strTitle.'"/><input type="submit" class="search" value=""/>'.QTuritoform($baseUrl,true,'page,group').'</form>';
  $strGroups .= '</div>';

  return $strGroups;
}

// --------

function HtmlTabs($arrTabs=array(0=>'Empty'),$strUrl='',$keyCurrent=0,$intMax=6,$strWarning='Data not yet saved. Quit without saving?')
{

// tabx means the last tab (can be special due to popup)
// if defined, the class/style tab_on replaces the class/style tab (but you can cumulate the classes in the definition)
// if defined, the class/style tabx_on replaces the class/style tabx (but you can cumulate the styles in the definition)
// When strCurrent is defined, this tab will not be clickable
// $arrTabs can be an array of: strings, arrays, cTab

// check

if ( !is_array($arrTabs) ) die('HtmlTabs: Argument #1 must be an array');
if ( !empty($strUrl) ) { if ( !strstr($strUrl,'?') ) $strUrl .= '?'; }

// check current (if not found or not set, uses the first as current)

if ( !isset($arrTabs[$keyCurrent]) ) { $arr=array_keys($arrTabs); $keyCurrent=$arr[0]; }

// display

$strOuts='';
$strOut='';
$intCol=0;

foreach($arrTabs as $key=>$oTab)
{
  ++$intCol;
  $strTab = '';
  $strTabDesc = '';

    if ( is_string($oTab) )
    {
      $strTab = $oTab;
    }
    elseif ( is_array($oTab) )
    {
      if ( isset($oTab['tabdesc']) )
      {
        if ( !empty($oTab['tabdesc']) ) $strTabDesc = $oTab['tabdesc'];
      }
      if ( isset($oTab['tabname']) )
      {
        if ( !empty($oTab['tabname']) ) { $strTab=$oTab['tabname']; } else { $strTab=ObjTrans('tab',$key); }
      }
      else
      {
        $strTab=ObjTrans('tab',$key);
      }
    }
    elseif ( is_a($oTab,'ctab') )
    {
      $strTabDesc = $oTab->tabdesc;
      $strTab = $oTab->tabname; if ( empty($strTab) ) $strTab = $oTab->tabid;
    }
    else
    {
      die('HtmlTabs: Arg #1 must be an array of strings, arrays or cTab');
    }

    $strOut .= '<li'.( $keyCurrent===$key ? ' class="active"' : '').'>';
    if ( empty($strUrl) || $keyCurrent===$key )
    {
      $strOut .=  $strTab;
    }
    else
    {
      $strOut .=  '<a href="'.$strUrl.'&amp;tt='.$key.'"'.(empty($strTabDesc) ? '' : ' title="'.$strTabDesc.'"').' onclick="return qtEdited(bEdited,\''.$strWarning.'\');">'.$strTab.'</a>';
    }
    $strOut .= '</li>'.PHP_EOL;

  if ( $intCol>=count($arrTabs) )
  {
    $strOuts = '<ul>'.PHP_EOL.$strOut.'</ul>'.$strOuts;
    break;
  }
  if ( $intCol>=$intMax )
  {
    $strOuts = '<ul>'.PHP_EOL.$strOut.'</ul>'.$strOuts;
    $intCol=0;
  }
}

return '
<!-- tab header -->
<div class="tab">'.$strOuts.'</div>
<!-- tab header end -->
';

}