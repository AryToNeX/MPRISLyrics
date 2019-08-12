<?php

include_once "src/AryToNeX/MPRISLyrics/Versioning.php";
$srcRoot = "src/";
$buildRoot = "build/";
@mkdir("build/");

$fileName = "MPRISLyrics-" . \AryToNeX\MPRISLyrics\Versioning::getVersion() . ".phar";
if(is_file($buildRoot . $fileName)) unlink($buildRoot . $fileName);
$phar = new Phar($buildRoot . $fileName, null, $fileName);
$phar->buildFromIterator(
	new RecursiveIteratorIterator(
		new RecursiveDirectoryIterator(
			"src/AryToNeX/MPRISLyrics",
			RecursiveDirectoryIterator::SKIP_DOTS
		)
	),
	"src/"
);
$phar->setStub($phar->createDefaultStub("AryToNeX/MPRISLyrics/Loader.php"));

echo "Phar created.\n";
