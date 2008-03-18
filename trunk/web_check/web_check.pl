#!/usr/bin/perl
# zhuzhu@perlchina.org
# $Id$
# 
use strict;
use warnings;
use Proc::ProcessTable;
use DBI;
use Sys::Syslog;

# apache pid file location
my $serverIp = "125.65.113.14";   # <------------------- need set
my $pidfile = "/usr/local/apache2/logs/httpd.pid"; # <-- need set
my $ctlApache = "/usr/local/apache2/bin/apachectl"; # <- need set
my $isRunApache = 1;
my $waitApacheStartTime = 60;
my $waitLoopTime = 30;
my $apacheUid = 99;               # <------------------- need set
my $maxCpuUse = 15;
my $maxRuntime = 1800;
my $dbhost = '192.168.1.48';      # <------------------- need set
my $dbuser = 'monitor';
my $dbpassword = 'monitor_db';
my $dbname = 'monitor';
my $procName = 'httpd';
my $minApacheProcNum = 3;

# work loop block
while ("true") {
    my $nowLoadavg =  CheckLoadavg();
	# check apache pid
	if ($nowLoadavg > 30 && $isRunApache == 1) {
		while (checkApacheRun() == 0){
			system("$ctlApache stop");
			logRecord('server', $serverIp, '0');
			while("true"){
				sleep(5);
				my $httpdNum = &checkProcNum($procName);
				last if($httpdNum == 0);
			}
		}
		$isRunApache = 0;
	}
	
	if ($isRunApache == 0 || checkApacheRun() == -1){
		system("$ctlApache start");
		logRecord('server', $serverIp, '1');
		$isRunApache =1;
		sleep($waitApacheStartTime);
	}

	# clean bad apache processes
	my ($t) = new Proc::ProcessTable;
	my $killedProcNum = 0;
	foreach my $p (@{$t->table}){
		if ($p->uid eq $apacheUid){
			if($p->fname eq $procName && ($p->pctcpu > $maxCpuUse || 
				(time() - $p->start) > $maxRuntime)){
				$p->kill(9);
				$killedProcNum++;
			}
		}
	}
	if ($killedProcNum > 0) {
		logRecord('proc', $serverIp, $killedProcNum);
	}

	sleep($waitLoopTime);
}

#
# functions
#
sub checkApacheRun {
	my $pid = "";
	my $status = "";
	if (-e $pidfile) {
		$pid = `/bin/cat $pidfile`;
		$pid =~ s/\n//g;
		if($pid eq "") {
			return -1;
		}else{
			$status = &checkProcNum($procName, $minApacheProcNum);
			if ( $status < $minApacheProcNum ) { return -1; } else { return 0; }
		}
	}else{
		return -1;
	}
}

sub CheckLoadavg {
	my @avg = split(/ /, `cat /proc/loadavg`);
	return $avg[0];
}

sub logRecord {
	my ($type, $serverIp, $status) = @_;
	my $dbh = DBI->connect("dbi:mysql:$dbname:$dbhost",$dbuser,$dbpassword); 
	if ($dbh){
		my $runTime = checkDateTime();
	    if ($type eq "server")	{
			$dbh->do(qq{INSERT INTO `web_server_status` (server_ip, status, created) VALUES ('$serverIp', '$status', 'checkDateTime()')});
		} elsif ($type eq 'proc'){
			$dbh->do(qq{INSERT INTO `web_proc_status` (server_ip, killed_num, created) VALUES ('$serverIp', '$status', '$runTime')});
		} else {
			exit 0;
		}
		$dbh->disconnect();
	}else{
		openlog($0, 'cons,pid', 'user');
		syslog('info', "Connection Error: $DBI::errstr");
		closelog();
	}
}

sub checkDateTime {
	my ($s, $mm, $h, $d, $m, $y) = (localtime) [0,1,2,3,4,5];
	$m++;
	$y+=1900;
	if ($m<10) { $m = "0".$m; }
	if ($d<10) { $d = "0".$d; }
	if ($s<10) { $s = "0".$s; }
	if ($mm<10) { $mm = "0".$mm; }
	if ($h<10) { $h = "0".$h; }
	my $mysqlDateTime = "$y-$m-$d $h:$mm:$s";
	return $mysqlDateTime;
}

sub checkProcNum {
	my ($procName, $status) = @_;
	my $tp = new Proc::ProcessTable;
	my $procNum = 0;
	foreach my $proc ( @{$tp->table} ){
		if ($proc->fname eq $procName ) {
			$procNum++;
			last if ($status && $procNum > $status); 
		}
	}
	return $procNum;
}
