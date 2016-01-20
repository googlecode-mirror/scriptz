# Nginx 负载均衡服务器 #
## 一、) 安装Nginx ##
### 1.) 安装 ###
Nginx发音为 **engine x** ，是由俄罗斯人Igor Sysoev建立的项目,基于BSD许可。据说他当初是F5的成员之一，英文主页：http://nginx.net 俄罗斯的一些大网站已经使用它超过两年多了，一直表现不凡。
Nginx的编译参数如下：
```
[root@localhost]#./configure --prefix=/usr/local/server/nginx --with-openssl=/usr/include \
--with-pcre=/usr/include/pcre/ --with-http_stub_status_module --without-http_memcached_module \
--without-http_fastcgi_module --without-http_rewrite_module --without-http_map_module \
--without-http_geo_module --without-http_autoindex_module
```
在这里，需要说明一下，由于Nginx的配置文件中我想用到正则，所以需要 pcre 模块的支持。我已经安装了 pcre 及 pcre-devel 的rpm包，但是 Ngxin 并不能正确找到 .h/.so/.a/.la 文件，因此我稍微变通了一下：
```
[root@localhost]#mkdir /usr/include/pcre/.libs/
[root@localhost]#cp /usr/lib/libpcre.a /usr/include/pcre/.libs/libpcre.a
[root@localhost]#cp /usr/lib/libpcre.a /usr/include/pcre/.libs/libpcre.la
```
然后，修改 objs/Makefile 大概在908行的位置上，注释掉以下内容：
```
./configure --disable-shared
```
接下来，就可以正常执行 make 及 make install 了。

### 2.) 修改配置文件 /usr/local/server/nginx/conf/nginx.conf ###
以下是我的 nginx.conf 内容，仅供参考：
```
#运行用户
user  nobody nobody;

#启动进程
worker_processes  2;

#全局错误日志及PID文件
error_log  logs/error.log notice;
pid        logs/nginx.pid;

#工作模式及连接数上限
events {
        use epoll;
        worker_connections      1024;
}

#设定http服务器，利用它的反向代理功能提供负载均衡支持
http {
        #设定mime类型
        include       conf/mime.types;
        default_type  application/octet-stream;

        #设定日志格式
        log_format main         '$remote_addr - $remote_user [$time_local] '
                                                '"$request" $status $bytes_sent '
                                                '"$http_referer" "$http_user_agent" '
                                                '"$gzip_ratio"';

        log_format download '$remote_addr - $remote_user [$time_local] '
                                                '"$request" $status $bytes_sent '
                                                '"$http_referer" "$http_user_agent" '
                                                '"$http_range" "$sent_http_content_range"';

        #设定请求缓冲
        client_header_buffer_size    1k;
        large_client_header_buffers  4 4k;

        #开启gzip模块
        gzip on;
        gzip_min_length  1100;
        gzip_buffers     4 8k;
        gzip_types       text/plain;

        output_buffers   1 32k;
        postpone_output  1460;

        #设定access log
        access_log  logs/access.log  main;

        client_header_timeout  3m;
        client_body_timeout    3m;
        send_timeout           3m;

        sendfile                on;
        tcp_nopush              on;
        tcp_nodelay             on;

        keepalive_timeout  65;

        #设定负载均衡的服务器列表
        upstream mysvr {
                #weigth参数表示权值，权值越高被分配到的几率越大
                #本机上的Squid开启3128端口
                server 192.168.8.1:3128 weight=5;
                server 192.168.8.2:80   weight=1;
                server 192.168.8.3:80   weight=6;
        }

        #设定虚拟主机
        server {
                listen          80;
                server_name     192.168.8.1 www.yejr.com;

                charset gb2312;

                #设定本虚拟主机的访问日志
                access_log  logs/www.yejr.com.access.log  main;

                #如果访问 /img/*, /js/*, /css/* 资源，则直接取本地文件，不通过squid
                #如果这些文件较多，不推荐这种方式，因为通过squid的缓存效果更好
                location ~ ^/(img|js|css)/  {
                        root    /data3/Html;
                        expires 24h;
                }

                #对 "/" 启用负载均衡
                location / {
                        proxy_pass      http://mysvr;

                        proxy_redirect          off;
                        proxy_set_header        Host $host;
                        proxy_set_header        X-Real-IP $remote_addr;
                        proxy_set_header        X-Forwarded-For $proxy_add_x_forwarded_for;
                        client_max_body_size    10m;
                        client_body_buffer_size 128k;
                        proxy_connect_timeout   90;
                        proxy_send_timeout      90;
                        proxy_read_timeout      90;
                        proxy_buffer_size       4k;
                        proxy_buffers           4 32k;
                        proxy_busy_buffers_size 64k;
                        proxy_temp_file_write_size 64k;
                }

                #设定查看Nginx状态的地址
                location /NginxStatus {
                        stub_status             on;
                        access_log              on;
                        auth_basic              "NginxStatus";
                        auth_basic_user_file  conf/htpasswd;
                }
        }
}
```
运行以下命令检测配置文件是否无误：

> 如果没有报错，那么就可以开始运行Nginx了，执行以下命令即可：

> 备注：conf/htpasswd 文件的内容用 apache 提供的 htpasswd 工具来产生即可，内容大致如下：

> 帐号 yejr,密码 123456 yejr:qLYyJ0ZRLAId2

### 3.) 查看 Nginx 运行状态 ###
> 输入地址 http://192.168.8.1/NginxStatus/ ，输入验证帐号密码，即可看到类似如下内容：
```
Active connections: 291
server accepts handled requests
16630948 16630948 31070465
Reading: 6 Writing: 179 Waiting: 106
```
active connections -- 对后端发起的活动连接数

server accepts handled requests -- nginx 总共处理了 16630948 个连接, 成功创建 16630948 次握手 (证明中间没有失败的), 总共处理了 31070465 个请求 (平均每次握手处理了 1.8个数据请求)

reading -- nginx 读取到客户端的Header信息数

writing -- nginx 返回给客户端的Header信息数

waiting -- 开启 keep-alive 的情况下，这个值等于 active - (reading + writing)，意思就是Nginx说已经处理完正在等候下一次请求指令的驻留连接 。