<?php


class Converter
{
    protected $dictName;

    public function __construct()
    {
        $this->dictName = require 'FontDict.php';
    }

    public function turn($str)
    {
        $str = trim($str);
        $length = strlen($str);

        $retStr = '';
        for ($i = 0; $i < $length; $i++) {
            //如果超过127则在UTF-8中是一个多字节字符
            if (ord(substr($str, $i, 1)) > 127) {
                $char = substr($str, $i, 3);

                $i += 2;
            } else {
                $char = substr($str, $i, 1);
            }

            $retStr .= isset($this->dictName[$char]) ? $this->dictName[$char] : $char;
        }

        return $retStr;
    }
}