#!/bin/sh
# $Id$
# this script can backup svn workspace file daily
# with cron.
# backup is a very import work.
# zhuzhu@perlchina.org
# eread develop server have two hard disk.
# we must backup data on two hard disk both!

SVN_WORK_DIR='/develop_dir/svn_work_space/'
BACKUP_SVN_WORK_DIR='/var/svn_work_space_daily_backup/'

# we use very simple rsync
rsync -a --delete $SVN_WORK_DIR $BACKUP_SVN_WORK_DIR

# backup svn and trac daily
#
SVN_DIR=/var/svn_home
TRAC_DIR=/var/trac_home
BACKUP_SVN_AND_TRAC_DIR='/develop_dir/backup_daily_svn_and_trac'
for i in $SVN_DIR $TRAC_DIR
do
	rsync -a --delete $i $BACKUP_SVN_AND_TRAC_DIR
done