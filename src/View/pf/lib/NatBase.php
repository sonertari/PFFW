<?php
/*
 * Copyright (C) 2004-2025 Soner Tari
 *
 * This file is part of PFRE.
 *
 * PFRE is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * PFRE is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with PFRE.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace View;

class NatBase extends Filter
{
	function display($ruleNumber, $count)
	{
		$this->dispHead($ruleNumber, $count);
		$this->dispAction();
		$this->dispValue('direction', _TITLE('Direction'));
		$this->dispInterface();
		$this->dispLog();
		$this->dispKey('quick', _TITLE('Quick'));
		$this->dispValue('proto', _TITLE('Proto'));
		$this->dispSrcDest();
		$this->dispValues('redirhost', _TITLE('Redirect Host'));
		$this->dispValue('redirport', _TITLE('Redirect Port'));
		$this->dispTail($ruleNumber);
	}
	
	function input()
	{
		$this->inputAction();

		$this->inputFilterHead();

		$this->inputLog();
		$this->inputBool('quick');

		$this->inputNat();

		$this->inputFilterOpts();

		$this->inputKey('comment');
		$this->inputDelEmpty();
	}

	function inputNat()
	{
		$this->inputDel('redirhost', 'delRedirHost');
		$this->inputAdd('redirhost', 'addRedirHost');
		$this->inputKey('redirport');
		$this->inputPoolType();
	}

	function edit($ruleNumber, $modified, $testResult, $generateResult, $action)
	{
		$this->editIndex= 0;
		$this->ruleNumber= $ruleNumber;

		$this->editHead($modified, $testResult, $generateResult, $action);

		$this->editAction();

		$this->editFilterHead();

		$this->editLog();
		$this->editCheckbox('quick', _TITLE('Quick'));

		$this->editNat();

		$this->editFilterOpts();

		$this->editComment();
		$this->editTail();
	}

	function editNat()
	{
		$this->editValues('redirhost', _TITLE('Redirect Host'), 'delRedirHost', 'addRedirHost', _CONTROL('ip, host, table or macro'), 'Nat', NULL);
		$this->editText('redirport', _TITLE('Redirect Port'), 'Nat', NULL, _CONTROL('number, name, table or macro'));
		$this->editPoolType();
	}
}
?>
