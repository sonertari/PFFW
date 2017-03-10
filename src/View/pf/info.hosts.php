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
 * Info, arp output, and list of leases.
 */

require_once('pf.php');

SwitchView('dhcpd', 'DHCP Server');
$View->ProcessRestartStopRequests();

SwitchView('named', 'DNS Server');
$View->ProcessRestartStopRequests();

$Reload= TRUE;

SwitchView('pf', 'Packet Filter');
require_once($VIEW_PATH.'/header.php');

SwitchView('dhcpd', 'DHCP Server');
$View->PrintStatusForm();

SwitchView('named', 'DNS Server');
$View->PrintStatusForm();

SwitchView('dhcpd', 'DHCP Server');
?>
<br />
<strong><?php echo _TITLE('Active IPs (arp table)').':' ?></strong>
<table id="logline" class="centered">
	<?php
	PrintTableHeaders('arp');

	if ($View->Controller($Output, 'GetArpTable')) {
		$ArpTable= json_decode($Output[0], TRUE);
		$LineCount= 1;
		foreach ($ArpTable as $Cols) {
			?>
			<tr>
			<?php
			PrintLogCols($LineCount++, $Cols, 'arp');
			?>
			</tr>
			<?php
		}
	}
	?>
</table>
<br />
<strong><?php echo _TITLE('Leases').':' ?></strong>
<table id="logline" class="centered">
	<?php
	PrintTableHeaders('lease');

	if ($View->Controller($Output, 'GetLeases')) {
		$Leases= json_decode($Output[0], TRUE);
		$LineCount= 1;
		foreach ($Leases as $Cols) {
			?>
			<tr>
			<?php
			PrintLogCols($LineCount++, $Cols, 'lease');
			?>
			</tr>
			<?php
		}
	}
	?>
</table>
<?php
PrintHelpWindow(_HELPWINDOW('DHCP server supports both dynamic dhcp and bootp protocols. Dynamic leases can be monitored on this page.'));
require_once($VIEW_PATH.'/footer.php');
?>
