#!/usr/bin/perl

#apache pid�ļ� 
$pidApache = "/usr/local/httpd/logs/httpd.pid"; 
#apache�����ļ� 
$ctlApache = "/usr/local/httpd/bin/apachectl"; 

#mysql pid�ļ� 
$pidMysql = "/db/mysql/jqinfo.pid"; 
#mysql�����ļ� 
$ctlMysql = "/etc/init.d/mysql"; 

$isRunApache = 1; 
$isRunMysql = 1; 
while (true) { 
	$nowLoadavg = CheckLoadavg(); 
	if ($nowLoadavg > 30 && $isRunApache == 1) { 
		#���ظ���30 
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
		#Mysql ���̴��� 60 
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
	#�Ƿ����pid �����ڷ���-1 
	if (-e $pidMysql) { 
		$PID = `cat $pidMysql`; 
		$PID = ~s/n//g; 
		if ($PID eq "") { #pid�ļ�Ϊ�� 
			return -1; 
		} else { 
			$Status = `ps -ef|grep mysqld|wc -l`; 
			if ($Status < 2) { return -1; } else { return 0; } 
		} 
	} else { 
		#pid�ļ������� 
		return -1; 
	} 
} 

sub CheckApacheRun { 
	my $PID = ""; 
	my $Status = ""; 
	#�Ƿ����pid �����ڷ���-1 
	if (-e $pidApache) { 
		$PID = `cat $pidApache`; 
		$PID = ~s/n//g; 
		if ($PID eq "") { #pid�ļ�Ϊ�� 
			return -1; 
		} else { 
			$Status = `ps -ef|grep httpd|wc -l`; 
			if ($Status < 3) { return -1; } else { return 0; } 
		} 
	} else { 
		#pid�ļ������� 
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
