<?php

require_once 'src/Converter.php';

//是否开启过滤
$ifFilter = true;

$dirPath = dirname(__FILE__);
$sourceFilePath = $dirPath . '/chinese-poetry/json/';
$sqlFileName = '/chinese-poetry.sql';
$sqlPath = $dirPath . $sqlFileName;

//判断古诗词仓库是否存在
$isPathExist = file_exists($sourceFilePath);
if ($isPathExist == false) {
    die('古诗词仓库不存在，请按说明下载');
}

//唐诗json文件的路径
$tangFilePathList = glob("{$sourceFilePath}poet.tang.*.json");

if (empty($tangFilePathList)) {
    die('路径不存在');
}

file_put_contents($sqlPath, "INSERT INTO `tb_poems` (`id`, `title`, `content`, `author`) VALUES \r\n");

$id = 0;
$converter = new Converter();
foreach ($tangFilePathList as $filePath) {
    $fileContent = file_get_contents($filePath);

    $fileContentArray = json_decode($fileContent, true);

    $content = '';
    foreach ($fileContentArray as $value) {
        $id++;

        //过滤
        if ($ifFilter) {
            $isAllow = filter($value['paragraphs']);
            if ($isAllow == false) {
                continue;
            }
        }

        $paragraphs = implode($value['paragraphs'], '\n');
        $paragraphs = $converter->turn($paragraphs);
        $paragraphsJson = json_encode(explode('\n', $paragraphs));

        //给上一行加入逗号
        if ($id > 1) {
            $content .= ",\r\n";
        }

        $content .= "($id,\"{$value['title']}\",\"{$paragraphsJson}\",\"{$value['author']}\")";
    }

    $handle = fopen($sqlPath, 'a+');
    fwrite($handle, $content);
    fclose($handle);
}

//最后一行添加分号
$handle = fopen($sqlPath, 'a+');
fwrite($handle, ';');
fclose($handle);


//过滤脚本
function filter($paragraphs, $sentenceLength = 2, $charLength = 16)
{
    if (count($paragraphs) > $sentenceLength) {
        return false;
    }

    //判断每句是否长短一样
    foreach ($paragraphs as $key => $value) {
        $length = strlen($value);
        if ($key >= 1) {
            //判断跟上一个元素长度是否相等
            if (strlen($paragraphs[$key - 1]) != $length) {
                return false;
            }
        }

        if ($length > $charLength * 3) {
            return false;
        }
    }

    return true;
}