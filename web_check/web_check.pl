#!/usr/bin/perl
# zhuzhu@perlchina.org
# $Id$
# 
use strict;
use warnings;
use Proc::ProcessTable;

# apache pid file location
my $pidApache = "/usr/local/apache2/log/httpd.pid"

my ($t) = new Proc::ProcessTable;
foreach my $p (@{$t->table}){
	print $p->cmndline,"\n";
	if ($p->pctmem > 95){
		$p->kill(9);
	}
}
