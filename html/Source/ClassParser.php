<?php

class	ClassParser
{
	public function		__construct($arr)
	{
		if (count($arr) == 1)
		{
			$this->file_content = $arr['content'];
		}
		else
		{
			$this->input_rules = $arr['rules'];
			$this->input_facts = $arr['facts'];
			$this->input_queries = $arr['queries'];
		}
	}

/////////////////////////////////////////////////////////////////////

	public function		parseFile()
	{
		$content_array = explode("\n", $this->strDelSpaces($this->file_content)); // PHP_EOL
		foreach ($content_array as $k => $v)
		{
			$v_no_com = explode('#', $v);
			if ($v_no_com[0] == "")
			{
				continue ;
			}
			else if (preg_match("/^[\?][A-Z]*$/", $v_no_com[0]))
			{
				$this->addInputQueries($k, $v_no_com[0]);
			}
			else if (preg_match('/^[\=][A-Z]*$/', $v_no_com[0]))
			{
				$this->addInputFacts($k, $v_no_com[0]);
			}
			else if ($this->pregMatchRule($v_no_com[0]))
			{
				$this->addInputRules($k, $v_no_com[0]);
			}
			else
			{
				$this->errors .= "Error: Syntax error, line " . ($k + 1) . "\n";
			}
		}
		if (!empty($this->errors))
		{
			$this->eraseInputs();
		}
		else
		{
			$this->errors = "File is upload done\n";
		}
	}

	public function		parseInputs()
	{
		$this->parseInputRules();
		$this->parseInputFacts();
		$this->parseInputQueries();
		if (!empty($this->errors))
		{
			return (false);
		}
		return (true);
	}

/////////////////////////////////////////////////////////////////////

	public function		getFileContent()
	{
		return ($this->file_content);
	}

	public function		getInputRules()
	{
		return ($this->input_rules);
	}

	public function		getInputFacts()
	{
		return ($this->input_facts);
	}

	public function		getInputQueries()
	{
		return ($this->input_queries);
	}

	public function		getErrors()
	{
		return ($this->errors);
	}

	public function		getArrRFQ()
	{
		return ($this->arr_RFQ);
	}

/////////////////////////////////////////////////////////////////////

	private function	strDelSpaces($str)
	{
		$str = str_replace(" ", '', $str);
		$str = str_replace("\t", '', $str);
		$str = str_replace("\r", '', $str);
		return ($str);
	}

	private function	pregMatchRule($str)
	{
		while (preg_match("/[(][!]?[A-Z]([\+\|\^][!]?[A-Z])*[)]/", $str, $match))
		{
			$str = str_replace($match[0], 'A', $str);
		}
		if (preg_match("/^[!]?[A-Z]([\+\|\^][!]?[A-Z])*[\<]?[\=][\>][!]?[A-Z]([\+\|\^][!]?[A-Z])*$/", $str))
		{
			return (true);
		}
		return false;
	}

	private function	eraseInputs()
	{
		$this->input_rules = '';
		$this->input_facts = '';
		$this->input_queries = '';
	}

/////////////////////////////////////////////////////////////////////

	private function		parseInputRules()
	{
		$content_array = explode("\n", $this->strDelSpaces($this->input_rules));
		foreach ($content_array as $k => $v)
		{
			$v_no_com = explode('#', $v);
			if ($v_no_com[0] == "")
			{
				continue ;
			}
			else if ($this->pregMatchRule($v_no_com[0]))
			{
				$this->addArrRules($v_no_com[0]);
			}
			else
			{
				$this->errors .= "Error: Syntax error in rules, line " . ($k + 1) . "\n";
			}
		}
	}

	private function		parseInputFacts()
	{
		$v_no_com = explode('#', $this->input_facts);
		if ($v_no_com[0] == "")
		{
			return ;
		}
		//if (preg_match('/^[\=][A-Z]*$/', $v_no_com[0]))
		if (preg_match('/^[A-Z]*$/', $v_no_com[0]))
		{
			$this->addArrFacts($v_no_com[0]);
		}
		else
		{
			$this->errors .= "Error: Syntax error in facts\n";
		}
	}

	private function		parseInputQueries()
	{
		$v_no_com = explode('#', $this->input_queries);
		if ($v_no_com[0] == "")
		{
			$this->errors .= "Error: Empty queries\n";
			return ;
		}
		//if (preg_match("/^[\?][A-Z]*$/", $v_no_com[0]))
		if (preg_match("/^[A-Z]*$/", $v_no_com[0]))
		{
			$this->addArrQueries($v_no_com[0]);
		}
		else
		{
			$this->errors .= "Error: Syntax error in queries\n";
		}
	}

/////////////////////////////////////////////////////////////////////

	private function	addInputRules($k, $v)
	{
		if (!empty($this->input_queries))
		{
			$this->errors .= "Error: Rule after queries, line " . ($k + 1) . "\n";
			return ;
		}
		if (!empty($this->input_facts))
		{
			$this->errors .= "Error: Rule after facts, line " . ($k + 1) . "\n";
			return ;
		}
		$this->input_rules .= $v . "\n";
	}

	private function	addInputFacts($k, $v)
	{
		if (!empty($this->input_queries))
		{
			$this->errors .= "Error: Facts after queries, line " . ($k + 1) . "\n";
			return ;
		}
		if (!empty($this->input_facts))
		{
			$this->errors .= "Error: Can not be more than one facts, line " . ($k + 1) . "\n";
			return ;
		}
		$this->input_facts = substr($v, 1);
	}

	private function	addInputQueries($k, $v)
	{
		if (!empty($this->input_queries))
		{
			$this->errors .= "Error: Can not be more than one queries, line " . ($k + 1) . "\n";
			return ;
		}
		if ($v == '?')
		{
			$this->errors .= "Error: Empty queries, line " . ($k + 1) . "\n";
			return ;
		}
		$this->input_queries = substr($v, 1);
	}

	private function	addArrRules($v)
	{
		if (preg_match('/[\<][\=][\>]/', $v))
		{
			$arr_v = explode('<=>', $v);
			$this->arr_RFQ['rules'][$arr_v[0]] = $arr_v[1];
			$this->arr_RFQ['rules'][$arr_v[1]] = $arr_v[0];
		}
		else
		{
			$arr_v = explode('=>', $v);
			$this->arr_RFQ['rules'][$arr_v[0]] = $arr_v[1];
		}
	}

	private function	addArrFacts($v)
	{
		$arr_v = str_split($v);
		foreach ($arr_v as $c)
		{
			$this->arr_RFQ['facts'][$c] = '1';
		}
	}

	private function	addArrQueries($v)
	{
		$arr_v = str_split($v);
		foreach ($arr_v as $c)
		{
			$this->arr_RFQ['queries'][$c] = '';
		}
	}

/////////////////////////////////////////////////////////////////////

	private $file_content = '';
	private $input_rules = '';
	private $input_facts = '';
	private $input_queries = '';
	private $errors = '';
	private $arr_RFQ = array(
		'rules' => array(),
		'facts' => array(),
		'queries' => array());
}
