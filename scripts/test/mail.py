#!/usr/bin/python
# -*- coding: utf-8 -*-
# $Id$

import os,sys,cgi,smtplib,email.Utils
from email.Message import Message
from email.MIMEText import MIMEText
from email.Header import Header

print "Content-type: text/html\n"
print "Simple Python Mail<br />"

to = "fred1982@gmail.com"
From = "fred@localhost"
subject = "测试一下"
body = "测试一下一下"
smtpServer = "localhost"

if len(body) > 4096:
	body = body[1:4096]

msg = MIMEText(body,'plain','UTF-8')
msg['From'] = From
msg['To'] = to
msg['Subject'] = Header(subject, 'UTF-8')

try:
	server = smtplib.SMTP(smtpServer)
	result = server.sendmail(From,to,msg.as_string())
	server.quit()

except smtplib.SMTPException, errmssg:
	print "Error sending mail:" + errmsg
