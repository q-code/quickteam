<?php

// QuickTeam 3.0 build:20140608

echo '
</td>
</tr>
</table>
';

if ( isset($strPrevUrl) && isset($strNextUrl) )
{
echo '
<div class="banner">
<table class="hidden">
<tr>
<td style="color:white;font-size:8pt">powered by <a style="color:white" href="http://www.qt-cute.org">QT-cute</a></td>
<td style="text-align:right"><a class="button" href="',$strPrevUrl,'">',$strPrevLabel,'</a><a class="button" href="',$strNextUrl,'">',$strNextLabel,'</a></td>
</tr>
</table>
</div>
';
}

echo '
</div>
</body>
</html>';