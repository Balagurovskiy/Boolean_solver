<?php

class	ClassReader
{
	public function		readingFile($file_name)
	{
		@$file_content = file_get_contents($file_name);
		return ($file_content);
	}
}
