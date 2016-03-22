<?php

include __DIR__ . '/../../src/GitStatus.php';

class GitStatusTest extends PHPUnit_Framework_TestCase
{


	public function test_normal_status_no_remote_with_changes()
	{
		$status = new GitStatus(<<<EOF
## master
 M composer.json
 M composer.lock
 M gitprompt.php
A  hello.txt
R  GitStatus.php -> src/GitStatus.php
AM tests/tests/GitStatusTest.php
?? tests/initialized.txt
?? tests/normal.txt
EOF
		);
		static::assertTrue($status->isGitRepository());
		static::assertEquals(2, $status->getUntrackedCount());
		static::assertEquals(1, $status->getAddedCount());
	}


	public function test_freshly_initialized()
	{
		$status = new GitStatus(<<<EOF
## Initial commit on master	
EOF
		);

		static::assertTrue($status->isGitRepository());
		static::assertEquals(0, $status->getUntrackedCount());
		static::assertEquals(null, $status->getRemoteBranch());

	}
}
