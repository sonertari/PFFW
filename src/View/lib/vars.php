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
require_once($VIEW_PATH . '/pf/include.php');

require_once($VIEW_PATH . '/lib/libauth.php');

if ($_SESSION['Timeout']) {
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

require_once($VIEW_PATH . '/lib/libwui.php');

$TopMenu= str_replace('.php', '', basename(filter_input(INPUT_SERVER, 'PHP_SELF')));
?>
