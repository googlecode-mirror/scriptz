#!/usr/bin/perl

#apache pid文件 
$pidApache = "/usr/local/httpd/logs/httpd.pid"; 
#apache操作文件 
$ctlApache = "/usr/local/httpd/bin/apachectl"; 

#mysql pid文件 
$pidMysql = "/db/mysql/jqinfo.pid"; 
#mysql操作文件 
$ctlMysql = "/etc/init.d/mysql"; 

$isRunApache = 1; 
$isRunMysql = 1; 
while (true) { 
	$nowLoadavg = CheckLoadavg(); 
	if ($nowLoadavg > 30 && $isRunApache == 1) { 
		#负载高于30 
		while (CheckApacheRun() == 0) { 
			system("$ctlApache stop"); 
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
	if ($nowMysql > 60 && $isRunMysql == 1) { 
		#Mysql 进程大于 60 
		while (CheckMysqlRun() == 0) { 
			system("$ctlMysql stop"); 
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
	#是否存在pid 不存在返回-1 
	if (-e $pidMysql) { 
		$PID = `cat $pidMysql`; 
		$PID = ~s/n//g; 
		if ($PID eq "") { #pid文件为空 
			return -1; 
		} else { 
			$Status = `ps -ef|grep mysqld|wc -l`; 
			if ($Status < 2) { return -1; } else { return 0; } 
		} 
	} else { 
		#pid文件不存在 
		return -1; 
	} 
} 

sub CheckApacheRun { 
	my $PID = ""; 
	my $Status = ""; 
	#是否存在pid 不存在返回-1 
	if (-e $pidApache) { 
		$PID = `cat $pidApache`; 
		$PID = ~s/n//g; 
		if ($PID eq "") { #pid文件为空 
			return -1; 
		} else { 
			$Status = `ps -ef|grep httpd|wc -l`; 
			if ($Status < 3) { return -1; } else { return 0; } 
		} 
	} else { 
		#pid文件不存在 
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
