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
 * All log archive pages include this file.
 * Log configuration is in $LogConf.
 */

require_once('../lib/vars.php');

/**
 * Prints archives log help box.
 */
function PrintLogsHelp($msg)
{
	$msg.= ($msg !== '' ? "\n\n" : '')._HELPWINDOW('Log keeping capacity of PFFW is limited only by the size of the disks on your system.

If you are not seeing as many number of lines as you were expecting, this may be because the log file has turned over and put in a compressed archive file. The default maximum number of archive files for most services is 100, and can be configured on the system configuration pages. Depending on how busy a service it is, this many log archives may mean months of logging in most cases. You can download the log files using the Download button.

You can search the logs by entering keywords or extended regular expressions in Regexp box. Regular expressions are de-facto standard for text searching.');

	PrintHelpWindow($msg);
}

$View->UploadLogFile();

$LogFile= GetLogFile();

ProcessStartLine($StartLine);
UpdateLogsPageSessionVars($LinesPerPage, $SearchRegExp, $SearchNeedle);

$ApplyDefaults= TRUE;

$DateArray= array();
if (count($_POST)) {
	if (!filter_has_var(INPUT_POST, 'Defaults')) {
		$DateArray['Month']= filter_input(INPUT_POST, 'Month');
		$DateArray['Day']= filter_input(INPUT_POST, 'Day');
		$DateArray['Hour']= filter_input(INPUT_POST, 'Hour');
		$DateArray['Minute']= filter_input(INPUT_POST, 'Minute');
		$ApplyDefaults = FALSE;
	}
}
// Use isset here, Month and Day may be empty string
else if (isset($_SESSION[$View->Model][$Submenu]['Month'],
	$_SESSION[$View->Model][$Submenu]['Day'],
	$_SESSION[$View->Model][$Submenu]['Hour'],
	$_SESSION[$View->Model][$Submenu]['Minute'])) {

	$DateArray['Month']= $_SESSION[$View->Model][$Submenu]['Month'];
	$DateArray['Day']= $_SESSION[$View->Model][$Submenu]['Day'];
	$DateArray['Hour']= $_SESSION[$View->Model][$Submenu]['Hour'];
	$DateArray['Minute']= $_SESSION[$View->Model][$Submenu]['Minute'];
	$ApplyDefaults= FALSE;
}

if ($ApplyDefaults) {
	$DateArray['Month']= '';
	$DateArray['Day']= '';
	$DateArray['Hour']= '';
	$DateArray['Minute']= '';
}

$_SESSION[$View->Model][$Submenu]['Month']= $DateArray['Month'];
$_SESSION[$View->Model][$Submenu]['Day']= $DateArray['Day'];
$_SESSION[$View->Model][$Submenu]['Hour']= $DateArray['Hour'];
$_SESSION[$View->Model][$Submenu]['Minute']= $DateArray['Minute'];

$LogSize= 0;
if ($LogFile !== FALSE && $View->Controller($Output, 'GetFileLineCount', $LogFile, $SearchRegExp, $SearchNeedle, $DateArray['Month'], $DateArray['Day'], $DateArray['Hour'], $DateArray['Minute'])) {
	$LogSize= $Output[0];
}

ProcessNavigationButtons($LinesPerPage, $LogSize, $StartLine, $HeadStart);

require_once($VIEW_PATH.'/header.php');

PrintLogFileChooser($LogFile);

PrintLogHeaderForm($StartLine, $LogSize, $LinesPerPage, $SearchRegExp, $CustomHiddenInputs, $SearchNeedle, TRUE, $DateArray);
?>
<table id="logline">
	<?php
	PrintTableHeaders($View->Model);

	$Logs= array();
	if ($LogFile !== FALSE && $View->Controller($Output, 'GetLogs', $LogFile, $HeadStart, $LinesPerPage, $SearchRegExp, $SearchNeedle, $DateArray['Month'], $DateArray['Day'], $DateArray['Hour'], $DateArray['Minute'])) {
		$Logs= json_decode($Output[0], TRUE);
	}

	$LineCount= $StartLine + 1;
	$LastLineNum= $StartLine + min(array(count($Logs), $LinesPerPage));
	foreach ($Logs as $Logline) {
		$View->PrintLogLine($Logline, $LineCount++, $LastLineNum);
	}
	?>
</table>
<?php
PrintLogsHelp($View->LogsHelpMsg);
require_once($VIEW_PATH.'/footer.php');
?>
