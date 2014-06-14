<?php

// If your board accept several languages (latin origin), the charset 'windows-1252' is recommended in order to render all accents correctly.
// If your board accept english only, you can use the charset 'utf-8'.
if ( !defined('QT_HTML_CHAR') ) define ('QT_HTML_CHAR', 'utf-8');
if ( !defined('QT_HTML_DIR') ) define ('QT_HTML_DIR', 'ltr');
if ( !defined('QT_HTML_LANG') ) define ('QT_HTML_LANG', 'nl');
if ( !defined('QT_QUERY_SEPARATOR') ) define ('QT_QUERY_SEPARATOR', ';');

// Is is recommended to always use capital on first letter in the translation
// software changes to lower case if necessary.

$L['Y']='Ja';
$L['N']='Nee';

// Specific vocabulary

$L['Domain']='Domein';
  $L['Domains']='Domeinen';
$L['Section']='Groep';
  $L['Sections']='Groepen';
$L['Field']='Veld';
  $L['Fields']='Velden';
$L['User']='Lid';
  $L['Users']='Leden';
  $L['User_man']='Leden beheren';
  $L['User_add']='Nieuwe gebruiker';
  $L['User_del']='Leden verwijderen';
  $L['User_upd']='Profiel bewerken';
$L['Role']='Rol';
  $L['Userrole_A']='Systeem&nbsp;beheerder';        $L['Userrole_As']='Systeem&nbsp;beheerders';
  $L['Userrole_M']='Systeem&nbsp;co&ouml;rdinator'; $L['Userrole_Ms']='Systeem&nbsp;co&ouml;rdinators';
  $L['Userrole_U']='Geregistreerd&nbsp;gebruiker';  $L['Userrole_Us']='Geregistreerde&nbsp;gebruikers';
  $L['Userrole_V']='Bezoeker';                      $L['Userrole_Vs']='Bezoekers';
  $L['Section_moderator']='Groep&nbsp;coordinator';
$L['Coppa_status']='Coppa statuut';
  $L['H_Coppa_status']='(zichtbaar allen door groep&nbsp;coordinators)';
  $L['Coppa_child'][0]='Meerderjarig';
  $L['Coppa_child'][1]='Minderjarig met goedkeuring van ouders';
  $L['Coppa_child'][2]='Minderjarig zonder goedkeuring van ouders';
  $L['Coppa_apply']='COPPA regels gebruiken';
  $L['Coppa_notapply']='COPPA regels verwijderen';
$L['Status']='Statuut';
  $L['Statuses']='Statuten';
$L['Title']='Tijtel';

// Common

$L['Add']='Toevoegen';
$L['Age']='Leeftijd';
$L['All']='Alles';
$L['Birthday']='Geboortedatum';
$L['Birthdays_calendar']='Verjaardag kalender';
$L['Birthdays_today']='Gelukkige verjaardag';
$L['Calendar']='Kalender';
$L['Cancel']='Annuleren';
$L['Change']='Bewerk';
$L['Change_status']='Statuut veranderen...';
$L['Change_role']='Role veranderen';
$L['Change_name']='Gebruikersnaam veranderen...';
$L['Continue']='Voortduren';
$L['Coord']='Co&ouml;rdinaten';
$L['Coord_latlon']='(lat,lon)';
$L['Csv']='Export'; $L['H_Csv']='Tonen in spreadsheet';
$L['Date']='Datum';
$L['Dates']='Datum';
$L['Day']='Dag';
$L['Days']='Dagen';
$L['Delete']='Uitwissen';
$L['Description']='Beschrijving';
$L['Display_at']='Tonen op datum van';
$L['Edit']='Bewerken';
$L['Email']='E-mail';
$L['Emails']='E-mailen';
$L['Exit']='Uitrit';
$L['First']='Eerste';
$L['Goto']='Ga naar';
$L['Happy_birthday']='Gelukkig verjaardag ';
$L['H_Website']='Uw website url (met http://)';
$L['Help']='Hulp';
$L['Hide']='Verborgen';
$L['Hidden']='Verborg';
$L['Hidden_info']= 'Slechts zichtbaar door zich en de staff';
$L['Information']='Informatie';
$L['Items_per_month']='Registraties per maand';
$L['Items_per_month_cumul']='Cumul registraties per maand';
$L['Last']='Laatste';
$L['Last_registration']='Laatste registratie';
$L['Legend']='Legend';
$L['List']='Lijst';
$L['Mandatory']='Verplicht';
$L['Maximum']='Maximum';
$L['Minimum']='Minimum';
$L['Missing']='Verplicht data niet gevonden';
$L['Month']='Maand';
$L['Move']='Verplaatsen';
$L['Move_to']='Verplatsen';
$L['Name']='Naam';
$L['Next']='Volgende';
$L['None']='Niets';
$L['Ok']='Ok';
$L['or']='of';
$L['or_drop_file'] = 'of drop een bestand op de knop';
$L['or_drop_files'] = 'of drop bestanden op de knop';
$L['Other']='Andere';
$L['Other_char']='een cijfer of symbool';
$L['Page']='pagina';
$L['Pages']='pagina\'s';
$L['Picture']='Foto';
$L['Previous']='Vorige';
$L['Preview']='Overzicht';
$L['Privacy']='Priv&eacute;-leven';
$L['Remove']='Uitwissen';
$L['Rename']='Herbenoemen';
$L['Result']='Resultaat';
$L['Results']='Resultaten';
$L['Save']='Opslaan';
$L['Seconds']='Seconden';
$L['Selected_from']='uit';
$L['Selection']='Selectie';
$L['Send']='Zend';
$L['Show']='Tonen';
$L['Starting_with']='Begin met';
$L['Time']='Tijd';
$L['Total']='Totaal';
$L['Type']='Type';
$L['Unknown']='Onbekend';
$L['Username']='Gebruikersnaam';
$L['Version']='Versie';
$L['Welcome']='Welkom';
$L['Welcome_to']='Welkom voor een nieuwe gebruiker, ';
$L['Welcome_not']='Ik ben %s niet !';
$L['Year']='Jaar';

// Menu

$L['FAQ']='FAQ';
$L['Search']='Zoeken';
$L['Login']='Inloggen';
$L['Logout']='Uitloggen';
$L['Register']='Registreer';
$L['Profile']='Profiel';
$L['PProfile']='Personeel&nbsp;profiel';
$L['TProfile']='Groep&nbsp;profiel';
$L['MProfile']='Lidmaatschap';
$L['SProfile']='Registratie';
$L['DProfile']='Dokumenten';
$L['Administration']='Administratie';
$L['Legal']='Privacybeleid';

// Team and Profile

$L['Last_column']='Extra colonne';
$L['Edit_start']='Begin het uitgeven...';
$L['Edit_stop']='Einde het uitgeven';
$L['Export_csv']='Uitvoeren';
$L['W_Somebody_else']='Pas op... U geeft het profiel van iemand anders uit';
$L['Secret_question']='Geheime vraag';
$L['Unregister']='Afmelden';
$L['Document_add']='Dokument toevoegen';
$L['Document_name']='Dokumentnaam';
$L['Calendar_show_all']='Leden uit alle groepen';
$L['Calendar_show_this']='Enkel leden in dit groep';
$L['Image_preview']='Voorbeelden';

// Search

$L['Advanced']='Geavanceerd';
$L['Keywords']='Sleutelwoord(en)';
$L['Search_by_key']='Zoeken voor sleutelwoord';
$L['Search_by_status']='Zoeken per statuut';
$L['Search_by_age']='Zoeken per leeftijd';
$L['Search_result']='Resultaat van het onderzoek';
$L['Search_criteria']='Zoekcriteria';
$L['In_section']='in de groep';
$L['In_all_sections']='In alle groepen';
$L['Users_without_section']='Gebruikers zonder groep';
$L['Users_in_0_only']='Gebuikers alleen in "%s"';

// Ajax helper

$L['All_categories']='Alle categorie&euml;n';
$L['Category_not_yet_used']='Categorie nog niet gebruikt';
$L['Impossible']='Onmogelijk';
$L['No_result']='Geen resultaat';
$L['Try_other_lettres']='Probeer andere karakter';
$L['Try_without_options']='Probeer zonder filter';
$L['Try_all_sections']='Probeer alle groupen';

// Stats

$L['Statistics']='Statistieken';
$L['Section_start_date']='Begin datum';
$L['Distinct_users']='Verschillende gebruikers (met ten minste een bericht ingevuld)';
$L['General_site']='Algemene site';

// Users

$L['Section_members']='Leden';
$L['Registered_members']='Gebruikers';
$L['Users_section']='Alle leden';
$L['Users_reg']    ='Alle gebruikers';
$L['Users_not_in_team']='Leden zonder groep';
$L['User_section_add']='Leden inschrijven';
$L['User_section_del']='Leden uitschrijven';
$L['H_semicolon_useredit']='Om een tweede waard te geven, waarden moet met komma ruimte gescheiden zijn.<br />Example: "1-5-123456(home), 1-5-123459(fax)"';
$L['H_semicolon_format']='Om a specifiek formaat te make, u kan de php formula gebruiken.<br />Bijvoorbeeld, voor de leeftijd: "%s jaar"<br />Om een selectielijst te maken, waarden moet met komma ruimte gescheiden zijn.<br />Bijvoorbeeld: "Ja, Nee"';
$L['H_hidden_fields']='De verborgen informatie kan door slechts administratoren en coordinatoren worden bekeken';
$L['Fields_default']='Standaard kolommen';
$L['Fields_personal']='Persoonlijke info';
$L['Fields_team']='Groep info';
$L['Fields_computed']='Berekende info';
$L['Fields_system']='Systeem info';

// Dates

$L['dateMMM']=array(1=>'Januari','Februari','Maart','April','Mei','Juni','Juli','Augustus','Septembre','Oktober','November','December');
$L['dateMM'] =array(1=>'Jan','Feb','Mrt','Apr','Mei','Jun','Jul','Aug','Sep','Okt','Nov','Dec');
$L['dateM']  =array(1=>'J','F','M','A','M','J','J','A','S','O','N','D');
$L['dateDDD']=array(1=>'Maandag','Dinsdag','Woensdag','Donderdag','Vrijdag','Zaterdag','Zondag');
$L['dateDD'] =array(1=>'Ma','Di','Wo','Do','Vr','Za','Zo');
$L['dateD']  =array(1=>'M','D','W','D','V','Z','Z');
$L['dateSQL']=array(
  'January'  => 'januari',
  'February' => 'februari',
  'March'    => 'maart',
  'April'    => 'april',
  'May'      => 'mei',
  'June'     => 'juni',
  'July'     => 'juli',
  'August'   => 'augustus',
  'September'=> 'september',
  'October'  => 'oktober',
  'November' => 'november',
  'December' => 'december',
  'Monday'   => 'maandag',
  'Tuesday'  => 'dinsdag',
  'Wednesday'=> 'woensdag',
  'Thursday' => 'donderdag',
  'Friday'   => 'vrijdag',
  'Saturday' => 'zaterdag',
  'Sunday'   => 'zondag',
  'Today'=>'Vandaag',
  'Yesterday'=>'Gisteren',
  'Jan'=>'jan',
  'Feb'=>'feb',
  'Mar'=>'mrt',
  'Apr'=>'apr',
  'May'=>'mei',
  'Jun'=>'jun',
  'Jul'=>'jul',
  'Aug'=>'aug',
  'Sep'=>'sep',
  'Oct'=>'okt',
  'Nov'=>'nov',
  'Dec'=>'dec',
  'Mon'=>'ma',
  'Tue'=>'di',
  'Wed'=>'wo',
  'Thu'=>'do',
  'Fri'=>'vr',
  'Sat'=>'za',
  'Sun'=>'zo');

// Icons

$L['Ico_user_p']='Gebruiker';
$L['Ico_user_pZ']='Onbekende gebruiker';
$L['Ico_user_w']='Open website';
$L['Ico_user_wZ']='Geen website';
$L['Ico_user_e']='Verzend e-mail';
$L['Ico_user_eZ']='Geen e-mail';
$L['Ico_section_0_0']='Openbaar groep (actif)';
$L['Ico_section_0_1']='Openbaar groep (gesloten)';
$L['Ico_section_1_0']='Verborgen groep (actif)';
$L['Ico_section_1_1']='Verborgen groep (gesloten)';
$L['Ico_section_2_0']='Priv&eacute; groep (actif)';
$L['Ico_section_2_1']='Priv&eacute; groep (gesloten)';
$L['Ico_view_n']='Normale stijl';
$L['Ico_view_c']='Compacte stijl';
$L['Ico_view_p']='Afdrukken';
$L['Ico_view_f_c']='Calendar stijl';
$L['Ico_view_f_n']='Tabel stijl';

// Restrictions

$L['R_login_register']='De toegang is beperkt tot slechts leden.<br /><br />Gelieve in te loggen, of ga naar Registreerd om lid te worden.';
$L['R_user']='De toegang is beperkt tot slechts leden.';
$L['R_staff']='De toegang is beperkt tot slechts systeem coordinatoren.';
$L['R_admin']='De toegang is beperkt tot slechts systeem beheerders.';

// Errors

$L['E_access']='De toegang is beperkt...';
$L['E_admin']='De toegang is beperkt tot slechts systeem administratoren.';
$L['E_already_used']='reeds gebruikt';
$L['E_editing']='Data zijn verandered. Verlaten zonder saven?';
$L['E_file_size']='Het bestand is te groot';
$L['E_invalid']='ongeldig';
$L['E_javamail']='Veiligheid: java is nodig om e-mail te zien';
$L['E_mandatory']='Verplicht data niet gevonden';
$L['E_min_4_char']='Minimum 4 karakters';
$L['E_min_2_char']='Minimum 2 karakters';
$L['E_missing_http']='url moet met http:// of https:// beginnen';
$L['E_no_document']='Geen dokument.';
$L['E_no_member']='Geen leden.';
$L['E_no_membership']='Dit gebruiker is niet leden van een groep...';
$L['E_private_membership']='Is ook in een system-groep (verborgen)';
$L['E_no_public_section']='Dit groep is privaat. De priv&eacute; groep toegang vereist login.';
$L['E_no_upload']='Upload van dokumenten door leden is onbruikbaar gemaakt...';
$L['E_pixels_max']='Pixels maximum';
$L['E_pwd_char']='Het wachtwoord bevat ongeldig karakter.';
$L['E_max_10']='Maximum 10';
$L['E_more_than_5']='Meer dan 5 velden worden niet geadviseerd';
$L['No_future'][0]='Toekomst toegestaan';
$L['No_future'][2]='Toekomst toegestaan, alleen dit jaar';
$L['No_future'][1]='Toekomst verboden';
$L['E_data_not_saved']='Gewijzigde gegevens. Zonder op te slaan verlaten ?';

// Success

$L['S_update']='Voltooide update...';
$L['S_delete']='Schrap voltooid...';
$L['S_insert']='Succesvolle verwezenlijking...';
$L['S_save']  ='Sparen voltooid...';