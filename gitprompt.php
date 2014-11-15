<?php

class GitStatus
{

	private $output;

	public function __construct()
	{
		$this->output = shell_exec('git status -u -b --porcelain');
		echo $this->output;
	}



}

$status = new GitStatus();


