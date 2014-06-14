<?php
echo '
<p>Cette section s\'adresse aux sites web utilisant les deux applications QuickTeam et QuickTalk/QuickTicket.<br />
Si vous n\'utilisez pas QuickTicket ou QuickTalk, laissez l\'option "Accepter les utilisateurs QuickTalk/QuickTicket" vide et "Accepter les utilisateurs externes" &agrave; Non.</p>
';
echo '
<h2>Que signifie : Accepter les utilisateurs QuickTalk/QuickTicket ?</h2>
<p>QuickTeam accepte les utilisateurs qui sont d&eacute;j&agrave; connect&eacute;s sur QuickTalk ou QuickTicket.</p>
<p>Pour activer cette option, indiquez le "SID" de QuickTicket ou QuickTalk. C\'est un code de 4 caract&egrave;res indiqu&eacute; dans la page Administration de QuickTicket ou QuickTalk, apr&egrave;s les indications de Version.
';
echo '
<h2>Que signifie : Accepter les utilisateurs externes ?</h2>
<p>Certains sites web ont leur propre page de login (et cette page utilise QuickTeam pour authentifier les utilisateurs et gerer les profils).</p>
<p>Comme pour les utilisateurs QuickTalk/QuickTicket, vous pouvez demandez &agrave; QuickTeam d\'accepter les utilisateurs qui sont d&eacute;j&agrave; connect&eacute;s via le site web.</p>
';
echo '
<h2>Limitation ?</h2>
<p>
Pour permettre cette identification automatique, il faut que l\'utilisateur soit enregist&eacute; dans QuickTeam, QuickTalk ou QuickTicket sous le m&ecirc;me nom d\'utilisateur. Si le nom est diff&eacute;rent dans les deux applications, QuickTeam ne peut assurer que ce login correspond &agrave; un utilisateur correctement authentif&eacute; dans QuickTalk ou QuickTicket. Dans une telle situation, l\'utilisateur est invit&eacute; &agrave; ce reconnecter pour acc&eacute;der &agrave; QuickTeam.</p>
';