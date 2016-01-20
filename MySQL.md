# MySQL优化经验 #

```
SHOW FULL PROCESSLIST;
```

同时在线访问量继续增大 对于1G内存的服务器明显感觉到吃力严重时甚至每天都会死机 或者时不时的服务器卡一下 这个问题曾经困扰了我半个多月MySQL使用是很具伸缩性的算法，因此你通常能用很少的内存运行或给MySQL更多的被存以得到更好的性能。

安装好mysql后，配制文件应该在/usr/local/mysql/share/mysql目录中，配制文件有几个，有my-huge.cnf my-medium.cnf my-large.cnf my-small.cnf,不同的流量的网站和不同配制的服务器环境，当然需要有不同的配制文件了。

一般的情况下，my-medium.cnf这个配制文件就能满足我们的大多需要；一般我们会把配置文件拷贝到/etc/my.cnf 只需要修改这个配置文件就可以了，使用 `mysqladmin variables extended-status –u root –p` 可以看到目前的参数，有３个配置参数是最重要的，即key\_buffer\_size,query\_cache\_size,table\_cache。

key\_buffer\_size只对MyISAM表起作用，

key\_buffer\_size指定索引缓冲区的大小，它决定索引处理的速度，尤其是索引读的速度。一般我们设为16M,实际上稍微大一点的站点　这个数字是远远不够的，通过检查状态值Key\_read\_requests和Key\_reads,可以知道key\_buffer\_size设置是否合理。比例key\_reads / key\_read\_requests应该尽可能的低，至少是1:100，1:1000更好（上述状态值可以使用 `SHOW STATUS LIKE ‘key_read%’` 获得）。 或者如果你装了phpmyadmin 可以通过服务器运行状态看到,笔者推荐用phpmyadmin管理mysql，以下的状态值都是本人通过phpmyadmin获得的实例分析:

这个服务器已经运行了20天
```
key_buffer_size – 128M 
key_read_requests – 650759289 
key_reads - 79112 
```
比例接近1:8000 健康状况非常好

另外一个估计key\_buffer\_size的办法　把你网站数据库的每个表的索引所占空间大小加起来看看以此服务器为例:比较大的几个表索引加起来大概125M 这个数字会随着表变大而变大。

从4.0.1开始，MySQL提供了查询缓冲机制。使用查询缓冲，MySQL将SELECT语句和查询结果存放在缓冲区中，今后对于同样的SELECT语句（区分大小写），将直接从缓冲区中读取结果。根据MySQL用户手册，使用查询缓冲最多可以达到238%的效率。

通过调节以下几个参数可以知道query\_cache\_size设置得是否合理
```
Qcache inserts 
Qcache hits 
Qcache lowmem prunes 
Qcache free blocks 
Qcache total blocks
```
Qcache\_lowmem\_prunes的值非常大，则表明经常出现缓冲不够的情况,同时Qcache\_hits的值非常大，则表明查询缓冲使用非常频繁，此时需要增加缓冲大小Qcache\_hits的值不大，则表明你的查询重复率很低，这种情况下使用查询缓冲反而会影响效率，那么可以考虑不用查询缓冲。此外，在SELECT语句中加入SQL\_NO\_CACHE可以明确表示不使用查询缓冲。

Qcache\_free\_blocks，如果该值非常大，则表明缓冲区中碎片很多query\_cache\_type指定是否使用查询缓冲

我设置:
```
query_cache_size = 32M 
query_cache_type= 1 
```
得到如下状态值:
```
Qcache queries in cache 12737 表明目前缓存的条数 
Qcache inserts 20649006 
Qcache hits 79060095 　看来重复查询率还挺高的 
Qcache lowmem prunes 617913　有这么多次出现缓存过低的情况 
Qcache not cached 189896 　　 
Qcache free memory 18573912　　目前剩余缓存空间 
Qcache free blocks 5328 这个数字似乎有点大　碎片不少 
Qcache total blocks 30953 
```
如果内存允许32M应该要往上加点

table\_cache指定表高速缓存的大小。每当MySQL访问一个表时，如果在表缓冲区中还有空间，该表就被打开并放入其中，这样可以更快地访问表内容。通过检查峰值时间的状态值Open\_tables和Opened\_tables，可以决定是否需要增加table\_cache的值。如果你发现open\_tables等于table\_cache，并且opened\_tables在不断增长，那么你就需要增加table\_cache的值了（上述状态值可以使用 `SHOW STATUS LIKE ‘Open%tables’` 获得）。注意，不能盲目地把table\_cache设置成很大的值。如果设置得太高，可能会造成文件描述符不足，从而造成性能不稳定或者连接失败。

对于有1G内存的机器，推荐值是128－256。

笔者设置table\_cache = 256

得到以下状态:
```
Open tables 256 
Opened tables 9046 
```
虽然open\_tables已经等于table\_cache，但是相对于服务器运行时间来说,已经运行了20天，opened\_tables的值也非常低。因此，增加table\_cache的值应该用处不大。如果运行了6个小时就出现上述值 那就要考虑增大table\_cache。

如果你不需要记录2进制log 就把这个功能关掉，注意关掉以后就不能恢复出问题前的数据了，需要您手动备份，二进制日志包含所有更新数据的语句，其目的是在恢复数据库时用它来把数据尽可能恢复到最后的状态。另外，如果做同步复制( Replication )的话，也需要使用二进制日志传送修改情况。

log\_bin指定日志文件，如果不提供文件名，MySQL将自己产生缺省文件名。MySQL会在文件名后面自动添加数字引，每次启动服务时，都会重新生成一个新的二进制文件。此外，使用log-bin-index可以指定索引文件；使用binlog-do-db可以指定记录的数据库；使用binlog-ignore-db可以指定不记录的数据库。注意的是：binlog-do-db和binlog-ignore-db一次只指定一个数据库，指定多个数据库需要多个语句。而且，MySQL会将所有的数据库名称改成小写，在指定数据库时必须全部使用小写名字，否则不会起作用。

关掉这个功能只需要在他前面加上#号
```
#log-bin 
```
开启慢查询日志( slow query log ) 慢查询日志对于跟踪有问题的查询非常有用。它记录所有查过long\_query\_time的查询，如果需要，还可以记录不使用索引的记录。下面是一个慢查询日志的例子：

开启慢查询日志，需要设置参数 log\_slow\_queries、long\_query\_times、log-queries-not-using-indexes。

log\_slow\_queries指定日志文件，如果不提供文件名，MySQL将自己产生缺省文件名。long\_query\_times指定慢查询的阈值，缺省是10秒。log-queries-not-using-indexes是4.1.0以后引入的参数，它指示记录不使用索引的查询。笔者设置long\_query\_time=10

笔者设置:
```
sort_buffer_size = 1M 
max_connections=120 
wait_timeout =120 
back_log=100 
read_buffer_size = 1M 
thread_cache=32 
interactive_timeout=120 
thread_concurrency = 4 
```
参数说明:

back\_log

要求MySQL能有的连接数量。当主要MySQL线程在一个很短时间内得到非常多的连接请求，这就起作用，然后主线程花些时间(尽管很短) 检查连接并且启动一个新线程。back\_log值指出在MySQL暂时停止回答新请求之前的短时间内多少个请求可以被存在堆栈中。只有如果期望在一个短时间内有很多连接，你需要增加它，换句话说，这值对到来的TCP/IP连接的侦听队列的大小。你的操作系统在这个队列大小上有它自己的限制。 Unix listen(2)系统调用的手册页应该有更多的细节。检查你的OS文档找出这个变量的最大值。试图设定back\_log高于你的操作系统的限制将是无效的。

max\_connections

并发连接数目最大，120 超过这个值就会自动恢复，出了问题能自动解决

thread\_cache

没找到具体说明，不过设置为32后 20天才创建了400多个线程 而以前一天就创建了上千个线程 所以还是有用的

thread\_concurrency

#设置为你的cpu数目x2,例如，只有一个cpu,那么thread\_concurrency=2
#有2个cpu,那么thread\_concurrency=4
skip-innodb
#去掉innodb支持

代码:
```
# Example MySQL config file for medium systems. 
# 
# This is for a system with little memory (32M - 64M) where MySQL plays 
# an important part, or systems up to 128M where MySQL is used together with 
# other programs (such as a web server) 
# 
# You can copy this file to 
# /etc/my.cnf to set global options, 
# mysql-data-dir/my.cnf to set server-specific options (in this 
# installation this directory is /var/lib/mysql) or 
# ~/.my.cnf to set user-specific options. 
# 
# In this file, you can use all long options that a program supports. 
# If you want to know which options a program supports, run the program 
# with the "--help" option. 


# The following options will be passed to all MySQL clients 
[client] 
#password = your_password 
port = 3306 
socket = /tmp/mysql.sock 
#socket = /var/lib/mysql/mysql.sock 
# Here follows entries for some specific programs 


# The MySQL server 
[mysqld] 
port = 3306 
socket = /tmp/mysql.sock 
#socket = /var/lib/mysql/mysql.sock 
skip-locking 
key_buffer = 128M 
max_allowed_packet = 1M 
table_cache = 256 
sort_buffer_size = 1M 
net_buffer_length = 16K 
myisam_sort_buffer_size = 1M 
max_connections=120 
#addnew config 
wait_timeout =120 
back_log=100 
read_buffer_size = 1M 
thread_cache=32 
skip-innodb 
skip-bdb 
skip-name-resolve 
join_buffer_size=512k 
query_cache_size = 32M 
interactive_timeout=120 
long_query_time=10 
log_slow_queries= /usr/local/mysql4/logs/slow_query.log 
query_cache_type= 1 
# Try number of CPU's*2 for thread_concurrency 
thread_concurrency = 4 


#end new config 
# Don't listen on a TCP/IP port at all. This can be a security enhancement, 
# if all processes that need to connect to mysqld run on the same host. 
# All interaction with mysqld must be made via Unix sockets or named pipes. 
# Note that using this option without enabling named pipes on Windows 
# (via the "enable-named-pipe" option) will render mysqld useless! 
# 
#skip-networking 


# Replication Master Server (default) 
# binary logging is required for replication 
#log-bin 


# required unique id between 1 and 2^32 - 1 
# defaults to 1 if master-host is not set 
# but will not function as a master if omitted 
server-id = 1 


# Replication Slave (comment out master section to use this) 
# 
# To configure this host as a replication slave, you can choose between 
# two methods : 
# 
# 1) Use the CHANGE MASTER TO command (fully described in our manual) - 
# the syntax is: 
# 
# CHANGE MASTER TO MASTER_HOST=, MASTER_PORT=, 
# MASTER_USER=, MASTER_PASSWORD= ; 
# 
# where you replace , , by quoted strings and 
# by the master's port number (3306 by default). 
# 
# Example: 
# 
# CHANGE MASTER TO MASTER_HOST='125.564.12.1', MASTER_PORT=3306, 
# MASTER_USER='joe', MASTER_PASSWORD='secret'; 
# 
# OR 
# 
# 2) Set the variables below. However, in case you choose this method, then 
# start replication for the first time (even unsuccessfully, for example 
# if you mistyped the password in master-password and the slave fails to 
# connect), the slave will create a master.info file, and any later 
# change in this file to the variables' values below will be ignored and 
# overridden by the content of the master.info file, unless you shutdown 
# the slave server, delete master.info and restart the slaver server. 
# For that reason, you may want to leave the lines below untouched 
# (commented) and instead use CHANGE MASTER TO (see above) 
# 
# required unique id between 2 and 2^32 - 1 
# (and different from the master) 
# defaults to 2 if master-host is set 
# but will not function as a slave if omitted 
#server-id = 2 
# 
# The replication master for this slave - required 
#master-host = 
# 
# The username the slave will use for authentication when connecting 
# to the master - required 
#master-user = 
# 
# The password the slave will authenticate with when connecting to 
# the master - required 
#master-password = 
# 
# The port the master is listening on. 
# optional - defaults to 3306 
#master-port = 
# 
# binary logging - not required for slaves, but recommended 
#log-bin 


# Point the following paths to different dedicated disks 
#tmpdir = /tmp/ 
#log-update = /path-to-dedicated-directory/hostname 


# Uncomment the following if you are using BDB tables 
#bdb_cache_size = 4M 
#bdb_max_lock = 10000 


# Uncomment the following if you are using InnoDB tables 
#innodb_data_home_dir = /var/lib/mysql/ 
#innodb_data_file_path = ibdata1:10M:autoextend 
#innodb_log_group_home_dir = /var/lib/mysql/ 
#innodb_log_arch_dir = /var/lib/mysql/ 
# You can set .._buffer_pool_size up to 50 - 80 % 
# of RAM but beware of setting memory usage too high 
#innodb_buffer_pool_size = 16M 
#innodb_additional_mem_pool_size = 2M 
# Set .._log_file_size to 25 % of buffer pool size 
#innodb_log_file_size = 5M 
#innodb_log_buffer_size = 8M 
#innodb_flush_log_at_trx_commit = 1 
#innodb_lock_wait_timeout = 50 


[mysqldump] 
quick 
max_allowed_packet = 16M 


[mysql] 
no-auto-rehash 
# Remove the next comment character if you are not familiar with SQL 
#safe-updates 


[isamchk] 
key_buffer = 20M 
sort_buffer_size = 20M 
read_buffer = 2M 
write_buffer = 2M 


[myisamchk] 
key_buffer = 20M 
sort_buffer_size = 20M 
read_buffer = 2M 
write_buffer = 2M 


[mysqlhotcopy] 
interactive-timeout
```
补充

优化table\_cachetable\_cache指定表高速缓存的大小。每当MySQL访问一个表时，如果在表缓冲区中还有空间，该表就被打开并放入其中，这样可以更快地访问表内容。通过检查峰值时间的状态值Open\_tables和Opened\_tables，可以决定是否需要增加 table\_cache的值。如果你发现open\_tables等于table\_cache，并且opened\_tables在不断增长，那么你就需要增加table\_cache的值了（上述状态值可以使用 `SHOW STATUS LIKE ‘Open%tables’` 获得）。注意，不能盲目地把table\_cache设置成很大的值。如果设置得太高，可能会造成文件描述符不足，从而造成性能不稳定或者连接失败。对于有1G内存的机器，推荐值是128－256。

案例1：该案例来自一个不是特别繁忙的服务器table\_cache – 512open\_tables – 103opened\_tables – 1273uptime – 4021421 (measured in seconds)该案例中table\_cache似乎设置得太高了。在峰值时间，打开表的数目比table\_cache要少得多。

案例2：该案例来自一台开发服务器。table\_cache – 64open\_tables – 64opened-tables – 431uptime – 1662790 (measured in seconds)虽然open\_tables已经等于table\_cache，但是相对于服务器运行时间来说，opened\_tables的值也非常低。因此，增加table\_cache的值应该用处不大。案例3：该案例来自一个upderperforming的服务器table\_cache – 64open\_tables – 64opened\_tables – 22423uptime – 19538该案例中table\_cache设置得太低了。虽然运行时间不到6小时，open\_tables达到了最大值，opened\_tables的值也非常高。这样就需要增加table\_cache的值。优化key\_buffer\_sizekey\_buffer\_size指定索引缓冲区的大小，它决定索引处理的速度，尤其是索引读的速度。通过检查状态值Key\_read\_requests和Key\_reads，可以知道key\_buffer\_size 设置是否合理。比例key\_reads / key\_read\_requests应该尽可能的低，至少是1:100，1:1000更好（上述状态值可以使用 `SHOW STATUS LIKE ‘key_read%’` 获得）。key\_buffer\_size只对MyISAM表起作用。即使你不使用MyISAM表，但是内部的临时磁盘表是 MyISAM表，也要使用该值。可以使用检查状态值created\_tmp\_disk\_tables得知详情。对于1G内存的机器，如果不使用 MyISAM表，推荐值是16M（8-64M）。

案例1：健康状况key\_buffer\_size – 402649088 (384M)key\_read\_requests – 597579931key\_reads - 56188案例2：警报状态key\_buffer\_size – 16777216 (16M)key\_read\_requests – 597579931key\_reads - 53832731案例1中比例低于1:10000，是健康的情况；案例2中比例达到1:11，警报已经拉响。