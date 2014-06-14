<?php

// If your board accept several languages (latin origin), the charset 'windows-1252' is recommended in order to render all accents correctly.
// If your board accept english only, you can use the charset 'utf-8'.
if ( !defined('QT_HTML_CHAR') ) define ('QT_HTML_CHAR', 'utf-8');
if ( !defined('QT_HTML_DIR') ) define ('QT_HTML_DIR', 'ltr');
if ( !defined('QT_HTML_LANG') ) define ('QT_HTML_LANG', 'en');
if ( !defined('QT_QUERY_SEPARATOR') ) define ('QT_QUERY_SEPARATOR', ';');

// Is is recommended to always use capital on first letter in the translation
// software changes to lower case if necessary.

$L['Y']='Yes';
$L['N']='No';

// Specific vocabulary

$L['Domain']='Domain';
  $L['Domains']='Domains';
$L['Section']='Team';
  $L['Sections']='Teams';
$L['Field']='Field';
  $L['Fields']='Fields';
$L['User']='Member';
  $L['Users']='Members';
  $L['User_man']='Manage members';
  $L['User_add']='New Member';
  $L['User_del']='Delete members';
  $L['User_upd']='Edit profile';
$L['Role']='Role';
  $L['Userrole_A']='System&nbsp;administrator'; $L['Userrole_As']='System&nbsp;administrators';
  $L['Userrole_M']='System&nbsp;coordinator';   $L['Userrole_Ms']='System&nbsp;coordinators';
  $L['Userrole_U']='Registered&nbsp;user';      $L['Userrole_Us']='Registered&nbsp;users';
  $L['Userrole_V']='Visitor';                   $L['Userrole_Vs']='Visitors';
$L['Section_moderator']='Team coordinator';
$L['Coppa_status']='Coppa status';
  $L['H_Coppa_status']='(visible by system coordinators only)';
  $L['Coppa_child'][0]='Major';
  $L['Coppa_child'][1]='Minor with parent agreement';
  $L['Coppa_child'][2]='Minor without parent agreement';
  $L['Coppa_apply']='Apply COPPA rules';
  $L['Coppa_notapply']='Drop COPPA rules';
$L['Status']='Status';
  $L['Statuses']='Statuses';
$L['Title']='Title';

// Common

$L['Add']='Add';
$L['Age']='Age';
$L['All']='All';
$L['Birthday']='Date of birth';
$L['Birthdays_calendar']='Birthdays calendar';
$L['Birthdays_today']='Happy birthday';
$L['Calendar']='Calendar';
$L['Cancel']='Cancel';
$L['Change']='Change';
$L['Change_status']='Change status';
$L['Change_role']='Change role';
$L['Change_name']='Change username';
$L['Continue']='Continue';
$L['Coord']='Coordinates';
$L['Coord_latlon']='(lat,lon)';
$L['Csv']='Export'; $L['H_Csv']='Download to spreadsheet';
$L['Date']='Date';
$L['Dates']='Dates';
$L['Day']='Day';
$L['Days']='Days';
$L['Delete']='Delete';
$L['Description']='Description';
$L['Display_at']='Display at date';
$L['Email']='E-mail';
$L['Emails']='E-mails';
$L['Edit']='Edit';
$L['Exit']='Exit';
$L['First']='First';
$L['Goto']='Jump to';
$L['Happy_birthday']='Happy birthday to ';
$L['H_Website']='Url of your website (with http://)';
$L['Help']='Help';
$L['Hide']='Hide';
$L['Hidden']='Hidden';
$L['Hidden_info']='Only visible by yourself and system coordinators';
$L['Information']='Information';
$L['Items_per_month']='Sign-up per month';
$L['Items_per_month_cumul']='Cumulative sign-up per month';
$L['Last']='Last';
$L['Last_registration']='Last registration';
$L['Legend']='Legend';
$L['List']='List';
$L['Mandatory']='Mandatory';
$L['Maximum']='Maximum';
$L['Minimum']='Minimum';
$L['Missing']='Missing information';
$L['Month']='Month';
$L['Move']='Move';
$L['Move_to']='Move to';
$L['Name']='Name';
$L['Next']='Next';
$L['None']='None';
$L['Ok']='Ok';
$L['or']='or';
$L['or_drop_file'] = 'or drop a file on the button';
$L['or_drop_files'] = 'or drop files on the button';
$L['Other']='Other';
$L['Other_char']='digit or symbol';
$L['Page']='page';
$L['Pages']='pages';
$L['Picture']='Picture';
$L['Preview']='Preview';
$L['Previous']='Previous';
$L['Privacy']='Privacy';
$L['Remove']='Remove';
$L['Rename']='Rename';
$L['Result']='Result';
$L['Results']='Results';
$L['Save']='Save';
$L['Seconds']='Seconds';
$L['Selected_from']='from';
$L['Selection']='Selection';
$L['Send']='Send';
$L['Show']='Show';
$L['Starting_with']='Starting with';
$L['Time']='Time';
$L['Total']='Total';
$L['Type']='Type';
$L['Unknown']='Unknown';
$L['Username']='Username';
$L['Version']='Version';
$L['Welcome']='Welcome';
$L['Welcome_to']='We welcome a new member, ';
$L['Welcome_not']='I\'m not %s !';
$L['Year']='Year';

// Menu // it's recommended to use &nbsp; to avoid double-line buttons

$L['FAQ']='FAQ';
$L['Search']='Search';
$L['Login']='Log&nbsp;in';
$L['Logout']='Log&nbsp;out';
$L['Register']='Register';
$L['Profile']='Profile';
$L['PProfile']='Personal&nbsp;profile';
$L['TProfile']='Team&nbsp;profile';
$L['MProfile']='Membership';
$L['SProfile']='Registration';
$L['DProfile']='Documents';
$L['Administration']='Administration';
$L['Legal']='Legal&nbsp;notices';

// Team and Profile

$L['Last_column']='Extra column';
$L['Edit_start']='Start editing...';
$L['Edit_stop']='Stop editing';
$L['Export_csv']='Export';
$L['W_Somebody_else']='Caution... You are editing the profile of somebody else';
$L['Secret_question']='Secret question';
$L['Unregister']='Unregister';
$L['Document_add']='New document';
$L['Document_name']='Document name';
$L['Calendar_show_all']='Members from all teams';
$L['Calendar_show_this']='Members in this team only';
$L['Image_preview']='Preview images';

// Search

$L['Advanced']='Advanced';
$L['Keywords']='Keyword(s)';
$L['Search_by_key']='Search by keyword';
$L['Search_by_status']='Search by status';
$L['Search_by_age']='Search by age';
$L['Search_result']='Search result';
$L['Search_criteria']='Matching criteria';
$L['In_section']='in the team';
$L['In_all_sections']='In all teams';
$L['Users_without_section']='Members without team';
$L['Users_in_0_only']='Members only in "%s"';

// Ajax helper

$L['All_categories']='All categories';
$L['Category_not_yet_used']='Category not yet used';
$L['Impossible']='Impossible';
$L['No_result']='No result';
$L['Try_other_lettres'] = 'Try other lettres';
$L['Try_without_options'] = 'Try without options';
$L['Try_all_sections'] = 'Try with all teams';

// Stats & emails

$L['Statistics']='Statistics';
$L['Section_start_date']='Board start date';
$L['Distinct_users']='Distinct users (having posted a message)';
$L['General_site']='General site';

// Users

$L['Section_members']='Team members';
$L['Registered_members']='Registered users';
$L['Users_section']='All team members';
$L['Users_reg']='All registered users';
$L['Users_not_in_team']='Users without team';
$L['User_section_add']='Add to the team';
$L['User_section_del']='Remove from the team';
$L['H_semicolon_useredit']='To insert several values use the comma to separate them.<br />Example: "1-5-123456(home), 1-5-123459(fax)"';
$L['H_semicolon_format']='To make a specific display, use php format.<br />Example for age: "%s years old"<br />To make a drop-down list of values use a comma separated values.<br />Example: "Yes, No"';
$L['H_hidden_fields']='Hidden information will be visible by system coordinators and administrators only';
$L['Fields_default']='Default columns';
$L['Fields_personal']='Personal info';
$L['Fields_team']='Team info';
$L['Fields_computed']='Computed info';
$L['Fields_system']='System info';

// Dates

$L['H_Date']='(yyyy-mm-dd)';
$L['dateMMM']=array(1=>'January','February','March','April','May','June','July','August','September','October','November','December');
$L['dateMM'] =array(1=>'Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
$L['dateM']  =array(1=>'J','F','M','A','M','J','J','A','S','O','N','D');
$L['dateDDD']=array(1=>'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday');
$L['dateDD'] =array(1=>'Mon','Tue','Wed','Thu','Fri','Sat','Sun');
$L['dateD']  =array(1=>'M','T','W','T','F','S','S');
$L['dateSQL']=array(
  'January'  => 'January',
  'February' => 'February',
  'March'    => 'March',
  'April'    => 'April',
  'May'      => 'May',
  'June'     => 'June',
  'July'     => 'July',
  'August'   => 'August',
  'September'=> 'September',
  'October'  => 'October',
  'November' => 'November',
  'December' => 'December',
  'Monday'   => 'Monday',
  'Tuesday'  => 'Tuesday',
  'Wednesday'=> 'Wednesday',
  'Thursday' => 'Thursday',
  'Friday'   => 'Friday',
  'Saturday' => 'Saturday',
  'Sunday'   => 'Sunday',
  'Today'=>'Today',
  'Yesterday'=> 'Yesterday',
  'Jan'=>'Jan',
  'Feb'=>'Feb',
  'Mar'=>'Mar',
  'Apr'=>'Apr',
  'May'=>'May',
  'Jun'=>'Jun',
  'Jul'=>'Jul',
  'Aug'=>'Aug',
  'Sep'=>'Sep',
  'Oct'=>'Oct',
  'Nov'=>'Nov',
  'Dec'=>'Dec',
  'Mon'=>'Mon',
  'Tue'=>'Tue',
  'Wed'=>'Wed',
  'Thu'=>'Thu',
  'Fri'=>'Fri',
  'Sat'=>'Sat',
  'Sun'=>'Sun');

// Icons

$L['Ico_user_p']='Username';
$L['Ico_user_pZ']='Anonymous user';
$L['Ico_user_w']='Open website';
$L['Ico_user_wZ']='no website';
$L['Ico_user_e']='Send e-mail';
$L['Ico_user_eZ']='no e-mail';
$L['Ico_section_0_0']='Public team (actif)';
$L['Ico_section_0_1']='Public team (frosen)';
$L['Ico_section_1_0']='Hidden team (actif)';
$L['Ico_section_1_1']='Hidden team (frosen)';
$L['Ico_section_2_0']='Private team (actif)';
$L['Ico_section_2_1']='Private team (frosen)';
$L['Ico_view_n']='Normal view';
$L['Ico_view_c']='Compact view';
$L['Ico_view_p']='Print view';
$L['Ico_view_f_c']='Calendar view';
$L['Ico_view_f_n']='Tabular view';

// Restrictions

$L['R_login_register']='Access is restricted to members only.<br /><br />Please log in, or proceed to registration to become member.';
$L['R_user']='Access is restricted to members only.';
$L['R_staff']='Access is restricted to system coordinators only.';
$L['R_admin']='Access is restricted to system administrators only.';

// Errors

$L['E_access']='Access denied...';
$L['E_admin']='Access is restricted to system administrators only.';
$L['E_already_used']='already used';
$L['E_editing']='Data not yet saved. Quit without saving?';
$L['E_file_size']='File is too large';
$L['E_invalid']='invalid';
$L['E_javamail']='Protection: java required to see e-mail addresses';
$L['E_mandatory']='Mandartory field is empty';
$L['E_min_4_char']='Minimum 4 characters';
$L['E_min_2_char']='Minimum 2 characters';
$L['E_missing_http']='The url must start with http:// or https://';
$L['E_no_document']='No document.';
$L['E_no_member']='No member.';
$L['E_no_membership']='This user is not member of a team...';
$L['E_private_membership']='Belongs also to a system team (hidden)';
$L['E_no_public_section']='The board does not contain any public team. Private team access requires login.';
$L['E_no_upload']='Upload of documents by members has been disabled...';
$L['E_pixels_max']='Pixels maximum';
$L['E_pwd_char']='The password contains invalid character.';
$L['E_max_10']='Maximum 10';
$L['E_more_than_5']='More than 5 fields is not recommended';
$L['No_future'][0]='Future allowed';
$L['No_future'][2]='Future allowed only this year';
$L['No_future'][1]='Future not allowed';
$L['E_data_not_saved']='Data not yet saved. Quit without saving?';

// Success

$L['S_update']='Update successful...';
$L['S_delete']='Delete completed...';
$L['S_insert']='Creation successful...';
$L['S_save']  ='Save completed...';