<?php

// Is is recommended to always use capital on first letter in the translation
// software change to lower case if necessary.

// coppa

$L['Agree']='Ik stem toe met de voorwaarden.';
$L['I_am_child']='Ik ben <b>jonger</b> dan 13 jaar';
$L['I_am_not_child']='Ik ben <b>ouder</b> dan 13 jaar';
$L['E_coppa_confirm']='Overeenkomst van uw ouder/beschermer nog niet ontvangen. Gelieve te wachten.';
$L['Coppa_status']='Coppa statuut';
$L['Coppa_agreement_date']='Ouder/beschermer overeenkomst';
$L['Coppa_request_date']='Aanvraag datum';

// registration

$L['Proceed']='Ga aan registratie';
$L['Choose_name']='Kies een gebruikersnaam';
$L['Choose_password']='Kies een wachtwoord';
$L['Old_password']='Oude wachtwoord';
$L['New_password']='Nieuwe wachtwoord';
$L['Confirm_password']='Bevestig wachtwoord';
$L['Password_updated']='Warchtwoord bijgewerkt';
$L['Password_by_mail']='De e-mail met een nieuwe wachtwoord is verstuurd.';
$L['Your_mail']='U e-mail';
$L['Parent_mail']='Ouder/beschermer e-mail';
$L['Security']='Veiligheid';
$L['Reset_pwd']='Herstel wachtwoord';
$L['Reset_pwd_help']='De applicatie zal een nieuwe wachtwoord door e-mail sturen.';
$L['Type_code']='Typ de code u ziet.';
$L['Request']='Aanvraag';
$L['Request_completed']='Aanvraag gestuurd';
$L['Register_completed']='Registratie voltooid...';
$L['H_Unregister']='Met un-registerie, zult u ophouden hebbend toegang tot deze applicatie als lid. Uw profiel zal worden geschrapt en uw naam zal niet meer in ledenlijsten zichtbaar zijn.<br/><br/>Wachtwoord om un-ezgistratie te bevestingen...';

// Secret question

$L['H_Secret_question']='Deze vraag zal worden gevraagd of vergeet u uw wachtwoord.';
$L['Update_secret_question']='Uw profiel moet bijgewerkt worden...<br /><br />Om veiligheid te verbeteren, verzoeken wij u om uw eigen "Geheime vraag" te bepalen. Deze vraag zal worden gevraagd of vergeet u uw wachtwoord.';
$L['Secret_q']['What is the name of your first pet?']='Wat was de naam van uw eerste huisdier?';
$L['Secret_q']['What is your favorite character?']='Wat is uw favoriet karakter?';
$L['Secret_q']['What is your favorite book?']='Wat is uw favoriet boek?';
$L['Secret_q']['What is your favorite color?']='Wat is uw favoriet kleur?';
$L['Secret_q']['What street did you grow up on?']='In welke straat groeide u ?';

// Login and profile

$L['Password']='Wachtwoord';
$L['Remember']='Log me automatisch in';
$L['Forgotten_pwd']='Wachtwoord vergeten';
$L['Change_password']='Watchwoord bijwerken';
$L['Change_picture']='Foto bijwerken';
$L['Picture_thumbnail'] = 'Beeld is te groot.<br />Om uw foto te make, schetst een rechthoek in het grote beeld.';
$L['My_picture']='Mijn foto';
$L['H_Change_picture']='(maximum '.$_SESSION[QT]['picture_width'].'x'.$_SESSION[QT]['picture_height'].' pixels, '.$_SESSION[QT]['picture_size'].' Kb)';
$L['Delete_picture']='Foto verwijderen';
$L['Goodbye']='U bent uitgelogd.<br /><br />Tot ziens...';

// Help

$L['Reg_help']='Gelieve te vullen deze vorm in om uw registratie te voltooien.<br /><br />De gebruikersnaam en het wachtwoord moeten minstens 4 karakters zonder html markeringen of ruimten zijn.<br /><br />Het e-mail adres zal worden gebruikt om u een nieuw wachtwoord te verzenden als u het vergat. Om het onzichtbaar te maken, verander uw privacy instellingen in uw Profiel.<br /><br />Als u met gezichtsstoornissen bent of de veiligheidscode niet kunt lezen, gelieve de <a class="small" href="mailto:'.$_SESSION[QT]['admin_email'].'">Beheerder</a> te contacteren.<br /><br />';
$L['Reg_mail']='U zult binnenkort een e-mail met een tijdelijk wachtwoord ontvangen.<br /><br />U wordt verzocht om uw profiel uit te geven en uw eigen wachtwoord te bepalen.';
$L['Reg_pass']='Nieuwe wachtwoord.<br /><br />Als u uw wachtwoord hebt vergeten, gelieve uw gebruikersnaam invullen. Wij zullen u een tijdelijk wachtwoord verzenden die u zal toestaan om een nieuw wachtwoord te selecteren.';
$L['Reg_pass_reset']='Wij kunnen u een nieuw wachtwoord verzenden als u uw geheime vraag kunt beantwoorden.';