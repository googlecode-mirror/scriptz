set echo on
host lsnrctl start
connect / as sysdba
startup
disconnect
exit
