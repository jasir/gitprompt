<?php
require_once __DIR__ . '/vendor/autoload.php';

use \Bramus\Ansi\Ansi;
use \Bramus\Ansi\ControlSequences\EscapeSequences\Enums\SGR;

// Create Ansi Instance
$ansi = new Ansi();
$ansi->text(' ');

// Output some styled text on screen, along with a Line Feed and a Bell
/*$ansi->color(array(SGR::COLOR_FG_WHITE, SGR::COLOR_BG_RED))
	->text('I will be blinking red on a wite background.')
     ->nostyle()
     ->text(' And I will be normally styled.')
     ->lf()
     ->text('Ooh, a bell is coming ...')
     ->bell();*/

$status = file_get_contents('php://stdin');

$lines= explode("\n", trim($status));
if (count($lines) === 0) {
	$colors->error($ansi)->text('NO GIT');
	exit();
}

$parsed = [];
foreach ($lines as $line) {
	$info = explode(' ', trim($line), 2);
	$parsed[$info[0]]['lines'][] = $info[1];
}

$line = $lines[0];
$regs = [];

if (preg_match('%##\s(?P<branch>[a-z/]+)(...(?P<remoteBranch>[a-z/]+)){0,1}(\s\[(?P<ahead>[a-zA-Z0-9 ]+)\]){0,1}%i', $line, $regs)) {
	;
}


$status = [
	'deleted' => count($parsed['D']['lines']),
	'deletedFiles' => count($parsed['D']['lines']) > 0 ? implode(', ', $parsed['D']['lines']) : [],

	'added' => count($parsed['A']['lines']),
	'addedFiles' => count($parsed['A']['lines']) > 0 ? implode(', ', $parsed['A']['lines']) : [],

	'modified' => count($parsed['M']['lines']),
	'modifiedFiles' => count($parsed['M']['lines']) > 0 ? implode(', ', $parsed['M']['lines']) : [],

	'untracked' => count($parsed['??']['lines']),
	'untrackedFiles' => count($parsed['??']['lines']) > 0 ? implode(', ', $parsed['??']['lines']) : [],

	'branch' => $regs['branch'] ?? 'no branch',
	'remoteBranch' => $regs['remoteBranch'] ?? 'no remote',
	'ahead' => $regs['ahead'] ?? ''
];




$ansi->color([SGR::COLOR_BG_BLACK, SGR::COLOR_FG_GREEN])
	->text("($status[branch])")
	->reset();

if ($status['ahead']) {
	$ansi->text(' [')
		->text($status['ahead'])
		->text(']')
		->reset();
}

$ansi->text(' ');


if ($status['modified'] > 0) {
	$ansi->color([SGR::COLOR_BG_RESET, SGR::COLOR_FG_YELLOW])->text("*{$status[modified]}"); // ({$status[modifiedFiles]})");
}

if ($status['added'] > 0) {
	$ansi->color([SGR::COLOR_BG_RESET, SGR::COLOR_FG_GREEN])->text("+{$status[added]}");// ($status[addedFiles])");
}

if ($status['deleted'] > 0) {
	$ansi->color([SGR::COLOR_BG_RESET, SGR::COLOR_FG_RED])->text("-{$status[deleted]}");// ($status[deletedFiles])");
}

if ($status['untracked'] > 0) {
	$ansi->color([SGR::COLOR_BG_RESET, SGR::COLOR_FG_CYAN])->text("?{$status[untracked]}"); // ($status[untracked])");
}


//print_r($status);
