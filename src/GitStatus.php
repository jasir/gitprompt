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
		$lines        = explode("\n", trim($status));
		$this->parsed = [];
		foreach ($lines as $line) {
			$info                           = explode(' ', trim($line), 2);
			$type                           = trim($info[0]);
			$this->parsed[$type]['lines'][] = trim($info[1]);
		}

		$regs = [];
		$line = $lines[0];
		if (preg_match('%## Initial commit on (?P<branch>[a-z0-9\/\-_\.]+)%i', $line, $regs)) {
			$this->branch = $regs['branch'] ?? 'no branch';
		} else {
			$line = str_replace('...', '~~~', $line);
			if (preg_match('%##\s(?P<branch>[a-z0-9\/\-_\.]+)(\~\~\~(?P<remoteBranch>[0-9a-z_\-/\.]+)){0,1}(\s\[(?P<ahead>[a-zA-Z0-9 ]+)\]){0,1}%i', $line, $regs)) {
				$this->branch       = $regs['branch'] ?? 'no branch';
				$this->remoteBranch = $regs['remoteBranch'] ?? false;
				$this->ahead        = $regs['ahead'] ?? '';
			}
		}
		$this->gitDir = $this->determineGitDir(realpath(getcwd()));
//		echo ">>>>" . $this->gitDir . "<<<<";
	}


	/**
	 * @return bool
	 */
	public function isGitRepository()
	{
		return $this->isGitRepository;
	}


	/**
	 * @return bool
	 */
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


	/**
	 * @return int
	 */
	public function getDeletedCount()
	{
		return $this->getCount('D');
	}


	/**
	 * @return int
	 */
	public function getAddedCount()
	{
		return $this->getCount('A');
	}


	/**
	 * @return int
	 */
	public function getModifiedCount()
	{
		return $this->getCount('M');
	}


	/**
	 * @return int
	 */
	public function getUntrackedCount()
	{
		return $this->getCount('??');
	}


	/**
	 * @return int
	 */
	public function getRenamedCount()
	{
		return $this->getCount('R');
	}


	/**
	 * @return mixed|string
	 */
	public function getAheadStatus()
	{
		return $this->ahead;
	}


	/**
	 * @return mixed|string
	 */
	public function getBranch()
	{
		return $this->branch;
	}


	/**
	 * @return bool|mixed
	 */
	public function getRemoteBranch()
	{
		return $this->remoteBranch;
	}


	/**
	 * @return bool
	 */
	public function hasRemoteBranch(): bool
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


	/**
	 * @return bool
	 */
	public function isInRebase(): bool
	{
		return file_exists($this->gitDir . '/rebase-merge/done');
	}


	/**
	 * @return bool|string
	 */
	public function getCurrentRebaseMessage()
	{
		return trim(file_get_contents($this->gitDir . '/rebase-merge/message'));
	}


	/**
	 * @return bool|string
	 */
	public function getCurrentRebaseHead()
	{
		return trim(file_get_contents($this->gitDir . '/rebase-merge/head-name'));
	}


	/**
	 * @return string
	 */
	public function getRebaseStep(): string
	{
		return trim(file_get_contents($this->gitDir . '/rebase-merge/msgnum'));
	}

	/**
	 * @return string
	 */
	public function getRebaseSteps(): string
	{
		return trim(file_get_contents($this->gitDir . '/rebase-merge/end'));
	}



	/**
	 * @param $fromDir
	 * @return false|string
	 * @internal
	 */
	public function determineGitDir($fromDir)
	{

		if (!$this->isGitRepository()) {
			return false;
		}

		$candidate = $fromDir;

		while (!is_dir($candidate . '/.git')) {
			$prev      = $candidate;
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


	/**
	 * @param $type
	 * @return int
	 */
	private function getCount($type): int
	{
		return count($this->parsed[$type]['lines'] ?? []);
	}
}
