<?php
/*
 * Copyright (C) 2004-2017 Soner Tari
 *
 * This file is part of PFFW.
 *
 * PFFW is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * PFFW is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with PFFW.  If not, see <http://www.gnu.org/licenses/>.
 */

/** @file
 * Common variables, arrays, and constants.
 */

/// Project version.
define('VERSION', '5.9');

$ROOT= dirname(dirname(dirname(__FILE__)));
$SRC_ROOT= dirname(dirname(__FILE__));

$VIEW_PATH= $SRC_ROOT . '/View';
$MODEL_PATH= $SRC_ROOT . '/Model';

/// Syslog priority strings.
$LOG_PRIOS= array(
	'LOG_EMERG',	// system is unusable
	'LOG_ALERT',	// action must be taken immediately
	'LOG_CRIT',		// critical conditions
	'LOG_ERR',		// error conditions
	'LOG_WARNING',	// warning conditions
	'LOG_NOTICE',	// normal, but significant, condition
	'LOG_INFO',		// informational message
	'LOG_DEBUG',	// debug-level message
	);

/// Superuser
$ADMIN= array('admin');
/// Unprivileged user who can modify any configuration
$USER= array('user');
/// All valid users
$ALL_USERS= array_merge($ADMIN, $USER);

/**
 * Locale definitions used by both View and Controller.
 *
 * It is recommended that all translations use UTF-8 codeset.
 *
 * @param string Name Title string
 * @param string Codeset Locale codeset
 */
$LOCALES = array(
    'en_EN' => array(
        'Name' => _('English'),
        'Codeset' => 'UTF-8'
		),
    'tr_TR' => array(
        'Name' => _('Turkish'),
        'Codeset' => 'UTF-8'
		),
	);

/// Used in translating months from number to string.
$MonthNames= array(
	'01' => 'Jan',
	'02' => 'Feb',
	'03' => 'Mar',
	'04' => 'Apr',
	'05' => 'May',
	'06' => 'Jun',
	'07' => 'Jul',
	'08' => 'Aug',
	'09' => 'Sep',
	'10' => 'Oct',
	'11' => 'Nov',
	'12' => 'Dec',
	);

/// Used in translating months from string to number.
$MonthNumbers= array(
	'Jan' => '01',
	'Feb' => '02',
	'Mar' => '03',
	'Apr' => '04',
	'May' => '05',
	'Jun' => '06',
	'Jul' => '07',
	'Aug' => '08',
	'Sep' => '09',
	'Oct' => '10',
	'Nov' => '11',
	'Dec' => '12',
	);

/// General tcpdump command used everywhere.
/// @todo All system binaries called should be defined like this.
/// @attention Redirect stderr to /dev/null to hush tcpdump warning: "tcpdump: WARNING: snaplen raised from 116 to 160".
/// Otherwise that warning goes in front of the data.
$TCPDUMP= 'exec 2>/dev/null; /usr/sbin/tcpdump -nettt -r';

$Re_Ip= '\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}';

/// @todo $num and $range need full testing. Define $port.
$preIPOctet= '(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])';
$preIP= "$preIPOctet\\.$preIPOctet\\.$preIPOctet\\.$preIPOctet";

$preIPRange= '(\d|[1-2]\d|3[0-2])';

$preMacByte= '[\da-f]{2}';
$preMac= "$preMacByte\:$preMacByte\:$preMacByte\:$preMacByte\:$preMacByte\:$preMacByte";

$preIfName= '\w+\d+';

/// For classifying gettext strings into files.
function _STATS($str)
{
	return _($str);
}

$StatsConf= array();

$pfStats = array(
	'Total' => array(
		'Title' => _STATS('All requests'),
		'Cmd' => $TCPDUMP.' <LF>',
		'Needle' => '',
		'Color' => '#004a4a',
		'NVPs' => array(
			'SrcIP' => _STATS('Source addresses'),
			'DstIP' => _STATS('Destination addresses'),
			'DPort' => _STATS('Destination ports'),
			'Type' => _STATS('Packet types'),
			),
		'BriefStats' => array(
			'Date' => _STATS('Requests by date'),
			'SrcIP' => _STATS('Source addresses'),
			'DstIP' => _STATS('Destination addresses'),
			'DPort' => _STATS('Destination ports'),
			'Type' => _STATS('Packet types'),
			),
		'Counters' => array(),
		),
	'Pass' => array(
		'Title' => _STATS('Allowed requests'),
		'Cmd' => $TCPDUMP.' <LF>',
		'Needle' => ' pass ',
		'Color' => 'green',
		'NVPs' => array(
			'SrcIP' => _STATS('Source addresses'),
			'DstIP' => _STATS('Destination addresses'),
			'DPort' => _STATS('Destination ports'),
			'Type' => _STATS('Packet types'),
			),
		),
	'Block' => array(
		'Title' => _STATS('Blocked requests'),
		'Cmd' => $TCPDUMP.' <LF>',
		'Needle' => ' block ',
		'Color' => 'red',
		'NVPs' => array(
			'SrcIP' => _STATS('Source addresses'),
			'DstIP' => _STATS('Destination addresses'),
			'DPort' => _STATS('Destination ports'),
			'Type' => _STATS('Packet types'),
			),
		),
	'Match' => array(
		'Title' => _STATS('Matched requests'),
		'Cmd' => $TCPDUMP.' <LF>',
		'Needle' => ' match ',
		'Color' => '#FF8000',
		'NVPs' => array(
			'SrcIP' => _STATS('Source addresses'),
			'DstIP' => _STATS('Destination addresses'),
			'DPort' => _STATS('Destination ports'),
			'Type' => _STATS('Packet types'),
			),
		),
	);

$StatsConf = array_merge(
	$StatsConf,
	array(
		'pf' => $pfStats,
		)
);

$PF_CONFIG_PATH= '/etc/pfre';
$TMP_PATH= '/tmp';

$TEST_DIR_PATH= '';
/// @attention Necessary to set to '/pffw' instead of '' to fix $ROOT . $TEST_DIR_SRC in model.php
$TEST_DIR_SRC= '/pffw';
$INSTALL_USER= 'root';
?>
