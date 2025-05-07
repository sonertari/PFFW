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
 * Required includes and vars.
 * 
 * @attention TAB size throughout this source code is 4 spaces.
 * @bug	There is partial PHP support in Doxygen, thus there are many issues.
 */

$ROOT= dirname(dirname(dirname(dirname(__FILE__))));
$SRC_ROOT= dirname(dirname(dirname(__FILE__)));

require_once($SRC_ROOT . '/lib/defs.php');

$Menu= array();
$LogConf= array();

require_once($VIEW_PATH . '/lib/view.php');
/// @todo Remove this line, but needed for rule editor for now
require_once($VIEW_PATH . '/pf/include.php');

/// Left menu items with captions and user permissions.
$PFFW_MODULES = array(
	'system' => array(
		'Name' => _MENU('SYSTEM'),
		'Perms' => $ALL_USERS,
		),
	'pf' => array(
		'Name' => _MENU('PACKET FILTER'),
		'Perms' => $ALL_USERS,
		),
	'dhcpd' => array(
		'Name' => _MENU('DHCP'),
		'Perms' => $ALL_USERS,
		),
	'dnsmasq' => array(
		'Name' => _MENU('DNS'),
		'Perms' => $ALL_USERS,
		),
	'openssh' => array(
		'Name' => _MENU('OPENSSH'),
		'Perms' => $ALL_USERS,
		),
	'ftp-proxy' => array(
		'Name' => _MENU('FTP PROXY'),
		'Perms' => $ALL_USERS,
		),
	'httpd' => array(
		'Name' => _MENU('WEB SERVER'),
		'Perms' => $ALL_USERS,
		),
	'monitoring' => array(
		'Name' => _MENU('MONITORING'),
		'Perms' => $ALL_USERS,
		),
	);

require_once($VIEW_PATH . '/lib/libauth.php');

if (isset($_SESSION['Timeout'])) {
	if ($_SESSION['Timeout'] <= time()) {
		LogUserOut('Session expired');
	}
} elseif (isset($_SESSION['USER']) && in_array($_SESSION['USER'], $ALL_USERS)) {
	$_SESSION['Timeout']= time() + $SessionTimeout;
}

if (!isset($_SESSION['USER']) || $_SESSION['USER'] == 'loggedout') {
	header('Location: /index.php');
	exit;
}

/// Path to image files used in help boxes and links.
$IMG_PATH= '/images/';

// Also represents categories, so used for bundling on the Dashboard too
$Status2Images= array(
	'C' => 'critical.png',
	'E' => 'error2.png',
	'W' => 'warning2.png',
	'R' => 'running.png',
	'S' => 'stop.png',
	'N' => 'noerror.png',
	'U' => 'warning2.png',
	);

$StatusTitles= array(
	'C' => _TITLE('Critical Error'),
	'E' => _TITLE('Error'),
	'W' => _TITLE('Warning'),
	'R' => _TITLE('Running'),
	'S' => _TITLE('Stopped'),
	'N' => _TITLE('No Errors'),
	'U' => _TITLE('Unknown'),
	);

require_once($VIEW_PATH . '/lib/libwui.php');

$TopMenu= str_replace('.php', '', basename(filter_input(INPUT_SERVER, 'PHP_SELF')));
$Submenu= '';

/// Used as arg to PrintProcessTable() to print the number of processes at the top.
define('PRINT_COUNT', TRUE);

$Reload= FALSE;
$CustomHiddenInputs= '';
?>
