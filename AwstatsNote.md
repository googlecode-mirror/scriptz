# Awstats Note #

## 单日统计 ##
  * 命令行统计增加： -databasebreak=day
  * 报表输出时增加： &databasebreak=day&day=DD

```
$ sudo /opt/local/bin/perl \
/opt/local/awstats/wwwroot/cgi-bin/awstats.pl \
-froce -config=isoshu.com update -databasebreak=day
```