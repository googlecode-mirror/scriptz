# How To Install Apache Web Server #

## Own Server ##
```
./configure --prefix=/opt/local --sysconfdir=/opt/local/etc/apache2 \
--libexecdir=/opt/local/lib/apache2 --datadir=/opt/local/var/www \
--enable-mods-shared=most --enable-cache --enable-disk-cache \
--enable-mem-cache --enable-example --enable-deflate \
--enable-file-cache --enable-modules=most --enable-exception-hook \
--enable-dbd --enable-version --enable-proxy --enable-proxy-connect \
--enable-proxy-ftp --enable-proxy-http --enable-proxy-balancer \
--enable-ssl --enable-optional-hook-export --enable-so \
--with-mpm=worker --enable-dav --enable-maintainer-mode
```