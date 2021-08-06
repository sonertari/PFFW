<?php
/*
 * Copyright (C) 2004-2021 Soner Tari
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

require_once($MODEL_PATH.'/monitoring.php');

class Collectd extends Monitoring
{
	public $Name= 'collectd';
	public $User= 'root';

	public $ConfFile= '/etc/collectd.conf';

	public $VersionCmd= '/usr/local/sbin/collectd -h 2>&1';

	protected $LogFilter= 'collectd';
	private $RrdFolder= '';

	function __construct()
	{
		parent::__construct();

		$this->StartCmd= '/usr/local/sbin/collectd';
	}

	function GetVersion()
	{
		return Output(explode(',', $this->RunShellCommand($this->VersionCmd.' | /usr/bin/head -21 | /usr/bin/tail -1'))[0]);
	}

	/**
	 * Kills collectd with the KILL signal, if the parent Stop() call fails.
	 *
	 * If a ping target is not reachable, we have to kill collectd passing 
	 * the -KILL signal.
	 *
	 * @return bool TRUE on success, FALSE on fail.
	 */
	function Stop()
	{
		$killed= parent::Stop();
		if (!$killed) {
			ctlr_syslog(LOG_INFO, __FILE__, __FUNCTION__, __LINE__, "Pkill $this->Proc with KILL signal");
			$killed= $this->Pkill($this->Proc, '-KILL');
		}
		return $killed;
	}
}
?>
