<?php

// QuickTeam

$arrLang = array();
$arrLang['en'] = array('EN', 'English',         'english');
$arrLang['fr'] = array('FR', 'Fran&ccedil;ais', 'francais');
$arrLang['nl'] = array('NL', 'Nederlands',      'nederlands');

// If you add a new language, create the corresponding line.
// The key must be the iso code. Exemple: $arrLang["en"]
// The language is defined by 3 values:
// [1] the label (displayed in the banner when user is allowed to change the language)
// [2] the name of the language (use html code for special characters)
// [3] the folder where the files are stored (must be a subfolder of /language/, special characters not allowed)