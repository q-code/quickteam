<?php

echo '<p><b>Application operated by</b></p>
<p>',$_SESSION[QT]['site_name'],'</p>
<p>Webmaster: <a href="mailto:',$_SESSION[QT]['admin_email'],'">',$_SESSION[QT]['admin_email'],'</a></p>
<p>Contact: ',$_SESSION[QT]['admin_name'],'<br />',$_SESSION[QT]['admin_addr'],'<br />',$_SESSION[QT]['admin_fax'],'</p>
<p><b>Application created by</b></p>
<p>QT-cute (www.qt-cute.org) version ',QTEVERSION,'</p>
<p><b>Application license</b></p>
<p><img src="admin/vgplv3.png" width="88" height="31" alt="GPL" title="GNU General Public License" /></p>
<p>See the <a href="admin/license.txt">Application License</a> and the <a href="admin/license_gpl.txt">GNU General Public License</a> for more details.</p>
<p><b>Application compliance</b></p>
';