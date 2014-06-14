<?php

// QuickTeam 3.0 build:20140608

echo '<div style="width:300px; margin-bottom:20px"><h1>',$oVIP->selfname,'</h1>';
if ( isset($strPageversion) ) echo '<p class="small">',$strPageversion,'</p>';
if ( !empty($error) ) echo '<p id="infomessage" class="error">',$error,'</p>';
if ( empty($error) && !empty($warning) ) echo '<p id="infomessage" class="warning">',$warning,'</p>';
if ( empty($error) && isset($strInfo) ) echo '<p id="infomessage" style="color:#007F11"><b>',$strInfo,'</b></p>';
echo '</div>
';

if ( file_exists(Translate($oVIP->selfurl.'.txt')) )
{
  echo '<div class="hlp_box">',PHP_EOL;
  echo '<div class="hlp_head">',L('Help'),'</div>',PHP_EOL;
  echo '<div class="hlp_body">'; include Translate($oVIP->selfurl.'.txt'); echo '</div>',PHP_EOL;
  echo '</div>',PHP_EOL;
}

// animation of the infomessage (errors remains on screen)
if ( isset($strInfo) )
{
echo '
<script type="text/javascript">
<!--
setTimeout(\'document.getElementById("infomessage").style.color="#bbbbbb"\',3000);
setTimeout(\'document.getElementById("infomessage").style.color="#cccccc"\',3300);
setTimeout(\'document.getElementById("infomessage").style.color="#dddddd"\',3600);
setTimeout(\'document.getElementById("infomessage").innerHTML="&nbsp;"\',3900);
-->
</script>
';
}

?>