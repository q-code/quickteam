<?php

// Is is recommended to always use capital on first letter in the translation
// software change to lower case if necessary.

// coppa

$L['Agree']='I have read, and agree to abide by these rules.';
$L['I_am_child']='I am under 13 years of age';
$L['I_am_not_child']='I am over or exactly 13 years of age';
$L['E_coppa_confirm']='Agreement from your parent/guardian not yet received. Please wait.';
$L['Coppa_status']='Coppa status';
$L['Coppa_agreement_date']='Parent/guardian agreement';
$L['Coppa_request_date']='Request date';

// registration

$L['Proceed']='Proceed to registration';
$L['Choose_name']='Choose a username';
$L['Choose_password']='Choose a password';
$L['Old_password']='Old password';
$L['New_password']='New password';
$L['Confirm_password']='Confirm the password';
$L['Password_updated']='Password updated';
$L['Password_by_mail']='Temporary password will be send to your e-mail address.';
$L['Your_mail']='Your e-mail';
$L['Parent_mail']='Parent/guardian e-mail';
$L['Security']='Security';
$L['Reset_pwd']='Reset password';
$L['Reset_pwd_help']='The application will send by e-mail a new single-use access password key.';
$L['Type_code']='Type the security code you see.';
$L['Request']='Request';
$L['Request_completed']='Request completed';
$L['Register_completed']='Registration completed...';
$L['H_Unregister']='By unregistering, you will stop having access to this application as a member.<br />Your profile will be deleted and your account will no more be visible in the memberlist.<br/><br/>Enter your password to confirm unregistration...';

// Secret question

$L['H_Secret_question']='This question will be asked if you forget your password.';
$L['Update_secret_question']='Your profile must be updated...<br /><br />To improve security, we request you to define your own "Secret question". This question will be asked if you forget your password.';
$L['Secret_q']['What is the name of your first pet?']='What is the name of your first pet?';
$L['Secret_q']['What is your favorite character?']='What is your favorite character?';
$L['Secret_q']['What is your favorite book?']='What is your favorite book?';
$L['Secret_q']['What is your favorite color?']='What is your favorite color?';
$L['Secret_q']['What street did you grow up on?']='What street did you grow up on?';

// login and profile

$L['Password']='Password';
$L['Remember']='Remember me';
$L['Forgotten_pwd']='Forgotten password';
$L['Change_password']='Change password';
$L['Change_picture']='Change picture';
$L['Picture_thumbnail'] = 'The uploaded image is too large.<br />To define your picture, draw a square in the large image.';
$L['My_picture']='My picture';
$L['H_Change_picture']='(maximum '.$_SESSION[QT]['picture_width'].'x'.$_SESSION[QT]['picture_height'].' pixels, '.$_SESSION[QT]['picture_size'].' Kb)';
$L['Delete_picture']='Delete picture';
$L['Goodbye']='You are disconnected.<br /><br />Goodbye...';

// Help

$L['Reg_help']='Please fill in this form to complete your registration.<br /><br />Username and password must be at least 4 characters without tags or trailing spaces.<br /><br />E-mail address will be used to send you a new password if you forgot it. In your profile you can change your privacy setting to make it visible only for the staff members.<br /><br />If you are visually impaired or cannot otherwise read the security code please contact the <a class="small" href="mailto:'.$_SESSION[QT]['admin_email'].'">Administrator</a> for help.<br /><br />';
$L['Reg_mail']='You will receive an email shortly including a temporary password.<br /><br />You are invited to log in and edit your profile to define your own password.';
$L['Reg_pass']='Password reset.<br /><br />If you have forgotten your password, please enter your username. We will send you a single-use access password key that will allow you to select a new password.';
$L['Reg_pass_reset']='We can send you a new password if you can answer your secret question.';