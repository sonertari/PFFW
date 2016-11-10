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

require_once ('pf.php');

function parse($lines)
{
	$queues= array();
	$q= array();

	//queue std on em1 bandwidth 100M qlimit 50
	//  [ pkts:          0  bytes:          0  dropped pkts:      0 bytes:      0 ]
	//  [ qlength:   0/ 50 ]
	foreach ($lines as $line) {
		if (preg_match('/^queue\s+(\S+)/', $line, $match)) {
			if (!isset($q['name'])) {
				$q['name']= '';
			}
			$queues[]= $q;

			$q= array('name' => $match[1]);
		} elseif (preg_match('/^\s*\[\s*pkts:\s*(\d+)\s*bytes:\s*(\d+)\s*dropped pkts:\s*(\d+)\s*bytes:\s*(\d+)\s*\]/', $line, $match)) {
			$q['pkts']= convertDecimal($match[1]);
			$q['bytes']= convertBinary($match[2]);
			$q['droppedPkts']= convertDecimal($match[3]);
			$q['droppedBytes']= convertBinary($match[4]);
		} elseif (preg_match('/^\s*\[\s*qlength:\s*(\d+)\s*\/\s*(\d+)\s*/', $line, $match)) {
			$q['length']= $match[1] . '/' . $match[2];
		} else {
			pffwwui_syslog(LOG_WARNING, __FILE__, __FUNCTION__, __LINE__, "Failed parsing queue line: $line");
		}
	}
	
	if (count($q) > 0) {
		$queues[]= $q;
	}
	return $queues;
}

$View->Controller($Output, 'GetPfQueueInfo');
$queues= parse($Output);

$Reload= TRUE;
require_once($VIEW_PATH . '/header.php');
?>
<div id="main">
	<table id="logline">
		<tr>
			<th><?php echo _('Name') ?></th>
			<th><?php echo _('Packets') ?></th>
			<th><?php echo _('Bytes') ?></th>
			<th><?php echo _('Dropped Packets') ?></th>
			<th><?php echo _('Dropped Bytes') ?></th>
			<th><?php echo _('Queue Length') ?></th>
		</tr>
		<?php
		$linenum= 0;
		foreach ($queues as $q) {
			$class= ($linenum++ % 2 == 0) ? 'evenline' : 'oddline';
			?>
			<tr class="<?php echo $class ?>">
				<td class="center" ><?php echo $q['name'] ?></td>
				<td class="right" ><?php echo $q['pkts'] ?></td>
				<td class="right" ><?php echo $q['bytes'] ?></td>
				<td class="right" ><?php echo $q['droppedPkts'] ?></td>
				<td class="right" ><?php echo $q['droppedBytes'] ?></td>
				<td class="center" ><?php echo $q['length'] ?></td>
			</tr>
			<?php
		}
		?>
	</table>
</div>
<?php
PrintHelpWindow(_HELPWINDOW('These are queue statistics reported by pf.'));
require_once($VIEW_PATH . '/footer.php');
?>
