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

require_once($MODEL_PATH.'/model.php');

class Named extends Model
{
	public $Name= 'named';
	public $User= 'root|_bind';
	
	private $ConfFile= '/var/named/etc/named.conf';

	function __construct()
	{
		global $TmpFile;
		
		parent::__construct();
		
		$this->StartCmd= "/usr/local/sbin/named -t /var/named/ > $TmpFile 2>&1 &";
		
		$this->Commands= array_merge(
			$this->Commands,
			array(
				'GetListenOn'	=> array(
					'argv'	=> array(),
					'desc'	=> _('Get IP DNS listens on'),
					),

				'SetListenOn'	=> array(
					'argv'	=> array(IPADRLIST|STR),
					'desc'	=> _('Set IP DNS listens on'),
					),

				'GetForwarders'	=> array(
					'argv'	=> array(),
					'desc'	=> _('Get DNS forwarders'),
					),

				'SetForwarders'	=> array(
					'argv'	=> array(IPADRLIST),
					'desc'	=> _('Set DNS forwarders'),
					),
				)
			);
	}

	/**
	 * Gets the IP address(es) that the name server listens on for requests.
	 *
	 * @return string IP address, semi-colon separated addresses, or any.
	 */
	function GetListenOn()
	{
		return Output($this->SearchFile($this->ConfFile, "/^\h*listen-on\h*{\h*(.*)\h*\;\h*}\h*\;\h*$/m"));
	}

	/**
	 * Sets the IP address that the name server listens on for requests.
	 *
	 * @param string $listenon Semicolon separated list of IPs, or any.
	 * @return bool TRUE on success, FALSE on fail.
	 */
	function SetListenOn($listenon)
	{
		return $this->ReplaceRegexp($this->ConfFile, "/^(\h*listen-on\h*{\h*)(.*)(\h*\;\h*}\h*\;\h*)$/m", '${1}'.$listenon.'${3}');
	}
	
	/**
	 * Gets name server forwarders.
	 *
	 * @return Forwarders IP, semi-colon separated.
	 */
	function GetForwarders()
	{
		return Output($this->SearchFile($this->ConfFile, "/^\h*forwarders\h*{\h*(.*)\h*\;\h*}\h*\;\h*$/m"));
	}

	/**
	 * Sets name server forwarders.
	 *
	 * @param string $forwarders Semicolon separated list of forwarder IP addresses.
	 * @return bool TRUE on success, FALSE on fail.
	 */
	function SetForwarders($forwarders)
	{
		return $this->ReplaceRegexp($this->ConfFile, "/^(\h*forwarders\h*{\h*)(.*)(\h*\;\h*}\h*\;\h*)$/m", '${1}'.$forwarders.'${3}');
	}
}
?>
