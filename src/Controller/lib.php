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
 * Defs and library functions for Controller.
 */

/// Redirect file for Controller().
$TmpFile= '/var/tmp/pffw/ctlr.out';

/// Matches model names to files. View provides the name only.
$ModelFiles= array(
	'system'			=> 'system.php',
	'pf'				=> 'pf.php',
	'dhcpd'				=> 'dhcpd.php',
	'named'				=> 'named.php',
	'httpd'				=> 'httpd.php',
	'httpdlogs'			=> 'httpdlogs.php',
	'wui_syslog'		=> 'wuilogs.php',
	'ctlr_syslog'		=> 'ctlrlogs.php',
	'openssh'			=> 'openssh.php',
	'ftp-proxy'			=> 'ftp-proxy.php',
	'symon'				=> 'symon.php',
	'symux'				=> 'symux.php',
	'monitoring'		=> 'monitoring.php',
	'docs'				=> 'docs.php',
	);

/// Matches model names to Classes. Used to create the object.
$Models= array(
	'system'			=> 'System',
	'pf'				=> 'Pf',
	'dhcpd'				=> 'Dhcpd',
	'named'				=> 'Named',
	'httpd'				=> 'Httpd',
	'httpdlogs'			=> 'Httpdlogs',
	'wui_syslog'		=> 'Wuilogs',
	'ctlr_syslog'		=> 'Ctlrlogs',
	'openssh'			=> 'Openssh',
	'ftp-proxy'			=> 'Ftpproxy',
	'symon'				=> 'Symon',
	'symux'				=> 'Symux',
	'monitoring'		=> 'Monitoring',
	'docs'				=> 'Docs',
	);

/**
 * Functions and info strings used in shell arg control.
 *
 * @param string func Function to check type
 * @param string desc Info string to use when check failed
 */
$ArgTypes= array(
	FILEPATH	=>	array(
		'func'	=> 'IsFilePath',
		'desc'	=> _('Filepath wrong'),
		),
	NAME		=>	array(
		'func'	=> 'IsName',
		'desc'	=> _('Name wrong'),
		),
	NUM			=>	array(
		'func'	=> 'IsNumber',
		'desc'	=> _('Number wrong'),
		),
	SHA1STR	=>	array(
		'func'	=> 'IsSha1Str',
		'desc'	=> _('Not sha1 encrypted string'),
		),
	BOOL	=>	array(
		'func'	=> 'IsBool',
		'desc'	=> _('Not boolean'),
		),
	SAVEFILEPATH	=>	array(
		'func'	=> 'IsFilePath',
		'desc'	=> _('Filepath wrong'),
		),
	JSON	=>	array(
		'func'	=> 'IsJson',
		'desc'	=> _('Not JSON encoded string'),
		),
	EMPTYSTR	=>	array(
		'func'	=> 'IsEmpty',
		'desc'	=> _('Not empty string'),
		),
	REGEXP	=>	array(
		'func'	=> 'IsStr',
		'desc'	=> _('Regular expression wrong'),
		),
	SERIALARRAY	=>	array(
		'func'	=> 'IsSerializedArray',
		'desc'	=> _('Not serialized array'),
		),
	IPADRLIST	=>	array(
		'func'	=> 'IsIPList',
		'desc'	=> _('IP or IP list wrong'),
		),
	STR			=>	array(
		'func'	=> 'IsStr',
		'desc'	=> _('String wrong'),
		),
	IPADR		=>	array(
		'func'	=> 'IsIPAddress',
		'desc'	=> _('IP address wrong'),
		),
	IPRANGE		=>	array(
		'func'	=> 'IsIPRange',
		'desc'	=> _('IP range wrong'),
		),
	HOST		=>	array(
		'func'	=> 'IsHost',
		'desc'	=> _('Host wrong'),
		),
	URL			=>	array(
		'func'	=> 'IsUrl',
		'desc'	=> _('Url wrong'),
		),
	EMAIL		=>	array(
		'func'	=> 'IsEmailAddress',
		'desc'	=> _('E-mail address wrong'),
		),
	DATETIME	=>	array(
		'func'	=> 'IsDateTime',
		'desc'	=> _('Datetime wrong'),
		),
	TAIL		=>	array(
		'func'	=> 'IsTailNumber',
		'desc'	=> _('Tail number wrong'),
		),
	ASTERISK	=>	array(
		'func'	=> 'IsAsterisk',
		'desc'	=> _('Not asterisk'),
		),
	CONFNAME	=>	array(
		'func'	=> 'IsStr',
		'desc'	=> _('Not config name'),
		),
);

$MonthDays= array(
	1	=> 31,
	2	=> 29, ///< Forget about leap year calculations for now.
	3	=> 31,
	4	=> 30,
	5	=> 31,
	6	=> 30,
	7	=> 31,
	8	=> 31,
	9	=> 30,
	10	=> 31,
	11	=> 30,
	12	=> 31,
	);

function IsName($str)
{
	return preg_match('/' . RE_NAME . '/', $str);
}

function IsNumber($str)
{
	return preg_match('/' . RE_NUM . '/', $str);
}

function IsSha1Str($str)
{
	return preg_match('/' . RE_SHA1 . '/', $str);
}

function IsBool($str)
{
	return preg_match('/' . RE_BOOL . '/', $str);
}

function IsJson($str)
{
	return json_decode($str) !== NULL;
}

function IsEmpty($str)
{
	return empty($str);
}

function IsStr($str)
{
	/// @todo This is still too general?
	return preg_match("/^[^\n]{0,500}$/", $str);
}

function IsSerializedArray($str)
{
	// Serialized arrays passed to the Model are small enough to warrant this json_decode() and array check
	// Otherwise, this is not true for the return values of the Model, especially logs and statistics
	// XXX: Was unserialize() before, redundant now.
	return is_array(json_decode($str, TRUE));
}

function IsIPList($iplist)
{
	$ips= explode(';', $iplist, 10);
	foreach ($ips as $ip) {
		if ($ip !== '' && !IsIPAddress(trim($ip))) {
			return FALSE;
		}
	}
	return TRUE;
}

function IsIPAddress($ip)
{
	global $preIP;

	return preg_match("/^$preIP$/", $ip);
}

function IsIPRange($iprange)
{
	global $preIP, $preIPRange;

	return preg_match("/^$preIP\/$preIPRange$/", $iprange);
}

function IsHost($host)
{
	global $preIP;

	return preg_match("/^($preIP|::1)\s[a-zA-Z][a-z0-9A-Z_.#\s]{0,100}$/", $host);
}

function IsUrl($name)
{
	return preg_match('|^[\w_.\-/?=]{1,100}$|', $name);
}

function IsEmailAddress($addr)
{
	return preg_match('/^root(@localhost|)$/', $addr)
		|| preg_match('/^[a-z]+[a-z0-9]*(\.|\-|_)?[a-z0-9]+@([a-z]+[a-z0-9]*(\.|\-)?[a-z]+[a-z0-9]*[a-z0-9]+){1,4}\.[a-z]{2,4}$/', $addr);
}

function IsAsterisk($str)
{
	return $str === '*';
}

/**
 * Checks the type of datetime string as supplied to date command.
 *
 * @param string $datetime Arg.
 * @return bool Type check result
 */
function IsDateTime($datetime)
{
	global $MonthDays;
	
	/// There should be 10 digits.
	if (preg_match('/^\d{10,}$/', $datetime)) {
		$datetime= str_split($datetime, 2);
		/// Year can be 00-99, so no need to check.
		//$Year= $datetime[0] + 0;
		$month= $datetime[1] + 0;
		$day= $datetime[2] + 0;
		$hour= $datetime[3] + 0;
		$min= $datetime[4] + 0;
		if (($month <= 12)
			&& ($day <= $MonthDays[$month])
			&& ($hour <= 23)
			&& ($min <= 59)) {
			return TRUE;
		}
	}
	return FALSE;
}

function IsTailNumber($str)
{
	if (IsNumber($str)) {
		/// @bug tail(1) on OpenBSD 5.9 amd64 gets stuck with: echo soner | /usr/bin/tail -99999999
		/// @link https://marc.info/?l=openbsd-bugs&m=148586652218524&w=2
		/// @attention Never allow large numbers here, otherwise the system becomes unusuable.
		return $str < 1000;
	}
}

/**
 * Computes and fills arg count variables.
 *
 * @param array $commands Available commands for the current model
 * @param array $argv Argument vector
 * @param string $cmd Method name, key to $commands
 * @param int $actual Given arg count
 * @param int $expected Expected arg count
 * @param int $acceptable Acceptable arg count
 * @param int $check Arg count used while validating
 */
function ComputeArgCounts($commands, $argv, $cmd, &$actual, &$expected, &$acceptable, &$check)
{
	$actual= count($argv);
	$expected= count($commands[$cmd]['argv']);

	$acceptable= $expected;
	for ($argpos= 0; $argpos < $expected; $argpos++) {
		$argtype= $commands[$cmd]['argv'][$argpos];
		if ($argtype & NONE) {
			$acceptable--;
		}
	}
	
	/// @attention There may be extra or missing args, hence min() here
	$check= min($actual, $expected);
}

/**
 * Checks types of the arguments passed.
 *
 * The arguments are checked against the types listed in $commands.
 *
 * @param array $commands Available commands for the current model
 * @param string $command Method name, key to $commands
 * @param array $argv Argument vector
 * @param int $check Arg count used while validating
 * @return bool Validation result
 *
 * @todo There are 2 types of argument checks in this project, which one to choose?
 */
function ValidateArgs($commands, $command, $argv, $check)
{
	global $ArgTypes;

	$helpmsg= $commands[$command]['desc'];
	$logmsg= $commands[$command]['desc'];
	
	$valid= FALSE;
	// Check each argument in order
	for ($argpos= 0; $argpos < $check; $argpos++) {
		$arg= $argv[$argpos];
		$argtype= $commands[$command]['argv'][$argpos];

		// Multiple types may match for an arg, hence the foreach loop
		foreach ($ArgTypes as $type => $conf) {
			// Acceptable types are bitwise ORed, hence the AND here
			if ($argtype & $type) {
				$validatefunc= $conf['func'];
				if ($validatefunc($arg)) {
					$valid= TRUE;

					if ($type & FILEPATH) {
						// Further check if file really exists
						exec("[ -e $arg ]", $output, $retval);
						if ($retval !== 0) {
							$valid= FALSE;

							$errormsg= "$command: $arg";
							Error(_('No such file').": $errormsg");
							ctlr_syslog(LOG_NOTICE, __FILE__, __FUNCTION__, __LINE__, "No such file: $errormsg");
						}
					}

					if ($valid) {
						// One type succeded, hence do not check for other possible types for this arg
						break;
					}
				}
				else {
					$valid= FALSE;
					
					$helpmsg.= "\n"._($conf['desc']).': '.$arg;
					$logmsg.= "\n".$conf['desc'].': '.$arg;
					// Will keep checking if further types are possible for this arg
				}
			}
		}

		if (!$valid) {
			// One arg failed to check, do not run the func
			break;
		}
	}
	
	if (!$valid) {
		Error(_('Arg type check failed').": $helpmsg");
		ctlr_syslog(LOG_NOTICE, __FILE__, __FUNCTION__, __LINE__, "Arg type check failed: $logmsg");
	}
	return $valid;
}
?>
