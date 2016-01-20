# MySQL 优化编译 #

1. -static  13%
```
   --with-client-ldflags=-all-static
   --with-mysqld-ldflags=-all-static
```

静态链接提高13%性能

2. -pgcc  1%
```
   CFLAGS="-O3 -mpentiumpro -mstack-align-double" CXX=gcc \
     CXXFLAGS="-O3 -mpentiumpro -mstack-align-double \
     -felide-constructors -fno-exceptions -fno-rtti"
```
如果是Inter处理器，使用pgcc提高1%性能

3. Unix Socket  7.5%
```
   --with-unix-socket-path=/usr/local/mysql/tmp/mysql.sock
```
使用unix套接字链接提高7.5%性能，所以在windows下mysql性能肯定不如unix下面

4. --enable-assembler
允许使用汇编模式(优化性能)

下面是总体的编译文件

编译代码

```
   1. CFLAGS="-O3" CXX=gcc CXXFLAGS="-O3 -felide-constructors \ 
   2.        -fno-exceptions -fno-rtti -fomit-frame-pointer -ffixed-ebp"  
   3.     ./configure \  
   4.        --prefix=/usr/local/mysql --enable-assembler \  
   5.        --with-mysqld-ldflags=-all-static  
   6.        --with-client-ldflags=-all-static  
   7.        --with-unix-socket-path=/usr/local/mysql/tmp/mysql.sock  
   8.      --with-charset=utf8  
   9.        --with-collation=utf8_general_ci  
  10.        --with-extra-charsets=all   
```
> mysql编译安装
> 2007年03月19日 下午 05:42
> 安装mysql
```
      # tar zxvf mysql-4.0.14.tar.gz -C /setup
      # cd /setup/mysql-4.0.14
      # groupadd mysql
```


> 
---

> 2、编译安装MySQL 5.0.45/Mysql4.0.26（现在以mysql5为例）
```
      /usr/sbin/groupadd mysql
      /usr/sbin/useradd -g mysql mysql
      tar zxvf mysql-5.0.45.tar.gz
      cd mysql-5.0.45
      ./configure –prefix=/usr/local/webserver/mysql/ –without-debug –with-unix-socket-path=/tmp/mysql.sock –with-client-ldflags=-all-static –with-mysqld-ldflags=-all-static –enable-assembler –with-extra-charsets=gbk,gb2312,utf8 –with-pthread –enable-thread-safe-client
      make && make install
      chmod +w /usr/local/webserver/mysql
      chown -R mysql:mysql /usr/local/webserver/mysql
      cp support-files/my-medium.cnf /usr/local/webserver/mysql/my.cnf
      cd ../
```
> 附：以下为附加步骤，如果你想在这台服务器上运行MySQL数据库，则执行以下两步。如果你只是希望让PHP支持MySQL扩展库，能够连接其他服务器上的MySQL数据库，那么，以下两步无需执行。
> ①、以mysql用户帐号的身份建立数据表：
```
      /usr/local/webserver/mysql/bin/mysql_install_db –defaults-file=/usr/local/webserver/mysql/my.cnf –basedir=/usr/local/webserver/mysql –datadir=/usr/local/webserver/mysql/data –user=mysql –pid-file=/usr/local/webserver/mysql/mysql.pid –skip-locking –port=3306 –socket=/tmp/mysql.sock
```
> ②、启动MySQL（最后的&表示在后台运行）
```
      /bin/sh /usr/local/webserver/mysql/bin/mysqld_safe –defaults-file=/usr/local/webserver/mysql/my.cnf &

      # useradd mysql -g mysql -M -s /bin/false
      # ./configure --prefix=/web/mysql \ 指定安装目录
      --without-debug \去除debug模式
      --with-extra-charsets=gb2312 \添加gb2312中文字符支持
      --enable-assembler \使用一些字符函数的汇编版本
      --without-isam \去掉isam表类型支持 现在很少用了 isam表是一种依赖平台的表
      --without-innodb \去掉innodb表支持 innodb是一种支持事务处理的表,适合企业级应用
      --with-pthread \强制使用pthread库(posix线程库)
      --enable-thread-safe-client \以线程方式编译客户端
      --with-client-ldflags=-all-static \
      --with-mysqld-ldflags=-all-static \以纯静态方式编译服务端和客户端

      # make
      # make install
      # scripts/mysql_install_db \生成mysql用户数据库和表文件
      # cp support-files/my-medium.cnf /etc/my.cnf \copy配置文件,有large,medium,small三个环境下的,根据机器性能选择,如果负荷比较大,可修改里面的一些变量的内存使用值
      # cp support-files/mysql.server /etc/init.d/mysqld \copy启动的mysqld文件
      # chmod 700 /etc/init.d/mysqld
      # cd /web
      # chmod 750 mysql -R
      # chgrp mysql mysql -R
      # chown mysql mysql/var -R
      # cd /web/mysql/libexec
      # cp mysqld mysqld.old
      # strip mysqld
      # chkconfig --add mysqld
      # chkconfig --level 345 mysqld on
      # service mysqld start
      # netstat -atln
      看看有没有3306的端口打开,如果mysqld不能启动,看看/web/mysql/var下的出错日志,一般都是目录权限没有设置好的问题
      # ln -s /web/mysql/bin/mysql /sbin/mysql
      # ln -s /web/mysql/bin/mysqladmin /sbin/mysqladmin
      # mysqladmin -uroot password "youpassword" #设置root帐户的密码
      # mysql -uroot -p
      # 输入你设置的密码
      mysql>use mysql;
      mysql>delete from user where password=""; #删除用于本机匿名连接的空密码帐号
      mysql>flush privileges;
      mysql>quit

--prefix=/data/app/mysql5123  --datadir=/data/mysqldata --sysconfdir=/data/app/mysql5123/etc --with-charset=utf8 --enable-assembler  --without-isam --with-pthread  --enable-thread-safe-client --with-client-ldflags=-all-static --with-mysqld-ldflags=-all-static --with-extra-charsets=all   --with-unix-socket-path=/data/app/mysql5123/tmp/mysql.sock  

```


---


2、编译安装MySQL 5.0.45/Mysql4.0.26（现在以mysql5为例）
```
/usr/sbin/groupadd mysql
/usr/sbin/useradd -g mysql mysql
tar zxvf mysql-5.0.45.tar.gz
cd mysql-5.0.45
./configure –prefix=/usr/local/webserver/mysql/ –without-debug –with-unix-socket-path=/tmp/mysql.sock –with-client-ldflags=-all-static –with-mysqld-ldflags=-all-static –enable-assembler –with-extra-charsets=gbk,gb2312,utf8 –with-pthread –enable-thread-safe-client
make && make install
chmod +w /usr/local/webserver/mysql
chown -R mysql:mysql /usr/local/webserver/mysql
cp support-files/my-medium.cnf /usr/local/webserver/mysql/my.cnf
cd ../
```
附：以下为附加步骤，如果你想在这台服务器上运行MySQL数据库，则执行以下两步。如果你只是希望让PHP支持MySQL扩展库，能够连接其他服务器上的MySQL数据库，那么，以下两步无需执行。
①、以mysql用户帐号的身份建立数据表：
```
/usr/local/webserver/mysql/bin/mysql_install_db –defaults-file=/usr/local/webserver/mysql/my.cnf –basedir=/usr/local/webserver/mysql –datadir=/usr/local/webserver/mysql/data –user=mysql –pid-file=/usr/local/webserver/mysql/mysql.pid –skip-locking –port=3306 –socket=/tmp/mysql.sock
```
②、启动MySQL（最后的&表示在后台运行）
```
/bin/sh /usr/local/webserver/mysql/bin/mysqld_safe –defaults-file=/usr/local/webserver/mysql/my.cnf &
```