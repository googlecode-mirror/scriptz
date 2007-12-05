#!/bin/sh

# directory to backup
BDIR=/develop_dir/web_develop
ARCHDIR=/var/backup_develop_dir/web_develop

EXCLUDES=$HOME/cron/excludes

BSERVER="root@127.0.0.1"

export RSYNC_PASSWORD=backup_pass

CURRENT=main

DAYBACKUPDIR=`date +%F`
BACKUPDIR=`date +%H-%M`

# make day dir
[ -d $ARCHDIR/$DAYBACKUPDIR ] || mkdir $ARCHDIR/$DAYBACKUPDIR
[ -d $ARCHDIR/$DAYBACKUPDIR/$BACKUPDIR ] || mkdir $ARCHDIR/$DAYBACKUPDIR/$BACKUPDIR

OPTS="--force --ignore-errors --delete-excluded --exclude-from=$EXCLUDES --delete --backup --backup-dir=$ARCHDIR/$DAYBACKUPDIR/$BACKUPDIR -av"

export PATH=$PATH:/bin:/usr/bin:/usr/local/bin

install -d /var/backup_develop_dir/$CURRENT

# run rsync
rsync $OPTS $DBIR $BSERVER::web_develop /var/backup_develop_dir/$CURRENT 2>&1 > $ARCHDIR/$DAYBACKUPDIR/$BACKUPDIR/rsync_log
