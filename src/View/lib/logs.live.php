<?php
/*
 * Copyright (C) 2004-2025 Soner Tari
 *
 * This file is part of UTMFW.
 *
 * UTMFW is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * UTMFW is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with UTMFW.  If not, see <http://www.gnu.org/licenses/>.
 */

/** @file
 * All live log pages include this file.
 *
 * Includes a different top menu if so configured. Currently only for pf.
 *
 * Module configuration are in $LogConf. Module pages which include
 * this file should first set its module index as $View.
 *
 * Restarts the session for live page reload rate.
 */
require_once('../lib/vars.php');

/**
 * Prints live logs help box.
 */
function PrintLiveLogsHelp($msg)
{
	$msg.= ($msg !== '' ? "\n\n" : '')._HELPWINDOW('If you are not seeing as many number of lines as you were expecting, this may be because the log file has turned over and put in a compressed archive file.

You can search the logs by entering keywords or extended regular expressions in Regexp box. Regular expressions are de-facto standard for text searching.');

	PrintHelpWindow($msg);
}

$Reload= TRUE;
SetRefreshInterval();

$View->Controller($Output, 'GetDefaultLogFile');
$LogFile= $Output[0];

UpdateLogsPageSessionVars($LinesPerPage, $SearchRegExp, $SearchNeedle);

$LogSize= 0;
if ($View->Controller($Output, 'GetFileLineCount', $LogFile, $SearchRegExp)) {
	$LogSize= $Output[0];
}

require_once($VIEW_PATH.'/header.php');

PrintLiveLogHeaderForm($LinesPerPage, $SearchRegExp);
?>
<table id="logline">
	<?php
	PrintTableHeaders($View->Model);

	$Logs= array();
	if ($View->Controller($Output, 'GetLiveLogs', $LogFile, $LinesPerPage, $SearchRegExp)) {
		$Logs= json_decode($Output[0], TRUE);
	} else if ($View->Controller($Output, 'GetLastLogs', $LogFile, $LinesPerPage)) {
		$Logs= json_decode($Output[0], TRUE);
	}

	$LineCount= 1;
	if ($LogSize > $LinesPerPage ) {
		$LineCount= $LogSize - $LinesPerPage + 1;
	}

	$LastLineNum= $LineCount + min(array($LogSize, $LinesPerPage)) - 1;
	foreach ($Logs as $Logline) {
		$View->PrintLogLine($Logline, $LineCount++, $LastLineNum);
	}
	?>
</table>
<?php
PrintLiveLogsHelp($View->LogsHelpMsg);
require_once($VIEW_PATH.'/footer.php');
?>
