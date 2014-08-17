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
  $end = (float)vsprintf('%d.%06d', gettimeofday());
  if ( isset($oDB->stats['num']) ) echo $oDB->stats['num'],' queries. ';
  if ( isset($oDB->stats['start']) ) echo 'End queries in ',round($end-$oDB->stats['start'],4),' sec. ';
  if ( isset($oDB->stats['pagestart']) ) echo 'End page in ',round($end-$oDB->stats['pagestart'],4),' sec. ';
}

echo $oHtml->End();

ob_end_flush();