<?php

// QuickTeam 3.0 build:20140608

echo cHtml::Page(END);

echo '<!-- MENU/PAGE -->
</td>
</tr>
</table>
<!-- END MENU/PAGE -->
';

if ( isset($oDB->stats) )
{
  $oDB->stats['end']=gettimeofday(true);
  echo '<br />&nbsp;',$oDB->stats['num'],' queries in ',round($oDB->stats['end']-$oDB->stats['start'],3),' sec';
}

echo $oHtml->End();

ob_end_flush();