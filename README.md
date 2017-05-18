[![Build Status](https://travis-ci.org/jonny-bo/zstu_xyb.svg?branch=develop)](https://travis-ci.org/jonny-bo/zstu_xyb)
[![Coverage Status](https://coveralls.io/repos/github/jonny-bo/zstu_xyb/badge.svg?branch=develop)](https://coveralls.io/github/jonny-bo/zstu_xyb?branch=develop)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/jonny-bo/zstu_xyb/badges/quality-score.png?b=develop)](https://scrutinizer-ci.com/g/g/jonny-bo/zstu_xyb/?branch=develop)
# 编码规范

为了提升团队开发效率，方便团队成员快速上手，制定此编码规范。本文档会不定期更新，所有开发成员均有义务参与本文档的编辑与完善。
内容涉及开发相关的技术规范、最佳实践、架构设计、团队协作等方面。

**为什么要有编码规范？**

编码规范对于程序员而言尤为重要，有以下几个原因:

  * 一个软件的生命周期中，80%的时间花费在维护
  * 几乎没有任何一个软件，在其整个生命周期中，均由最初的开发人员来维护
  * 编码规范可以改善软件的可读性，可以让程序员尽快而彻底地理解新的代码
  * 如果你将源码作为产品发布，就需要确任它是否被很好的打包并且清晰无误.

为了保证开发过程中各成员的程序结构和开发框架的一致性及代码的可维护性，作为项目开发过程中相关人员的工作基础和依据，并作为项目质量评估的标准。


## 开发规范

* [Git 使用规范](coding-standards/git.md)
* [MySQL开发规范](coding-standards/mysql.md)
* [REST API编写规范](coding-standards/rest-api.md)
* [PHP规范GITHUB中文版](https://github.com/PizzaLiu/PHP-FIG)
* [Android规范](coding-standards/android.md)



## PHP初始化

  * 安装依赖

    ```
    composer install
    ```

  * 初始化数据库

    创建数据库：
    ```
    CREATE DATABASE `zstu_xyb` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
    ```

    创建数据库表：
    ```
    bin/phpmig migrate
    ```

    示例程序会初始化`user`表。phpmig的使用见[文档](https://github.com/codeages/biz-framework-doc/blob/master/migration.md)。

  * 初始化应用

    ```
    app/console app:init
    ```

    此命令会创建一个用户名为`admin`，密码为`kaifazhe`的，角色为`超级管理员`的用户。


## 本地运行（需PHP>=5.4.0）

```
app/console server:run
```

## 前端初始化（需安装nodejs环境）

  * 依赖安装

    ```
    npm install
    ```

  * 开发模式

    ```
    npm run dev
    ```

## 单元测试

  * 执行所有单元测试

    ```
    phpunit -c app/ 
    ```

  * 执行某个单元测试

    ```
    phpunit -c app TEST_CAST_FILEPATH
    ```

## 系统演示
 - 校园帮－接口文档(密码zstu) [http://www.xiaoyaoji.com.cn/dashboard/#!/share/1xsmCDqx8i](http://www.xiaoyaoji.com.cn/dashboard/#!/share/1xsmCDqx8i) 
 - 体验校园帮管理系统, 请访问 [http://112.74.36.71:8000/admin](http://112.74.36.71:8000/admin)
 - 注册帐号, 请访问app或者通过api

一. 测试用户

- **用户名:** `test123123`
- **密码:** `test123`
