<?php

$dirPath = dirname(__FILE__);

$sourceFilePath = $dirPath . 'chinese-poetry/json/';

//判断古诗词仓库是否存在
$isPathExist = file_exists($sourceFilePath);
if ($isPathExist == false) {
    gitClone($dirPath);
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
}

//最后一行添加分号
$handle = fopen($sqlPath, 'a+');
fwrite($handle, ';');


function gitClone($path)
{
    echo "检查git是否存在\n";

    list($status, $log) = runLocalCommand('git --version');
    if ($status == false) {
        echo "git 不存在，请安装\n";
        die($log);
    }

    $cmd = [
        sprintf('cd %s', $path),
        'git clone https://github.com/chinese-poetry/chinese-poetry.git'

    ];

    echo "下载古诗词仓库\n";
    $command = join(' && ', $cmd);
    list($status, $log) = runLocalCommand($command);

    if ($status == false) {
        echo "git 不存在，请安装\n";
        die($log);
    }
}

function runLocalCommand($command)
{
    exec(trim($command) . ' 2>&1', $log, $statusNum);
    $log = implode($log, "\r\n");

    $status = true;
    if ($statusNum != 0) {
        $status = false;
    }

    return [$status, $log];
}