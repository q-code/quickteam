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
 * @version    3.0 build:20141222
 */

session_start();
require 'bin/qte_init.php';
if ( !sUser::CanView('V4') ) { $oHtml->PageMsg(11); return; }
$oHtml->links['css'] = '<link rel="stylesheet" type="text/css" href="'.$_SESSION[QT]['skin_dir'].'/qte_profile.css" />';

$id = -1;
$tt = 'p';
QThttpvar('id tt','int str');
if ( $id<0 ) die('Wrong id in '.$oVIP->selfurl);
if ( isset($_GET['edit']) ) $_SESSION[QT]['editing']=($_GET['edit']=='1' ? true : false);
if ( isset($_POST['edit']) ) $_SESSION[QT]['editing']=($_POST['edit']=='1' ? true : false);

// --------
// FUNCTION
// --------

function InputFormat($strField,$aValue,$bCanBeList=false,$intSize=30,$intMaxlength=32)
{
  QTargs('InputFormat',array($strField,$bCanBeList,$intSize,$intMaxlength),array('str','boo','int','int')); //$aValue can be null or integer
  if ( $bCanBeList )
  {
    $arr = AsList(ObjTrans('ffield',$strField),false,',',20,'');
    if ( count($arr)>1 )
    {
      $arrResult = array();
      $arrResult[' '] = ' ';
      foreach ($arr as $strValue)
      {
        $str = trim($strValue);
        $arrResult[$str] = $str;
      }
      $arr = array_unique($arrResult);
      // if aValue is an other value, add this to the list as first item
      if ( !in_array($aValue,$arr) ) $arr = array($aValue=>$aValue) + $arr;
      return '<select class="profile" id="'.$strField.'" name="'.$strField.'" onchange="bEdited=true;">'.QTasTag($arr,$aValue).'</select>';
    }
  }
  return '<input class="profile" type="text" id="'.$strField.'" name="'.$strField.'" size="'.$intSize.'" maxlength="'.$intMaxlength.'" value="'.$aValue.'" onchange="bEdited=true;" />';
}

// --------
// INITIALISE
// --------

include Translate(APP.'_reg.php');

$oVIP->selfurl = 'qte_user.php';
$oVIP->selfname = $L['Profile'];

// check if editmode was started (or start now)

$bCanEdit = false;
if ( sUser::Id()==$id || sUser::IsStaff() ) $bCanEdit=true;
if ( $id==0 ) $bCanEdit=false;
if ( sUser::Id()!=$id && !sUser::IsStaff() ) $bCanEdit=false;
if ( sUser::Id()==$id && !sUser::IsStaff() && $_SESSION[QT]['member_right']=='0' ) $bCanEdit=false;
if ( !isset($_SESSION[QT]['editing']) || !$bCanEdit) $_SESSION[QT]['editing']=false;

// QUERY USER

$oItem = new cItem($id,true); // privatise

$strOldname = $oItem->firstname.' '.$oItem->lastname;
$arrTabs = array('p'=>$L['PProfile'],'t'=>$L['TProfile'],'m'=>$L['MProfile'],'d'=>$L['DProfile'],'s'=>$L['SProfile']);
$arrFLD = array();
if ($tt=='p') $arrFLD = GetFLDs('title;firstname;midname;lastname;alias;nationality;sexe;status;birthdate;address;phones;emails;www');
if ($tt=='t') $arrFLD = GetFLDs('teamid1;teamid2;teamrole1;teamrole2;teamdate1;teamdate2;teamvalue1;teamvalue2;teamflag1;teamflag2;descr');

$strPosition='';

// MAP MODULE (only for tab 'p')

$bMap=false;
if ( $tt=='p' && UseModule('map') )
{
  include Translate('qtem_map.php');
  include 'qtem_map_lib.php';
  $bMap=true;
  if ( $_SESSION[QT]['editing'] || !QTgemptycoord($oItem) ) $oHtml->links[]='<link rel="stylesheet" type="text/css" href="qtem_map.css" />';
  if ( isset($_GET['hidemap']) ) $_SESSION[QT]['m_map_hidelist']=true;
  if ( isset($_GET['showmap']) ) $_SESSION[QT]['m_map_hidelist']=false;
  if ( !isset($_SESSION[QT]['m_map_hidelist']) ) $_SESSION[QT]['m_map_hidelist']=false;
}

// --------
// HTML START
// --------

// scripts in case of document edit

$bCanUpload = false;
if ( $tt=='d' && $_SESSION[QT]['editing'] && ( $_SESSION[QT]['upload']=='1' || sUser::IsStaff() ) )
{
  $bCanUpload = true;
  $oHtml->scripts[] = '<script type="text/javascript" src="bin/js/qte_table.js"></script>
  <script type="text/javascript">
  function datasetcontrol_click(checkboxname,action)
  {
    var checkboxes = document.getElementsByName(checkboxname);
    var n = 0;
    for (var i=0; i<checkboxes.length; ++i) if ( checkboxes[i].checked ) ++n;
    if ( n>0 )
    {
    var doc = document.getElementById("form_docs");
    doc.form_docs_action.value=action;
    doc.submit();
    return;
    }
    else
    {
    alert(qtHtmldecode("'.L('No_selected_row').'"));
    return false;
    }
  }
  </script>';
  $oHtml->scripts_jq[] = '
  var doc = document.getElementById("draganddrop");
  if (doc)
  {
  if (navigator.userAgent.toLowerCase().indexOf("firefox") != -1) doc).style.display="inline";
  if (navigator.userAgent.toLowerCase().indexOf("opera") != -1) doc.style.display="inline";
  if (navigator.userAgent.toLowerCase().indexOf("chrome") != -1) doc.style.display="inline";
  }
  // jquery check file upload

  $(function(){
    $("#fileselect").change(function(){
        var $fileUpload = $("#fileselect");
        if (parseInt($fileUpload.get(0).files.length)>5) { alert("You can only upload a maximum of 5 files"); return false; }
    });
    $("#upload").click(function(){
        var $fileUpload = $("#fileselect");
        if (parseInt($fileUpload.get(0).files.length)>5) { alert("You can only upload a maximum of 5 files"); return false; }
    });

    $("#t1 td:not(.c-checkbox)").click(function() { qtCheckboxToggle(this.parentNode.id.substring(3)); });

    // SHIFT-CLICK CHECKBOX

    var lastChecked1 = null;
    var lastChecked2 = null;
    $("input[name=\'t1_cb[]\']").click(function(event) {
      if(!lastChecked1)
      {
        lastChecked1 = this;
        qtHighlight("tr_"+this.id,this.checked);
        return;
      }
      if(event.shiftKey)
      {
        var start = $("input[name=\'t1_cb[]\']").index(this);
        var end = $("input[name=\'t1_cb[]\']").index(lastChecked1);
        for(var i=Math.min(start,end);i<=Math.max(start,end);++i)
        {
        $("input[name=\'t1_cb[]\']")[i].checked = lastChecked1.checked;
        qtHighlight("tr_"+$("input[name=\'t1_cb[]\']")[i].id,lastChecked1.checked);
        }
      }
      lastChecked1 = this;
      qtHighlight("tr_"+this.id,this.checked);
    });

  });
  ';
}

// Content header

include 'qte_inc_hd.php';

if ( isset($bCanEdit) && $bCanEdit ) echo '<div class="profilecmd">',( $_SESSION[QT]['editing'] ? '<a class="button" href="'.Href($oVIP->selfurl).'?id='.$id.'&amp;tt='.$tt.'&amp;edit=0">'.$L['Edit_stop'].'</a>' : '<a class="button" href="'.Href($oVIP->selfurl).'?id='.$id.'&amp;tt='.$tt.'&amp;edit=1">'.$L['Edit_start'].'</a>'),'</div>';
echo '<h2>',UserFirstLastName($oItem,' ',$oItem->username),'</h2>',PHP_EOL;

// Profile table

echo '<table class="profile">
<tr>
<td class="profileleft">
';

//echo  AsImgBoxUser($oItem);
echo UserPicture($oItem);

// message area

if ( $_SESSION[QT]['editing'] && sUser::Id()!=$id ) echo '<div class="warning">',$L['W_Somebody_else'],'</div>';

// edit menu

if ( $bCanEdit )
{
  if ( $_SESSION[QT]['picture']!='0' )
  {
  echo '<p><a href="',Href('qte_user_img.php'),'?id=',$id,'">',$L['Change_picture'],'</a></p>';
  }
  echo '<p><a href="',Href('qte_user_pwd.php'),'?id=',$id,'">',$L['Change_password'],'</a></p>';
  echo '<p><a href="',Href('qte_user_question.php'),'?id=',$id,'">',$L['Secret_question'],'</a></p>';
  if ( sUser::IsStaff() )
  {
  echo '<hr/>';
  echo '<p class="small disabled">'.$L['Userrole_'.sUser::Role()].':</p>';
  echo '<p><a href="'.Href('qte_change.php').'?a=pwdreset&amp;u='.$id.'">'.L('Reset_pwd').'</a></p>';
  if ( $id>1 )
  {
  echo '<p><a href="'.Href('qte_change.php').'?a=userrole&amp;u='.$id.'">'.L('Change_role').'</a></p>';
  echo '<p><a href="'.Href('qte_change.php').'?a=deleteuser&amp;u='.$id.'">'.L('Delete').' '.L('user').'</a></p>';
  }
  }
}

// birthdays calendar
if ( cVIP::CanViewCalendar() )
{
  echo '<hr/><p><a href="',Href('qte_calendar.php'),(empty($oItem->birthdate) ? '' : '?m='.substr($oItem->birthdate,4,2)),'">',$L['Birthdays_calendar'],'</a></p>';
}

echo '</td>
<td class="profileright">
';

//''if ( $_SESSION[QT]['editing'] ) echo '<form method="post" action="',Href('qte_user.php'),'?id='.$id.'&amp;tt=',$tt,'">',PHP_EOL;

echo '<div class="userprofile">
';

// DISPLAY TAB PANEL

echo HtmlTabs($arrTabs,Href().'?id='.$id,$tt);

echo '
<!-- pan -->
<div class="pan">
';

switch($tt)
{
case 't': include 'qte_user_t.php'; break;
case 'd': if ( sUser::Role()==='V' ) { include 'qte_user_na.php'; } else { include 'qte_user_d.php'; } break;
case 'm': include 'qte_user_m.php'; break;
case 's': if ( sUser::Role()==='V' ) { include 'qte_user_na.php'; } else { include 'qte_user_s.php'; } break;
default:  include 'qte_user_p.php';
}

echo '
</div>
<!-- pan end -->

';

// show map (only in profile panel)

if ( $tt=='p' && $bMap )
{
	if ( $_SESSION[QT]['editing'] )
	{
		$strPosition = '<p class="commands" style="margin:2px 0 4px 2px;text-align:right">'.$L['map_cancreate'];
    if ( !QTgemptycoord($oItem) )
		{
			$_SESSION[QT]['m_map_gcenter'] = $oItem->y.','.$oItem->x;
			$strPosition = '<p class="gmap commands" style="margin:2px 0 4px 2px;text-align:right">'.$L['map_canmove'];
		}
		$strPosition .= ' | <a class="gmap" href="javascript:void(0)" onclick="createMarker(); return false;" title="'.$L['map_H_pntadd'].'">'.$L['map_pntadd'].'</a>';
		$strPosition .= ' | <a class="gmap" href="javascript:void(0)" onclick="deleteMarker(); return false;">'.$L['map_pntdelete'].'</a>';
		$strPosition .= '</p>'.PHP_EOL;
		$strPosition .= '<div id="map_canvas"></div>'.PHP_EOL;
		$strPosition .= '<p class="gmap commands" style="margin:4px 0 2px 2px;text-align:right">'.$L['map_addrlatlng'].' ';
		$strPosition .= '<input type="text" size="24" id="find" name="find" class="small" value="'.$_SESSION[QT]['m_map_gfind'].'" title="'.$L['map_H_addrlatlng'].'" onkeypress="enterkeyPressed=qtKeyEnter(event); if (enterkeyPressed) showLocation(this.value,null);"/>';
		$strPosition .= '<img id="findit" src="qtem_map_find.png" onclick="showLocation(document.getElementById(\'find\').value,null);" style="margin:0 0 0 2px;padding:2px;vertical-align:middle;width:16px;height:16px;border:solid 1px #cccccc;border-radius:3px;cursor:pointer" title="'.L('Search').'"/>';
		echo '<div class="gmap" style="margin-top:10px">',$strPosition,'</div>';
	}
  elseif ( count($arrExtData)>0 )
	{
		$_SESSION[QT]['m_map_gcenter'] = $oItem->y.','.$oItem->x;
		$strPosition .= '<div id="map_canvas"></div>'.PHP_EOL;
		$strPosition .= '<p class="gmap commands" style="margin:4px 0 2px 2px;text-align:right">'.$L['map_addrlatlng'].' ';
		$strPosition .= '<input type="text" size="24" id="find" name="find" class="small" value="'.$_SESSION[QT]['m_map_gfind'].'" title="'.$L['map_H_addrlatlng'].'" onkeypress="enterkeyPressed=qtKeyEnter(event); if (enterkeyPressed) showLocation(this.value,null);"/>';
		$strPosition .= '<img id="findit" src="qtem_map_find.png" onclick="showLocation(document.getElementById(\'find\').value,null);" style="margin:0 0 0 2px;padding:2px;vertical-align:middle;width:16px;height:16px;border:solid 1px #cccccc;border-radius:3px;cursor:pointer" title="'.L('Search').'"/>';
		echo '<div class="gmap" style="margin-top:10px">',$strPosition,'</div>';
	}
  else
  {
    $bMap = false; // map is skipped because not edit-mode and no coordinates
  }
}

// End div userprofile

echo '</div>',PHP_EOL;

// End td profileright

echo '</td>
</tr>
</table>
';

// --------
// HTML END
// --------

// map settings only in tab 'p' (note: $bMap=false if not editing and no coordinates)
if ( $tt=='p' && $bMap )
{
  $gmap_shadow = false;
  $gmap_symbol = false;
  if ( !empty($_SESSION[QT]['m_map_gsymbol']) )
  {
    $arr = explode(' ',$_SESSION[QT]['m_map_gsymbol']);
    $gmap_symbol=$arr[0];
    if ( isset($arr[1]) ) $gmap_shadow=$arr[1];
  }

  // check new map center
  $y = floatval(QTgety($_SESSION[QT]['m_map_gcenter']));
  $x = floatval(QTgetx($_SESSION[QT]['m_map_gcenter']));

  // First item is the user's location and symbol
  if ( isset($arrExtData[$id]) )
  {
    // symbol by role
    $oMapPoint = $arrExtData[$id];
    if ( !empty($oMapPoint->icon) ) $gmap_symbol = $oMapPoint->icon;
    if ( !empty($oMapPoint->shadow) ) $gmap_shadow = $oMapPoint->shadow;

    // center on user
    if ( !empty($oMapPoint->y) && !empty($oMapPoint->x) )
    {
    $y=$oMapPoint->y;
    $x=$oMapPoint->x;
    }
  }

  // update center
  $_SESSION[QT]['m_map_gcenter'] = $y.','.$x;

  $gmap_markers = array();
  $gmap_events = array();
  $gmap_functions = array();
  foreach($arrExtData as $oMapPoint)
  {
    if ( !QTgemptycoord($oMapPoint) )
    {
      $user_symbol = $gmap_symbol; // required to reset symbol on each user
      $user_shadow = $gmap_shadow;
      if ( !empty($oMapPoint->icon) ) $user_symbol = $oMapPoint->icon;
      if ( !empty($oMapPoint->shadow) ) $user_shadow = $oMapPoint->shadow;
      $gmap_markers[] = QTgmapMarker($oMapPoint->y.','.$oMapPoint->x,$_SESSION[QT]['editing'],$user_symbol,$oMapPoint->title,$oMapPoint->info,$user_shadow);
    }
  }
  if ( $_SESSION[QT]['editing'] )
  {
  $gmap_events[] = '
	google.maps.event.addListener(markers[0], "position_changed", function() {
		if (document.getElementById("yx")) {document.getElementById("yx").value = gmapRound(marker.getPosition().lat(),10) + "," + gmapRound(marker.getPosition().lng(),10);}
	});
	google.maps.event.addListener(marker[0], "dragend", function() {
		map.panTo(marker.getPosition());
	});';
  $gmap_functions[] = '
  function showLocation(address,title)
  {
    if ( infowindow ) infowindow.close();
    geocoder.geocode( { "address": address}, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK)
      {
        map.setCenter(results[0].geometry.location);
        if ( markers[0] )
        {
          markers[0].setPosition(results[0].geometry.location);
        } else {
          markers[0] = new google.maps.Marker({map: map, position: results[0].geometry.location, draggable: true, animation: google.maps.Animation.DROP, title: title});
        }
        gmapYXfield("yx",markers[0]);
      } else {
        alert("Geocode was not successful for the following reason: " + status);
      }
    });
  }
  function createMarker()
  {
    if ( !map ) return;
    if (infowindow) infowindow.close();
    deleteMarker();
    '.QTgmapMarker('map',true,$gmap_symbol).'
    gmapYXfield("yx",markers[0]);
    google.maps.event.addListener(markers[0], "position_changed", function() { gmapYXfield("yx",markers[0]); });
    google.maps.event.addListener(markers[0], "dragend", function() { map.panTo(markers[0].getPosition()); });
  }
  function deleteMarker()
  {
    if (infowindow) infowindow.close();
    for(var i=markers.length-1;i>=0;i--)
    {
      markers[i].setMap(null);
    }
    gmapYXfield("yx",null);
    markers=[];
  }
  ';
  }
  else
  {
  $gmap_functions[] = '
  function showLocation(address,title)
  {
    if ( infowindow ) infowindow.close();
    geocoder.geocode( { "address": address}, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK)
      {
        map.setCenter(results[0].geometry.location);
        if ( markers[1] )
        {
          markers[1].setPosition(results[0].geometry.location);
        } else {
          markers[1] = new google.maps.Marker({map: map, position: results[0].geometry.location, draggable: true, animation: google.maps.Animation.DROP, title: "'.L('Search_result').'", icon:"qtem_map/point_sample.png"});
        }
      } else {
        alert("Geocode was not successful for the following reason: " + status);
      }
    });
  }
  ';
  }
  include 'qtem_map_load.php';
}

include 'qte_inc_ft.php';