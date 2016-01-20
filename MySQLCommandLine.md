# MySQL实用命令 #

一) 连接MYSQL：

> 格式： mysql -h主机地址 -u用户名 －p用户密码


1、例1：连接到本机上的MYSQL

> 首先在打开DOS窗口，然后进入mysql安装目录下的bin目录下，例如： D:\mysql\bin，再键入命令mysql -uroot -p，回车后提示你输密码，如果刚安装好MYSQL，超级用户root是没有密码的，故直接回车即可进入到MYSQL中了，MYSQL的提示符是：mysql>

2、例2：连接到远程主机上的MYSQL

> 假设远程主机的IP为：10.0.0.1，用户名为root,密码为123。则键入以下命令：
```
   mysql -h10.0.0.1 -uroot -p123 
```
> （注：u与root可以不用加空格，其它也一样）

3、退出MYSQL命令
```
   exit （回车） 
```

(二) 修改密码：

> 格式：mysqladmin -u用户名 -p旧密码 password 新密码

1、例1：给root加个密码123。首先在DOS下进入目录C:\mysql\bin，然后键入以下命令：
```
   mysqladmin -uroot -password 123 
```
> 注：因为开始时root没有密码，所以-p旧密码一项就可以省略了。

2、例2：再将root的密码改为456
```
   mysqladmin -uroot -pab12 password 456 
```
(三) 增加新用户：（注意：和上面不同，下面的因为是MYSQL环境中的命令，所以后面都带一个分号作为命令结束符）

> 格式：grant select on 数据库  to 用户名@登录主机 identified by “密码”

> 例1、增加一个用户test1密码为abc，让他可以在任何主机上登录，并对所有数据库有查询、插入、修改、删除的权限。首先用以root用户连入MYSQL，然后键入以下命令：
```
   grant select,insert,update,delete on *.* to test1@"%" Identified by "abc";
```
> 但例1增加的用户是十分危险的，你想如某个人知道test1的密码，那么他就可以在internet上的任何一台电脑上登录你的mysql数据库并对你的数据可以为所欲为了，解决办法见例2。

> 例2、增加一个用户test2密码为abc,让他只可以在localhost上登录，并可以对数据库mydb进行查询、插入、修改、删除的操作（localhost指本地主机，即MYSQL数据库所在的那台主机），这样用户即使用知道test2的密码，他也无法从internet上直接访问数据库，只能通过MYSQL主机上的web页来访问了。
```
   grant select,insert,update,delete on mydb.* to test2@localhost identified by "abc"; 
```
> 如果你不想test2有密码，可以再打一个命令将密码消掉。
```
   grant select,insert,update,delete on mydb.* to test2@localhost identified by ""; 
```
(四) 显示命令

1、显示数据库列表：
```
   show databases; 
```
> 刚开始时才两个数据库：mysql和test。mysql库很重要它里面有MYSQL的系统信息，我们改密码和新增用户，实际上就是用这个库进行操作。

2、显示库中的数据表：
```
   use mysql； //打开库
   show tables; 
```
3、显示数据表的结构：
```
   describe 表名; 
```
4、建库：
```
   create database 库名; 
```
5、建表：
```
   use 库名； 
   create table 表名 (字段设定列表)； 
```
6、删库和删表:
```
   drop database 库名; 
   drop table 表名； 
```
7、将表中记录清空：
```
   delete from 表名; 
```
8、显示表中的记录：
```
   select * from 表名;
```