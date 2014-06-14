<?php

// If your board accept several languages (latin origin), the charset 'windows-1252' is recommended in order to render all accents correctly.
// If your board accept english only, you can use the charset 'utf-8'.
if ( !defined('QT_HTML_CHAR') ) define ('QT_HTML_CHAR', 'utf-8');
if ( !defined('QT_HTML_DIR') ) define ('QT_HTML_DIR', 'ltr');
if ( !defined('QT_HTML_LANG') ) define ('QT_HTML_LANG', 'fr');
if ( !defined('QT_QUERY_SEPARATOR') ) define ('QT_QUERY_SEPARATOR', ';');

// Is is recommended to always use capital on first letter in the translation
// software changes to lower case if necessary.

$L['Y']='Oui';
$L['N']='Non';

// Specific vocabulary

$L['Domain']='Domaine';
  $L['Domains']='Domaines';
$L['Section']='Groupe';
  $L['Sections']='Groupes';
$L['Field']='Champ';
  $L['Fields']='Champs';
$L['User']='Membre';
  $L['Users']='Membres';
  $L['User_man']='G&eacute;rer les membres';
  $L['User_add']='Nouveau membre';
  $L['User_del']='Effacer membres';
  $L['User_upd']='Editer le profil';
$L['Role']='R&ocirc;le';
  $L['Userrole_A']='Administrateur&nbsp;syst&egrave;me'; $L['Userrole_As']='Administrateurs&nbsp;syst&egrave;me';
  $L['Userrole_M']='Coordinateur&nbsp;syst&egrave;me';   $L['Userrole_Ms']='Coordinateurs&nbsp;syst&egrave;me';
  $L['Userrole_U']='Utilisateur&nbsp;enregistr&eacute;'; $L['Userrole_Us']='Utilisateurs&nbsp;enregistr&eacute;s';
  $L['Userrole_V']='Visiteur';                           $L['Userrole_Vs']='Visiteurs';
  $L['Section_moderator']='Coordinateur du groupe';
$L['Coppa_status']='Coppa statut';
  $L['H_Coppa_status']='(visible uniquement pas les membres du staff)';
  $L['Coppa_child'][0]='Majeur';
  $L['Coppa_child'][1]='Mineur avec accord parental';
  $L['Coppa_child'][2]='Mineur sans accord parental';
  $L['Coppa_apply']='Appliquer les r&egrave;gles COPPA';
  $L['Coppa_notapply']='Abandonner les r&egrave;gles COPPA';
$L['Status']='Statut';
  $L['Statuses']='Statuts';
$L['Title']='Titre';

// Common

$L['Add']='Ajouter';
$L['Age']='Age';
$L['All']='Tous';
$L['Birthday']='Date de naissance';
$L['Birthdays_calendar']='Calendrier des anniversaires';
$L['Birthdays_today']='Joyeux anniversaire';
$L['Calendar']='Calendrier';
$L['Cancel']='Annuler';
$L['Change']='Changer';
$L['Change_status']='Changer le statut';
$L['Change_role']='Changer le role';
$L['Change_name']='Changer l\'identifiant';
$L['Continue']='Continuer';
$L['Coord']='Coordonn&eacute;es';
$L['Coord_latlon']='(lat,lon)';
$L['Csv']='Export'; $L['H_Csv']='Ouvrir dans un tableur';
$L['Date']='Date';
$L['Dates']='Dates';
$L['Day']='Jour';
$L['Days']='Jours';
$L['Delete']='Effacer';
$L['Description']='Description';
$L['Display_at']='Afficher &agrave; la date';
$L['Edit']='Editer';
$L['Email']='E-mail';
$L['Emails']='E-mails';
$L['Exit']='Exit';
$L['First']='Premi&egrave;re';
$L['Goto']='Allez';
$L['Happy_birthday']='Joyeux anniversaire &agrave; ';
$L['H_Website']='Url avec http://';
$L['Help']='Aide';
$L['Hide']='Masquer';
$L['Hidden']='Cach&eacute;';
$L['Hidden_info']='Uniquement visible par vous-m&ecirc;me et les membres du staff';
$L['Information']='Information';
$L['Items_per_month']='Inscriptions par mois';
$L['Items_per_month_cumul']='Cumul des inscriptions par mois';
$L['Last']='Derni&egrave;re';
$L['Last_registration']='Derni&egrave;re inscription';
$L['Legend']='L&eacute;gende';
$L['List']='Liste';
$L['Mandatory']='Obligatoire';
$L['Maximum']='Maximum';
$L['Minimum']='Minimum';
$L['Missing']='Un champ obligatoire est vide';
$L['Month']='Mois';
$L['Move']='D&eacute;placer';
$L['Move_to']='D&eacute;placer vers';
$L['Name']='Nom';
$L['Next']='Suivant';
$L['None']='Aucun';
$L['Ok']='Ok';
$L['or']='ou';
$L['or_drop_file'] = 'ou d&eacute;poser un fichier sur le button';
$L['or_drop_files'] = 'ou d&eacute;poser des fichiers sur le button';
$L['Other']='Autre';
$L['Other_char']='un chiffre ou symbole';
$L['Page']='page';
$L['Pages']='pages';
$L['Picture']='Photo';
$L['Preview']='Aper&ccedil;u';
$L['Previous']='Pr&eacute;c&eacute;dente';
$L['Privacy']='Vie&nbsp;priv&eacute;e';
$L['Remove']='Enlever';
$L['Rename']='Renommer';
$L['Result']='R&eacute;sultat';
$L['Results']='R&eacute;sultats';
$L['Save']='Sauver';
$L['Seconds']='Secondes';
$L['Selected_from']='sur';
$L['Selection']='S&eacute;lection';
$L['Send']='Envoyer';
$L['Show']='Afficher';
$L['Starting_with']='commen&ccedil;ant par';
$L['Time']='Heure';
$L['Total']='Total';
$L['Type']='Type';
$L['Unknown']='Inconnu';
$L['Username']='Nom d\'utilisateur';
$L['Version']='Version';
$L['Welcome']='Bienvenue';
$L['Welcome_to']='Bienvenue &agrave; un nouveau membre, ';
$L['Welcome_not']='Je ne suis pas %s !';
$L['Year']='Ann&eacute;e';

// Menu

$L['Search']='Chercher';
$L['Login']='Connexion';
$L['Logout']='D&eacute;connexion';
$L['Register']='S\'enregistrer';
$L['Profile']='Profil';
$L['PProfile']='Profil&nbsp;personnel';
$L['TProfile']='Profil&nbsp;de&nbsp;groupe';
$L['MProfile']='Appartenances';
$L['SProfile']='Inscription';
$L['DProfile']='Documents';
$L['Administration']='Administration';
$L['Legal']='Notices l&eacute;gales';

// Team and Profile

$L['Last_column']='Extra colonne';
$L['Edit_start']='Editer...';
$L['Edit_stop']='Stopper l\'&eacute;dition';
$L['Export_csv']='Exporter';
$L['W_Somebody_else']='Attention... Vous &eacute;ditez le profil de quelqu\'un d\'autre';
$L['Secret_question']='Question secr&egrave;te';
$L['Unregister']='D&eacute;sinscription';
$L['Document_add']='Ajouter un document';
$L['Document_name']='Nom du document';
$L['Calendar_show_all']='Membres de tous les groupes';
$L['Calendar_show_this']='Members de ce groupe uniquement';
$L['Image_preview']='Aper&ccedil;u des images';

// Search

$L['Advanced']='Recherche avanc&eacute;e';
$L['Keywords']='Mot(s) cl&eacute;';
$L['Search_by_key']='Chercher par mot cl&eacute;';
$L['Search_by_status']='Chercher par statut';
$L['Search_by_age']='Chercher par &acirc;ge';
$L['Search_result']='R&eacute;sultat de la recherche';
$L['Search_criteria']='Crit&egrave;re de recherche';
$L['In_section']='dans le groupe';
$L['In_all_sections']='Dans tous les groupes';
$L['Users_without_section']='Membres sans groupe';
$L['Users_in_0_only']='Membres uniquement dans "%s"';

// Ajax helper

$L['All_categories']='Toutes les cat&eacute;gories';
$L['Category_not_yet_used']='Cat&eacute;gorie non utilis&eacute;e';
$L['Impossible']='Impossible';
$L['No_result']='Aucun r&eacute;sultat';
$L['Try_other_lettres'] = 'Essayez d\'autres lettres';
$L['Try_without_options'] = 'Essayez sans option';
$L['Try_all_sections'] = 'Essayez avec tous les groupes';

// Stats & emails

$L['Statistics']='Statistiques';
$L['General_site']='Site en g&eacute;neral';
$L['Section_start_date']='Date de d&eacute;but';
$L['Distinct_users']='Utilisateurs diff&eacute;rents (ayant post&eacute;s un message)';

// Users

$L['Section_members']='Membres du groupe';
$L['Registered_members']='Utilisateurs enregistr&eacute;s';
$L['Users_section']='Tous les membres du groupe';
$L['Users_reg']    ='Tous les utilisateurs enregistr&eacute;s';
$L['Users_not_in_team']='Utilisateurs sans groupe';
$L['User_section_add']='Ajouter au groupe';
$L['User_section_del']='Enlever du groupe';
$L['H_semicolon_useredit']='Pour ins&eacute;rer plusieurs informations, s&eacute;parez-les avec une virgule.<br />Exemple: "1-5-123456(gsm), 1-5-123459(fax)"';
$L['H_semicolon_format']='Pour cr&eacute;er un format particulier, utilisez les formules php.<br />Par exemple, pour afficher l\'&acirc;ge "%s ans".<br />Pour cr&eacute;er une liste d&eacute;roulante, notez les valeurs s&eacute;par&eacute;es par une virgule.<br />Exemple: "Oui, Non"';
$L['H_hidden_fields']='Les informations cach&eacute;es seront visible uniquement par les coordinateurs et administrateurs du syst&egrave;me';
$L['Fields_default']='Colonnes par d&eacute;faut';
$L['Fields_personal']='Info personnelles';
$L['Fields_team']='Info de groupe';
$L['Fields_computed']='Info calcul&eacute;es';
$L['Fields_system']='Info syst&egrave;me';

// Timezones

$L['H_Date']='(aaaa-mm-jj)';
$L['dateMMM']=array(1=>'Janvier','F&eacute;vrier','Mars','Avril','Mai','Juin','Juillet','Ao&ucirc;t','Septembre','Octobre','Novembre','D&eacute;cembre');
$L['dateMM'] =array(1=>'Jan','F&eacute;v','Mar','Avr','Mai','Juin','Juil','Ao&ucirc;t','Sept','Oct','Nov','D&eacute;c');
$L['dateM']  =array(1=>'J','F','M','A','M','J','J','A','S','O','N','D');
$L['dateDDD']=array(1=>'Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi','Dimanche');
$L['dateDD'] =array(1=>'Mon','Tue','Wed','Thu','Fri','Sat','Sun');
$L['dateD']  =array(1=>'L','M','M','J','V','S','D');
$L['dateSQL']=array(
  'January'  => 'Janvier',
  'February' => 'F&eacute;vrier',
  'March'    => 'Mars',
  'April'    => 'Avril',
  'May'      => 'Mai',
  'June'     => 'Juin',
  'July'     => 'Juillet',
  'August'   => 'Ao&ucirc;t',
  'September'=> 'Septembre',
  'October'  => 'Octobre',
  'November' => 'Novembre',
  'December' => 'D&eacute;cembre',
  'Monday'   => 'Lundi',
  'Tuesday'  => 'Mardi',
  'Wednesday'=> 'Mercredi',
  'Thursday' => 'Jeudi',
  'Friday'   => 'Vendredi',
  'Saturday' => 'Samedi',
  'Sunday'   => 'Dimanche',
  'Today'=>'Aujourd\'hui',
  'Yesterday'=>'Hier',
  'Jan'=>'Jan',
  'Feb'=>'F&eacute;v',
  'Mar'=>'Mar',
  'Apr'=>'Avr',
  'May'=>'Mai',
  'Jun'=>'Jun',
  'Jul'=>'Jul',
  'Aug'=>'Ao&ucirc;t',
  'Sep'=>'Sept',
  'Oct'=>'Oct',
  'Nov'=>'Nov',
  'Dec'=>'D&eacute;c',
  'Mon'=>'Lu',
  'Tue'=>'Ma',
  'Wed'=>'Me',
  'Thu'=>'Je',
  'Fri'=>'Ve',
  'Sat'=>'Sa',
  'Sun'=>'Di');

// Icons

$L['Ico_section_0_0']='Groupe public (&eacute;ditable)';
$L['Ico_section_0_1']='Groupe public (verrouill&eacute;)';
$L['Ico_section_1_0']='Groupe cach&eacute; (&eacute;ditable)';
$L['Ico_section_1_1']='Groupe cach&eacute; (verrouill&eacute;)';
$L['Ico_section_2_0']='Groupe priv&eacute; (&eacute;ditable)';
$L['Ico_section_2_1']='Groupe priv&eacute; (verrouill&eacute;)';
$L['Ico_user_p']='Utilisateur';
$L['Ico_user_pZ']='Utilisateur non identifi&eacute;';
$L['Ico_user_w']='Voir le site web';
$L['Ico_user_wZ']='pas de site web';
$L['Ico_user_e']='Envoyer un e-mail';
$L['Ico_user_eZ']='pas d\'e-mail';
$L['Ico_view_n']='Vue normale';
$L['Ico_view_c']='Vue compacte';
$L['Ico_view_p']='Imprimer';
$L['Ico_view_f_c']='Vue calendrier';
$L['Ico_view_f_n']='Vue tableau';

// Restrictions

$L['R_login_register']='Acc&egrave;s r&eacute;serv&eacute; aux seuls membres...<br /><br />Veuillez vous connecter pour pouvoir continuer. Pour devenir membre, utilisez le menu s\'enregistrer.';
$L['R_user']='Acc&egrave;s r&eacute;serv&eacute; aux seuls membres.';
$L['R_staff']='Acc&egrave;s r&eacute;serv&eacute; aux coordinateurs syst&egrave;me.';

// Errors

$L['E_access']='Acc&egrave;s refus&eacute;...';
$L['E_admin']='Acc&egrave;s r&eacute;serv&eacute; aux administrateurs syst&egrave;me.';
$L['E_already_used']='d&eacute;j&agrave; utilis&eacute;';
$L['E_editing']='Des donn&eacute;es sont modifi&eacute;es. Quitter sans sauver?';
$L['E_file_size']='Fichier trop gros';
$L['E_invalid']='erron&eacute;';
$L['E_javamail']='Protection: activez java pour voir les adresses e-mail';
$L['E_mandatory']='Un champ obligatoire est vide';
$L['E_min_4_char']='Minimum 4 caract&egrave;res';
$L['E_min_2_char']='Minimum 2 caract&egrave;res';
$L['E_missing_http']='l\'url doit commencer par http:// ou https://';
$L['E_no_document']='Aucun document.';
$L['E_no_member']='Aucun membre.';
$L['E_no_membership']='Cet utilisateur n\'est pas membre d\'un groupe...';
$L['E_private_membership']='Est &eacute;galement dans un groupe syst&egrave;me (non visible au public)';
$L['E_no_public_section']='Le site ne contient pas de groupe publique. Pour acc&eacute;der aux groupes priv&eacute;s, vous devez vous identifier.';
$L['E_no_upload']='L\'ajout de documents par les membres a &eacute;t&eacute; d&eacute;sactiv&eacute;...';
$L['E_pixels_max']='Pixels maximum';
$L['E_pwd_char']='Le mot de passe contient des caract&egrave;res non-valides.';
$L['E_max_10']='Maximum 10';
$L['E_more_than_5']='Plus de 5 champs n\'est pas recommand&eacute;';
$L['No_future'][0]='Futur autoris&eacute;';
$L['No_future'][2]='Futur autoris&eacute; pour cette ann&eacute;e';
$L['No_future'][1]='Futur interdit';
$L['E_data_not_saved']='Edition en cours. Quitter sans sauver ?';

// Success

$L['S_update']='Changement effectu&eacute;...';
$L['S_delete']='Effacement effectu&eacute;...';
$L['S_insert']='Cr&eacute;ation termin&eacute;e...';
$L['S_save']='Sauvegarde r&eacute;ussie...';