<?php

// QuickTeam 3.0 build:20140608

$bCanUpload = false;
if ( $_SESSION[QT]['editing'] ) {
if ( $_SESSION[QT]['upload']=='1' || sUser::IsStaff() ) {
  $bCanUpload = true;
}}

function GetFileIcon($url,$name)
{
  $ext = strtolower(substr($url,strrpos($url, '.')+1));
  $img = 'doc';
  switch(strtolower($ext))
  {
  case 'jpg':
  case 'jpeg':
  case 'gif':
  case 'png': $img='img'; break;
  case 'rar':
  case 'tar':
  case '7z':
  case 'zip': $img='zip'; break;
  case 'pdf': $img='pdf'; break;
  default: $img='txt';
  }
  return '<img src="admin/ico_'.$img.'.gif" width="20" height="20" style="vertical-align:middle" title="'.$ext.'"/> <a href="'.$url.'" target="_blank">'.$name.'</a>';
}
function GetFilePreview($url,$name)
{
  $ext = strtolower(substr($url,strrpos($url, '.')+1));
  $img = '<img src="admin/ico_doc.gif" class="docico" title="'.$ext.'"/>';
  switch(strtolower($ext))
  {
  case 'jpg':
  case 'jpeg':
  case 'gif':
  case 'png': $img='<img src="'.$url.'" class="docimg" title="'.$ext.'"/>'; break;
  case 'rar':
  case 'tar':
  case '7z':
  case 'zip': $img='<img src="admin/ico_zip_64.gif" class="docico" title="'.$ext.'"/>'; break;
  case 'pdf': $img='<img src="admin/ico_pdf_64.png" class="docico" title="'.$ext.'"/>'; break;
  default: $img='<img src="admin/ico_txt_64.png" class="docico" title="'.$ext.'"/>';
  }
  return '<a href="'.$url.'" target="_blank">'.$img.'</a>';
}

// --------
// SUBMITTED preview
// --------

if ( !isset($_SESSION[QT]['docpreview']) ) $_SESSION[QT]['docpreview']=false;
if ( isset($_POST['docpreview']) ) $_SESSION[QT]['docpreview']=($_POST['docpreview']==='1');
if ( isset($_GET['docpreview']) ) $_SESSION[QT]['docpreview']=($_GET['docpreview']==='1');

// --------
// SUBMITTED UPLOAD
// --------

if ( isset($_POST['ok']) )
{
  // initialiaze upload constraint

  $strFileextensions = '';
  $strMimetypes = '';
  include 'bin/qte_upload.php';
  if ( isset($arrFileextensions) ) {
  if ( !empty($arrFileextensions) ) {
  if ( is_array($arrFileextensions) ) {
    $strFileextensions = implode(',',$arrFileextensions);
  }}}
  if ( isset($arrMimetypes) ) {
  if ( !empty($arrMimetypes) ) {
  if ( is_array($arrMimetypes) ) {
    $strMimetypes = implode(',',$arrMimetypes);
  }}}

  // check uploaded files

  for($f=0;$f<count($_FILES['fileselect']['name']);++$f)
  {
    if ($f==5) { $error = 'Maximum 5 files uploaded at once'; break; }
    $strFile = strtolower($_FILES['fileselect']['name'][$f]);
    $strName = $strFile;
    $strFile = $id.'_'.date('Ymd').'_'.date('Hi').'_'.$strFile;

    // Check uploaded document ($f)
    $arrFile = array( 'name'=>$_FILES['fileselect']['name'][$f],
                      'type'=>$_FILES['fileselect']['type'][$f],
                      'size'=>$_FILES['fileselect']['size'][$f],
                      'tmp_name'=>$_FILES['fileselect']['tmp_name'][$f]);
    $error = InvalidUpload($arrFile,$strFileextensions,$strMimetypes,(int)$_SESSION[QT]['uploadsize']);

    // Save

    if ( empty($error) )
    {
      $strDir = TargetDir(QTE_DIR_DOC,$id);
      if ( copy($_FILES['fileselect']['tmp_name'][$f],QTE_DIR_DOC.$strDir.$strFile) )
      {
        $oDB->Exec('INSERT INTO '.TABDOC.' (id,docname,docfile,docpath,docdate) VALUES ('.$id.',"'.substr($strName,0,255).'","'.substr($strFile,0,255).'","'.$strDir.'","'.date('Ymd').'")');
        $bUpload=true;
      }
      else
      {
        echo '<p class="error">Unable to copy the file. The directory /',QTE_DIR_DOC,' is probably not writeable...</p>';
      }
      unlink($_FILES['fileselect']['tmp_name'][$f]);
    }
  }
}

// --------
// Read Docs (only registered user can view the documents) and this part is not accissible by visitor)
// --------

$arrDocs = array();
$oDB->Query('SELECT * FROM '.TABDOC.' WHERE id='.$id.' ORDER BY docdate DESC');
while ( $row=$oDB->Getrow() ) $arrDocs[] = $row;

// --------
// HTML START
// --------

if ( count($arrDocs)>0 && !$_SESSION[QT]['editing'] ) echo '<div class="pan_menu"><a class="docoption aslist'.($_SESSION[QT]['docpreview'] ? '' : ' actif').'" href="'.($_SESSION[QT]['docpreview'] ? Href().'?'.GetUri('docpreview').'&amp;docpreview=0' : 'javascript:void(0);').'" title="'.L('list').'"></a><a class="docoption asgrid'.($_SESSION[QT]['docpreview'] ? ' actif' : '').'" href="'.($_SESSION[QT]['docpreview'] ? 'javascript:void(0);' : Href().'?'.GetUri('docpreview').'&amp;docpreview=1').'" title="'.L('preview').'"></a></div>',PHP_EOL;

echo '<div class="pan-top">',(isset($L[strtoupper($tt).'Profile']) ? $L[strtoupper($tt).'Profile'] : $L['Profile']),'</div>',PHP_EOL;

// -- DISPLAY DOCS --

if ( !empty($error) ) echo '<span class="error">',$error,'</span>',PHP_EOL;

if ( $_SESSION[QT]['editing'] )
{
  if ( $_SESSION[QT]['upload']=='0' ) echo '<p class="colct disabled">',$L['E_no_upload'],'<br />',$L['R_staff'],'</p>';
  if ( $bCanUpload )
  {
  echo '<div class="editform"><form method="post" action="',Href('qte_user.php'),'?id='.$id.'&amp;tt=',$tt,'" enctype="multipart/form-data">',PHP_EOL;
  echo '<table>',PHP_EOL;
  echo '<tr>';
  echo '<td><input class="profile" type="file" name="fileselect[]" id="fileselect" size="35" multiple="multiple"/> <span class="small" id="draganddrop" style="display:none">(',L('or_drop_files'),')</span></td>',PHP_EOL;
  echo '<td class="right"><input type="hidden" name="id" value="',$id,'"/><input type="hidden" id="MAX_FILE_SIZE" name="MAX_FILE_SIZE" value="2000000" /><input id="upload" type="submit" name="ok" value="',$L['Add'],'"/></td>';
  echo '</tr>',PHP_EOL;
  echo '</table>',PHP_EOL;
  echo '</form></div>',PHP_EOL;

  echo '<form method="post" id="form_docs" action="qte_change.php"><input type="hidden" name="u" value="'.$id.'"/><input type="hidden" id="form_docs_action" name="a" value=""/>',PHP_EOL;
  if ( count($arrDocs)>1 )
  {
  $strDataCommand = L('selection').': <a class="datasetcontrol" onclick="datasetcontrol_click(\'t1_cb[]\',\'docs_del\'); return false;" href="#">'.L('delete').'</a>';
  echo '<p class="pager-zt"><img src="admin/selection_up.gif" style="width:10px;height:10px;vertical-align:bottom;margin:0 10px 0 13px" alt="|" />'.$strDataCommand.'</p>',PHP_EOL;
  }
  echo '<table id="t1" class="t-data">',PHP_EOL;
  foreach($arrDocs as $intId=>$arrDoc)
  {
    echo '<tr id="tr_t1_cb'.$arrDoc['docfile'].'" class="rowlight">',PHP_EOL;
    echo '<td class="c-checkbox"><input type="checkbox" name="t1_cb[]" id="t1_cb'.$arrDoc['docfile'].'" value="'.$arrDoc['docfile'].'" /></td>',PHP_EOL;
    if ( file_exists(QTE_DIR_DOC.$arrDoc['docpath'].$arrDoc['docfile']) )
    {
      echo '<td>',GetFileIcon(QTE_DIR_DOC.$arrDoc['docpath'].$arrDoc['docfile'],$arrDoc['docname']),'</td>',PHP_EOL;
    }
    else
    {
      echo '<td>',$arrDoc['docname'],'</td>',PHP_EOL;
    }
    echo '<td class="tddocdate">',QTdatestr($arrDoc['docdate'],'$','',true),'</td>',PHP_EOL;
    echo '<td class="c-action"><a class="small" href="'.Href('qte_change.php').'?u='.$id.'&amp;a=renamedoc&amp;v='.$arrDoc['docfile'].'">'.L('Rename').'</a> | <a class="small" href="'.Href('qte_change.php').'?a=docs_del&amp;u='.$id.'&amp;v='.urlencode($arrDoc['docfile']).'">'.L('Delete').'</a></td>',PHP_EOL;
    echo '</tr>',PHP_EOL;
  }
  if ( count($arrDocs)==0 ) echo '<tr><td class="center">',$L['E_no_document'],'</td></tr>';
  echo '</table>',PHP_EOL;
  if ( count($arrDocs)>4 ) echo '<p class="pager-zb"><img src="admin/selection_down.gif" style="width:10px;height:10px;vertical-align:top;margin:0 10px 0 13px" alt="|" />'.$strDataCommand.'</p>',PHP_EOL;
  echo '</form>',PHP_EOL;
  }
}
else
{
  if ( count($arrDocs)==0 )
  {
    echo '<p class="center">',$L['E_no_document'],'</p>';
  }
  else
  {
    if ( $_SESSION[QT]['docpreview'] )
    {
      foreach($arrDocs as $intId=>$arrDoc)
      {
        echo '<div class="docblock">';
        if ( file_exists(QTE_DIR_DOC.$arrDoc['docpath'].$arrDoc['docfile']) )
        {
        echo GetFilePreview(QTE_DIR_DOC.$arrDoc['docpath'].$arrDoc['docfile'],$arrDoc['docname']),'<br/>';
        echo '<a href="',QTE_DIR_DOC.$arrDoc['docpath'].$arrDoc['docfile'],'" target="_blank">',$arrDoc['docname'],'</a><br /><span class="small">',QTdatestr($arrDoc['docdate'],'$','',true),'</span>';
        }
        else
        {
        echo '<span>',$arrDoc['docname'],'</span><br/>';
        echo '<span class="small">',QTdatestr($arrDoc['docdate'],'$','',true),'</span>';
        }
        echo '</div>',PHP_EOL;
      }
    }
    else
    {
      echo '<table class="t-data">',PHP_EOL;
      foreach($arrDocs as $intId=>$arrDoc)
      {
        echo '<tr>',PHP_EOL;
        if ( file_exists(QTE_DIR_DOC.$arrDoc['docpath'].$arrDoc['docfile']) )
        {
          echo '<td class="headfirst small">',QTdatestr($arrDoc['docdate'],'$','',true),'</td>',PHP_EOL;
          echo '<td>',GetFileIcon(QTE_DIR_DOC.$arrDoc['docpath'].$arrDoc['docfile'],$arrDoc['docname']),'</td>',PHP_EOL;
        }
        else
        {
          echo '<td class="headfirst small">',QTdatestr($arrDoc['docdate'],'$','',true),'</td>',PHP_EOL;
          echo '<td>',$arrDoc['docname'],'</td>',PHP_EOL;
        }
        echo '</tr>',PHP_EOL;
      }
      echo '</table>';
    }
  }
}