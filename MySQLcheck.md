# MySQL 修复 #
在长时间使用MySQL后，数据库可能会出现一些问题。大多数问题可以通过简单的操作进行快速修复。下面介绍两种快速检修

MySQL数据库的方法。

1. myisamchk

使用myisamchk必须暂时停止MySQL服务器。例如，我们要检修discuz数据库。执行以下操作：
```
# service mysql stop (停止MySQL)；
# myisamchk -r /var/lib/mysql/discuz/*MYI
# service mysql start
```
myisamchk会自动检查并修复数据表中的索引错误。

2. mysqlcheck

使用mysqlcheck无需停止MySQL，可以进行热修复。操作步骤如下：
```
# mysqlcheck -r discuz.*
```
注意，无论是myisamchk还是mysqlcheck，一般情况下不要使用-f强制修复，-f参数会在遇到一般修复无法成功的时候删除

部分出错数据以尝试修复。所以，不到万不得已不要使用-f。