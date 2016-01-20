# FTP User Individuality #

Ok, so I fixed the next issue with my ftp, which is upload access. This is an easy issue if you just want to give everybody and their mother upload access. However, I don’t want to do this so I had to setup per-user configuration in vsftpd. Since this is built-in it is a lot easier than it sounds.

  1. Add the following line to /etc/vsftpd/vsftpd.conf:

> user\_config\_dir=/etc/vsftpd/vsftpd\_user\_conf
> anon\_world\_readable\_only=NO
> 2. Create that directory:

> mkdir /etc/vsftpd/vsftpd\_user\_conf
> 3. Next create the users configuration:

> echo "write\_enable=YES" >> /etc/vsftpd/vsftpd\_user\_conf/fred
> echo “anon\_upload\_enable=YES” >> /etc/vsftpd/vsftpd\_user\_conf/fred
> echo “anon\_mkdir\_write\_enable=YES” >> /etc/vsftpd/vsftpd\_user\_conf/fred

I created a couple simple bash scripts that can be used add users and add upload access to users. Both scripts must be ran as root.

Script to add users to your ftp.
```
#!/bin/bash
# Name: addFtpUser.sh
# Description: Adds users to /etc/vsftpd/logins.txt and resets the database
# MUST BE RAN AS ROOT
# Arguments: $1=Username $2=Password
# Author: Kevin Harriss
# special.kevin@gmail.com
# Version: 0.01
if [ $# -ne 2 ]
then
echo “Username and Password needed!!!”
else
echo $1 >> /etc/vsftpd/logins.txt
echo $2 >> /etc/vsftpd/logins.txt
db_load -T -t hash -f /etc/vsftpd/logins.txt /etc/vsftpd/vsftpd_login.db
fi
```
Here is the bash script to add upload access to a user.
```
#!/bin/bash
# Name: grantFtpUpload.sh
# Description: Gives user upload access to ftp.
# MUST BE RAN AS ROOT
# Arguments: $1=Username
# Author: Kevin Harriss
# special.kevin@gmail.com
# Version: 0.01
if [ $# -ne 1 ]
then
echo “Username needed!!!”
exit
else
echo “write_enable=YES” >> /etc/vsftpd/vsftpd_user_conf/$1
echo “anon_upload_enable=YES” >> /etc/vsftpd/vsftpd_user_conf/$1
echo “anon_mkdir_write_enable=YES” >> /etc/vsftpd/vsftpd_user_conf/$1
fi
```

## vsftpd with pam\_pwdfile ##
[vsftpd with pam\_pwdfile ](http://www.productionmonkeys.net/guides/ftp-server/vsftpd)