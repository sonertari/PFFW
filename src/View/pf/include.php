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
 * Required includes.
 */

$ROOT= dirname(dirname(dirname(dirname(__FILE__))));
$SRC_ROOT= dirname(dirname(dirname(__FILE__)));

require_once($SRC_ROOT.'/lib/defs.php');
require_once($SRC_ROOT.'/lib/setup.php');
require_once($SRC_ROOT.'/lib/lib.php');

require_once($VIEW_PATH.'/lib/setup.php');

/// PF module absolute path.
$PF_PATH= $VIEW_PATH.'/pf';

/// @attention Include these rule classes before session start in /lib/libauth.php
/// because we save instances of these in the session
require_once($PF_PATH.'/lib/RuleSet.php');
require_once($PF_PATH.'/lib/Rule.php');
require_once($PF_PATH.'/lib/Timeout.php');
require_once($PF_PATH.'/lib/State.php');
require_once($PF_PATH.'/lib/FilterBase.php');
require_once($PF_PATH.'/lib/Filter.php');
require_once($PF_PATH.'/lib/Antispoof.php');
require_once($PF_PATH.'/lib/Anchor.php');
require_once($PF_PATH.'/lib/NatBase.php');
require_once($PF_PATH.'/lib/NatTo.php');
require_once($PF_PATH.'/lib/BinatTo.php');
require_once($PF_PATH.'/lib/RdrTo.php');
require_once($PF_PATH.'/lib/AfTo.php');
require_once($PF_PATH.'/lib/DivertTo.php');
require_once($PF_PATH.'/lib/DivertPacket.php');
require_once($PF_PATH.'/lib/Route.php');
require_once($PF_PATH.'/lib/Macro.php');
require_once($PF_PATH.'/lib/Table.php');
require_once($PF_PATH.'/lib/Queue.php');
require_once($PF_PATH.'/lib/Scrub.php');
require_once($PF_PATH.'/lib/Option.php');
require_once($PF_PATH.'/lib/Limit.php');
require_once($PF_PATH.'/lib/LoadAnchor.php');
require_once($PF_PATH.'/lib/Include.php');
require_once($PF_PATH.'/lib/Comment.php');
require_once($PF_PATH.'/lib/Blank.php');

$pfMenu = array(
    'info' => array(
        'Name' => _MENU('Info'),
        'Perms' => $ALL_USERS,
        'SubMenu' => array(
			'pf' => _MENU('Pf'),
			'system' => _MENU('System'),
			'hosts' => _MENU('Hosts'),
			'ifs' => _MENU('Interfaces'),
			'states' => _MENU('States'),
			'queues' => _MENU('Queues'),
			),
		),
    'stats' => array(
        'Name' => _MENU('Statistics'),
        'Perms' => $ALL_USERS,
        'SubMenu' => array(
			'general' => _MENU('General'),
			'daily' => _MENU('Daily'),
			'hourly' => _MENU('Hourly'),
			'live' => _MENU('Live'),
			),
		),
    'graphs' => array(
        'Name' => _MENU('Graphs'),
        'Perms' => $ALL_USERS,
        'SubMenu' => array(
			'ifs' => _MENU('Interfaces'),
			'transfer' => _MENU('Transfer'),
			'states' => _MENU('States'),
			'mbufs' => _MENU('Mbufs'),
			),
		),
    'logs' => array(
        'Name' => _MENU('Logs'),
        'Perms' => $ALL_USERS,
        'SubMenu' => array(
			'archives' => _MENU('Archives'),
			'live' => _MENU('Live'),
			),
		),
    'rules' => array(
        'Name' => _MENU('Rules'),
        'Perms' => $ADMIN,
        'SubMenu' => array(
			'info' => _MENU('Info'),
			'editor' => _MENU('Editor'),
			'write' => _MENU('Display & Install'),
			'files' => _MENU('Load & Save'),
			),
		),
    'conf' => array(
        'Name' => _MENU('Config'),
        'Perms' => $ADMIN,
        'SubMenu' => array(
			'system' => _MENU('System'),
			'network' => _MENU('Network'),
			'dhcp' => _MENU('DHCP'),
			'dns' => _MENU('DNS'),
			'init' => _MENU('Init'),
			'wui' => _MENU('WUI'),
			),
		),
);

$Menu = array_merge(
	$Menu,
	array(
		'pf' => $pfMenu,
		)
);

$pfLogs = array(
	'Fields' => array(
		'Date',
		'Time',
		'Rule',
		'Act',
		'Dir',
		'If',
		'SrcIP',
		'SPort',
		'DstIP',
		'DPort',
		'Type',
		'Log',
		),
	'HighlightLogs' => array(
		'Col' => 'Act',
		'REs' => array(
			'red' => array('\bblock\b'),
			'yellow' => array('\bmatch\b'),
			'green' => array('\bpass\b'),
			),
		),
	);

$LogConf = array_merge(
	$LogConf,
	array(
		'pf' => $pfLogs,
		'arp' => array(
			'Fields' => array(
				'IP',
				'MAC',
				'Interface',
				'Expire',
				),
			),
		'lease' => array(
			'Fields' => array(
				'IP',
				'Start',
				'End',
				'MAC',
				'Host',
				'Status',
				),
			),
		)
);

/**
 * Modifies Model and Caption vars of the View.
 * 
 * This is just a hack to run the functions provided by Models other than pf.
 */
function SwitchView($model, $caption)
{
	global $View;

	$View->Model= $model;
	$View->Caption= $caption;
}

function convertBinary($value)
{
	$g= round($value / 1073741824);
	if ($g) {
		return $g . 'G';
	}

	$m= round($value / 1048576);
	if ($m) {
		return $m . 'M';
	}

	$k= round($value / 1024);
	if ($k) {
		return $k . 'K';
	}

	return $value;
}

function convertDecimal($value)
{
	$g= round($value / 1000000000);
	if ($g) {
		return $g . 'G';
	}

	$m= round($value / 1000000);
	if ($m) {
		return $m . 'M';
	}

	$k= round($value / 1000);
	if ($k) {
		return $k . 'K';
	}

	return $value;
}
?>
