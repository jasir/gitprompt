<?php

class GitStatus
{
	private $parsed;

	private $isGitRepository = true;

	private $branch;

	private $remoteBranch;

	private $ahead;


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
		if (preg_match('%## Initial commit on (?P<branch>[a-z/]+)%i', $line, $regs)) {
			$this->branch = $regs['branch'] ?? 'no branch';
		} elseif (preg_match('%##\s(?P<branch>[a-z/]+)(\.\.\.(?P<remoteBranch>[a-z/]+)){0,1}(\s\[(?P<ahead>[a-zA-Z0-9 ]+)\]){0,1}%i', $line, $regs)) {
			$this->branch = $regs['branch'] ?? 'no branch';
			$this->remoteBranch = $regs['remoteBranch'] ?? false;
			$this->ahead = $regs['ahead'] ?? '';
		}
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
}
