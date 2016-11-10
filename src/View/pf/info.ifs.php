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

require_once('pf.php');

function parse($lines)
{
	$ifs= array();
	$i= array();

	//all
	//	Cleared:     Thu Jan  1 02:00:01 1970
	//	References:  [ States:  13                 Rules: 1                  ]
	//	In4/Pass:    [ Packets: 0                  Bytes: 0                  ]
	//	In4/Block:   [ Packets: 0                  Bytes: 0                  ]
	//	Out4/Pass:   [ Packets: 0                  Bytes: 0                  ]
	//	Out4/Block:  [ Packets: 0                  Bytes: 0                  ]
	//	In6/Pass:    [ Packets: 0                  Bytes: 0                  ]
	//	In6/Block:   [ Packets: 0                  Bytes: 0                  ]
	//	Out6/Pass:   [ Packets: 0                  Bytes: 0                  ]
	//	Out6/Block:  [ Packets: 0                  Bytes: 0                  ]
	foreach ($lines as $line) {
		if (preg_match('/^([\w\s\(\)]+)$/', $line, $match)) {
			if (count($i) > 0) {
				if (!isset($i['name'])) {
					$i['name']= '';
				}
				$ifs[]= $i;
			}

			$i= array('name' => $match[1]);
		} elseif (preg_match('/^\s*Cleared:\s*(.*)\s*$/', $line, $match)) {
			$i['cleared']= $match[1];
		} elseif (preg_match('/^\s*References:\s*\[\s*States:\s*(\d+)\s*Rules:\s*(\d+)\s*\]/', $line, $match)) {
			$i['states']= convertDecimal($match[1]);
			$i['rules']= convertDecimal($match[2]);
		} elseif (preg_match('/^\s*In4\/Pass:\s*\[\s*Packets:\s*(\d+)\s*Bytes:\s*(\d+)\s*\]/', $line, $match)) {
			$i['in4PassPackets']= convertDecimal($match[1]);
			$i['in4PassBytes']= convertBinary($match[2]);
		} elseif (preg_match('/^\s*In4\/Block:\s*\[\s*Packets:\s*(\d+)\s*Bytes:\s*(\d+)\s*\]/', $line, $match)) {
			$i['in4BlockPackets']= convertDecimal($match[1]);
			$i['in4BlockBytes']= convertBinary($match[2]);
		} elseif (preg_match('/^\s*Out4\/Pass:\s*\[\s*Packets:\s*(\d+)\s*Bytes:\s*(\d+)\s*\]/', $line, $match)) {
			$i['out4PassPackets']= convertDecimal($match[1]);
			$i['out4PassBytes']= convertBinary($match[2]);
		} elseif (preg_match('/^\s*Out4\/Block:\s*\[\s*Packets:\s*(\d+)\s*Bytes:\s*(\d+)\s*\]/', $line, $match)) {
			$i['out4BlockPackets']= convertDecimal($match[1]);
			$i['out4BlockBytes']= convertBinary($match[2]);
		} elseif (preg_match('/^\s*In6\/Pass:\s*\[\s*Packets:\s*(\d+)\s*Bytes:\s*(\d+)\s*\]/', $line, $match)) {
			$i['in6PassPackets']= convertDecimal($match[1]);
			$i['in6PassBytes']= convertBinary($match[2]);
		} elseif (preg_match('/^\s*In6\/Block:\s*\[\s*Packets:\s*(\d+)\s*Bytes:\s*(\d+)\s*\]/', $line, $match)) {
			$i['in6BlockPackets']= convertDecimal($match[1]);
			$i['in6BlockBytes']= convertBinary($match[2]);
		} elseif (preg_match('/^\s*Out6\/Pass:\s*\[\s*Packets:\s*(\d+)\s*Bytes:\s*(\d+)\s*\]/', $line, $match)) {
			$i['out6PassPackets']= convertDecimal($match[1]);
			$i['out6PassBytes']= convertBinary($match[2]);
		} elseif (preg_match('/^\s*Out6\/Block:\s*\[\s*Packets:\s*(\d+)\s*Bytes:\s*(\d+)\s*\]/', $line, $match)) {
			$i['out6BlockPackets']= convertDecimal($match[1]);
			$i['out6BlockBytes']= convertBinary($match[2]);
		} else {
			pffwwui_syslog(LOG_WARNING, __FILE__, __FUNCTION__, __LINE__, "Failed parsing interface line: $line");
		}
	}
	
	if (count($i) > 0) {
		$ifs[]= $i;
	}
	
	return $ifs;
}

$View->Controller($Output, 'GetPfIfsInfo');
$ifs= parse($Output);

$Reload= TRUE;
require_once($VIEW_PATH . '/header.php');
?>
<div id="main">
	<table id="logline">
		<tr>
			<th rowspan="2" ><?php echo _('Name') ?></th>
			<th rowspan="2" ><?php echo _('States') ?></th>
			<th rowspan="2" ><?php echo _('Rules') ?></th>
			<th colspan="2" ><?php echo _('In4 Pass') ?></th>
			<th colspan="2" ><?php echo _('In4 Block') ?></th>
			<th colspan="2" ><?php echo _('Out4 Pass') ?></th>
			<th colspan="2" ><?php echo _('Out4 Block') ?></th>
			<th colspan="2" ><?php echo _('In6 Pass') ?></th>
			<th colspan="2" ><?php echo _('In6 Block') ?></th>
			<th colspan="2" ><?php echo _('Out6 Pass') ?></th>
			<th colspan="2" ><?php echo _('Out6 Block') ?></th>
			<th rowspan="2" ><?php echo _('Cleared') ?></th>
		</tr>
		<tr>
			<th><?php echo _('Packets') ?></th>
			<th><?php echo _('Bytes') ?></th>
			<th><?php echo _('Packets') ?></th>
			<th><?php echo _('Bytes') ?></th>
			<th><?php echo _('Packets') ?></th>
			<th><?php echo _('Bytes') ?></th>
			<th><?php echo _('Packets') ?></th>
			<th><?php echo _('Bytes') ?></th>
			<th><?php echo _('Packets') ?></th>
			<th><?php echo _('Bytes') ?></th>
			<th><?php echo _('Packets') ?></th>
			<th><?php echo _('Bytes') ?></th>
			<th><?php echo _('Packets') ?></th>
			<th><?php echo _('Bytes') ?></th>
			<th><?php echo _('Packets') ?></th>
			<th><?php echo _('Bytes') ?></th>
		</tr>
		<?php
		$linenum= 0;
		foreach ($ifs as $i) {
			$class= ($linenum++ % 2 == 0) ? 'evenline' : 'oddline';
			?>
			<tr class="<?php echo $class ?>">
				<td class="center" ><?php echo $i['name'] ?></td>
				<td class="right" ><?php echo $i['states'] ?></td>
				<td class="right" ><?php echo $i['rules'] ?></td>
				<td class="right" ><?php echo $i['in4PassPackets'] ?></td>
				<td class="right" ><?php echo $i['in4PassBytes'] ?></td>
				<td class="right" ><?php echo $i['in4BlockPackets'] ?></td>
				<td class="right" ><?php echo $i['in4BlockBytes'] ?></td>
				<td class="right" ><?php echo $i['out4PassPackets'] ?></td>
				<td class="right" ><?php echo $i['out4PassBytes'] ?></td>
				<td class="right" ><?php echo $i['out4BlockPackets'] ?></td>
				<td class="right" ><?php echo $i['out4BlockBytes'] ?></td>
				<td class="right" ><?php echo $i['in6PassPackets'] ?></td>
				<td class="right" ><?php echo $i['in6PassBytes'] ?></td>
				<td class="right" ><?php echo $i['in6BlockPackets'] ?></td>
				<td class="right" ><?php echo $i['in6BlockBytes'] ?></td>
				<td class="right" ><?php echo $i['out6PassPackets'] ?></td>
				<td class="right" ><?php echo $i['out6PassBytes'] ?></td>
				<td class="right" ><?php echo $i['out6BlockPackets'] ?></td>
				<td class="right" ><?php echo $i['out6BlockBytes'] ?></td>
				<td><?php echo $i['cleared'] ?></td>
			</tr>
			<?php
		}
		?>
	</table>
</div>
<?php
PrintHelpWindow(_HELPWINDOW('These are interface statistics reported by pf.'));
require_once($VIEW_PATH . '/footer.php');
?>
