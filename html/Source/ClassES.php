<?php

class	ClassES
{
	public function		__construct($RFQ)
	{
		if (!empty($_POST))
		{
			$this->arr_RFQ = $RFQ;
		}
	}

	public function		processing()
	{
		foreach ($this->arr_RFQ['queries'] as $k_q => $v_q)
		{
			if (isset($this->arr_RFQ['facts'][$k_q]))
			{
				$this->arr_RFQ['queries'][$k_q] = $this->arr_RFQ['facts'][$k_q];
				continue ;
			}
			$this->works = array();
			$this->cur_query = $k_q;
			$this->working($k_q);
			$this->arr_RFQ['queries'][$k_q] = $this->works[$k_q]['value'];
		}
	}

	public function		getResult()
	{
		$result = '';
		foreach ($this->arr_RFQ['queries'] as $k_q => $v_q)
		{
			$result .= $k_q . ' is ';
			if ($v_q == '1')
			{
				$result .= 'true';
			}
			else if ($v_q == '0')
			{
				$result .= 'false';
			}
			else
			{
				$result .= 'undetermined';
			}
			$result .= "\n";
		}
		return ($result);
	}

/////////////////////////////////////////////////////////////////////

private function	working($name)
{																			static $i = 0;
														if ($i++ == 20)
															{echo $i; exit;}

	$this->works[$name] = array(
		'value' => '0',
		'status' => false,
		'left' => NULL,
		'right_0' => NULL,
		'right_1' => NULL);
	if (isset($this->arr_RFQ['facts'][$name]))
	{
		$this->works[$name]['value'] = $this->arr_RFQ['facts'][$name];
	}
	foreach ($this->arr_RFQ['rules'] as $k_r => $v_r)
	{
		if ($this->works[$name]['value'] == '1')
		{
			break ;
		}
		if (!preg_match('/'.$name.'/', $v_r)
			|| ($this->works[$name]['left'] = $this->exprSolution($k_r)) === false
			|| ($this->works[$name]['right_0'] = $this->exprSolution($v_r, $name, '0')) === false
			|| ($this->works[$name]['right_1'] = $this->exprSolution($v_r, $name, '1')) === false)
		{
			continue ;
		}
		$this->analisysCurWork($name);
	}
	if ($this->works[$name]['value'] == '2'
		&& $name != $this->cur_query)
	{
		$this->works[$name]['value'] = '0';
	}
	$this->works[$name]['status'] = true;
}

private function	exprSolution($expr, $name = '', $value = '')
{
	$expr = $this->findMatches($expr, $name, $value);
	if ($expr === false)
	{
		return (false);
	}
	$expr = $this->brackets($expr);
	if (preg_match("/^[!]?[A-Z]$/", $expr))
	{
		if (preg_match("/[A-Z]/", $expr, $match))
		{
			$this->working($match[0]);
			$expr = str_replace($match[0], $this->works[$match[0]]['value'], $expr);
		}
	}
	if (preg_match("/^[!]?[01]$/", $expr))
	{
		$expr = str_replace(['!0', '!1'], ['1', '0'], $expr);
	}
	return ($expr);
}

private function	findMatches($expr, $name, $value)
{
	if (preg_match_all("/[A-Z]/", $expr, $match))
	{
		foreach ($match[0] as $v_match)
		{
			$repl = NULL;
			if ($v_match[0] == $name)
			{
				$repl = $value;
			}
			else if (!empty($this->arr_RFQ['facts'][$v_match[0]]))
			{
				$repl = $this->arr_RFQ['facts'][$v_match[0]];
			}
			else if (!empty($this->works[$v_match[0]]))
			{
				if (!$this->works[$v_match[0]]['status'])
				{
					return (false);
				}
				$repl = $this->works[$v_match[0]]['value'];
			}
			if (isset($repl))
			{
				$expr = str_replace($v_match[0], $repl, $expr);
			}
		}
	}
	return ($expr);
}

private function	brackets($expr)
{
	while (preg_match("/[(][^()]*[)]/", $expr, $match))
	{
		$selectOperation_result = $this->selectOperation(str_replace(['(', ')'], '', $match[0]));
		$expr = str_replace($match[0], $selectOperation_result, $expr);
	}
	return ($this->selectOperation($expr));
}

private function	selectOperation($expr)
{
	while (preg_match("/[\+\|\^]/", $expr))
	{
		if (preg_match("/[!]?.[\+][!]?./", $expr, $match))
		{
			$operations_result = $this->operations($match[0], '+');
		}
		else if (preg_match("/[!]?.[\|][!]?./", $expr, $match))
		{
			$operations_result = $this->operations($match[0], '|');
		}
		else if (preg_match("/[!]?.[\^][!]?./", $expr, $match))
		{
			$operations_result = $this->operations($match[0], '^');
		}
		$expr = str_replace($match[0], $operations_result, $expr);
	}
	return ($expr);
}

private function	operations($expr, $op)
{
	$expr = str_replace(['!0', '!1'], ['1', '0'], $expr);
	foreach($this->tables['t1'][$op] as $k_t1 => $v_t1)
	{
		if ($k_t1 == $expr)
		{
			return ($v_t1);
		}
	}
	foreach($this->tables['t2'][$op] as $k_t2 => $v_t2)
	{
		if (preg_match($k_t2, $expr))
		{
			return ($v_t2);
		}
	}
	preg_match("/[A-Z]/", $expr, $match);
	$this->working($match[0]);
	$expr = str_replace($match[0], $this->works[$match[0]]['value'], $expr);
	return ($this->operations($expr, $op));
}

private function	analisysCurWork($name)
{
	if ($this->works[$name]['left'] == $this->works[$name]['right_1']
		&& $this->works[$name]['left'] != $this->works[$name]['right_0'])
	{
		$this->works[$name]['value'] = '1';
		return ;
	}
	if (($this->works[$name]['left'] == $this->works[$name]['right_1']
		&& $this->works[$name]['left'] == $this->works[$name]['right_0'])
		||
		($this->works[$name]['left'] != $this->works[$name]['right_1']
		&& $this->works[$name]['left'] != $this->works[$name]['right_0']))
	{
		$this->works[$name]['value'] = '2';
	}
}

/////////////////////////////////////////////////////////////////////

	private $arr_RFQ = array(
		'rules' => array(),
		'facts' => array(),
		'queries' => array());
	private $tables = array(
		't1' => array(
			'+' => array
			(
				'1+1' => '1',
				'1+0' => '0',
				'0+1' => '0',
				'0+0' => '0',
			),
			'|' => array
			(
				'1|1' => '1',
				'1|0' => '1',
				'0|1' => '1',
				'0|0' => '0',
			),
			'^' => array
			(
				'1^1' => '0',
				'1^0' => '1',
				'0^1' => '1',
				'0^0' => '0',
			)),
		't2' => array(
			'+' => array
			(
				'/0[\+][!]?[A-Z]/' => '0',
				'/[!]?[A-Z][\+]0/' => '0',
			),
			'|' => array
			(
				'/1[\|][!]?[A-Z]/' => '1',
				'/[!]?[A-Z][\|]1/' => '1',
			),
			'^' => array
			(
			)));
	private $works = array();
	private $cur_query = '';
}
