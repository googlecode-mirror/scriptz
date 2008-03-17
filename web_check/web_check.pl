#!/usr/bin/perl
# zhuzhu@perlchina.org
# $Id$
# 
use strict;
use warnings;
use Proc::ProcessTable;
use DBI;

# apache pid file location
my $ip = '192.168.1.122';
my $pidfile = "/var/run/httpd/httpd.pid";
my $ctlApache = "/usr/sbin/apachectl";
my $isRunApache = 1;
my $waitApacheStartTime = 60;
my $waitLoopTime = 15;
my $apacheUid = 'apache';
my $maxCpuUse = '15';
my $maxRuntime = '1800';
my $dbhost = '192.168.1.220';
my $dbuser = 'root';
my $dbpassword = '';
my $dbname = 'monitor';

# work loop block
while(1) {
    my $nowLoadavg = checkLoadavg();
	# check apache pid
	if ($nowLoadavg > 30 && $isRunApache == 1) {
		while (checkApacheRun() == 0){
			system("$ctlApache stop");
			logRecord('server', $ip, '0');
			while(1){
				sleep(5);
				my $httpdNum = `ps -ef | grep httpd | grep -v grep | wc -l`;
				last if($httpdNum == 0);
			}
		}
		$isRunApache = 0;
	}
	
	if ($isRunApache == 0 || checkApacheRun() == -1){
		system("$ctlApache start");
		logRecord('server', $ip, '1');
		$isRunApache =1;
		sleep($waitApacheStartTime);
	}

	# clean bad apache processes
	my ($t) = new Proc::ProcessTable;
	my $killedProcNum = 0;
	foreach my $p (@{$t->table}){
		if ($p->uid eq $apacheUid){
			if($p->fname eq 'httpd' && ($p->pctcpu > $maxCpuUse || 
				(time() - $p->start) > $maxRuntime)){
				$p->kill(9);
				$killedProcNum++;
			}
		}
	}
	if ($killedProcNum > 0) {
		logRecord('proc', $ip, $killedProcNum);
	}

	sleep($waitLoopTime);
}

#
# functions
#
sub checkApacheRun {
	my $pid = "";
	my $status = "";
	if (-e $pidfile){
		$pid = `/bin/cat $pidfile`;
		$pid =~ s/\n//g;
		if($pid eq "") {
			return -1;
		}else{
			my $status = `ps -ef|grep httpd|wc -l`;
			if ($status < 3) {return -1;} else {return 0}
		}
	}else{
		return -1;
	}
}

sub checkLoadavg {
	my @avg = split(/ /, `cat /proc/loadavg`);
	return $avg[0];
}

sub logRecord {
	my ($type, $ip, $status) = @_;
	my $dbh = DBI->connect("dbi:mysql:$dbname",$dbuser,$dbpassword) or 
		die "Connection Error: $DBI::errstr\n";
	    if ($type == "server")	{
			$dbh->do("INSERT INTO `web_server_status` (server_ip, 
				status, created) VALUES ($ip, $status, &getDateTime())");
		} elsif ($type == 'proc'){
			$dbh->do("INSERT INTO `web_proc_status` (server_ip,
				killedNum, created) VALUES ($ip, $status, &getDateTime())");
		}
}

sub getDateTime {
	my ($s, $mm, $h, $d, $m, $y) = (localtime) [0,1,2,3,4,5];
	$m++;
	$y+=1900;
	if ($m<10) { $m = "0".$m; }
	if ($d<10) { $d = "0".$d; }
	my $mysqlDateTime = "$y-$m-$d $h:$mm:$s";
	return $mysqlDateTime;
}
