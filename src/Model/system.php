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
 * System-wide.
 */

require_once($MODEL_PATH.'/model.php');

class System extends Model
{
	public $Name= 'system';

	private $confDir= '/etc/';

	function __construct()
	{
		parent::__construct();
	
		$this->Proc= '.';
		
		$this->Commands= array_merge(
			$this->Commands,
			array(
				'GetMyName'		=>	array(
					'argv'	=>	array(),
					'desc'	=>	_('Read system hostname'),
					),

				'GetRootEmail'	=>	array(
					'argv'	=>	array(),
					'desc'	=>	_('Get system admin e-mail address'),
					),

				'GetIfConfig'		=>	array(
					'argv'	=>	array(NAME),
					'desc'	=>	_('Get if config'),
					),

				'GetStaticGateway'		=>	array(
					'argv'	=>	array(),
					'desc'	=>	_('Read system gateway'),
					),

				'GetHosts'		=>	array(
					'argv'	=>	array(),
					'desc'	=>	_('List hosts'),
					),

				'GetNameServer'	=>	array(
					'argv'	=>	array(),
					'desc'	=>	_('Read system nameserver'),
					),

				'GetConfig'=>	array(
					'argv'	=>	array(),
					'desc'	=>	_('Get configuration'),
					),

				'SetMyName'		=>	array(
					'argv'	=>	array(NAME),
					'desc'	=>	_('Set system hostname'),
					),

				'SetRootEmail'	=>	array(
					'argv'	=>	array(EMAIL),
					'desc'	=>	_('Set e-mail address'),
					),

				'SystemMakeStaticGateway'		=>	array(
					'argv'	=>	array(),
					'desc'	=>	_('Make system gateway static'),
					),

				'SystemMakeDynamicGateway'		=>	array(
					'argv'	=>	array(),
					'desc'	=>	_('Make system gateway dynamic'),
					),

				'GetDynamicGateway'	=>	array(
					'argv'	=>	array(),
					'desc'	=>	_('Get system gateway'),
					),

				'SetMyGate'		=>	array(
					'argv'	=>	array(IPADR),
					'desc'	=>	_('Set system gateway'),
					),

				'SetIf'		=>	array(
					/// @todo Is there any pattern or size for options, 6th param?
					'argv'	=>	array(NAME, NAME, IPADR|NAME|EMPTYSTR, IPADR|NAME|EMPTYSTR, IPADR|NAME|EMPTYSTR, STR|EMPTYSTR),
					'desc'	=>	_('Configure an interface'),
					),

				'DeleteIf'	=>	array(
					'argv'	=>	array(NAME),
					'desc'	=>	_('Unconfigure an interface'),
					),

				'SetNameServer'	=>	array(
					'argv'	=>	array(IPADR),
					'desc'	=>	_('Set system nameserver'),
					),

				'AddHost'		=>	array(
					'argv'	=>	array(HOST),
					'desc'	=>	_('Add host'),
					),

				'DelHost'		=>	array(
					'argv'	=>	array(HOST),
					'desc'	=>	_('Delete host'),
					),

				'SetDateTime'		=>	array(
					'argv'	=>	array(DATETIME),
					'desc'	=>	_('Set system clock'),
					),

				'UpdateMailAliases'	=>	array(
					'argv'	=>	array(),
					'desc'	=>	_('Update mail aliases'),
					),

				'DisplayRemoteTime'	=>	array(
					'argv'	=>	array(URL|IPADR),
					'desc'	=>	_('Display remote time'),
					),
				
				'SetRemoteTime'	=>	array(
					'argv'	=>	array(URL|IPADR),
					'desc'	=>	_('Set remote time'),
					),

				'GetRemoteTime'	=>	array(
					'argv'	=>	array(),
					'desc'	=>	_('Get remote time'),
					),

				'AutoConfig'	=>	array(
					'argv'	=>	array(),
					'desc'	=>	_('Automatic configuration'),
					),

				'InitGraphs'	=>	array(
					'argv'	=>	array(),
					'desc'	=>	_('Init graphs'),
					),

				'DeleteStats'	=>	array(
					'argv'	=>	array(),
					'desc'	=>	_('Erase statistics files'),
					),
				
				'Shutdown'	=>	array(
					'argv'	=>	array(),
					'desc'	=>	_('System shutdown'),
					),

				'Restart'	=>	array(
					'argv'	=>	array(),
					'desc'	=>	_('System restart'),
					),

				'NetStart'	=>	array(
					'argv'	=>	array(),
					'desc'	=>	_('Restart network'),
					),
				)
			);
	}

	/**
	 * Adds host line to hosts file.
	 *
	 * @param string $host Host definition line.
	 * @return bool TRUE on success, FALSE on fail.
	 */
	function AddHost($host)
	{
		$this->DelHost($host);
		return $this->AppendToFile($this->confDir.'hosts', $host);
	}

	/**
	 * Deletes host line from hosts file.
	 *
	 * @param string $host Host definition line.
	 * @return bool TRUE on success, FALSE on fail.
	 */
	function DelHost($host)
	{
		return $this->ReplaceRegexp($this->confDir.'hosts', "/^(\h*$host(\s|))/m", '');
	}

	/**
	 * Reads hosts file contents.
	 *
	 * @return string Uncommented lines of hosts file.
	 */
	function GetHosts()
	{
		global $Re_Ip;

		return Output($this->SearchFileAll($this->confDir.'hosts', "/^\h*(($Re_Ip|[:\d]+)\b.*)\h*$/m"));
	}

	/**
	 * Reads hostname.
	 *
	 * @return string System name, output of hostname too.
	 */
	function GetMyName()
	{
		return Output($this->_getMyName());
	}

	function _getMyName()
	{
		return $this->GetFile($this->confDir.'myname');
	}

	/**
	 * Reads nameserver setting.
	 *
	 * @return string System-wide nameserver.
	 */
	function GetNameServer()
	{
		return Output($this->SearchFile($this->confDir.'resolv.conf', "/^\h*nameserver\h*([^#]*)\h*$/m"));
	}

	/**
	 * Reads root e-mail address.
	 *
	 * @return string Root e-mail address.
	 */
	function GetRootEmail()
	{
		return Output($this->SearchFile($this->confDir.'mail/aliases', "/^\h*root:\h*([^#]*)\h*$/m"));
	}

	/**
	 * Reads interface configuration.
	 *
	 * @param string $if Interface to get configuration of.
	 * @return string Root e-mail address.
	 */
	function GetIfConfig($if)
	{
		return Output($this->_getIfConfig($if));
	}

	function _getIfConfig($if)
	{
		$file= $this->confDir."hostname.".$if;
		if (file_exists($file)) {
			if (($contents= $this->GetFile($file)) !== FALSE) {
				$re= '^\s*(inet|dhcp)\s*(\S*)\s*(\S*)\s*(\S*)\s*(\S*)\s*$';
				if (preg_match("/$re/m", $contents, $match)) {
					return json_encode(array_slice($match, 1));
				}
			}
		}
		return FALSE;
	}

	/**
	 * Reads static gateway address from mygate file.
	 *
	 * @return string IP address of the gateway.
	 */
	function GetStaticGateway()
	{
		return Output($this->_getStaticGateway());
	}

	function _getStaticGateway()
	{
		return $this->GetFile($this->confDir.'mygate');
	}

	/**
	 * Reads the default gateway on the routing table.
	 * 
	 * @return string IP address of the gateway.
	 */
	function GetDynamicGateway()
	{
		return Output($this->_getDynamicGateway());
	}

	function _getDynamicGateway()
	{
		global $Re_Ip;

		$cmd= "/sbin/route -n get default | /usr/bin/grep gateway 2>&1";
		exec($cmd, $output, $retval);
		if ($retval === 0) {
			if (count($output) > 0) {
				#    gateway: 10.0.0.2
				$re= "\s*gateway:\s*($Re_Ip)\s*";
				if (preg_match("/$re/m", $output[0], $match)) {
					return $match[1];
				}
			}
		}
		else {
			$errout= implode("\n", $output);
			Error($errout);
			ctlr_syslog(LOG_ERR, __FILE__, __FUNCTION__, __LINE__, "Get dynamic gateway failed: $errout");
		}
		return FALSE;
	}

	/**
	 * Reads hostname, gateway, and interface configuration.
	 * 
	 * @return array Configuration.
	 */
	function GetConfig()
	{
		$config= array();
		
		if (($myname= $this->_getMyName()) !== FALSE) {
			$config['Myname']= trim($myname);
		}
	
		if (($mygate= $this->_getStaticGateway()) !== FALSE) {
			$config['Mygate']= trim($mygate);
			$config['StaticGateway']= TRUE;
		}
		else if (($mygate= $this->_getDynamicGateway()) !== FALSE) {
			$config['Mygate']= trim($mygate);
			$config['StaticGateway']= FALSE;
		}

		if (($intif= $this->_getIntIf()) !== FALSE) {
			$config['IntIf']= trim($intif, '"');
		}
		
		if (($extif= $this->_getExtIf()) !== FALSE) {
			$config['ExtIf']= trim($extif, '"');
		}
		
		if (($ifs= $this->_getPhyIfs()) !== FALSE) {
			$ifs= explode("\n", $ifs);
			foreach ($ifs as $if) {
				$config['Ifs'][$if]= array();
				if (($output= $this->_getIfConfig($if)) !== FALSE) {
					$config['Ifs'][$if]= json_decode($output, TRUE);
				}
			}
		}
		
		return Output(json_encode($config));
	}

	/**
	 * Converts dynamic gateway address to static address.
	 * 
	 * @return bool TRUE on success, FALSE on fail.
	 */
	function SystemMakeStaticGateway()
	{
		if (($gateway= $this->_getDynamicGateway()) !== FALSE) {
			return $this->SetMyGate($gateway);
		}
		return FALSE;
	}

	/**
	 * Converts static gateway address to dynamic address.
	 * 
	 * Simply deletes mygate file.
	 * 
	 * @return bool TRUE on success, FALSE on fail.
	 */
	function SystemMakeDynamicGateway()
	{
		return $this->DeleteFile($this->confDir.'mygate');
	}

	/**
	 * Sets system static gateway.
	 * 
	 * Writes the given IP address to mygate file.
	 *
	 * @param string $mygate Gateway IP address.
	 * @return int Return value of file_put_contents().
	 */
	function SetMyGate($mygate)
	{
		// File is created, if does not exist. Otherwise, overwrites the existing file.
		return file_put_contents($this->confDir.'mygate', $mygate.PHP_EOL);
	}

	/**
	 * Sets system hostname.
	 *
	 * Writes the given name to myname file.
	 * 
	 * @param string $myname Hostname.
	 * @return int Return value of file_put_contents().
	 */
	function SetMyName($myname)
	{
		return file_put_contents($this->confDir.'myname', $myname.PHP_EOL);
	}

	/**
	 * Sets system interface configuration.
	 *
	 * @param string $if Interface name.
	 * @param string $type inet or dhcp only.
	 * @param string $ip IP address.
	 * @param string $mask Netmask.
	 * @param string $bc Broadcast address.
	 * @param string $opt Options.
	 * @return mixed Return value of file_put_contents() or FALSE on fail.
	 */
	function SetIf($if, $type, $ip, $mask, $bc, $opt)
	{
		global $Re_Ip;
		
		// Trim whitespace caused by empty strings
		$ifconf= trim("$type $ip $mask $bc $opt");
		// PFFW supports only these configuration
		if (preg_match("/^inet\s*$Re_Ip\s*$Re_Ip\s*($Re_Ip|).*$/", $ifconf)
			|| preg_match('/^dhcp\s*NONE\s*NONE\s*NONE.*$/', $ifconf)
			|| preg_match('/^dhcp$/', $ifconf)) {
			/// @warning Need a new line char at the end of hostname.if, otherwise /etc/netstart fails
			/// Since file_put_contents() removes the last new line char, we append a PHP_EOL.
			return file_put_contents($this->confDir.'hostname.'.$if, $ifconf.PHP_EOL);
		}
		else {
			Error(_('Unsupported interface configuration').": $ifconf");
		}
		return FALSE;
	}

	/**
	 * Deconfigures an interface by deleting its hostname file.
	 *
	 * @param string $if Interface name.
	 * @return bool TRUE on success, FALSE on fail.
	 */
	function DeleteIf($if)
	{
		exec("/sbin/ifconfig $if down");
		exec("/sbin/ifconfig $if delete");
		return $this->DeleteFile($this->confDir.'hostname.'.$if);
	}

	/**
	 * Changes nameserver.
	 *
	 * @param string $nameserver System nameserver IP.
	 * @return bool TRUE on success, FALSE on fail.
	 */
	function SetNameServer($nameserver)
	{
		global $Re_Ip;
		
		return $this->ReplaceRegexp($this->confDir.'resolv.conf', "/^(\h*nameserver\h*)($Re_Ip)(\b.*)$/m", '${1}'.$nameserver.'${3}');
	}

	/**
	 * Changes root e-mail address.
	 *
	 * @param string $emailaddr E-mail address.
	 * @return bool TRUE on success, FALSE on fail.
	 */
	function SetRootEmail($emailaddr)
	{
		return $this->ReplaceRegexp($this->confDir.'mail/aliases', "/^(\h*root:\h*)([^#\s]*)(.*)$/m", '${1}'.$emailaddr.'${3}');
	}

	/**
	 * Sets system clock.
	 *
	 * @param string $datetime Datetime.
	 * @return bool TRUE on success, FALSE on fail.
	 */
	function SetDateTime($datetime)
	{
		exec("/bin/date $datetime 2>&1", $output, $retval);
		/// /bin/date returns 0 on success on OpenBSD 5.9 now.
		if ($retval === 0) {
			return TRUE;
		}
		$errout= implode("\n", $output);
		Error($errout);
		ctlr_syslog(LOG_ERR, __FILE__, __FUNCTION__, __LINE__, "Set date failed: $errout");
		return FALSE;
	}

	/**
	 * Updates mail aliases.
	 * 
	 * This is necessary after modifying the aliases file.
	 * 
	 * @return string Output of newaliases.
	 */
	function UpdateMailAliases()
	{
		return Output($this->RunShellCommand('/usr/bin/newaliases'));
	}

	/**
	 * Runs installer with the automatic configuration option.
	 * 
	 * @return bool TRUE on success, FALSE on fail.
	 */
	function AutoConfig()
	{
		global $SRC_ROOT;
		
		exec("$SRC_ROOT/Installer/install.php -a 2>&1", $output, $retval);
		if ($retval === 0) {
			return TRUE;
		}
		$errout= implode("\n", $output);
		Error($errout);
		ctlr_syslog(LOG_ERR, __FILE__, __FUNCTION__, __LINE__, "Auto configuration failed: $errout");
		return FALSE;
	}

	/**
	 * Deletes all graph files and recreates them if necessary.
	 * 
	 * @return bool TRUE on success, FALSE on fail.
	 */
	function InitGraphs()
	{
		global $VIEW_PATH;

		$result= TRUE;
		// symon
		exec("/bin/rm -f ${VIEW_PATH}/symon/cache/* 2>&1", $output, $retval);
		// Failing to clear the cache dir is not fatal
		exec("/bin/rm -f ${VIEW_PATH}/symon/rrds/localhost/*.rrd 2>&1", $output, $retval);
		if ($retval === 0) {
			exec('/bin/sh /usr/local/share/examples/symon/c_smrrds.sh all 2>&1', $output, $retval);
			if ($retval !== 0) {
				$result= FALSE;
			}
		}
		else {
			$result= FALSE;
		}
		
		if (!$result) {
			$errout= implode("\n", $output);
			Error($errout);
			ctlr_syslog(LOG_ERR, __FILE__, __FUNCTION__, __LINE__, "Failed initializing graphs: $errout");
		}
		return $result;
	}

	/**
	 * Deletes temporary logs, statistics, and output files created by the WUI.
	 * 
	 * @return bool TRUE on success, FALSE on fail.
	 */
	function DeleteStats()
	{
		exec('/bin/rm -rf /var/tmp/pffw/* 2>&1', $output, $retval);
		if ($retval === 0) {
			return TRUE;
		}
		$errout= implode("\n", $output);
		Error($errout);
		ctlr_syslog(LOG_ERR, __FILE__, __FUNCTION__, __LINE__, "Failed erasing statistics files: $errout");
		return FALSE;
	}
	
	/**
	 * Halts and powers the system down.
	 */
	function Shutdown()
	{
		global $TmpFile;
		
		$this->RunShellCommand("/sbin/shutdown -h -p now > $TmpFile 2>&1 &");
	}

	/**
	 * Restarts the system.
	 */
	function Restart()
	{
		global $TmpFile;
		
		$this->RunShellCommand("/sbin/shutdown -r now > $TmpFile 2>&1 &");
	}

	/**
	 * Displays datetime from the given time server.
	 * 
	 * Writes its output to a temporary file.
	 */
	function DisplayRemoteTime($timeserver)
	{
		global $TmpFile;
		
		// First make sure there are no running rdate processes.
		$this->Pkill('rdate');
		$this->RunShellCommand("/usr/sbin/rdate -p $timeserver > $TmpFile 2>&1 &");
		return TRUE;
	}

	/**
	 * Sets datetime from the given time server.
	 * 
	 * Writes its output to a temporary file.
	 */
	function SetRemoteTime($timeserver)
	{
		global $TmpFile;

		// First make sure there are no running rdate processes.
		$this->Pkill('rdate');
		$this->RunShellCommand("/usr/sbin/rdate $timeserver > $TmpFile 2>&1 &");
		return TRUE;
	}

	/**
	 * Reads datetime temporary output file.
	 * 
	 * DisplayRemoteTime() and SetRemoteTime() functions write their output to a tmp file.
	 * We wait for their output in a loop.
	 * 
	 * @attention PHP sleep() function is affected by changes to the system clock,
	 * so we cannot use it in the while loop. Note that we change the system time by calling SetRemoteTime().
	 * But, the shell sleep command seems not affected by changes, to the clock, so we use it instead.
	 * 
	 * @return string Datetime.
	 */
	function GetRemoteTime()
	{
		global $TmpFile;
		
		$count= 0;
		while ($count++ < self::PROC_STAT_TIMEOUT) {
			// Wait until rdate exits
			if (!$this->IsRunning('rdate')) {
				break;
			}
			// Shell sleep command seems not affected by changes to clock
			exec('/bin/sleep .1');
		}
		
		if ($count < self::PROC_STAT_TIMEOUT) {
			if (($output= $this->GetFile($TmpFile)) !== FALSE) {
				$retval= $output;
			}
		}
		else {
			$retval= _('The process is taking too long, thus will run in the background.');
		}
		return Output($retval);
	}
	
	/**
	 * Restarts the network.
	 * 
	 * This method should be called after changing the network configuration of the system.
	 * 
	 * @attention We have to reload pf rules too, otherwise all access to the system may be blocked.
	 * For example, if you change the IP address of int_if, you need to reload the rules;
	 * otherwise, pf would still be running with rules using the old IP address of int_if.
	 * 
	 * @return bool TRUE on success, FALSE on fail.
	 */
	function NetStart()
	{
		// Refresh pf rules too
		$cmd= "/bin/sh /etc/netstart 2>&1 && /sbin/pfctl -f $this->PfRulesFile 2>&1";
		exec($cmd, $output, $retval);
		if ($retval === 0) {
			return TRUE;
		}
		$errout= implode("\n", $output);
		Error($errout);
		ctlr_syslog(LOG_ERR, __FILE__, __FUNCTION__, __LINE__, "Netstart failed: $errout");
		return FALSE;
	}
	
	/**
	 * Reads all processes from ps output.
	 *
	 * Used to list all processes.
	 *
	 * @param array $psout ps output obtained elsewhere.
	 * @return array Parsed ps output.
	 */
	function SelectProcesses($psout)
	{
		//   PID STARTED  %CPU      TIME %MEM   RSS   VSZ STAT  PRI  NI TTY      USER     GROUP    COMMAND
		//     1  5:10PM   0.0   0:00.03  0.0   388   412 Is     10   0 ??       root     wheel    /sbin/init
		$re= '/^\s*(\d+)\s+(\S+)\s+(\S+)\s+(\S+)\s+(\S+)\s+(\d+)\s+(\d+)\s+(\S+)\s+(\d+)\s+(\d+)\s+\S+\s+(\S+)\s+(\S+)\s+(.+)$/';
		
		$processes= array();
		foreach ($psout as $line) {
			if (preg_match($re, $line, $match)) {
				$processes[]= array(
					$match[1],
					$match[2],
					$match[3],
					$match[4],
					$match[5],
					$match[6],
					$match[7],
					$match[8],
					$match[9],
					$match[10],
					$match[11],
					$match[12],
					$match[13],
					);
			}
		}
		return $processes;
	}
}
?>
