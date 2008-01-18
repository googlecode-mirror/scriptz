#!/usr/bin/perl
# $Id$
# This script can remove BOM attr in unicode files
# make sure what will you do before use the script
# zhuzhu@perlchina.org
#

@DIRLIST = '.';
use File::Find;

sub process_file {
	$file 	 = 	$File::Find::name;
	if ($file =~ m/\.php$/g)
	{
		# print $file, "\n";

		$old = $file;
		open (OLD, "< $old");
		@file_=<OLD>;
		$file_[0] =~ s/^\xEF\xBB\xBF//;
		rename ($old, "$old.orig");
		open (NEW, "> $old" );
		print NEW @file_;
		close (NEW);
	}
}
find(\&process_file, @DIRLIST);

# print(@file);
