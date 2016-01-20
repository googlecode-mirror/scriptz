# MySQL遗忘密码 #

MySQL密码忘记后该怎么办

首先杀掉原有的mysqld进程.再启动一个跳过grant的mysqld服务
```
/path/to/mysql/bin/mysqld_safe --skip-grant --user=mysql &
```
然后直接登陆
```
/path/to/mysql/bin/mysql -uroot
```
登陆以后就可以设置密码了: mysql> use mysql;
```
mysql> update user set password=password("new-password") where user="root";

mysql> flush privileges;

mysql> exit
```
这时候再正常启动mysql就可以使用新的密码了。