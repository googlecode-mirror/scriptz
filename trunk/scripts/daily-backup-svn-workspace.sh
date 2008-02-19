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