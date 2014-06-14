<?php

// QT re-usable component 1.0 build:20130802

class cField
{
	public $on = false;
	public $id = '';     // field key (can be "emails_i")
	public $uid = '';    // db field
	public $icon = '';
	public $name = '';   // field translation
	public $format = ''; // field format
	public $tip='';      // field description

	function __construct($id,$name='')
	{
		$this->id = $id;
		$this->uid = (substr($id,-2,2)=='_i' ? $this->uid = substr($id,0,-2) : $id);
		$this->on = in_array($this->uid,explode(',',$_SESSION[QT]['fields_c'].','.$_SESSION[QT]['fields_u'].','.$_SESSION[QT]['fields_t'].',coord,ufield'));
		$this->name = (empty($name) ? $this->id : $name);
		if ( $this->on )
		{
			$this->name = ObjTrans('field',$this->uid);
			$this->format = ObjTrans('ffield',$this->uid,'');
			$this->tip = $this->name;
		}
	}
	
	public static function ArrayFields($arr,$OnOnly=true)
	{
		// Returns an array of cField object (key is the id)
	  if ( is_string($arr) ) $arr = explode(';',$arr);
	  if ( !is_array($arr) ) die('cField::ArrayFields invalid argument');
	  $arrFields = array();
	  foreach($arr as $key)
	  {
	  	$oField = new cField($key);
	  	if ( $OnOnly && !$oField->on ) continue;
	  	$arrFields[$key] = $oField;
	  }
	  return $arrFields;
	}
	
	public static function ArrayFieldnames($arr,$OnOnly=true)
	{
		// Returns an array of fieldnames translated (key is the id)
		// $arr can be an array of cField objects or an array of strings, or a csv string
		// note when $arr is an array the keys are not preserved (it uses the key id)
		if ( is_string($arr) ) $arr = explode(';',$arr);
		if ( !is_array($arr) ) die('cField::ArrayFieldnames invalid argument');
		$arrFields = array();
		foreach($arr as $oField)
		{
			if ( !is_a($oField,'cField') ) $oField = new cField($oField); 
			if ( $OnOnly && !$oField->on ) continue;
			$arrFields[$oField->id] = $oField->name;
		}
		return $arrFields;
	}
}

// QT re-usable component 1.0 build:20130802 to be deleted

class cFLD
{

public $on = false;
public $id = '';     // field key (can be "emails_i")
public $uid = '';
public $sort = false;// default sort order (ASC,DESC,FALSE)
public $name = '';   // field translation
public $format = ''; // field format
public $class_th = '';
public $style_th = '';
public $class_td = '';
public $style_td = '';
public $class_dynamic = false; // To use this, define an array('formula-%s','field',array-of-classes). Note: keys of the array-of-classes must be strings
public $style_dynamic = false; // To use this, define an array('formula-%s','field',array-of-styles). Note: keys of the array-of-styles must be strings

function __construct($id,$name,$class_th='',$style_th='',$class_td='',$style_td='',$sort=false)
{
  $this->id = $id;
  $this->uid = ( substr($id,-2,2)=='_i' ? substr($id,0,-2) : $id );
  $this->on = in_array($this->uid,explode(',',$_SESSION[QT]['fields_c'].','.$_SESSION[QT]['fields_u'].','.$_SESSION[QT]['fields_t'].',coord'));
  $this->name = $name;
  $this->class_th = $class_th;
  $this->style_th = $style_th;
  $this->class_td = $class_td;
  $this->style_td = $style_td;
  $this->sort = $sort;
  if ( $this->on )
  {
  $this->name = ObjTrans('field',$this->uid);
  $this->format = ObjTrans('ffield',$this->uid,'');
  }
}

function AddStyleDynamic($arr)
{
  // Change $this->style_td to add a dynamic style based on the parameters in $this->style_dynamic and according to the values in $arr
  if ( !is_array($arr) ) return; // row not defined
  if ( count($arr)==0 ) return; // row not defined
  if ( !is_array($this->style_dynamic) ) return; // formula not defined
  if ( count($this->style_dynamic)!=3 ) return; // formula not defined
  if ( !is_string($this->style_dynamic[0]) ) return; // formula not defined
  if ( !is_string($this->style_dynamic[1]) ) return; // formula not defined
  if ( !is_array($this->style_dynamic[2]) ) return; // styles not defined
  $key      = $this->style_dynamic[1];
  $arrStyle = $this->style_dynamic[2]; 
  $strStyle = '';
  if ( isset($arr[$key]) ) {
  if ( isset($arrStyle[strval($arr[$key])]) ) {
    $strStyle = (empty($this->style_td) ? '' : ';').sprintf( $this->style_dynamic[0], $arrStyle[strval($arr[$key])] );
  }}
  return $strStyle;
}

function AddClassDynamic($arr)
{
  // Change $this->style_td to add a dynamic style based on the parameters in $this->style_dynamic and according to the values in $arr
  if ( !is_array($arr) ) return; // row not defined
  if ( count($arr)==0 ) return; // row not defined
  if ( !is_array($this->class_dynamic) ) return; // formula not defined
  if ( count($this->class_dynamic)!=3 ) return; // formula not defined
  if ( !is_string($this->class_dynamic[0]) ) return; // formula not defined
  if ( !is_string($this->class_dynamic[1]) ) return; // formula not defined
  if ( !is_array($this->class_dynamic[2]) ) return; // styles not defined
  $key      = $this->class_dynamic[1];
  $arrClass = $this->class_dynamic[2]; 
  $strClass = '';
  if ( isset($arr[$key]) ) {
  if ( isset($arrClass[strval($arr[$key])]) ) {
    $strClass = ' '.sprintf( $this->class_dynamic[0], $arrClass[strval($arr[$key])] );
  }}
  return $strClass;
}

}