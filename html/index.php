<?php

include_once "init.inc";

$Page = new ClassPage();

if (!empty($_POST))
{
	$Page->submitForm();
}
$Page->drawPage();
