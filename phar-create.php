<?php

include_once "src/AryToNeX/MPRISLyrics/Versioning.php";
$srcRoot = "src/";
$buildRoot = "build/";
$fileName = "MPRISLyrics-" . \AryToNeX\MPRISLyrics\Versioning::getVersion() . ".phar";
$phar = new Phar($buildRoot . $fileName, FilesystemIterator::CURRENT_AS_FILEINFO|FilesystemIterator::KEY_AS_FILENAME, $fileName);
$phar->buildFromDirectory($srcRoot,'/.php$/');
$phar->setStub($phar->createDefaultStub("AryToNeX/MPRISLyrics/Loader.php"));

echo "Phar created.\n";