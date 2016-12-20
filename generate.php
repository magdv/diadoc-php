<?php

declare(strict_types=1);

$pattern = __DIR__.'/proto/*.proto';

echo 'Строим список proto файлов'.PHP_EOL;
$files = rglob($pattern, 0);

echo 'Начинаем генерацию'.PHP_EOL;
foreach($files as $file) {
    echo 'Обработка файла - '.$file.PHP_EOL;
    exec('protoc --proto_path=proto --php_out=phpProto '.str_replace(__DIR__.DIRECTORY_SEPARATOR, '', $file), $output);
}

echo 'Запуск composer dump-autoload'.PHP_EOL;
exec('composer dump-autoload', $funcOutput);
foreach ($funcOutput as $item) {
    echo $item.PHP_EOL;
}

function rglob($pattern, $flags = 0)
{
    $files = glob($pattern, $flags);
    foreach (glob(dirname($pattern) . '/*', GLOB_ONLYDIR | GLOB_NOSORT) as $dir) {
        $files = array_merge($files, rglob($dir . '/' . basename($pattern), $flags));
    }
    return $files;
}