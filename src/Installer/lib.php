<?php
/*
 * Copyright (C) 2004-2016 Soner Tari
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
 * Installer library.
 */

/**
 * To satisfy Controller().
 * 
 * This is due to common code used by all. Otherwise, the Controller cannot display help windows.
 */
function PrintHelpWindow($msg, $width= 'auto', $type= 'INFO')
{
	$msg= preg_replace('?<br\s*(|/)>?', ' ', $msg);
	echo "$type: $msg\n";
}

/**
 * Applies configuration.
 * 
 * @param bool $auto Whether to apply auto features too.
 * @return bool TRUE on success, FALSE on fail.
 */
function ApplyConfig($auto)
{
	global $Config, $Re_Ip, $View;

	try {
		$mygate= $Config['Mygate'];
		
		$lanif= $Config['IntIf'];
		$wanif= $Config['ExtIf'];

		$lanip= $Config['Ifs'][$lanif][1];
		$lanmask= $Config['Ifs'][$lanif][2];

		ComputeIfDefs($lanip, $lanmask, $lannet, $lanbc, $lancidr);

		$View->Model= 'pf';
		if (!$View->Controller($output, 'SetIfs', $lanif, $wanif)) {
			pffwwui_syslog(LOG_ERR, __FILE__, __FUNCTION__, __LINE__, "Failed setting pf interfaces: $lanif, $wanif");
		}

		if (!$View->Controller($output, 'SetIntnet', $lancidr)) {
			pffwwui_syslog(LOG_ERR, __FILE__, __FUNCTION__, __LINE__, "Failed setting pf internal net: $lancidr");
		}
		
		$View->Model= 'named';
		if (! $View->Controller($output, 'SetListenOn', $lanip)) {
			pffwwui_syslog(LOG_ERR, __FILE__, __FUNCTION__, __LINE__, "Failed setting listen-on: $lanip");
		}
		
		if (!$View->Controller($output, 'SetForwarders', $mygate)) {
			pffwwui_syslog(LOG_ERR, __FILE__, __FUNCTION__, __LINE__, "Failed setting forwarders: $mygate");
		}
		
		$View->Model= 'dhcpd';
		ComputeDhcpdIpRange($lanip, $lannet, $lanbc, $min, $max);
		if (!$View->Controller($output, 'SetDhcpdConf', $lanip, $lanmask, $lannet, $lanbc, $min, $max)) {
			pffwwui_syslog(LOG_ERR, __FILE__, __FUNCTION__, __LINE__, "Failed setting dhcpd configuration: $lanip, $lanmask, $lannet, $lanbc, $min, $max");
		}
		
		if (!$View->Controller($output, 'AddIf', $lanif)) {
			pffwwui_syslog(LOG_ERR, __FILE__, __FUNCTION__, __LINE__, "Failed setting dhcpd interface: $lanif");
		}
		
		$View->Model= 'symon';
		if (!$View->Controller($output, 'SetIfs', $lanif, $wanif)) {
			pffwwui_syslog(LOG_ERR, __FILE__, __FUNCTION__, __LINE__, "Failed setting symon ifs: $lanif, $wanif");
		}

		/// @attention There is an issue with sysctl on OpenBSD 5.9; it does not return on chrooted install environment
		// Hence, the following symon configuration which require the use of sysctl should be run during normal operation instead
		if ($auto) {
			if (!$View->Controller($output, 'SetConf', $lanif, $wanif)) {
				pffwwui_syslog(LOG_ERR, __FILE__, __FUNCTION__, __LINE__, 'Failed setting symon conf');
			}
		}

		return TRUE;
	}
	catch (Exception $e) {
		echo 'Caught exception: ', $e->getMessage(), "\n";
		return FALSE;
	}
}

/**
 * Applies configuration which cannot be completed during installation.
 */
function FirstBootTasks()
{
	// Run symon script to create rrd files again for cpu and sensor probes
	exec('/bin/sh /usr/local/share/examples/symon/c_smrrds.sh all');

	// Disable rc.local line which leads to this function call
	$file= '/etc/rc.local';
	if (copy($file, $file.'.bak')) {
		$re= '|^(\h*/var/www/htdocs/pffw/Installer/install\.php\h+-f\h*)|ms';
		$contents= preg_replace($re, '#${1}', file_get_contents($file), 1, $count);
		if ($contents !== NULL && $count === 1) {
			file_put_contents($file, $contents);
		}
	}
}

/** Computes network, broadcast, and CIDR net addresses, given ip and netmask.
 *
 * Quoting from nice explanations here:
 * http://downloads.openwrt.org/people/mbm/network
 *
 * if we take the ip and netmask and do an AND: (hint, AND the columns)
 *
 *       11000000 10101000 00000001 00001011 = 192.168.1.11 (some ip address)
 *       11111111 11111111 11111111 11110000 = 255.255.255.240 (netmask)
 *  AND: 11000000 10101000 00000001 00000000 = 192.168.1.0 (network address)
 *
 *  This gives our network address, the lowest address in the subnet
 *  Now, flip the netmask: (hint, NOT the columns)
 *
 *       11111111 11111111 11111111 11110000 = 255.255.255.240 (netmask)
 *  NOT: 00000000 00000000 00000000 00001111 = 0.0.0.15 (NOT 255.255.255.240)
 *
 *  then OR this with the network address: (hint, OR the columns)
 *
 *       11000000 10101000 00000001 00000000 = 192.168.1.0 (network address)
 *       00000000 00000000 00000000 00001111 = 0.0.0.15 (NOT 255.255.255.240)
 *  OR:  11000000 10101000 00000001 00001111 = 192.168.1.15 (broadcast address)
 *
 * @param string $ip IPv4 address.
 * @param string $mask Netmask.
 * @param string $net Network address.
 * @param string $bc Broadcast address.
 * @param string $cidr CIDR.
 */
function ComputeIfDefs($ip, $mask, &$net, &$bc, &$cidr)
{
	global $Re_Ip;
	
	if (preg_match("/^$Re_Ip$/", $ip) && preg_match("/^$Re_Ip$/", $mask)) {
		$net= long2ip(ip2long($ip) & ip2long($mask));
		$bc= long2ip(ip2long($net) | ~ip2long($mask));
		$cidr= $net.'/'.(32 - round(log(sprintf("%u", ip2long('255.255.255.255')) - sprintf("%u", ip2long($mask)), 2)));
	}
}

/** Computes a DHCP IP range.
 *
 * This function provides a guess only.
 *
 * @param string $ip System internal ip.
 * @param string $net System local network.
 * @param string $bc System broadcast address to guess max range.
 * @param string $min DHCP IP range min.
 * @param string $max DHCP IP range max.
 * @return bool TRUE on success, FALSE on fail.
 */
function ComputeDhcpdIpRange($ip, $net, $bc, &$min, &$max)
{
	if (preg_match('/(\d{1,3}\.\d{1,3}\.\d{1,3})\.(\d{1,3})/', $net, $match)) {
		$minnet= $match[1];
		$minoct= $match[2];
		$min= $minnet.'.'.($minoct + 1);

		// Avoid clash with system internal IP
		if ($ip === $min) {
			$min= $minnet.'.'.($minoct + 2);
		}
	
		if (preg_match('/(\d{1,3}\.\d{1,3}\.\d{1,3})\.(\d{1,3})/', $bc, $match)) {
			$maxnet= $match[1];
			$maxoct= $match[2];
			$max= $maxnet.'.'.($maxoct - 1);

			// Avoid clash with system internal IP
			if ($ip === $max) {
				$max= $maxnet.'.'.($maxoct - 2);
			}
			return TRUE;
		}
	}
	return FALSE;
}

/**
 * Initializes interfaces.
 * 
 * @return bool TRUE on success, FALSE on fail.
 */
function InitIfs()
{
	global $Config;

	if (!isset($Config['Ifs'])) {
		$Config['Ifs']= array();
	}
	$Ifs= array_keys($Config['Ifs']);
	
	if (count($Ifs) > 0) {
		// Necessary during first install with lan0/wan0 in pf.conf
		if (!in_array($Config['IntIf'], $Ifs)) {
			$Config['IntIf']= $Ifs[0];
		}
		
		if (count($Ifs) > 1) {
			if (!in_array($Config['ExtIf'], $Ifs)) {
				$Config['ExtIf']= $Ifs[1];
			}
		}
		else {
			$Config['ExtIf']= $Config['IntIf'];
			pffwwui_syslog(LOG_WARNING, __FILE__, __FUNCTION__, __LINE__, 'WARNING: Found only one interface, assigned internal to external if');
		}
		return TRUE;
	}
	pffwwui_syslog(LOG_ERR, __FILE__, __FUNCTION__, __LINE__, 'ERROR: Expected at least one interface, found: '.count($Ifs));
	return FALSE;
}

/**
 * Asks user for internal and external interface selection.
 */
function GetIfSelection()
{
	global $Config;

	$ifs= array_keys($Config['Ifs']);
	$iflist= implode(', ', $ifs);
	$ifcount= count($ifs);
	
	while (TRUE) {
		// Reset to system values
		$lanif= $Config['IntIf'];
		$wanif= $Config['ExtIf'];
		
		if (!isset($lanif) || (($ifcount > 1) && ($lanif === $wanif))) {
			$lanif= $ifs[0] === $wanif ? $ifs[1] : $ifs[0];
		}
		PrintIfConfig($lanif, $wanif);
		
		$selection= ReadIfSelection("Internal interface ($iflist or enter) [$lanif] ", $ifs);
		if ($selection !== '') {
			$lanif= $selection;
		}

		// Fix wan if necessary
		if (!isset($wanif) || (($ifcount > 1) && ($lanif === $wanif))) {
			$wanif= $ifs[0] === $lanif ? $ifs[1] : $ifs[0];
		}
		PrintIfConfig($lanif, $wanif);
		
		$selection= ReadIfSelection("External interface ($iflist or enter) [$wanif] ", $ifs);
		if ($selection !== '') {
			$wanif= $selection;
		}

		$warn= PrintIfConfig($lanif, $wanif);
		
		$selection= readline2('Type done to accept or press enter to try again: ');
		if ($selection === 'done') {
			break;
		}
		echo "\n";
	}
	
	if ($warn) {
		echo "\nProceeding with warnings...\n";
	}
	
	$Config['IntIf']= $lanif;
	$Config['ExtIf']= $wanif;
}

/**
 * Prints current internal/external interface selections.
 *
 * @param string $lanif Internal if.
 * @param string $wanif External if.
 * @return bool Whether the user should be warned or not.
 */
function PrintIfConfig($lanif, $wanif)
{
	global $Config;

	$warn= FALSE;
	
	echo "\nInterface assignment:\n";
	$lanconfig= trim(implode(' ', $Config['Ifs'][$lanif]));
	$lanconfig= $lanconfig === '' ? 'not configured':$lanconfig;
	echo "  internal= $lanif ($lanconfig)\n";
	$wanconfig= trim(implode(' ', $Config['Ifs'][$wanif]));
	$wanconfig= $wanconfig === '' ? 'not configured':$wanconfig;
	echo "  external= $wanif ($wanconfig)\n\n";
	
	if (($lanconfig == 'not configured') || ($wanconfig == 'not configured')) {
		echo "WARNING: There are unconfigured interfaces\n";
		$warn= TRUE;
	}
	
	if ($lanif === $wanif) {
		echo "WARNING: Internal and external interfaces are the same\n";
		$warn= TRUE;
	}

	if (isset($Config['Ifs'][$lanif][0])) {
		if ($Config['Ifs'][$lanif][0] === 'dhcp') {
			echo "WARNING: Internal interface is configured as dhcp\n";
			$warn= TRUE;
		}
	}
	return $warn;
}

/**
 * Prompts for and reads internal/external interface selection.
 *
 * @param string $prompt Message to display.
 * @param array $ifs Interface names.
 * @return string User input.
 */
function ReadIfSelection($prompt, $ifs)
{
	while (TRUE) {
		$selection= readline2($prompt);
		if (($selection === '') || in_array($selection, $ifs)) {
			return $selection;
		}
		echo "\nInvalid choice\n";
	}
}

/**
 * Reads a line of input from stdin.
 *
 * @param string $prompt Message to display.
 * @return string User input, no newlines.
 */
function readline2($prompt= '')
{
    echo $prompt;
    return rtrim(fgets(STDIN), "\n");
}

/**
 * Sets admin and user passwords on the WUI.
 *
 * Password should have at least 8 alphanumeric chars.
 */
function SetWuiPasswd()
{
	global $View;
	
	// In case
	$View->Model= 'system';
	
	echo "\nPassword for web administration interface:\n";
	
	while (TRUE) {
		echo "Password? (will not echo) ";
		$passwd= AskPass();
		
		echo "\nPassword? (again) ";
		if ($passwd === AskPass()) {
			if (preg_match('/^\w{8,}$/', $passwd)) {
				echo "\n";
				// Update admin password
				if ($View->Controller($output, 'SetPassword', 'admin', sha1($passwd))) {
					pffwwui_syslog(LOG_NOTICE, __FILE__, __FUNCTION__, __LINE__, 'User password changed: admin');
					// Update user password
					if ($View->Controller($output, 'SetPassword', 'user', sha1($passwd))) {
						pffwwui_syslog(LOG_NOTICE, __FILE__, __FUNCTION__, __LINE__, 'User password changed: user');
					}
					else {
						pffwwui_syslog(LOG_ERR, __FILE__, __FUNCTION__, __LINE__, 'Password change failed: user');
					}
				}
				else {
					pffwwui_syslog(LOG_ERR, __FILE__, __FUNCTION__, __LINE__, 'Password change failed: admin');
				}
				echo "Successfully set admin and user passwords.\n\n";
				break;
			}
			else {
				echo "\nERROR: Choose a password with at least 8 alphanumeric characters.\n";
			}
		}
		else {
			echo "\nERROR: Passwords do not match.\n";
		}
	}
}

/**
 * Reads typed chars without echo.
 *
 * @return string exec() return value is the last line of shell cmd output, i.e. user input
 */
function AskPass()
{
	return exec('set -o noglob; stty -echo; read resp; stty echo; set +o noglob; echo $resp');
}
?>
