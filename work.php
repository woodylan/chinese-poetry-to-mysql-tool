<?php

$dirPath = dirname(__FILE__);

$sourceFilePath = $dirPath . 'chinese-poetry/json/';

//判断古诗词仓库是否存在
$isPathExist = file_exists($sourceFilePath);
if ($isPathExist == false) {
    die('古诗词仓库不存在，请按说明下载');
}

$sqlFileName = 'chinese-poetry.sql';

$sqlPath = $dirPath . $sqlFileName;

//唐诗json文件的路径
$tangFilePathList = glob("{$sourceFilePath}poet.tang.*.json");

if (empty($tangFilePathList)) {
    die('路径不存在');
}

file_put_contents($sqlPath, "INSERT INTO `tb_poems` (`id`, `title`, `content`, `author`) VALUES \r\n");

$id = 0;
foreach ($tangFilePathList as $filePath) {
    $fileContent = file_get_contents($filePath);

    $fileContentArray = json_decode($fileContent, true);

    $content = '';
    foreach ($fileContentArray as $value) {
        $id++;
        $paragraphs = implode($value['paragraphs'], '\n');

        //给上一行加入逗号
        if ($id > 1) {
            $content .= ",\r\n";
        }

        $content .= "($id,\"{$value['title']}\",\"{$paragraphs}\",\"{$value['author']}\")";
    }

    $handle = fopen($sqlPath, 'a+');
    fwrite($handle, $content);
    fclose($handle);
}

//最后一行添加分号
$handle = fopen($sqlPath, 'a+');
fwrite($handle, ';');
fclose($handle);