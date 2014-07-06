<?php

/**
 * PHP version 5
 *
 * LICENSE: This source file is subject to version 3.0 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @package    QuickTeam
 * @author     Philippe Vandenberghe <info@qt-cute.org>
 * @copyright  2014 The PHP Group
 * @version    3.0 build:20140608
 */

session_start();
require_once 'bin/qte_init.php';
$oHtml->links['css'] = '<link rel="stylesheet" type="text/css" href="'.$_SESSION[QT]['skin_dir'].'/qte_index.css" />';
if ( !sUser::CanView('V3') ) die(Error(11));

// --------
// INITIALISE
// --------

$oVIP->selfurl = 'qte_search.php';
$oVIP->selfname = $L['Search'];

$s = -1;
if ( isset($_GET['s']) ) $s = (int)strip_tags($_GET['s']);
if ( $s<0 && isset($_SESSION[QT]['section']) ) $s = (int)$_SESSION[QT]['section'];

// --------
// HTML START
// --------

$oHtml->scripts[] = '<script type="text/javascript">
function split( val ) { return val.split( "'.QT_QUERY_SEPARATOR.'" ); }
function extractLast( term ) { return split( term ).pop().replace(/^\s+/g,"").replace(/\s+$/g,""); }

function ValidateForm(theForm)
{
  if ( theForm.id=="kwd_form" ) if (document.getElementById("kwd_v").value.length==0) { alert("Text - "+qtHtmldecode("'.$L['Missing'].'")); return false; }
  if ( theForm.id=="age_form" )
  {
  if (document.getElementById("date").value.length==0) { alert("Date - "+qtHtmldecode("'.$L['Missing'].'")); return false; }
  if (document.getElementById("date").value.length==0) { alert("Date - "+qtHtmldecode("'.$L['Missing'].'")); return false; }
  }
  return null;
}
function ShowAge(str)
{
  if ( document.getElementById("age_v") )
  {
    if ( str=="0" ) { document.getElementById("age_v").style.display="none"; return; }
    document.getElementById("age_v").style.display="inline";
  }
}
</script>
';
$arrFields = GetFields('all');
$arrNames = array(); foreach($arrFields as $str) $arrNames[] = ObjTrans('field',$str);

$oHtml->scripts_end[] = '<script type="text/javascript">
var doc = document;
var arrS = new Array("'.implode('","',$arrFields).'");
var arrT = new Array("'.implode('","',$arrNames).'");
qtFocusEnd("kwd_v");
if ( doc.getElementById("kwd_v") )
{
  if ( doc.getElementById("kwd_v").value.length>0 ) qtFocusEnd("kwd_v");
}

function fldTranslate(src)
{
  var i = arrS.indexOf(src);
	if ( i<0 ) return src;
	if ( arrT[i]===undefined ) return src;
	return arrT[i];
}
</script>
';
$oHtml->scripts_jq[] = '
var e0 = "'.L('No_result').'";
var e1 = "'.L('try_other_lettres').'";
var e2 = "'.L('try_all_teams').'";
var e3 = "'.L('All_categories').'";
var e4 = "'.L('Impossible').'";
$(function() {
  $( "#kwd_v" ).autocomplete({
    minLength: 2,
    source: function(request, response) {
      $.ajax({
        url: "qte_j_index.php",
        dataType: "json",
        data: { term: request.term, s:function() { return $("#kwd_s").val(); }, e0: e0, e1: e1 },
        success: function(data) { response(data); }
      });
    },
    select: function( event, ui ) {
      $( "#kwd_v" ).val( ui.item.rItem );
      return false;
    }
  })
  .data( "ui-autocomplete" )._renderItem = function( ul, item ) {
    return $( "<li>" )
      .data( "item.autocomplete", item )
      .append( "<a class=\"jvalue\">" + (item.rInfo=="*" ? "Type: "+item.rItem : item.rItem ) + (item.rInfo=="" || item.rInfo=="*"  ? "" : " &nbsp;<span class=\"jinfo\">(" + fldTranslate(item.rInfo) + ")</span>") + "</a>" )
      .appendTo( ul );
  };
});
';

include 'qte_inc_hd.php';

// advanced search

if ( isset($_GET['a']) || isset($_GET['s']) || isset($_SESSION[QT]['section']) || isset($_GET['status']) || isset($_GET['role']) || isset($_GET['age']))
{
  if ( isset($_GET['status']) ) { $strStatus = strip_tags($_GET['status']); } else { $strStatus = null; }
  if ( isset($_GET['age_criteria']) ) { $strCriteria = strip_tags($_GET['age_criteria']); } else { $strCriteria = null; }

  echo '<h2>',$L['Search_by_key'],'</h2>
  <form id="kwd_form" method="get" action="',Href('qte_section.php'),'" onsubmit="return ValidateForm(this);">
  <table class="t-sec">
  <tr>
  <td class="c-icon">',AsImg($_SESSION[QT]['skin_dir'].'/ico_section_search.gif','search',$L['Search'],'ico i-sec'),'</td>
  <td><input type="text" id="kwd_v" name="v" size="30" maxlength="32" value="',(isset($_GET['v']) ? $_GET['v'] : ''),'" /> ',$L['In_section'],'&nbsp;<select id="kwd_s" name="s" size="1"><option value="-1"',($s<0 ? QSEL : ''),'>'.$L['All'].'</option>',Sectionlist($s),'</select></td>
  <td class="right"><input type="hidden" name="q" value="kwd"/><input type="submit" id="kwd_ok" name="ok" value="',$L['Search'],'"/></td>
  </tr>
  </table>
  </form>
  ';
  echo '<h2>',$L['Search_by_status'],'</h2>
  <form id="sta_form" method="get" action="',Href('qte_section.php'),'">
  <table class="t-sec">
  <tr>
  <td class="c-icon">',AsImg($_SESSION[QT]['skin_dir'].'/ico_section_search.gif','search',$L['Search'],'ico i-sec'),'</td>
  <td>'.$L['Status'],'&nbsp;<select id="sta_v" name="v" size="1">',QTasTag($oVIP->statuses,$strStatus),'</select> ',$L['In_section'],'&nbsp;<select id="sta_s" name="s" size="1"><option value="-1"',($s<0 ? QSEL : ''),'>'.$L['All'].'</option>',Sectionlist($s),'</select></td>
  <td class="right"><input type="hidden" name="q" value="sta"/><input type="submit" id="sta_ok" name="ok" value="',$L['Search'],'"/></td>
  </tr>
  </table>
  </form>
  ';
  echo '<h2>',$L['Search_by_age'],'</h2>
  <form id="age_form" method="get" action="',Href('qte_section.php'),'">
  <table class="t-sec">
  <tr>
  <td class="c-icon">',AsImg($_SESSION[QT]['skin_dir'].'/ico_section_search.gif','search',$L['Search'],'ico i-sec'),'</td>
  <td>', ObjTrans('field','age').'&nbsp;<select id="age_w" name="w" size="1" onchange="ShowAge(this.value);">',QTasTag(array('l'=>'&lt;','u'=>'&gt;=','e'=>'=','0'=>L('Undefined')),$strCriteria),'</select>
  <input type="text" id="age_v" name="v" size="2" maxlength="2" value="',(isset($_GET['ag_v']) ? $_GET['ag_v'] : QTE_SEARCH_AGE),'" /> ',$L['In_section'],'&nbsp;<select id="age_s" name="s" size="1"><option value="-1"',($s<0 ? QSEL : ''),'>'.$L['All'].'</option>',Sectionlist($s),'</select></td>
  <td class="right"><input type="hidden" name="q" value="age"/><input type="submit" id="age_ok" name="ok" value="',$L['Search'],'"/></td>
  </tr>
  </table>
  </form>
  <div class="right">
  ';
  if ( sUser::IsStaff() )
  {
  	echo '<form id="adv_form" method="get" action="',Href('qte_section.php'),'">',PHP_EOL;
  	echo '<a href="',Href('qte_section.php'),'?q=role&amp;v=S">',$L['Userrole_Ms'],'</a> &middot; ';
  	echo L('Administration').'&nbsp;<select id="adv_q" size="1" name="q" onchange="document.getElementById(\'adv_form\').submit();"><option value="" selected="selected"></option>';
    echo '<option value="uwt">',L('Users_without_section'),'</option>';
    $str = (isset($_SESSION[QT]['sys_sections'][0]) ? $_SESSION[QT]['sys_sections'][0] : 'team 0');
    echo '<option value="ui0">',sprintf(L('Users_in_0_only'),$str),'</option>';
    echo '</select>',PHP_EOL;
  	echo '<input type="submit" id="adv_ok" name="ok" value="ok"><script type="text/javascript">document.getElementById("adv_ok").style.display="none";</script></form>',PHP_EOL;
  }
  else
  {
  	echo '<a href="',Href('qte_section.php'),'?q=role&amp;v=S">',$L['Userrole_Ms'],'</a>';
  }
  echo '</div>'.PHP_EOL;
}
else // simple search
{
  echo '<h2>',$L['Search_by_key'],'</h2>',PHP_EOL;
  echo '<form id="kwd_form" method="get" action="',Href('qte_section.php'),'" onsubmit="return ValidateForm(this);">',PHP_EOL;
  echo '<table class="t-sec">',PHP_EOL;
  echo '<tr>',PHP_EOL;
  echo '<td class="c-icon">',AsImg($_SESSION[QT]['skin_dir'].'/ico_section_search.gif','search',$L['Search'],'ico i-sec'),'</td>',PHP_EOL;
  echo '<td><input type="text" id="kwd_v" name="v" size="30" maxlength="32" value="',(isset($_GET['v']) ? $_GET['v'] : ''),'" /> <input type="hidden" name="q" value="kwd"/><input type="hidden" id="kwd_s" name="s" value="-1"/><input type="submit" id="kwd_ok" name="ok" value="',$L['Search'],'"/></td>
  <td class="right"><a id="advanced" href="',Href(),'?a=1&amp;title=" onclick="document.getElementById(\'advanced\').href += document.getElementById(\'title\').value;">',$L['Advanced'],'...</a>&nbsp;</td>
  </tr>
  </table>
  </form>
  ';
}

// --------
// HTML END
// --------

include 'qte_inc_ft.php';