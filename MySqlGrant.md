# MySQL用户权限问题 #
我的mysql安装在c:\mysql
## 一、更改密码 ##
### 第一种方式： ###
1、更改之前root没有密码的情况
_c:\mysql\bin>mysqladmin -u root password "your password"_
2、更改之前root有密码的情况,假如为123456
_c:\mysql\bin>mysqladmin -u root -p123456 password "your password"_
注意：更改的密码不能用单引号，可用双引号或不用引号

### 第二种方式： ###
1、c:\mysql\bin>mysql -uroot -p密码 以root身份登录
2、mysql>use mysql 选择数据库
3、mysql>update user set password=password('你的密码') where User='root';
4、mysqlflush privileges; 重新加载权限表

## 二、用户权限设置 ##
1、以root（也可用其它有权限的用户）身份登录
2、下面创建一个test用户，密码为test，并且只能对picture数据库进行操作的命令
```
mysql>GRANT ALL ON picture.* TO test IDENTIFIED BY "test";
```
GRANT语句的语法看上去像这样：
```
GRANT privileges (columns) ON what TO user IDENTIFIED BY "password" WITH GRANT OPTION　
```
要使用该语句，你需要填写下列部分：
```
　　privileges 授予用户的权限，下表列出可用于GRANT语句的权限指定符：
　权限指定符 权限允许的操作
　　Alter 　　　　　　修改表和索引
　　Create 　　　　 创建数据库和表
　　Delete 　　　　 删除表中已有的记录
　　Drop 　　 抛弃（删除）数据库和表
　　INDEX 　　　　 创建或抛弃索引
　　Insert 　　　　 向表中插入新行
　　REFERENCE 　　未用
　　Select　　　　 检索表中的记录
　　Update 　　　　 修改现存表记录
　　FILE 　　　　　　读或写服务器上的文件
　　PROCESS 　　 查看服务器中执行的线程信息或杀死线程
　　RELOAD 　　　　重载授权表或清空日志、主机缓存或表缓存。
　　SHUTDOWN　　 关闭服务器
　　ALL 　　　　　　所有；ALL PRIVILEGES同义词
　　USAGE 　　　　特殊的“无权限”权限

　　上表显示在第一组的权限指定符适用于数据库、表和列，第二组数管理权限。一般，这些被相对严格地授权，因为它们允许用户影响服务器的操作。第三组权限特殊，ALL意味着“所有权限”，UASGE意味着无权限，即创建用户，但不授予权限。

　　columns 　　权限运用的列，它是可选的，并且你只能设置列特定的权限。如果命令有多于一个列，应该用逗号分开它们。

　　what 　　权限运用的级别。权限可以是全局的（适用于所有数据库和所有表）、特定数据库（适用于一个数据库中的所有表）或特定表的。可以通过指定一个columns字句是权限是列特定的。

　　user 　　权限授予的用户，它由一个用户名和主机名组成。在MySQL中，你不仅指定谁能连接，还有从哪里连接。这允许你让两个同名用户从不同地方连接。 MySQL让你区分他们，并彼此独立地赋予权限。MySQL中的一个用户名就是你连接服务器时指定的用户名，该名字不必与你的Unix登录名或 Windows名联系起来。缺省地，如果你不明确指定一个名字，客户程序将使用你的登录名作为MySQL用户名。这只是一个约定。你可以在授权表中将该名字改为nobody，然后以nobody连接执行需要超级用户权限的操作。

　　password 　　赋予用户的口令，它是可选的。如果你对新用户没有指定IDENTIFIED BY子句，该用户不赋给口令（不安全）。对现有用户，任何你指定的口令将代替老口令。如果你不指定口令，老口令保持不变，当你用IDENTIFIED BY时，口令字符串用改用口令的字面含义，GRANT将为你编码口令，不要你用SET PASSWORD 那样使用password()函数。

　　WITH GRANT OPTION子句是可选的。如果你包含它，用户可以授予权限通过GRANT语句授权给其它用户。你可以用该子句给与其它用户授权的能力。
```
注意：用户名、口令、数据库和表名在授权表记录中是大小写敏感的，主机名和列名不是。

一般地，你可以通过询问几个简单的问题来识别GRANT语句的种类：
谁能连接，从那儿连接？
用户应该有什么级别的权限，他们适用于什么？
用户应该允许管理权限吗？

下面就讨论一些例子。

1.1 谁能连接，从那儿连接？
你可以允许一个用户从特定的或一系列主机连接。有一个极端，如果你知道降职从一个主机连接，你可以将权限局限于单个主机：
```
GRANT ALL ON samp_db.* TO boris@localhost IDENTIFIED BY "ruby"
GRANT ALL ON samp_db.* TO fred@res.mars.com IDENTIFIED BY "quartz"
```
(samp\_db.**意思是“samp\_db数据库的所有表)另一个极端是，你可能有一个经常旅行并需要能从世界各地的主机连接的用户max。在这种情况下，你可以允许他无论从哪里连接：
```
GRANT ALL ON samp_db.* TO max@% IDENTIFIED BY "diamond"
```
“%”字符起通配符作用，与LIKE模式匹配的含义相同。在上述语句中，它意味着“任何主机”。所以max和max@%等价。这是建立用户最简单的方法，但也是最不安全的。
其中，你可以允许一个用户从一个受限的主机集合访问。例如，要允许mary从snake.net域的任何主机连接，用一个%.snake.net主机指定符:
```
GRANT ALL ON samp_db.* TO mary@.snake.net IDENTIFIED BY "quartz"
```
如果你喜欢，用户标识符的主机部分可以用IP地址而不是一个主机名来给定。你可以指定一个IP地址或一个包含模式字符的地址，而且，从MySQL 3.23，你还可以指定具有指出用于网络号的位数的网络掩码的IP号：
```
GRANT ALL ON samp_db.* TO boris@192.168.128.3 IDENTIFIED BY "ruby"
GRANT ALL ON samp_db.* TO fred@192.168.128.% IDENTIFIED BY "quartz"
GRANT ALL ON samp_db.* TO rex@192.168.128.0/17 IDENTIFIED BY "ruby"
```
第一个例子指出用户能从其连接的特定主机，第二个指定对于C类子网192.168.128的IP模式，而第三条语句中，192.168.128.0/17指定一个17位网络号并匹配具有192.168.128头17位的IP地址。**

1.2 用户应该有什么级别的权限和它们应该适用于什么？
你可以授权不同级别的权限，全局权限是最强大的，因为它们适用于任何数据库。要使ethel成为可做任何事情的超级用户，包括能授权给其它用户，发出下列语句：
```
GRANT ALL ON *.* TO ethel@localhost IDENTIFIED BY "coffee" WITH GRANT OPTION
```
GRANT RELOAD ON **.** TO flushl@localhost IDENTIFIED BY "flushpass"
GRANT ALL ON samp\_db TO bill@racer.snake.net INDETIFIED BY "rock" GRANT Select ON samp\_db TO ro\_user@% INDETIFIED BY "rock"
GRANT Select,Insert,Delete,Update ON samp\_db TO bill@snake.net INDETIFIED BY "rock"
GRANT Select ON samp\_db.member TO bill@localhost INDETIFIED BY "rock"
GRANT Update (expiration) ON samp\_db. member TO bill@localhost
GRANT Update (street,city,state,zip) ON samp\_db TO assistant@localhost
GRANT ALL ON sales.