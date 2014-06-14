<?php
echo '
<p>Deze sectie is voor websites die zowel QuickTeam als QuickTicket/QuickTalk gebruiken. Als u niet de QuickTalk of QuickTicket ge&iuml;nstalleerd hebt, laat de optie "QuickTicket/QuickTalk gebruikers toelaten" lege en Nee voor "Website gebruikers toelaten".</p>
';
echo '
<h2>Wat betekent: QuickTicket/QuickTalk gebruikers toelaten ?</h2>
<p>QuickTeam keurt gebruikers goed als zij reeds in QuickTicket/QuickTalk ingelogd zijn.</p>
<p>Om deze optie werkend te maken, verstrek de "SID". Deze waarde is een 4 karakterscode die in de QuickTicket of QuickTalk administratiepagina wordt genoteerd, na de Versie nummer.</p>
';
echo '
<h2>Wat betekent: Website gebruikers toelaten ?</h2>
<p>Sommige websites hebben hun eigen login pagina (en deze pagina gebruikt QuickTeam om de gebruikers voor authentiek te verklaren en de profielen op te slaan).</p>
<p>Zoals voor QuickTicket/QuickTalk gebruikers, kunt u willen dat QuickTeam gebruikers goedkeurt als zij via de website ingelogd worden.</p>
';
echo '
<h2>Wat de beperking is ?</h2>
<p>
Om dit automatische log-in werkend te maken, moet de gebruiker in QuickTeam en in QuickTicket/QuickTalk onder de zelfde gebruikernaam worden geregistreerd. Als de gebruikernaam in beide systeem verschillend is, kan QuickTeam niet op dit login als geldige voor authentiek verklaarde gebruiker vertrouwen, en deze gebruiker zal worden verzocht om een log-in te make.</p>
';