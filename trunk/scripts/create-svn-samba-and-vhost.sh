#!/bin/sh
# $Id$
# author: zhuzhu@perlchina.org
# date: 2007-11-26
# Lisence: MIT
# 


# valid user

if [ ! -n "$1" -o ! -n "$2" ];then
	echo ""
	echo " Usage: `basename $0` argument1 argument2"
	echo ""
	echo "!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!"
	echo "!!! Please use this script carefully !!!"
	echo "!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!"
	exit 0
fi	

# Set first
VHOST_CONF=/usr/local/apache2/conf/extra/vhost.conf
SAMBA_CONF=/etc/samba/smb.conf
SVN_HOME=/var/svn_home
TRAC_HOME=/var/trac_home
SAMBA_WORK_DIR=/develop_dir/svn_work_space
SAMBA_WORK_DIR_SED='\/develop_dir\/svn_work_space'
INIT_PROJECT_DIR=/tmp/project
SVN_URL="http://192.168.1.249/svn/"
# Set programme
SVN=/usr/local/bin/svn
TRACADMIN=/usr/local/bin/trac-admin
SVNADMIN=/usr/local/bin/svnadmin
APACHECTL=/usr/local/apache2/bin/apachectl
# Set Project Name
PROJECT=$1
PROJECTNAME=$2

# Valid conf file
if [ ! -d $SVN_HOME ];then
	echo "SVN home do not set"\n
	exit 0
elif [ ! -d $SAMBA_WORK_DIR ];then
	echo "Samba-SVN work dir can't found, make sure what are you doing?"
elif [ ! -d $TRAC_HOME ];then
	echo "Trac home do not set"\n
	exit 0
elif [ ! -d $INIT_PROJECT_DIR ];then
	echo "svn init project dir can't found, make it first like follow"
	echo " -/tmp/project [dir]"
	echo "  \-branches   [dir]"
	echo "  \-tags       [dir]"
	echo "  \-trunk      [dir]"
	echo "    \-README    [file]"
	exit 0
elif [ ! -f $VHOST_CONF ];then
	echo "Apache Virtual host file do not set"\n
	exit 0
elif [ ! -f $SAMBA_CONF ];then
	echo "Samba Config file do not set"\n
	exit 0
fi	

# Add svn depository
$SVNADMIN create $SVN_HOME/$PROJECT 1>&2 > /dev/null
echo "Create $SVN_HOME/$PROJECT success!"

# Init project to svn 
$SVN import $INIT_PROJECT_DIR file:///$SVN_HOME/$PROJECT -m "init version for $PROJECT" 1>&2 > /dev/null
echo "Init Project version success!"

# Add samba-svn work directory
mkdir $SAMBA_WORK_DIR/$PROJECT
if [ ! -L /web/$PROJECTNAME.isoshu.com ];then
	ln -s $SAMBA_WORK_DIR/$PROJECT /web/$PROJECTNAME.isoshu.com
fi
echo "Create $SAMBA_WORK_DIR/$PROJECT success!"
echo "Link $SAMBA_WORK_DIR/$PROJECT to /web/$PROJECTNAME.isoshu.com success!"

# Export init svn work files to samba svn work directory
$SVN co $SVN_URL/$PROJECT/trunk $SAMBA_WORK_DIR/$PROJECT 1>&2 > /dev/null
echo "Export init version to samba-work dircetory success!"

# Add new trac site
$TRACADMIN $TRAC_HOME/$PROJECT initenv "$PROJECTNAME project" sqlite:db/trac.db svn $SVN_HOME/$PROJECT /usr/local/share/trac/templates 1>&2 > /dev/null
echo "Create trac site success!"

# config new trac site
cat >> $TRAC_HOME/$PROJECT/conf/trac.ini << "EOF"

[components]
tracpygments.pygmentsrenderer = enabled
webadmin.* = enabled

EOF
sed -i 's/iso-8859-15/utf-8/' $TRAC_HOME/$PROJECT/conf/trac.ini&

# Add vhost config
cat >> $VHOST_CONF << "EOF"

# TEMP-TEXT-PROJECT-CONF-249
<VirtualHost *:80>
   DocumentRoot "/web/TEMP-TEXT-PROJECT-CONF-ROOT"
   ServerName TEMP-TEXT-PROJECT-CONF-249
   ErrorLog logs/TEMP-TEXT-PROJECT-CONF-249
</VirtualHost>

EOF
sed -i 's/TEMP-TEXT-PROJECT-CONF-249/'$PROJECT'/' $VHOST_CONF
sed -i 's/TEMP-TEXT-PROJECT-CONF-ROOT/'$PROJECTNAME'.isoshu.com/' $VHOST_CONF

# Restart Apache
$APACHECTL restart 1>&2 > /dev/null
echo "Apache vhost add add restart success!"

# Add Samba
cat >> $SAMBA_CONF <<EOF

[TEMP-TEXT-PROJECT-CONF-249]
    path=TEMP-TEXT-PROJECT-CONF-PATH
    writeable = yes
    browseable=yes
    guest ok=yes
EOF

sed -i 's/TEMP-TEXT-PROJECT-CONF-249/'$PROJECT'/' $SAMBA_CONF
sed -i 's/TEMP-TEXT-PROJECT-CONF-PATH/'$SAMBA_WORK_DIR_SED'\/'$PROJECT'/' $SAMBA_CONF

# restart samba
service smb restart 1>&2 > /dev/null
echo "Samba work directory add and restart success!"

# chown dirs to nobody user
chown -R nobody.nobody $SVN_HOME/$PROJECT
chown -R nobody.nobody $TRAC_HOME/$PROJECT
chown -R nobody.nobody $SAMBA_WORK_DIR/$PROJECT
echo "All work filished, your check first!!!"
echo "visit http://192.168.1.249/svn/"
echo "visit http://192.168.1.249/projects/"
echo "visit \\\\192.168.1.249\\"
echo "visti http://$PROJECT"
