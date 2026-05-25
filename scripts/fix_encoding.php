<?php

$dirs = ['app', 'resources', 'database', 'routes'];

$replacements = [
    'Ã©' => 'é',
    'Ã¨' => 'è',
    'Ãª' => 'ê',
    'Ã«' => 'ë',
    'Ã ' => 'à',
    'Ã¢' => 'â',
    'Ã´' => 'ô',
    'Ã®' => 'î',
    'Ã¯' => 'ï',
    'Ã»' => 'û',
    'Ã¹' => 'ù',
    'Ã§' => 'ç',
    'Ã‰' => 'É',
    'Ãˆ' => 'È',
    'Ã€' => 'À',
];

$changedFiles = 0;

foreach ($dirs as $dir) {
    if (!is_dir(__DIR__ . '/../' . $dir)) continue;
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__DIR__ . '/../' . $dir));
    foreach ($iterator as $file) {
        if ($file->isFile() && in_array($file->getExtension(), ['php'])) {
            $content = file_get_contents($file->getPathname());
            $originalContent = $content;
            
            $content = str_replace(array_keys($replacements), array_values($replacements), $content);
            
            if ($content !== $originalContent) {
                file_put_contents($file->getPathname(), $content);
                echo "Fixed encoding in: " . $file->getPathname() . "\n";
                $changedFiles++;
            }
        }
    }
}

echo "Total files fixed: $changedFiles\n";
