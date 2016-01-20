# NFS Mount Howto #

server(192.168.1.2,121.15.123.12) _/etc/
```
/nfs                     192.168.1.2/255.255.255.0(rw,sync,no_root_squash)
/nfs/upload-img           192.168.1.2/255.255.255.0(rw,sync,no_root_squash)
/nfs/www.example.com/upload      121.15.123.12/255.255.255.128(rw,sync,no_root_squash)
```_

client(192.168.1.3,121.15.123.32) _/etc/fstab_
```
121.15.123.12:/down /down           nfs defaults    0 0
192.168.1.2:/web/ads.isoshu.com/upload  /upload   nfs defaults    0 0
```