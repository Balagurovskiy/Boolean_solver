<?php

class	ClassPage
{
	public function		__construct()
	{
		if (!empty($_POST))
		{
			$this->input_rules = $_POST["inputRules"];
			$this->input_facts = $_POST["inputFacts"];
			$this->input_queries = $_POST["inputQueries"];
			$this->stdout = $_POST["consoleOut"];
			$this->stderr = $_POST["consoleErr"];
		}
	}

	public function		submitForm()
	{
		if (!empty($_POST["go_x"]))
		{
			$this->go();
		}
		else if (!empty($_POST["upload_x"]))
		{
			$this->upload();
		}
		else if (!empty($_POST["help_x"]))
		{
			$this->help();
		}
		else
		{
			$this->restart();
		}
	}

	public function		drawPage()
	{
		$this->drawStart();
		$this->drawForm();
		$this->drawFinish();
	}

/////////////////////////////////////////////////////////////////////

	private function	go()
	{
		$Parser = new ClassParser(array("rules" => $_POST["inputRules"],
										"facts" => $_POST["inputFacts"],
										"queries" => $_POST["inputQueries"]));

		if ($Parser->parseInputs())
		{
			$ES = new ClassES($Parser->getArrRFQ());

			$ES->processing();
			$this->stdout = $ES->getResult();
			$this->stderr = 'Done';
		}
		else
		{
			$this->stdout = '';
			$this->stderr = $Parser->getErrors();
		}
	}

	private function	upload()
	{
		if (!empty($_FILES["fileName"]["name"]))
		{
			$Reader = new ClassReader();

			$file_content = $Reader->readingFile($_FILES["fileName"]["tmp_name"]);
			if ($file_content !== false)
			{
				$Parser = new ClassParser(array("content" => $file_content));

				$Parser->parseFile();
				$this->stdout = $Parser->getFileContent();
				$this->stderr = $Parser->getErrors();
				$this->input_rules = $Parser->getInputRules();
				$this->input_facts = $Parser->getInputFacts();
				$this->input_queries = $Parser->getInputQueries();
			}
			else
			{
				$this->stdout = '';
				$this->stderr = "Error: Bad file\n";
			}
		}
		else
		{
			$this->stdout = '';
			$this->stderr = "Error: File name is empty\n";
		}
	}

	private function	help()
	{
		$this->stdout = 'INFORMATION FOR IDIOTS';
		$this->stderr = '';
	}

	private function	restart()
	{
		$this->input_rules = '';
		$this->input_facts = '';
		$this->input_queries = '';
		$this->stdout = '';
		$this->stderr = '';
	}

/////////////////////////////////////////////////////////////////////

	private function	drawStart()
	{
		echo '<!DOCTYPE html>
			<head>
			<meta http-equiv="Content-Type" content="text/html charset=utf-8">
			<title>Expert System</title>
			<link href="./styles/textareas.css" rel="stylesheet" type="text/css">
			<link href="./styles/inputs.css" rel="stylesheet" type="text/css">
			<link href="./styles/buttons.css" rel="stylesheet" type="text/css">
			<link href="./styles/bg_color.css" rel="stylesheet" type="text/css">
			<link href="./styles/page.css" rel="stylesheet" type="text/css">
			
			<script src="./scripts/jquery.min.js"></script>		
			<script src="./scripts/bg_color_on_hover_event.js"></script>		
			<script src="./scripts/hide_items_on_hover.js"></script>
			<script src="./scripts/stuck_bg_position_to_mouse_coords.js"></script>
			</head>
			<body class="cover-bg">';
	}

	private function	drawFinish()
	{
		echo '</body>
			</html>';
	}

	private function	drawForm()
	{
		echo ('
	
	<div class="clearfix">
	<form action="" method="post" enctype="multipart/form-data">
		
		<!-- LEFT -->
		<div class="left top h2-3 border" >			
			<textarea class="h2-3 cover-bg bg-rules" name="inputRules"/>'
			. $this->input_rules
			. '</textarea>
			<input class="in facts" type="text" name="inputFacts" value= '
			. $this->input_facts
			. '   >								
			<input class="in queries" type="text" name="inputQueries" value= '
			. $this->input_queries
			. '  >
		</div>

		<div class="left bottom h1-3 border" >
			<!-- INPUT TYPE="FILE"-->
 
			<input type="file" name="fileName" id="file" class="inputfile" />
			<label for="file" ><img class="bg-black" src="images/select.png" width="50%"height="70%" alt="select" ></label>
			<!--<input type="submit" class="button cube bg-black shadow-hover" name="upload" value="Upload">-->
			<input type="image" name="upload" class=" bg-gray" src="images/up.png" style="margin-left:10%" width="30%"height="70%" alt="upload" >
		</div>
<!-- MIDDLE -->
		<div class="middle top h1 border" >
			<!--<input type="submit" class="button cube top bg-red w30 shadow-hover" name="restart" value="Restart">
			<input type="button" class="button cube top bg-gray w30 shadow-hover" name="help" value="Help">
			<input type="submit" class="button cube bottom bg-green w30 shadow-hover" name="go" value="Go">-->
			<input type="image" name="restart"  class=" bg-red" src="images/notok_sm.png" width="30%"height="20%" alt="Restart" >
			<input type="image" name="help"  class=" bg-blue" src="images/help.png" width="35%"height="20%" alt="Help" >
			<input type="image" name="go" class=" bg-green" src="images/ok_sm.png" width="30%"height="20%" alt="ok" >
			
			<img class="empty hide" src="images/empty.png" alt="empty" >
			<img class="empty ok" src="images/ok.png" alt="ok" >
			<img class="empty notok" src="images/notok.png" alt="nok" >
			<img class="empty up" src="images/upload.png" alt="upload" >
			<img class="empty upload" src="images/up2.png" alt="up" >
			<img class="empty helptext" src="images/help_text.png" alt="help" >
		</div> 
<!-- RIGHT -->		
		<div class="right top h1-2 border" >
			<textarea class="h1 cover-bg bg-out" name="consoleOut"/>'
			. $this->stdout
			. '</textarea>
		</div>

		<div class="right bottom h1-2 border" >
			<textarea class="h1 cover-bg bg-errors" name="consoleErr"/>'
			. $this->stderr
			. '</textarea>  
		</div>
	</form>
	</div>');
	}

/////////////////////////////////////////////////////////////////////

	private $input_rules = '';
	private $input_facts = '';
	private $input_queries = '';
	private $stdout = '';
	private $stderr = '';
}
