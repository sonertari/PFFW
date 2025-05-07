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
 * Service startup configuration.
 */

if (count($_POST)) {
	foreach ($_POST['Services'] as $Service) {
		if (filter_has_var(INPUT_POST, '>>')) {
			$View->Controller($Status, 'DisableService', $Service);
		}
		else if (filter_has_var(INPUT_POST, '<<')) {
			$View->Controller($Status, 'EnableService', $Service);
		}
	}
}

require_once($VIEW_PATH.'/header.php');

$ServiceDescs= array(
	'/usr/local/sbin/php-fpm-8.4'	=> _TITLE2('PHP FastCGI Server'),
	'/usr/local/sbin/dnsmasq'		=> _TITLE2('DNS Forwarder'),
	'/usr/local/libexec/symux'		=> _TITLE2('Symux System Monitoring'),
	'/usr/local/libexec/symon'		=> _TITLE2('Symon System Monitoring'),
	'/usr/local/sbin/collectd'		=> _TITLE2('Collectd System Statistics'),
	'pf'							=> _TITLE2('Packet Filter'),
	'httpd_flags'					=> _TITLE2('Web Server (WUI)'),
	'slowcgi_flags'					=> _TITLE2('CGI Server'),
	'dhcpd_flags'					=> _TITLE2('DHCP Server'),
	'ftpproxy_flags'				=> _TITLE2('FTP Proxy'),
	'ntpd_flags'					=> _TITLE2('Network Time'),
	'apmd_flags'					=> _TITLE2('Advanced Power Management'),
	);

if ($View->Controller($Output, 'GetServiceStartStatus')) {
	$StartStatus= json_decode($Output[0], TRUE);
	?>
	<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
		<table style="width: auto;">
			<tr>
				<td style="width: 0;">
					<?php
					echo _TITLE('Enabled Services').':';
					?>
					<br />
					<select name="Services[]" multiple style="width: 250px; height: 350px;">
						<?php
						foreach ($StartStatus as $Service => $Status) {
							if ($Status) {
								?>
								<option value="<?php echo $Service ?>" title="<?php echo $Service ?>"><?php echo _($ServiceDescs[$Service]) ?></option>
								<?php
							}
						}
						?>
					</select>
				</td>
				<td style="width: 0;">
					<input type="submit" name=">>" value=">>"/>
					<br />
					<input type="submit" name="&lt&lt" value="&lt&lt"/>
				</td>
				<td style="width: 0;">
					<?php
					echo _TITLE('Disabled Services').':';
					?>
					<br />
					<select name="Services[]" multiple style="width: 250px; height: 350px;">
						<?php
						foreach ($StartStatus as $Service => $Status) {
							if (!$Status) {
								?>
								<option value="<?php echo $Service ?>" title="<?php echo $Service ?>"><?php echo _($ServiceDescs[$Service]) ?></option>
								<?php
							}
						}
						?>
					</select>
				</td>
			</tr>
		</table>
	</form>
	<?php
}

PrintHelpWindow(_HELPWINDOW('PFFW runs many services or daemons in default installation. On this page you can configure these services to start at boot time.

Note that if you modify startup configuration for some services, you may need to change packet filter rules or other related settings too.'));
require_once($VIEW_PATH.'/footer.php');
?>
