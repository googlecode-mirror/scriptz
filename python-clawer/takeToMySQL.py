#!/opt/local/bin/python
# zhuzhu@cpan.org
# $Id$

import pycurl
import StringIO
import time
import datetime
import re
import sys, os
import urllib
import stat
import md5
import time
import random
from BeautifulSoup import BeautifulSoup, Comment

# read urls file
#fWrite = open("url.list", "a")
#f = open("linkinfo", "r")
#for line in f:
#	regx = r"""^\('\d+/(.*\.html)',""" 
#	match = re.search(regx, line)
#	if match:
#		newLine = match.groups()[0]+"\n"
#		fWrite.write(newLine)
#f.close()		

def buildHeaders(browser, referer=""):
	if referer != "":
		buildHeaders = ['User-agent: ' + browser,'Accept-Language: en-us', 'Accept-Encoding:gzip,compress;q=0.9,deflate;q=0', 'Referer:' + referer]
	else:
		buildHeaders = ['User-agent: ' + browser,'Accept-Language: en-us', 'Accept-Encoding:gzip,compress;q=0.9,deflate;q=0']
	return buildHeaders

def urlComp(x, y):
	def getNum(str): return float(re.findall(r'_(\d{1,2})\.html$', str)[0])
	return cmp(getNum(x),getNum(y))

def getPage(url):
	USER_AGENT = '"Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.0.6) Gecko/20060728 Firefox/1.5.0.6"'
	theHeaders = buildHeaders(USER_AGENT)
	timeout = 300
	c = pycurl.Curl()
	c.setopt(pycurl.NOSIGNAL, 1)
	c.setopt(pycurl.URL, url)
	if timeout:
		c.setopt(pycurl.CONNECTTIMEOUT, timeout)
		c.setopt(pycurl.TIMEOUT, timeout)
	c.setopt(pycurl.USERAGENT, USER_AGENT)
	c.setopt(pycurl.HTTPHEADER, theHeaders)
	c.setopt(pycurl.COOKIEFILE, '')
	c.setopt(pycurl.HTTPHEADER, ["Accept:"])
	b = StringIO.StringIO()
	c.setopt(pycurl.WRITEFUNCTION, b.write)
	c.setopt(pycurl.FOLLOWLOCATION, 1)
	c.setopt(pycurl.MAXREDIRS, 5)
	try:
		c.perform()
		ret_code = c.getinfo(pycurl.HTTP_CODE)
		# print value
		value = b.getvalue()
		b.close()
		c.close()
		return value
	except pycurl.error, exc:
		if exc[0] == 7:
			print 'connect time out fetch '+url
			getPage(url)
		elif exc[0] == 28:
			print 'fetch time out '+url
			getPage(url)
		elif exc[0] == 3:
			print 'Error URL '+url
			return None
		else:
			print 'pycurl error '+url
			getPage(url)

def uploadImg(path, file):
	import ftplib
	s = ftplib.FTP('fred.webcan.cn', 'fivery@lz3.org', 'slackware')
	s.cwd('uploads/img/'+path)
	f = open('img/'+path+'/'+file, 'rb')
	s.storbinary('STOR '+file, f)
	f.close()
	s.quit()

def parsePage(url, page='a'):
	pycurlData = str(getPage(url)).lower()
#	print pycurlData
	if pycurlData is None:
		return None
	soup = BeautifulSoup(pycurlData)
	for script in soup("script"):
		soup.script.extract()
	for strong in soup("strong"):
		soup.strong.extract()
	if (page!='b') :
		title = soup.find('div', id="contentzwleft01_a")
		if title:
			data = [ title.string ]
		else:
			return None
	content = soup.find('div', id="zhengwen")
	if not content:
		return None
	else:
		content = str(content)
	content = re.sub('(<div.*?>\n?)|(</div>\n?)|\n', '', content)
	content = re.sub('(<a .*?>\n?)|(</a>\n?)|(<font.*?>)|(</font>)', '', content)
	content = re.sub('<p( align="center"|)></p>', '', content)
	content = re.sub('<p( style="text-indent: 2em"|)>(|&nbsp;)</p>', '', content)
	content = re.sub('<strong>.*?</strong>', '', content)
#	content = re.sub('(<p align="center">)|(</p>)', '', content)
	content = BeautifulSoup(content).prettify()

	content1 = content
	content = re.sub('<img src="\S+/(\S+?\.\S+?)" (.*?)/>', r'<div class="wp-caption aligncenter" style="width: 510px"><img src="http://www.fivery.com/uploads/img/'+str(dirPath)+r'/\1" \2/></div>', content)

	fileStats = 'img/'+str(dirPath)
	if not os.path.isdir(fileStats):
		os.mkdir('img/'+str(dirPath))

#	regx = r"""<img\s*?src\s*?="(\S+)".*?>"""
	p = re.compile('<img\s*?src\s*?="(\S+)".*?>')
	matchs = p.findall(content1)
	if matchs!=None:
		for img in matchs:
			regx = r""".*/(.*)$"""
			match = re.search(regx, img)
			imgName =  match.groups()[0]
			imgStats = 'img/'+dirPath+'/'+imgName
			print dirPath + img
			if not os.path.isfile(imgStats):
				urllib.urlretrieve(img, 'img/'+dirPath+'/'+imgName)
				uploadImg(dirPath, imgName)

	soup = BeautifulSoup(str(content))
	content = soup.prettify()
	if (page != 'a'):
		content = '</div><!--nextpage--><div class="pycontent">' + content
		return content
	else:
		bodyContent = content
		firstPage = re.sub('(.*)\.html$', r'\1', url)
		pageList = []
		for line in lines:
			p = re.compile(firstPage+"""_\d{1,2}\.html$""")
			if p.search(line) is not None:
				pageList += [line]
		pageList.sort(urlComp)
		for page in pageList:
			print page
			bodyContent += parsePage(page, 'b')
	data.append('<div class="pycontent">' + bodyContent + '</div>')
	return data

def tranChinese(string):
	string = re.sub('[\'\"\.\~\!\@\#\$%\^\&\*\[\]\(\)\?\{\},;:]', '', string)
	q=string.encode('utf8')
	q=urllib.urlencode({'q':q, 'hl':'zh-CN', 'clss':'', 'tq':'', 'sl':'zh-CN', 'tl':'en'})
	url = 'http://203.208.39.104/translate_s?'+q
	data = getPage(url)
	regx = r"""<span id=otq><b>(.*?)</b>"""
	match = re.search(regx, data)
	try:
		data = match.groups()[0]
	except data:
		if data is None:
			data = random.randint(1000,10000)
		else:
			data = random.randint(0,999)
	
	data = data.lower()
	data = re.sub('[\'\"\.\~\!\@\#\$%\^\&\*\[\]\(\)\?\{\},;:]', '', data)
	data = re.sub('\s+', '-', data)
	data = re.sub('[^a-z0-9_-]', '', data)
	data = re.sub('(-+|_+)', '-', data)
	if (len(str(data))<3):
		data = data + '-log'
	return data

def importMySQL(data):
	import MySQLdb
	db = MySQLdb.connect("fred.webcan.cn", "lzthrorg_fred", "slackware", "lzthrorg_fiverycom")
	c=db.cursor()
	c.execute("""INSERT INTO `wp_posts` (post_excerpt, to_ping, pinged, post_content_filtered, post_author, post_date, post_date_gmt, post_content, post_title, post_category, post_name) VALUES('',%s,'','','1', %s, %s,  %s, %s, '0', %s)""", (data[2], datetime.datetime.now().strftime("%Y-%m-%d %I:%M:%S") , datetime.datetime.now().strftime("%Y-%m-%d %I:%M:%S"), data[1], data[0], tranChinese(data[0])))
	c.execute("""SELECT id FROM `wp_posts` ORDER BY id DESC LIMIT %s""", (1,))
	lastID = c.fetchone()
	c.execute("""INSERT INTO `wp_term_relationships` (object_id, term_taxonomy_id,term_order) VALUES(%s, %s, %s)""", (lastID[0], '3', '0'))

# loop links
# get pages
f = open("url.list", "r")
lines = f.readlines()
f.close()

f = open("url.list", "r")
for url in f:
	url = re.sub('\n', '', url)
	dirPath = md5.new(url).hexdigest()
	dirPath = dirPath.rstrip()[:2]
	p = re.compile('_\d{1,2}\.html$')
	if p.search(url) is None:
		url = re.sub('(\s+|\n+)', '', url)
		print url
		getData = parsePage(str(url))
		if getData is not None:
			getData.append('http://'+url)
#			importMySQL(getData)

f.close()		
