在Apache, PHP, MySQL的体系架构中，MySQL对于性能的影响最大，也是关键的核心部分。对于Discuz!论坛程序也是如此，MySQL的设置是否合理优化，直接影响到论坛的速度和承载量！同时，MySQL也是优化难度最大的一个部分，不但需要理解一些MySQL专业知识，同时还需要长时间的观察统计并且根据经验进行判断，然后设置合理的参数。

下面我们了解一下MySQL优化的一些基础，MySQL的优化我分为两个部分，一是服务器物理硬件的优化；二是MySQL自身(my.cnf)的优化。

  1. 服务器硬件对MySQL性能的影响
    1. 磁盘寻道能力（磁盘I/O）,以目前高转速SCSI硬盘(7200转/秒)为例，这种硬盘理论上每秒寻道7200次，这是物理特性决定的，没有办法改变。 MySQL每秒钟都在进行大量、复杂的查询操作，对磁盘的读写量可想而知。所以，通常认为磁盘I/O是制约MySQL性能的最大因素之一，对于日均访问量在100万PV以上的Discuz!论坛，由于磁盘I/O的制约，MySQL的性能会非常低下！解决这一制约因素可以考虑以下几种解决方案：
> > > 使用RAID-0+1磁盘阵列，注意不要尝试使用RAID-5，MySQL在RAID-5磁盘阵列上的效率不会像你期待的那样快； 抛弃传统的硬盘，使用速度更快的闪存式存储设备。经过Discuz!公司技术工程的测试，使用闪存式存储设备可比传统硬盘速度高出6－10倍左右。
    1. CPU 对于MySQL应用，推荐使用S.M.P.架构的多路对称CPU，例如：可以使用两颗Intel Xeon 3.6GHz的CPU。
    1. 物理内存对于一台使用MySQL的Database Server来说，服务器内存建议不要小于2GB，推荐使用4GB以上的物理内存。
  1. MySQL自身因素当解决了上述服务器硬件制约因素后，让我们看看MySQL自身的优化是如何操作的。对MySQL自身的优化主要是对其配置文件my.cnf中的各项参数进行优化调整。下面我们介绍一些对性能影响较大的参数。

> > 由于my.cnf文件的优化设置是与服务器硬件配置息息相关的，因而我们指定一个假想的服务器硬件环境：
```
CPU: 2颗Intel Xeon 2.4GHz
内存: 4GB DDR
硬盘: SCSI 73GB
```
> > 下面，我们根据以上硬件配置结合一份已经优化好的my.cnf进行说明：

```
# vi /etc/my.cnf
```

以下只列出my.cnf文件中[mysqld](mysqld.md)段落中的内容，其他段落内容对MySQL运行性能影响甚微，因而姑且忽略。
```
[mysqld]
port = 3306
serverid = 1 
socket = /tmp/mysql.sock
skip-locking
# 避免MySQL的外部锁定，减少出错几率增强稳定性。
skip-name-resolve
```
禁止MySQL对外部连接进行DNS解析，使用这一选项可以消除MySQL进行DNS解析的时间。但需要注意，如果开启该选项，则所有远程主机连接授权都要使用IP地址方式，否则MySQL将无法正常处理连接请求！
```
back_log = 384
```
指定MySQL可能的连接数量。当MySQL主线程在很短的时间内接收到非常多的连接请求，该参数生效，主线程花费很短的时间检查连接并且启动一个新线程。

back\_log参数的值指出在MySQL暂时停止响应新请求之前的短时间内多少个请求可以被存在堆栈中。如果系统在一个短时间内有很多连接，则需要增大该参数的值，该参数值指定到来的TCP/IP连接的侦听队列的大小。不同的操作系统在这个队列大小上有它自己的限制。

试图设定back\_log高于你的操作系统的限制将是无效的。默认值为50。对于Linux系统推荐设置为小于512的整数。

```
key_buffer_size = 256M
# key_buffer_size指定用于索引的缓冲区大小，增加它可得到更好的索引处理性能。
```

> 对于内存在4GB左右的服务器该参数可设置为256M或384M。
> 注意：该参数值设置的过大反而会是服务器整体效率降低！
```
max_allowed_packet = 4M
thread_stack = 256K
table_cache = 128K
sort_buffer_size = 6M
```
查询排序时所能使用的缓冲区大小。注意：该参数对应的分配内存是每连接独占！如果有100个连接，那么实际分配的总共排序缓冲区大小为100 × 6 ＝ 600MB。所以，对于内存在4GB左右的服务器推荐设置为6-8M。
```
read_buffer_size = 4M
```
读查询操作所能使用的缓冲区大小。和sort\_buffer\_size一样，该参数对应的分配内存也是每连接独享！
```
join_buffer_size = 8M
```
联合查询操作所能使用的缓冲区大小，和sort\_buffer\_size一样，该参数对应的分配内存也是每连接独享！
```
myisam_sort_buffer_size = 64M
table_cache = 512
thread_cache_size = 64
query_cache_size = 64M
```
指定MySQL查询缓冲区的大小。可以通过在MySQL控制台执行以下命令观察：
```
 # > SHOW VARIABLES LIKE '%query_cache%';
 # > SHOW STATUS LIKE 'Qcache%';
 # 如果Qcache_lowmem_prunes的值非常大，则表明经常出现缓冲不够的情况；
```
如果Qcache\_hits的值非常大，则表明查询缓冲使用非常频繁，如果该值较小反而会影响效率，那么可以考虑不用查询缓冲；Qcache\_free\_blocks，如果该值非常大，则表明缓冲区中碎片很多。
```
tmp_table_size = 256M
max_connections = 768
```
指定MySQL允许的最大连接进程数。如果在访问论坛时经常出现Too Many Connections的错误提 示，则需要增大该参数值。
```
max_connect_errors = 10000000
wait_timeout = 10
```
指定一个请求的最大连接时间，对于4GB左右内存的服务器可以设置为5-10。
```
thread_concurrency = 8
```
该参数取值为服务器逻辑CPU数量×2，在本例中，服务器有2颗物理CPU，而每颗物理CPU又支持H.T超线程，所以实际取值为4 × 2 ＝ 8
```
skip-networking
```
开启该选项可以彻底关闭MySQL的TCP/IP连接方式，如果WEB服务器是以远程连接的方式访问MySQL数据库服务器则不要开启该选项！否则将无法正常连接！