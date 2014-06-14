<?php
echo '
<p>This section is for website that uses both QuickTeam and QuickTalk/QuickTicket applications.<br />
If you don\'t have the QuickTicket/QuickTalk application installed, let the option "Accept QuickTicket/QuickTalk users" empty and "Accept external users" to No.</p>
';
echo '
<h2>What means: Accept QuickTicket/QuickTalk users?</h2>
<p>QuickTeam accepts users if they are already logged in QuickTicket or QuickTalk.</p>
<p>To make this option working, provide the "SID". This value is a 4 characters code noted in the QuickTicket/QuickTalk administration page, after the Version indications.</p>
';
echo '
<h2>What means: Accept external users?</h2>
<p>Some websites have their own login page (and this page uses QuickTeam to authenticate the users and store the profiles).</p>
<p>As for QuickTicket/QuickTalk users, you may want that QuickTeam accepts users if they are logged in the website.</p>
';
echo '
<h2>What is the limitation?</h2>
<p>
To make this automatic log-in working, the user must be registered in QuickTeam and in QuickTicket/QuickTalk under the same username. If the username is different in both applications, QuickTeam cannot trust this login as a valid authenticated user, and this user will be invited to log in QuickTeam.</p>
';