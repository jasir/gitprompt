<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/src/GitStatus.php';

use RXX\Colors\Colors;

$ansi = new Colors();

$printToStr = function ($msg, $autoreset = false) use ($ansi) {
	return $ansi::cprint($msg, false, $autoreset, true);
};

echo ' ';

$input = file_get_contents('php://stdin');
//echo $input;
$status = new GitStatus($input);

if ($status->isGitRepository() === false) {
	echo $printToStr('%r;<not under git> %x;');
	exit();
}

echo $printToStr("%B;({$status->getBranch()}");

/** Remote branch develop..develop, ahead 1*/

if ($status->hasRemoteBranch()) {
	echo $printToStr("%b;..{$status->getRemoteBranch()})");
	$ahead = $status->getAheadStatus();
	if (!empty($ahead)) {
		$color = Colors::color(Colors::MAGENTA, true);
		echo $printToStr(", {$color}{$status->getAheadStatus()}");
	}

} else {
	echo $printToStr(')%x;');
}

/** Changes  */

$totalChanges = $status->getTotalCount();

if ($totalChanges > 0) {
	echo $printToStr(", changed %Y;{$totalChanges}%x; " . ($totalChanges === 1 ? 'file' : 'files') . ':');
} else {
	$color = Colors::color(Colors::BLACK, true);
	echo $printToStr("{$color} ✓ clean%x;");
}

$counts = [];

if ($status->getModifiedCount() > 0) {
	$counts[] = $printToStr("%B;*{$status->getModifiedCount()} modified");
}

if ($status->getAddedCount() > 0) {
	$counts[] = $printToStr("%G;+{$status->getAddedCount()} added");
}

if ($status->getDeletedCount() > 0) {
	$counts[] = $printToStr("%R;-{$status->getDeletedCount()} deleted");
}

if ($status->getUntrackedCount() > 0) {
	$counts[] = $printToStr("%c;?{$status->getUntrackedCount()} untracked");

}

if (count($counts) > 0) {
	echo ' [' . implode(' ', $counts) . $printToStr('%x;]');
	//echo $printToStr("%R;hello%r;", true);
}

if ($status->isInRebase()) {
	echo $printToStr(" %Wr; REBASE {$status->getRebaseStep()}/{$status->getRebaseSteps()} %G; {$status->getCurrentRebaseHead()} on %b'{$status->getCurrentRebaseMessage()}'", true);
}


