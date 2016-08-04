<?php

class GitStatus
{
	private $parsed;

	private $isGitRepository = true;

	private $branch;

	private $remoteBranch;

	private $ahead;

	private $gitDir;


	public function __construct($status)
	{
		if ($status === '') {
			$this->isGitRepository = false;
			return;
		}
		$lines = explode("\n", trim($status));
		$this->parsed = [];
		foreach ($lines as $line) {
			$info = explode(' ', trim($line), 2);
			$type = trim($info[0]);
			$this->parsed[$type]['lines'][] = trim($info[1]);
		}

		$regs = [];
		$line = $lines[0];
		if (preg_match('%## Initial commit on (?P<branch>[a-z0-9\/\-_\.]+)%i', $line, $regs)) {
			$this->branch = $regs['branch'] ?? 'no branch';
		} else {
			$line = str_replace('...', '~~~', $line);
			if (preg_match('%##\s(?P<branch>[a-z0-9\/\-_\.]+)(\~\~\~(?P<remoteBranch>[0-9a-z_\-/\.]+)){0,1}(\s\[(?P<ahead>[a-zA-Z0-9 ]+)\]){0,1}%i', $line, $regs)) {
				$this->branch = $regs['branch'] ?? 'no branch';
				$this->remoteBranch = $regs['remoteBranch'] ?? false;
				$this->ahead = $regs['ahead'] ?? '';
			}
		}
		$this->gitDir = $this->determineGitDir(getcwd());
	}


	public function determineGitDir($fromDir)
	{

		if (!$this->isGitRepository()) {
			return false;
		}

		$candidate = $fromDir;

		while (!is_dir($candidate . '/.git')) {
			$prev = $candidate;
			$candidate = dirname($candidate, 1);
			if ($candidate === $prev) {
				$candidate = false;
				break;
			}
		}

		if ($candidate) {
			return $candidate . '/.git';
		}
		return false;
	}

	public function isGitRepository()
	{
		return $this->isGitRepository;
	}

	public function isClean()
	{
		return $this->getTotalCount() === 0;
	}

	/**
	 * @return int
	 */
	public function getTotalCount()
	{
		return $this->getDeletedCount() + $this->getAddedCount() + $this->getModifiedCount() + $this->getUntrackedCount() + $this->getRenamedCount();
	}

	public function getDeletedCount()
	{
		return $this->getCount('D');
	}

	private function getCount($type)
	{
		return count($this->parsed[$type]['lines'] ?? []);
	}

	public function getAddedCount()
	{
		return $this->getCount('A');
	}

	public function getModifiedCount()
	{
		return $this->getCount('M');
	}

	public function getUntrackedCount()
	{
		return $this->getCount('??');
	}

	public function getRenamedCount()
	{
		return $this->getCount('R');
	}

	public function getAheadStatus()
	{
		return $this->ahead;
	}

	public function getBranch()
	{
		return $this->branch;
	}

	public function getRemoteBranch()
	{
		return $this->remoteBranch;
	}

	public function hasRemoteBranch()
	{
		return !empty($this->remoteBranch);
	}

	/**
	 * @return bool|string
	 */
	public function getGitDir()
	{
		return $this->gitDir;
	}
}
