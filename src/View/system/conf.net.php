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
 * Network configuration.
 */

/**
 * Displays a list box with hosts file contents.
 *
 * @todo This form needs improvement, should not be one line entry.
 */
function PrintHostsForm()
{
	global $View, $Class;
	?>
	<tr class="<?php echo $Class ?>">
		<td class="title">
			<?php echo _TITLE('Hosts').':' ?>
		</td>
		<td>
			<?php
			if ($View->Controller($hosts, 'GetHosts')) {
				?>
				<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
					<input style="display:none;" type="submit" name="Add" value="<?php echo _CONTROL('Add') ?>"/>
					<select name="HostsToDelete[]" multiple style="width: 300px; height: 100px;">
						<?php
						foreach ($hosts as $host) {
						?>
						<option value="<?php echo $host ?>"><?php echo $host ?></option>
						<?php
						}
						?>
					</select>
					<input type="submit" name="Delete" value="<?php echo _CONTROL('Delete') ?>"/><br />
					<input type="text" name="HostToAdd" style="width: 300px;" maxlength="250"/>
					<input type="submit" name="Add" value="<?php echo _CONTROL('Add') ?>"/>
				</form>
				<?php
			}
			?>
		</td>
		<td class="none" rowspan="2">
			<?php
			PrintHelpBox(_HELPBOX('You can modify the hosts file here. Format of a hosts entry is: IP hostname alias [alias [...]]'));
			?>
		</td>
	</tr>
	<?php
}

if (count($_POST)) {
	if (filter_has_var(INPUT_POST, 'NetStart')) {
		$View->Controller($Output, 'NetStart');
	}
	// Other vars may be empty strings, do not check
	else if (filter_has_var(INPUT_POST, 'IfName')) {
		if (filter_has_var(INPUT_POST, 'Apply')) {
			$View->Controller($Output, 'SetIf', filter_input(INPUT_POST, 'IfName'), filter_input(INPUT_POST, 'IfType'), filter_input(INPUT_POST, 'InterfaceIP'),
					filter_input(INPUT_POST, 'IfMask'), filter_input(INPUT_POST, 'IfBc'), filter_input(INPUT_POST, 'IfOpt'),
					filter_has_var(INPUT_POST, 'IfNwId') ? filter_input(INPUT_POST, 'IfNwId') : '',
					filter_has_var(INPUT_POST, 'IfKeyType') ? filter_input(INPUT_POST, 'IfKeyType') : '',
					filter_has_var(INPUT_POST, 'IfKey') ? filter_input(INPUT_POST, 'IfKey') : '',
					filter_has_var(INPUT_POST, 'IfHostap') ? filter_input(INPUT_POST, 'IfHostap') : '');
		}
		else if (filter_has_var(INPUT_POST, 'Restart')) {
			$View->Controller($Output, 'NetStart', filter_input(INPUT_POST, 'IfName'));
		}
		else if (filter_has_var(INPUT_POST, 'Up')) {
			$View->Controller($Output, 'IfUpDown', filter_input(INPUT_POST, 'IfName'), 1);
		}
		else if (filter_has_var(INPUT_POST, 'Down')) {
			$View->Controller($Output, 'IfUpDown', filter_input(INPUT_POST, 'IfName'), 0);
		}
		else if (filter_has_var(INPUT_POST, 'Delete')) {
			$View->Controller($Output, 'DeleteIf', filter_input(INPUT_POST, 'IfName'));
		}
	}
	else if (filter_has_var(INPUT_POST, 'MyGate')) {
		if (filter_has_var(INPUT_POST, 'Apply')) {
			$View->Controller($Output, 'SetMyGate', filter_input(INPUT_POST, 'MyGate'));
		}
		else if (filter_has_var(INPUT_POST, 'MakeDynamic')) {
			$View->Controller($Output, 'SystemMakeDynamicGateway');
		}
	}
	else if (filter_has_var(INPUT_POST, 'MakeStatic')) {
		$View->Controller($Output, 'SystemMakeStaticGateway');
	}
	else if (filter_has_var(INPUT_POST, 'NameServer')) {
		$View->Controller($Output, 'SetNameServer', filter_input(INPUT_POST, 'NameServer'));
	}
	else if (filter_has_var(INPUT_POST, 'Add') && filter_has_var(INPUT_POST, 'HostToAdd')) {
		$View->Controller($Output, 'AddHost', filter_input(INPUT_POST, 'HostToAdd'));
	}
	else if (filter_has_var(INPUT_POST, 'Delete')) {
		foreach ($_POST['HostsToDelete'] as $Host) {
			$View->Controller($Output, 'DelHost', $Host);
		}
	}
}

require_once($VIEW_PATH.'/header.php');
?>
<table id="nvp">
	<?php
	$Row= 1;
	$Class= $Row++ % 2 == 0 ? 'evenline' : 'oddline';
	?>
	<tr class="<?php echo $Class ?>">
		<td class="title">
			<?php echo _TITLE('Restart network').':' ?>
		</td>
		<td>
			<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
				<input type="submit" name="NetStart" value="<?php echo _CONTROL('Apply') ?>" onclick="return confirm('<?php echo _NOTICE('Are you sure you want to restart the network?') ?>')"/>
			</form>
		</td>
		<td class="none">
			<?php
			PrintHelpBox(_HELPBOX('If you modify network configuration, you can use this button to restart the network.'));
			?>
		</td>
	</tr>
	<tr>
		<td class="rowspanhelp">
		</td>
		<td class="rowspanhelp">
		</td>
		<td class="rowspanhelp" rowspan="2">
			<?php PrintHelpBox(_HELPBOX('Network interfaces are listed here. You can configure an interface as dhcp/autoconf or inet. Changes made here do not take effect until you restart the network or reboot the system.')) ?>
		</td>
	</tr>
	<?php
	if ($View->Controller($Ifs, 'GetPhyIfs')) {
		if ($View->Controller($Output, 'GetIntIf')) {
			$IntIf= trim($Output[0], '"');
		}
		
		if ($View->Controller($Output, 'GetExtIf')) {
			$ExtIf= trim($Output[0], '"');
		}

		foreach ($Ifs as $If) {
			$CanDelete= FALSE;
			if ($IntIf === $If) {
				$IfAssigned= _TITLE('Internal interface');
			}
			else if ($ExtIf === $If) {
				$IfAssigned= _TITLE('External interface');
			}
			else {
				$IfAssigned= _TITLE('Interface');
				$CanDelete= TRUE;
			}
		
			$IfConfigured= '';
			$IfType= $IfIp= $IfMask= $IfBc= $IfOpt= $IfLladdr= $IfIp2= $IfMask2= $IfBc2= $IfNwId= $IfKeyType= $IfKey= $IfHostap= $IfWifi= $IfUp='';
			if ($View->Controller($Output, 'GetIfConfig', $If)) {
				list($IfType, $IfIp, $IfMask, $IfBc, $IfOpt, $IfLladdr, $IfIp2, $IfMask2, $IfBc2, $IfNwId, $IfKeyType, $IfKey, $IfHostap, $IfWifi, $IfUp)= json_decode($Output[0], TRUE);
			} else {
				$IfConfigured= '<br />('._('unconfigured').')';
				$CanDelete= FALSE;
			}

			$Class= $Row++ % 2 == 0 ? 'evenline' : 'oddline';
			?>
			<tr class="<?php echo $Class ?>">
				<td class="title">
					<?php echo "$IfAssigned $If:$IfConfigured" ?>
				</td>
				<td>
					<table style="width: auto;">
						<tr>
							<td class="ifs">
								<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
									<table>
										<tr>
											<td class="iftitle">type</td>
											<td class="ifs"><input type="text" name="IfType" style="width: 100px;" maxlength="15" value="<?php echo $IfType ?>"/></td>
										</tr>
										<tr>
											<td class="iftitle">ip</td>
											<td class="ifs"><input type="text" name="InterfaceIP" style="width: 100px;" maxlength="15" value="<?php echo $IfIp ?>" placeholder="<?php echo $IfIp2 ?>"/> <?php echo ($IfIp != $IfIp2 ? $IfIp2 : '') ?></td>
										</tr>
										<tr>
											<td class="iftitle">netmask</td>
											<td class="ifs"><input type="text" name="IfMask" style="width: 100px;" maxlength="15" value="<?php echo $IfMask ?>" placeholder="<?php echo $IfMask2 ?>"/></td>
										</tr>
										<tr>
											<td class="iftitle">broadcast</td>
											<td class="ifs"><input type="text" name="IfBc" style="width: 100px;" maxlength="15" value="<?php echo $IfBc ?>" placeholder="<?php echo $IfBc2 ?>"/></td>
										</tr>
										<tr>
											<td class="iftitle">options</td>
											<td class="ifs"><input type="text" name="IfOpt" style="width: 100px;" maxlength="15" value="<?php echo $IfOpt ?>"/></td>
										</tr>
										<tr>
											<td class="iftitle">lladdr</td>
											<td class="ifs"><?php echo $IfLladdr ?></td>
										</tr>
										<?php
										if ($IfWifi == 'wifi') {
											?>
											<tr>
												<td class="iftitle">nwid</td>
												<td class="ifs"><input type="text" name="IfNwId" style="width: 200px;" maxlength="50" value="<?php echo $IfNwId ?>"/></td>
											</tr>
											<tr>
												<td class="iftitle">keytype</td>
												<td class="ifs">
													<select name="IfKeyType">
														<option value=""></option>
														<option <?php echo $IfKeyType == 'wpakey' ? 'selected' : '' ?> value="wpakey">WPA</option>
														<option <?php echo $IfKeyType == 'nwkey' ? 'selected' : '' ?> value="nwkey">WEP</option>
													</select>
												</td>
											</tr>
											<tr>
												<td class="iftitle">key</td>
												<td class="ifs"><input type="text" name="IfKey" style="width: 200px;" maxlength="50" value="<?php echo $IfKey ?>"/></td>
											</tr>
											<?php
											if ($IntIf === $If) {
												?>
												<tr>
													<td class="iftitle">hostap</td>
													<td class="ifs"><input name="IfHostap" type="checkbox" <?php echo $IfHostap == 'hostap' ? 'checked' : '' ?>></td>
												</tr>
												<?php
											}
										}
										?>
										<tr>
											<td class="ifs"></td>
											<td class="ifs">
												<input type="submit" name="Apply" value="<?php echo _CONTROL('Apply') ?>"/>
												<?php
												$confirm= _NOTICE('Are you sure you want to <ACTION> the interface <IF>?');

												$confirm_restart= preg_replace('/<ACTION>/', _NOTICE('restart'), $confirm);
												$confirm_restart= preg_replace('/<IF>/', $If, $confirm_restart);

												$UpName= $IfUp == 'up' ? 'Down' : 'Up';
												$UpButton= $IfUp == 'up' ? _CONTROL('Down') : _CONTROL('Up');

												$confirm_updown= preg_replace('/<ACTION>/', $IfUp == 'up' ? _NOTICE('disable') : _NOTICE('enable'), $confirm);
												$confirm_updown= preg_replace('/<IF>/', $If, $confirm_updown);
												?>
												<input type="submit" name="Restart" value="<?php echo _CONTROL('Restart') ?>" onclick="return confirm('<?php echo $confirm_restart ?>')"/>
												<input type="submit" name="<?php echo $UpName ?>" value="<?php echo $UpButton ?>" onclick="return confirm('<?php echo $confirm_updown ?>')"/>
												<?php
												if ($CanDelete) {
													$confirm_delete= preg_replace('/<ACTION>/', _NOTICE('delete'), $confirm);
													$confirm_delete= preg_replace('/<IF>/', $If, $confirm_delete);
													?>
													<input type="submit" name="Delete" value="<?php echo _CONTROL('Delete') ?>" onclick="return confirm('<?php echo $confirm_delete ?>')"/>
													<?php
												}
												?>
											</td>
										</tr>
									</table>
									<input type="hidden" name="IfName" value="<?php echo $If ?>" />
								</form>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<?php
		}
	}
	
	$Class= $Row++ % 2 == 0 ? 'evenline' : 'oddline';
	?>
	<tr class="<?php echo $Class ?>">
		<td class="title">
			<?php echo _TITLE('Gateway').':' ?>
		</td>
		<td>
			<?php
			$GetStaticGatewaySuccess= $View->Controller($MyGate, 'GetStaticGateway');
			if (!$GetStaticGatewaySuccess && $View->Controller($Gateway, 'GetDynamicGateway')) {
				?>
				<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
					<?php echo $Gateway[0] ?>
					<input type="submit" name="MakeStatic" value="<?php echo _CONTROL('Make Static') ?>"/>
				</form>
				<?php
			}
			else {
				?>
				<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
					<input type="text" name="MyGate" style="width: 100px;" maxlength="50" value="<?php echo $MyGate[0] ?>" />
					<input type="submit" name="Apply" value="<?php echo _CONTROL('Apply') ?>"/>
					<?php
					if ($GetStaticGatewaySuccess) {
						?>
						<input type="submit" name="MakeDynamic" value="<?php echo _CONTROL('Make Dynamic') ?>"/>
						<?php
					}
					?>
				</form>
				<?php
			}
			?>
		</td>
		<td class="none">
			<?php
			PrintHelpBox(_HELPBOX('This is the default gateway used by the system to reach the Internet, and may have been assigned dynamically. You can make your gateway configuration static or dynamic. If you make dynamic, static gateway file will be deleted. If you make static, it will be recreated with the current default gateway.'));
			?>
		</td>
	</tr>
	<?php
	$Class= $Row++ % 2 == 0 ? 'evenline' : 'oddline';
	?>
	<tr class="<?php echo $Class ?>">
		<td class="title">
			<?php echo _TITLE('Nameserver').':' ?>
		</td>
		<td>
			<?php
			if ($View->Controller($NameServer, 'GetNameServer')) {
				?>
				<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
					<input type="text" name="NameServer" style="width: 100px;" maxlength="50" value="<?php echo $NameServer[0] ?>" />
					<input type="submit" name="Apply" value="<?php echo _CONTROL('Apply') ?>"/>
				</form>
				<?php
			}
			?>
		</td>
		<td class="none">
			<?php
			PrintHelpBox(_HELPBOX('This is the name server used by the system. You can use the DNS server on the system, i.e. enter 127.0.0.1 here.'));
			?>
		</td>
	</tr>
	<?php
	$Class= $Row++ % 2 == 0 ? 'evenline' : 'oddline';
	PrintHostsForm();
	?>
</table>
<?php
PrintHelpWindow(_HELPWINDOW('<b>Make sure you have applied your changes to network settings system-wide using automatic configuration button.</b>

If you change the IP address of the network interface over which you are connected to this web user interface, and use the network restart button on this page, do not forget to change the URL on your web browser accordingly.

It is not advised to configure the internal interface as dhcp/autoconf.'));
require_once($VIEW_PATH.'/footer.php');
?>
