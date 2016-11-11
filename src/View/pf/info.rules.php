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
	$rules= array();
	$r= array();

	//@0 match in all scrub (no-df)
	//  [ Evaluations: 1558      Packets: 16048     Bytes: 7312376     States: 2     ]
	//  [ Inserted: uid 0 pid 7529 State Creations: 0     ]
	foreach ($lines as $line) {
		if (preg_match('/^@(\d+)\s+(.*)$/', $line, $match)) {
			if (count($r) > 0) {
				if (!isset($r['number'])) {
					$r['number']= '';
				}
				$rules[]= $r;
			}

			$r= array(
				'number' => $match[1],
				'rule' => $match[2],
				);
		} elseif (preg_match('/^\s*\[\s*Evaluations:\s*(\d+)\s*Packets:\s*(\d+)\s*Bytes:\s*(\d+)\s*States:\s*(\d+)\s*\]/', $line, $match)) {
			$r['evaluations']= convertDecimal($match[1]);
			$r['packets']= convertDecimal($match[2]);
			$r['bytes']= convertBinary($match[3]);
			$r['states']= convertDecimal($match[4]);
		} elseif (preg_match('/^\s*\[\s*Inserted:\s*(.*)\s*State Creations:\s*(\d+)\s*\]/', $line, $match)) {
			$r['inserted']= $match[1];
			$r['stateCreations']= convertDecimal($match[2]);
		} else {
			pffwwui_syslog(LOG_WARNING, __FILE__, __FUNCTION__, __LINE__, "Failed parsing rule line: $line");
		}
	}
	
	if (count($r) > 0) {
		$rules[]= $r;
	}
	
	return $rules;
}

$View->Controller($Output, 'GetPfRulesInfo');
$rules= parse($Output);

$Reload= TRUE;
require_once($VIEW_PATH . '/header.php');
?>
<div id="main">
	<table id="logline">
		<tr>
			<th><?php echo _('Number') ?></th>
			<th><?php echo _('Evaluations') ?></th>
			<th><?php echo _('Packets') ?></th>
			<th><?php echo _('Bytes') ?></th>
			<th><?php echo _('States') ?></th>
			<th><?php echo _('State Creations') ?></th>
			<th><?php echo _('Rule') ?></th>
			<th><?php echo _('Inserted') ?></th>
		</tr>
		<?php
		$linenum= 0;
		foreach ($rules as $r) {
			$class= ($linenum++ % 2 == 0) ? 'evenline' : 'oddline';
			?>
			<tr class="<?php echo $class ?>">
				<td class="center" ><?php echo $r['number'] ?></td>
				<td class="right" ><?php echo $r['evaluations'] ?></td>
				<td class="right" ><?php echo $r['packets'] ?></td>
				<td class="right" ><?php echo $r['bytes'] ?></td>
				<td class="right" ><?php echo $r['states'] ?></td>
				<td class="right" ><?php echo $r['stateCreations'] ?></td>
				<td><?php echo $r['rule'] ?></td>
				<td><?php echo $r['inserted'] ?></td>
			</tr>
			<?php
		}
		?>
	</table>
</div>
<?php
PrintHelpWindow(_HELPWINDOW('These are the active rules loaded into pf. Note that the rule numbers reported here do not necessarily match with the numbers on the rule editor.'));
require_once($VIEW_PATH . '/footer.php');
?>
