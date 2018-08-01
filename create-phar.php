#!/usr/bin/env
<?php

$buildRoot = __DIR__;
$pharFile =$buildRoot . '/build/csv-to-audio.phar';

if (file_exists($pharFile)) {
    unlink($pharFile);
}

$phar = new Phar($pharFile, Phar::NONE, 'csv-to-audio.phar');
$phar->buildFromDirectory($buildRoot, '/^(?=(.*src|.*bin|.*vendor))(.*)php$/i');
$phar->setStub("#!/usr/bin/env php\n" . Phar::createDefaultStub('bin/application.php'));
