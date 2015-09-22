<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/GitStatus.php';

use RXX\Colors\Colors;

$ansi = new Colors();

$printToStr = function ($msg) use ($ansi) {
	return $ansi->cprint($msg, false, false, true);
};


echo " ";

$status = new GitStatus(file_get_contents('php://stdin'));

if ($status->isGitRepository() === false) {
	echo $printToStr("%yb;no git%x;");
	exit();
}

echo $printToStr("%G;({$status->getBranch()}%x;");
if ($status->hasRemoteBranch()) {
	echo $printToStr("%g;..{$status->getRemoteBranch()})%x;, %m;{$status->getAheadStatus()}%x;");
} else {
	echo ")";
}



$counts = [];

if ($status->getModifiedCount() > 0) {
	$counts[] = $printToStr("%y;*{$status->getModifiedCount()}");
}

if ($status->getAddedCount() > 0) {
	$counts[] = $printToStr("%g;+{$status->getAddedCount()}");
}

if ($status->getDeletedCount() > 0) {
	$counts[] = $printToStr("%r;-{$status->getDeletedCount()}");
}

if ($status->getUntrackedCount() > 0) {
	$counts[] = $printToStr("%c;?{$status->getUntrackedCount()}");

}

if (count($counts) > 0) {
	echo  ' [' . implode(' ', $counts) .  $printToStr("%x;]");
}




//print_r($status);
