<?php

echo '<p><b>Applicatie eigenaar</b></p>
<p>',$_SESSION[QT]['site_name'],'</p>
<p>Webmaster: <a href="mailto:',$_SESSION[QT]['admin_email'],'">',$_SESSION[QT]['admin_email'],'</a></p>
<p>Contact: ',$_SESSION[QT]['admin_name'],'<br />',$_SESSION[QT]['admin_addr'],'<br />',$_SESSION[QT]['admin_fax'],'</p>
<p><b>Applicatie gemaakt door</b></p>
<p>QT-cute (www.qt-cute.org) versie ',QTEVERSION,'</p>
<p><b>Vergunning (engels)</b></p>
<p><img src="admin/vgplv3.png" width="88" height="31" alt="GPL" title="GNU General Public License" /></p>
<p>Zie documenten <a href="admin/license.txt">Application License</a> en <a href="admin/license_gpl.txt">GNU General Public License</a> voor meer informatie.</p>
<p><b>Naleving</b></p>
';