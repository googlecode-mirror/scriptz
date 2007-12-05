#!/usr/local/bin/perl
# the script use to auto upload database dict 
# for projects
# write by zhuzhu@perlchina.org
# 2007/10/15
use strict;
use warnings;
use DBI;

my $database = 'book_shop';
my $saved_path = '/home/fred/tmp/book_shop_dict.html';

my $dbh;
# content to MySQL database
$dbh = DBI->connect('dbi:mysql:zhu_test','root','123456') or
	die "Connection Error: $DBI::errstr\n";

print $dbh->do("SHOW TABLES FROM zhu_test");	
