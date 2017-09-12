<?php

include __DIR__ . '/../../src/GitStatus.php';

class GitStatusTest extends PHPUnit_Framework_TestCase
{


	public function test_normal_status_no_remote_with_changes()
	{
		$status = $this->createExampleGitStatus();
		static::assertTrue($status->isGitRepository());
		static::assertEquals(2, $status->getUntrackedCount());
		static::assertEquals(1, $status->getAddedCount());
		static::assertEquals('master-it_is', $status->getBranch());
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


	public function test_upstreamGone()
	{
		$status = new GitStatus(<<<EOF
On branch feature/sklad
Your branch is based on 'origin/feature/sklad', but the upstream is gone.
nothing to commit, working tree clean
EOF
		);
		static::assertEquals(null, $status->getRemoteBranch());
	}


	public function test_determineGitDir()
	{
		$status = $this->createExampleGitStatus();
		static::assertEquals(
			'c:\work\projects\bashsettings\gitprompt/.git',
			strtolower($status->determineGitDir(getcwd())));
		static::assertFalse($status->determineGitDir('c:/'));
	}


	/**
	 * @return GitStatus
	 */
	public function createExampleGitStatus()
	{
		return new GitStatus(<<<EOF
## master-it_is
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
	}


}
