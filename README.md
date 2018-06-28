# chinese-poetry-to-mysql-tool

## 简介

把 [chinese-poetry](https://github.com/chinese-poetry/chinese-poetry) 仓库里的json数据转换成 sql 文件的工具



## 环境

- php 5.6 +
- git



## 使用

1、下载本工具

```shell
git clone https://github.com/woodylan/chinese-poetry-to-mysql-tool.git
```



2、在本工具目录下，下载 [chinese-poetry](https://github.com/chinese-poetry/chinese-poetry) 仓库

~~~shell
cd chinese-poetry-to-mysql-tool
git clone https://github.com/chinese-poetry/chinese-poetry.git
~~~

3、安装composer包依赖

~~~shell
composer install
~~~

4、开始编译成 `sql` 文件

```shell
php work.php
```

