# Nginx and Apache vHosts #

If you have not already tried using nginx, one of the fastest http and reverse proxy servers in the world, you definitely should try doing so!

Nginx can save lots of CPU cycles simply by “standing” before the main heavy http server(e.g. Apache) and taking the role of serving effectively all static content. Actually it can be your main and the only http server but that’s a story for another day since it will require quite a lot hassle if you already have many virtual hosts served by another server(Apache again).

Most of the nginx documentation is written in Russian and unfortunately there are not so many configuration examples out there(please correct me if I’m wrong). I’d like to fill this shameful gap a bit by providing a configuration sample we(at my company) currently use in production. Please keep in mind, this tutorial implies that you have already some Apache virtual hosts infrastructure installed.

Nginx can be very easily installed from the source following standard “./configure && make && make install” procedure however usage of some package manager can make this process even simpler. Being a fan of Gentoo and emerge facility, I’ll use this great distro as an example of nginx installation. Just execute the following command in the shell:
```
# USE="pcre ssl zlib status" emerge nginx
```
…and that’s it ;) By the end of the build process you’ll get a fresh nginx instance(0.5.32 as of this writing) supporting Perl compatible regexes, SSL, zlib compression and real time self-status reporting. Emerge also installs a convenient control script _/etc/init.d/nginx_ which can be used for standard _start/stop/restart_ daemon operations.

Before starting nginx let’s edit main nginx configuration at /etc/nginx/nginx.conf. Actually the main config installed by emerge provides reasonable default settings, we’ll just tune it to our taste a little:
```
# vim /etc/nginx/nginx.conf

user  apache apache;
worker_processes  2;

events {
  worker_connections  8192;
  use epoll;#one of the most effective I/O notification facilities for Linux 2.6+, try "kqueue" for *BSD
}

http {
  root  /var/www/localhost/htdocs/;
  error_page   502 503 504  /50x.html;

  server_names_hash_max_size 512;
  server_names_hash_bucket_size 128;

  include         /etc/nginx/mime.types;
  default_type    application/octet-stream;

  log_format main
  '$remote_addr - $remote_user [$time_local] '
  '"$request" $status $bytes_sent '
  '"$http_referer" "$http_user_agent" '
  '"$gzip_ratio"';

  client_header_timeout   10m;
  client_body_timeout     10m;
  send_timeout            10m;
  connection_pool_size            512;
  client_header_buffer_size       1k;
  large_client_header_buffers     4 2k;
  request_pool_size               4k;

  #gzipping all text content
  gzip on;
  gzip_http_version 1.0;
  gzip_min_length 5000;
  gzip_buffers    4 8k;
  gzip_types text/plain text/html text/css application/x-javascript text/xml application/xml application/xml+rss text/javascript;
  gzip_proxied  any;
  gzip_comp_level 2;

  output_buffers  1 32k;
  postpone_output 1460;
  sendfile        on;
  tcp_nopush      on;
  tcp_nodelay     on;
  keepalive_timeout       75 20;
  ignore_invalid_headers  on;
  index index.html;

  access_log  /var/log/nginx/access_log main;
  error_log /var/log/nginx/error_log;

  #default nginx virtual host server
  #it's used if there is no other matching nginx virtual host found
  server {

    #your public host IP, consult ifconfig if not sure
    listen your.external.interface.ip:80;

    #you can access nginx internal stats using lynx or alike console based web browser
    #at http://your.external.interface.ip/nginx_status address
    location /nginx_status {
      access_log   off;
      allow your.external.interface.ip;
      deny all;
    }

    #default proxy settings for each virtual host
    include /etc/nginx/proxy.conf; #see below
  }

  #add fine-tuned virtual hosts
  include /etc/nginx/proxied_hosts.conf; #see below
}
```
In the configuration above we are assuming that Apache instance is located on the same box and listening to the loopback 127.0.0.1:80 network interface while nginx is listening to the external your.external.interface.ip:80 network interface.

Default virtual host, which immediately passes control to Apache, allows us to add new virtual hosts into Apache without worrying about adding their equivalents into nginx configuration file. This is very useful if some admin created a new Apache virtual host and then forgot about updating nginx virtual hosts configuration. Of course, there will be no static content serving speedup in this case but at least the new virtual host will be available which is better than an angry client calling you in the middle of the night and asking what happened to his lovely web-site.

In our scenario Apache server is always “hidden” behind nginx and that is why we provide default proxy settings for each virtual host. In order to avoid duplication these settings are included from _/etc/nginx/proxy.conf_:
```
# vim /etc/nginx/proxy.conf

location / {
  proxy_pass         http://127.0.0.1:80/;
  proxy_redirect     default;
  proxy_set_header   Host             $host;
  proxy_set_header   X-Real-IP        $remote_addr;
  proxy_set_header   X-Forwarded-For  $proxy_add_x_forwarded_for;
  client_max_body_size       10m;
  client_body_buffer_size    128k;
  proxy_connect_timeout      90;
  proxy_send_timeout         90;
  proxy_read_timeout         90;
  proxy_buffer_size          4k;
  proxy_buffers              4 32k;
  proxy_busy_buffers_size    64k;
  proxy_temp_file_write_size 64k;
}
```
Apart from default virtual host we definitely want to add other virtual hosts fine-tuned for static content serving. Our carefully crafted per virtual host settings reside in _/etc/nginx/proxied\_hosts.conf_ which is included in the main nginx config file:
```
# vim /etc/nginx/proxied_hosts.conf

#Example of foo.com, www.foo.com virtual hosts settings
server {
  listen your.external.interface.ip:80;
  server_name foo.com www.foo.com;
  #default proxy settings shared are among all virtual hosts
  include /etc/nginx/proxy.conf;
  location ~* ^.+.(jpe?g|gif|png|ico|css|zip|tgz|gz|rar|bz2|doc|xls|exe|pdf|ppt|html?|txt|tar|mid|midi|wav|bmp|rtf|js|swf|avi|mp3)${
    #forcing browser to cache locally static content for 30 days
    expires 30d;
    root /var/www/foo.com;
    #graceful fallback in case if static content doesn't exist
    include /etc/nginx/proxy_fallback.conf; #see below
  }
}
```
```
#Example of bar.com virtual hosts settings
server {
  listen your.external.interface.ip:80;
  server_name bar.com;
  include /etc/nginx/proxy.conf;
  location ~* ^.+.(jpe?g|gif|png|ico|css|zip|tgz|gz|rar|bz2|doc|xls|exe|pdf|ppt|html?|txt|tar|mid|midi|wav|bmp|rtf|js|swf|avi|mp3)${
    expires 30d;
    root /var/www/bar.com;
    include /etc/nginx/proxy_fallback.conf; #see below
  }
}

#other virtual hosts below
...
...
```
In the example above default proxy settings are overridden in custom virtual hosts in order to bypass big-and-fat Apache for static content serving. It’s the location ~… line which is responsible for mapping the request to the file on the disk and in case of success sending this file directly to the client. However in some cases we can have a request for non-existing static file(some application may generate it on the fly), in this situation we need to pass control to the Apache and this is what _/etc/nginx/proxy\_fallback.conf_ does:
```
# vim /etc/nginx/proxy_fallback.conf

#proxy options can't be set inside if directive
proxy_set_header   Host             $host;
proxy_set_header   X-Real-IP        $remote_addr;
proxy_set_header   X-Forwarded-For  $proxy_add_x_forwarded_for;
if (!-f $request_filename) {
  break;
  proxy_pass http://127.0.0.1:80;
}
```
We are almost done and can try starting nginx. However, before doing so make sure your Apache is listening to 127.0.0.1:80. You should have something like this somewhere in your _httpd.conf_:
```
...
Listen 127.0.0.1:80
...
```
And Apache virtual hosts’ address:port should also be configured properly, for example:
```
<VirtualHost *:80>
...
</VirtualHost>
```
This time we should be ready to restart Apache and finally start nginx:
```
# /etc/init.d/apache restart
# /etc/init.d/nginx start
```
Remember we added self-status nginx hook? It’s time to test it! Try typing this in your server shell:
```
$ lynx --dump http://your.external.interface.ip/nginx_status
```
You should see something like the following:
```
Active connections: 50
server accepts handled requests
124052 124052 428710
Reading: 0 Writing: 5 Waiting: 45
```
If you see something similar to above - congratulations, nginx is working!

You can also try testing actual requests serving. Try the following with curl(foo.com/image.gif should be a real file address!):
```
$ curl -I http://foo.com/image.gif
```
Which should output something like this:
```
HTTP/1.1 200 OK
Server: nginx/0.5.32
Date: Thu, 27 Sep 2007 19:50:12 GMT
Content-Type: image/gif
Content-Length: 1754
Last-Modified: Wed, 07 Feb 2007 13:15:27 GMT
Connection: keep-alive
Keep-Alive: timeout=20
Expires: Sat, 27 Oct 2007 19:50:12 GMT
Cache-Control: max-age=2592000
Accept-Ranges: bytes
```
Whew, that was a long post ;) I really want to hope someone finds it any useful.

Next time in my nginx adventures series I’ll show a couple of simple scripts we use to automate nginx virtual hosts synchronization with Apache’s virtual hosts.
