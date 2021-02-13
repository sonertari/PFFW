<?php
/*
 * Copyright (C) 2004-2021 Soner Tari
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
 * Common variables, arrays, and constants.
 */

/// Project version.
define('VERSION', '6.8.1');

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

$Re_MonthNames= implode('|', array_values($MonthNames));

$MonthNumbersNoLeadingZeros = array_map(function ($str) { return $str + 0; }, array_keys($MonthNames));
$Re_MonthNumbersNoLeadingZeros= implode('|', $MonthNumbersNoLeadingZeros);

$DaysNoLeadingZeros = array(
	'1', '2', '3', '4', '5', '6', '7', '8', '9', '10',
	'11', '12', '13', '14', '15', '16', '17', '18', '19', '20',
	'21', '22', '23', '24', '25', '26', '27', '28', '29', '30', '31'
	);
$Re_DaysNoLeadingZeros= implode('|', $DaysNoLeadingZeros);

$WeekDays= array('Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun');
$Re_WeekDays= implode('|', $WeekDays);

/// General tcpdump command used everywhere.
/// @todo All system binaries called should be defined like this.
/// @attention Redirect stderr to /dev/null to hush tcpdump warning: "tcpdump: WARNING: snaplen raised from 116 to 160".
/// Otherwise that warning goes in front of the data.
$TCPDUMP= 'exec 2>/dev/null; /usr/sbin/tcpdump -nettt -r';

/// Type definitions for config settings as PREs
/// @todo Fix leading 0's problem(s)
define('UINT_0_2', '[0-2]');
define('STR_on_off', 'on|off');
define('STR_On_Off', 'On|Off');
define('STR_SING_QUOTED', '\'[^\']*\'');
define('STR_yes_no', 'yes|no');
define('UINT', '[0-9]+');
define('INT_M1_0_UP', '-1|[0-9]+');
define('UINT_0_1', '0|1');
define('INT_M1_0_3', '-1|[0-3]');
define('UINT_0_3', '[0-3]');
define('UINT_1_4', '[0-4]');
define('IP', '\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}');
define('IPorNET', '(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})|(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\/\d{1,2})');
define('PORT', '[0-9]+');
define('FLOAT', '[0-9]+|([0-9]+\.[0-9]+)');
define('CHAR', '.');

/// Common regexps.
/// @todo Find a proper regexp for IPv4 addresses, this is too general.
$Re_Ip= '\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}';
$Re_Net= "$Re_Ip\/\d{1,2}";
$Re_IpPort= '\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}:\d{1,5}';
/// @todo $num and $range need full testing. Define $port.
$preIPOctet= '(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])';
$preIPRange= '(\d|[1-2]\d|3[0-2])';
$preIP= "$preIPOctet\\.$preIPOctet\\.$preIPOctet\\.$preIPOctet";
$preNet= "$preIP\/$preIPRange";

$preMacByte= '[\da-f]{2}';
$preMac= "$preMacByte\:$preMacByte\:$preMacByte\:$preMacByte\:$preMacByte\:$preMacByte";

$preIfName= '\w+\d+';

/// For classifying gettext strings into files.
function _STATS($str)
{
	return _($str);
}

/**
 * Master statistics configuration.
 *
 * This array provides stats configuration parameters needed for each module.
 * Detailed behaviour of each module is defined by the settings in this array.
 *
 * @param Stats				Parent field in configuration details used on
 *							statistics pages.
 * @param Stats>Total		Mandatory sub-field for each Stats field. Configures the
 *							the general settings for the basic stats for the module.
 * @param Stats>Total>Title	Title to display on top of the graph.
 * @param Stats>Total>Cmd	Command line to get log lines. Usually to get all lines.
 * @param Stats>Total>Needle To get only those lines that contain the Needle text among
 *							the lines obtained by Stats>Total>Cmd.
 * @param Stats>Total>Color	The color of the bars on the graph.
 * @param Stats>Total>NVPs	Name-Value-Pairs to print at the bottom of the graph.
 *							Usually top 5 of some of the more important stats.
 *							Displayed in 2 columns.
 * @param Stats>Total>BriefStats Statistics (parsed field names) to collect as
 *							BriefStats. Top 100 of collected data are
 *							shown on the left of General statistics page.
 * @param Stats>Total>Counters Statistics to collect and show as a graph over total
 *							data. The difference between these counters and Stats>\<StatName\>
 *							is that these are collected using the command line for
 *							the Total stats. So there is no separate Cmd field.
 *							Counters has one extra field, Divisor, which is used to
 *							divide the total count. Usually need to convert bytes to
 *							kilobytes.
 * @param Stats><StatName>	Custom statistics to be collected. The data for these
 *							graphs are collected using the Cmd and Needle fields.
 *							Stats>Total>Counters could have been merged with this one perhaps.
 *							The sub-fields for these custom stats is the same as the
 *							Total field described above.
 */
$StatsConf = array(
	'pf' => array(
		'Total' => array(
			'Title' => _STATS('All requests'),
			'Cmd' => $TCPDUMP.' <LF>',
			// @attention An empty needle is needed while collecting stats, see IncStats()
			'Needle' => '',
			'SearchRegexpPrefix' => '([[:blank:]\|.]+)',
			'Color' => '#01466b',
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
		),
    'named' => array(
		'Total' => array(
			'Title' => _STATS('All queries'),
			'Cmd' => '/bin/cat <LF>',
			'Needle' => '( query)',
			'SearchRegexpPrefix' => '([[:blank:]]+)',
			'BriefStats' => array(
				'Date' => _STATS('Requests by date'),
				'Domain' => _STATS('Domains'),
				'IP' => _STATS('IPs querying'),
				'Type' => _STATS('Query types'),
				'Reason' => _STATS('Failure reason'),
				),
			'Counters' => array(),
			),
		'Queries' => array(
			'Title' => _STATS('All queries'),
			'Needle' => '( query: )',
			'Color' => '#01466b',
			'NVPs' => array(
				'Domain' => _STATS('Domains'),
				'IP' => _STATS('IPs querying'),
				'Type' => _STATS('Query types'),
				),
			),
		'Failures' => array(
			'Title' => _STATS('Failed queries'),
			'Needle' => '( query failed )',
			'Color' => 'Red',
			'NVPs' => array(
				'Domain' => _STATS('Domains'),
				'IP' => _STATS('IPs querying'),
				'Type' => _STATS('Query types'),
				'Reason' => _STATS('Failure reason'),
				),
			),
		),
    'openssh' => array(
		'Total' => array(
			'Title' => _STATS('All attempts'),
			'Cmd' => '/bin/cat <LF>',
			'Needle' => '(Accepted|Failed)',
			'Color' => '#01466b',
			'NVPs' => array(
				'IP' => _STATS('Client IPs'),
				'User' => _STATS('Users'),
				'Type' => _STATS('SSH version'),
				),
			'BriefStats' => array(
				'Date' => _STATS('Requests by date'),
				'Type' => _STATS('SSH version'),
				'Reason' => _STATS('Failure reason'),
				'IP' => _STATS('Client IPs'),
				'User' => _STATS('Users'),
				),
			'Counters' => array(),
			),
		'Failures' => array(
			'Title' => _STATS('Failed attempts'),
			'Needle' => '(Failed .* for )',
			'Color' => 'Red',
			'NVPs' => array(
				'IP' => _STATS('Client IPs'),
				'User' => _STATS('Failed users'),
				'Type' => _STATS('SSH version'),
				'Reason' => _STATS('Failure reason'),
				),
			),
		'Successes' => array(
			'Title' => _STATS('Successful logins'),
			'Needle' => '(Accepted .* for )',
			'Color' => 'Green',
			'NVPs' => array(
				'IP' => _STATS('Client IPs'),
				'User' => _STATS('Logged in user'),
				'Type' => _STATS('SSH version'),
				),
			),
		),
    'ftp-proxy' => array(
		'Total' => array(
			'Title' => _STATS('All sessions'),
			'Cmd' => '/bin/cat <LF>',
			'Needle' => '(FTP session )',
			'Color' => '#01466b',
			'NVPs' => array(
				'Client' => _STATS('Client'),
				'Server' => _STATS('Server'),
				),
			'BriefStats' => array(
				'Client' => _STATS('Client'),
				'Server' => _STATS('Server'),
				),
			),
		),
    'httpdlogs' => array(
		'Total' => array(
			'Title' => _STATS('All requests'),
			'Cmd' => '/bin/cat <LF>',
			'Needle' => '',
			'SearchRegexpPrefix' => '([^[:alnum:]]+)',
			'Color' => '#01466b',
			'NVPs' => array(
				'IP' => _STATS('Clients'),
				'Link' => _STATS('Links'),
				'Mtd' => _STATS('Methods'),
				'Code' => _STATS('HTTP Codes'),
				),
			'BriefStats' => array(
				'Date' => _STATS('Requests by date'),
				'IP' => _STATS('Clients'),
				'Mtd' => _STATS('Methods'),
				'Code' => _STATS('HTTP Codes'),
				'Link' => _STATS('Links'),
				),
			'Counters' => array(
				'Sizes' => array(
					'Field' => 'Size',
					'Title' => _STATS('Downloaded (KB)'),
					'Color' => '#FF8000',
					'Divisor' => 1000,
					'NVPs' => array(
						'Link' => _STATS('Size by Link (KB)'),
						'IP' => _STATS('Size by IP (KB)'),
						),
					),
				),
			),
		),
	);

/// For classifying gettext strings into files.
/// @attention Moved here to satisfy $ModelsToStat below
function _TITLE($str)
{
	return _($str);
}

/// Models to get statuses
/// @todo Code reuse issue: Titles are already available as class vars
$ModelsToStat= array(
	'system' => _TITLE('System'),
	'pf' => _TITLE('Packet Filter'),
	'dhcpd' => _TITLE('DHCP Server'),
	'named' => _TITLE('DNS Server'),
	'openssh' => _TITLE('OpenSSH'),
	'ftp-proxy' => _TITLE('FTP Proxy'),
	'httpd' => _TITLE('Web User Interface'),
	'symon' => _TITLE('Symon'),
	'symux' => _TITLE('Symux'),
	);

$PF_CONFIG_PATH= '/etc/pfre';
$TMP_PATH= '/tmp';

$TEST_DIR_PATH= '';
/// @attention Necessary to set to '/pffw' instead of '' to fix $ROOT . $TEST_DIR_SRC in model.php
$TEST_DIR_SRC= '/pffw';
$INSTALL_USER= 'root';
?>
