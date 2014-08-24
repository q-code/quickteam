<?php

/**
 * PHP version 5
 *
 * LICENSE: This source file is subject to version 3.0 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category   Team
 * @package    QuickTeam team
 * @author     Philippe Vandenberghe <info@qt-cute.org>
 * @copyright  2014 The PHP Group
 * @version    3.0 build:20140608
 */

session_start();
require 'bin/qte_init.php';
include Translate(APP.'_coppa.php');

include APP.'_adm_inc_hd.php';

echo <<<END
<h2>{$L['Register']} / COPPA</h2>
<p>{$L['Coppa']['Form_info']}<br />{$_SESSION[QT]['admin_name']}<br />{$_SESSION[QT]['admin_email']}<br />{$_SESSION[QT]['admin_fax']}<br />{$_SESSION[QT]['admin_addr']}</p>
<table class="hidden">
<colgroup span="5">
<col width="300"></col>
<col width="30"></col>
<col width="170"></col>
<col width="30"></col>
<col width="170"></col>
</colgroup>
<tr>
<td class="hidden"><b>{$L['Coppa']['Permission']}</b></td>
<td style="padding: 4px;border-style: solid;border-width: 1px; border-color: black">&nbsp;</td>
<td style="padding: 4px;border-style: solid;border-width: 0px; border-color: black">{$L['Y']}</td>
<td style="padding: 4px;border-style: solid;border-width: 1px; border-color: black">&nbsp;</td>
<td style="padding: 4px;border-style: solid;border-width: 0px; border-color: black">{$L['N']}</td>
</tr>
<tr>
<td class="hidden">{$L['Coppa']['Child_name']}</td>
<td colspan="4" style="padding: 4px;border-style: solid;border-width: 1px; border-color: black">&nbsp;</td>
</tr>
<tr>
<td class="hidden">{$L['Coppa']['Child_login']}</td>
<td colspan="4" style="padding: 4px;border-style: solid;border-width: 1px; border-color: black">&nbsp;</td>
</tr>
<tr>
<td class="hidden">{$L['Coppa']['Child_email']}</td>
<td colspan="4" style="padding: 4px;border-style: solid;border-width: 1px; border-color: black">&nbsp;</td>
</tr>
<tr>
<td class="hidden">{$L['Coppa']['Child_privacy']}</td>
<td style="padding: 4px;border-style: solid;border-width: 1px; border-color: black">&nbsp;</td>
<td style="padding: 4px;border-style: solid;border-width: 0px; border-color: black">{$L['Coppa']['Members']}</td>
<td style="padding: 4px;border-style: solid;border-width: 1px; border-color: black">&nbsp;</td>
<td style="padding: 4px;border-style: solid;border-width: 0px; border-color: black">{$L['Coppa']['Nobody']}</td>
</tr>
</table>
<p>{$L['Coppa']["Agreement"]}</p>
<table class="hidden">
<colgroup span="2">
<col width="300"></col>
<col width="400"></col>
</colgroup>
<tr>
<td class="hidden">{$L['Coppa']['Parent_name']}</td>
<td style="padding: 4px;border-style: solid;border-width: 1px; border-color: black">&nbsp;</td>
</tr>
<tr>
<td class="hidden">{$L['Coppa']['Parent_relation']}</td>
<td style="padding: 4px;border-style: solid;border-width: 1px; border-color: black">&nbsp;</td>
</tr>
<tr>
<td class="hidden">{$L['Coppa']['Parent_email']}</td>
<td style="padding: 4px;border-style: solid;border-width: 1px; border-color: black">&nbsp;</td>
</tr>
<tr>
<td class="hidden">{$L['Coppa']['Parent_phone']}</td>
<td style="padding: 4px;border-style: solid;border-width: 1px; border-color: black">&nbsp;</td>
</tr>
<tr>
<td class="hidden"><br /><br />{$L['Coppa']['Parent_sign']}</td>
<td style="padding: 4px;border-style: solid;border-width: 1px; border-color: black"><br /></br /><br /><br /><br /></td>
</tr>
</table>

<p>{$L['Coppa']['End']}</p>\n
END;

include APP.'_adm_inc_ft.php';