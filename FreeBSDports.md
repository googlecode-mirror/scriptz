# FreeBSD About #

_/etc/make.conf_
```
MASTER_SITE_BACKUP?= \
ftp://ftp.freebsdchina.org/pub/FreeBSD/ports/distfiles/${DIST_SUBDIR}/\
ftp://ftp.tw.freebsd.org/pub/FreeBSD/distfiles/${DIST_SUBDIR}/\
ftp://ftp5.tw.freebsd.org/pub/FreeBSD/distfiles/${DIST_SUBDIR}/\
ftp://ftp10.tw.freebsd.org/pub/FreeBSD/distfiles/${DIST_SUBDIR}/\
ftp://ftp2.tw.freebsd.org/pub/FreeBSD/distfiles/${DIST_SUBDIR}/\
ftp://ftp3.tw.freebsd.org/pub/FreeBSD/distfiles/${DIST_SUBDIR}/\
ftp://ftp4.tw.freebsd.org/pub/FreeBSD/distfiles/${DIST_SUBDIR}/\
ftp://ftp7.tw.freebsd.org/pub/FreeBSD/distfiles/${DIST_SUBDIR}/\
ftp://ftp8.tw.freebsd.org/pub/FreeBSD/distfiles/${DIST_SUBDIR}/\
ftp://ftp9.tw.freebsd.org/pub/FreeBSD/distfiles/${DIST_SUBDIR}/\
ftp://ftp11.tw.freebsd.org/pub/FreeBSD/distfiles/${DIST_SUBDIR}/\
ftp://ftp12.tw.freebsd.org/pub/FreeBSD/distfiles/${DIST_SUBDIR}/\
ftp://ftp.freebsd.org/pub/FreeBSD/ports/distfiles/${DIST_SUBDIR}/
MASTER_SITE_OVERRIDE?= ${MASTER_SITE_BACKUP}
```