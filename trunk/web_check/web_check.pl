#!/usr/bin/perl
# zhuzhu@perlchina.org
# $Id$
# 
use strict;
use warnings;
use Proc::ProcessTable;

# apache pid file location
my $pidfile = "/var/run/httpd/httpd.pid";
my $ctlApache = "/usr/sbin/apachectl";
my $isRunApache = 1;
my $waitApacheStartTime = 60;
my $waitLoopTime = 15;
my $apacheUid = 'apache';
my $maxCpuUse = '15';
my $maxRuntime = '1800';

# work loop block
while(1) {
    my $nowLoadavg = checkLoadavg();
	# check apache pid
	if ($nowLoadavg > 30 && $isRunApache == 1) {
		while (checkApacheRun() == 0){
			system("$ctlApache stop");
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
		$isRunApache =1;
		sleep($waitApacheStartTime);
	}

	# clean bad apache processes
	my ($t) = new Proc::ProcessTable;
	foreach my $p (@{$t->table}){
		if ($p->uid eq $apacheUid){
			if($p->fname eq 'httpd' && ($p->pctcpu > $maxCpuUse || 
				(time() - $p->start) > $maxRuntime)){
				$p->kill(9);
			}
		}
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
			if ($status < 2) {return -1;} else {return 0}
		}
	}else{
		return -1;
	}
}

sub checkLoadavg {
	my @avg = split(/ /, `cat /proc/loadavg`);
	return $avg[0];
}
