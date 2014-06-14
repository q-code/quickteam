/*
Summary
-------
Theses functions add/remove rows (called "sub-rows") under a specific row of a table.
To work with this function, the table <table> and each row <tr> must have unique id.
If your page contains several tables, be sure that the <tr> id are unique through all tables.

Technical tips
--------------
The created sub-rows <tr> will have id like id+r0. Example, sub-rows added under the row 't1a1' will have id: 't1a1r0', 't1a1r1', ...
These id are used to be able to remove the sub-rows (ex: removing sub-rows under the row 't1a1' will remove all rows having id like 't1a1r*')
The created  cells <td> will have id like id+r0+c0. Example, cells of the sub-row 't1a1r0' will have id: 't1a1r0c0', 't1a1r0c1', ...
The sub-rows <tr> include the class 'popuprow' and the cells <td> include the class 'popupcell'.
The cells are filled with the array values of [rows] (and the blank character &nbsp; if no data).
*/

function qtCheckboxAll(checkboxid,name,useHighlight)
{
	var doc = document;
	var checkbox = doc.getElementById(checkboxid); if ( !checkbox ) return;
	var checkboxes = doc.getElementsByName(name);
	var i = checkboxes.length-1; if ( i<0 ) return;
	do
	{
	checkboxes[i].checked=checkbox.checked;
	if (useHighlight) qtHighlight("tr_"+checkboxes[i].id,checkbox.checked);
	}
	while(i--);
}
function qtCheckboxOne(name,id)
{
	// Check/uncheck header checkbox when all/none boxes are checked
	// This function is not mandatory
	var doc = document; if ( !doc.getElementById(id) ) return;
	var checkboxes = doc.getElementsByName(name); if ( checkboxes.length<1 ) return;
	var n = 0, i = checkboxes.length-1; if ( i<0 ) return;
	do { if ( checkboxes[i].checked ) n++; } while(i--);
	doc.getElementById(id).checked = ( n==checkboxes.length );
}
function qtCheckboxToggle(id)
{
	var doc = document.getElementById(id); if ( !doc ) return;
	doc.click();
}
function qtHighlight(id,bHighlighted)
{
	var doc = document.getElementById(id); if ( !doc ) return;
	doc.className = doc.className.replace(" checked","");
	if ( bHighlighted ) doc.className += " checked";
}