#!/usr/bin/perl
# $Id$
# Author: zhuzhu@perlchina.org
# License: MIT

#apache pid file location
$pidApache = "/usr/local/apache2/logs/httpd.pid"; 
#apache controller file location
$ctlApache = "/usr/local/apache2/bin/apachectl"; 

#mysql pid file location
$pidMysql = "/var/lib/mysql/webserver.pid"; 
#mysql controller file location
$ctlMysql = "/etc/init.d/mysql.server"; 

#mail address to send log
$mailAddress = 'fred1982@gmail.com';

$isRunApache = 1; 
$isRunMysql = 1; 
while (true) { 
	$nowLoadavg = CheckLoadavg(); 
	if ($nowLoadavg > 15 && $isRunApache == 1) { 
		# Check httpd run status
		while (CheckApacheRun() == 0) { 
			system("$ctlApache stop"); 
			SendMail("[Eread Dev Server] httpd stop");
			sleep(20); 
		} 
		$isRunApache = 0; 
	} 
	if ($isRunApache == 0 || CheckApacheRun() == -1) { 
		system("$ctlApache start"); 
		$isRunApache = 1; 
		sleep(60); 
	}
	$nowMysql = CheckMysqlLink();
	if ($nowMysql > 20 && $isRunMysql == 1) { 
		#Mysql connection limit to 60 
		while (CheckMysqlRun() == 0) { 
			system("$ctlMysql stop"); 
			SendMail("[Eread Dev Server] MySQL Server stop");
			sleep(20); 
		} 
		$isRunMysql = 0; 
	}
	if ($isRunMysql == 0 || CheckMysqlRun() == -1) { 
		system("$ctlMysql start"); 
		$isRunMysql = 1; 
		sleep(60); 
	}
	sleep(5); 
} 

sub CheckMysqlRun { 
	my $PID = ""; 
	my $Status = ""; 
	# If MySQL server not run return -1 
	if (-e $pidMysql) { 
		$PID = `cat $pidMysql`; 
		$PID = ~s/n//g; 
		if ($PID eq "") { # pid file empty 
			return -1; 
		} else { 
			$Status = `ps -ef|grep mysqld|wc -l`; 
			if ($Status < 2) { return -1; } else { return 0; } 
		} 
	} else { 
		#pid return -1
		return -1; 
	} 
} 

sub CheckApacheRun { 
	my $PID = ""; 
	my $Status = ""; 
	# If Apache server not run, return -1 
	if (-e $pidApache) { 
		$PID = `cat $pidApache`; 
		$PID = ~s/n//g; 
		if ($PID eq "") { #pid empty
			return -1; 
		} else { 
			$Status = `ps -ef|grep httpd|wc -l`; 
			if ($Status < 2) { return -1; } else { return 0; } 
		} 
	} else { 
		#pid return -1
		return -1; 
	} 
} 

sub CheckMysqlLink() {
	my $Status = ""; 
	$Status = `ps -ef|grep mysqld|wc -l`; 
	$Status = $Status - 2;
	return $Status;
}

sub CheckLoadavg { 
	my @avg=split(/ /,`cat /proc/loadavg`); 
	return $avg[0]; 
} 

sub SendMail {
	my $msg = shift;
	system("echo \"Date: `date +%F\\ %r `\" | mail -s \"$msg\" $mailAddress");
}
