# Nginx and Squid #

squid+nginx负载均衡实现单机跑多个squid.


如今，在大访问量的网站中，squid反向代理已经成为一种常用的缓存技术。但是，众所周知，squid本身不支持SMP，因此其原本是不支持在单台服务器同一端口（例如要反向代理web必须指定80端口）下开多个进程的。

而今多核多内存服务器已成趋势，如果单台服务器只运行一个squid反向代理跑web则显得太浪费，而根据官方意见要想运行多个squid实例，要么就指定不同的IP不同端口来实现。

而nginx是一个高性能的 HTTP 和反向代理服务器软件，运用nginx的负载均衡功能，我们就能很好的实现在同一台服务器中跑多个squid的目的，充分发挥多核大内存的作用。

**具体步骤如下：**

  1. 将N个squid安装到不同目录，并指定好多个用户以及不同的监听端口，这样便于监控时查看，例如：
```
   squid1：/opt/squid1 监听在127.0.0.1:8081
   squid2：/opt/squid2 监听在127.0.0.1:8082 
   squid3：/opt/squid3 监听在127.0.0.1:8083 
```
  1. 编译并安装，配置nginx
```
./configure
```
nginx配置文件`nginx.conf`
```
user  www www;
worker_processes  10;


worker_rlimit_nofile 51200;


events {
        use epoll;
        worker_connections  51200;
}


http {
    include       mime.types;
    default_type  application/octet-stream;

    log_format  main  '$remote_addr - $remote_user [$time_local] "$request" '
                      '$status $body_bytes_sent "$http_referer" '
                      '"$http_user_agent" "$http_x_forwarded_for"';

    #access_log  logs/access.log  main;

    sendfile        on;
    tcp_nopush     on;
   tcp_nodelay  on;

    #keepalive_timeout  0;
    keepalive_timeout  65;

    upstream jianglb {
     server 127.0.0.1:8081;
     server 127.0.0.1:8082;
     server 127.0.0.1:8083;

    }

    #gzip  on;

    server {
        listen       192.168.1.3:80;
        server_name  www.jianglb.com jianglb.com ;
        access_log  logs/host.access.log  main;


        location / {
                proxy_pass        http://jianglb;
                proxy_redirect          off;
                proxy_set_header   Host             $host:80;
                proxy_set_header   X-Real-IP        $remote_addr;
                proxy_set_header   X-Forwarded-For  $proxy_add_x_forwarded_for;

        }
    }
}
```

这里有几个配置的注意点：
  1. 如果需要同时代理加速多个域名，而这些域名是同时做负载均衡的话，不需要分开来指定，`upstream`只需要一个即可，`proxy_pass`那里的名称能对应起来即可；
  1. `proxy_set_header Host $host:80`;这里最好加上端口80，因为我一开始没加80，发现`nginx`转发的时候`squid`会收到`www.jianglb.com:8081`这样的头信息，这明显是不对的，一次加上80会比较好。