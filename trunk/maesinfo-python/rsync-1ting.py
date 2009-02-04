#!/usr/bin/python
# -*- coding: utf-8 -*-
# $Id$
# GPLv2
# Download music file form 1ting.com
# fred@webcan.cn
# Get best editor "Emacs" on http://www.gnu.org
# FreeSoft, Opensource is everything.
# Visit http://www.linuxpk.com get more about Linux.
# Use Python! http://www.python.org
# Best music site http://www.1ting.com
#
# YOU MUST INSTALL Pycurl and BeautifulSoup FIRST!
# 
import pycurl, StringIO, re, sys, os
from BeautifulSoup import BeautifulSoup, Comment

DOWNLOAD_DIR = "/home/lenny/music/"
# Use Pycurl
def buildHeaders(browser, referer=""):
    """
    
    Arguments:
    - `browser`:
    - `referer`:
    """
    if referer != "":
        buildHeaders = ['User-Agent: ' + browser, 'Accept: text/html, application/xml;q=0.9, application/xhtml+xml, image/png, image/jpeg, image/gif, image/x-xbitmap, */*;q=0.1', 'Accept-Language: en-us', 'Accept-Encoding: deflate, gzip, x-gzip, identity, *;q=0', 'Accept-Charset: iso-8859-1, utf-8, utf-16, *;q=0.1', 'Cookie: PIN=G39J3kmH2AU0SBieDgavAg==', 'Referer:' + referer]
    else:
        buildHeaders = ['User-agent: ' + browser, 'Accept: text/html, application/xml;q=0.9, application/xhtml+xml, image/png, image/jpeg, image/gif, image/x-xbitmap, */*;q=0.1', 'Accept-Language: en-us', 'Accept-Encoding: deflate, gzip, x-gzip, identity, *;q=0', 'Accept-Charset: iso-8859-1, utf-8, utf-16, *;q=0.1', 'Cookie: PIN=G39J3kmH2AU0SBieDgavAg==']
    return buildHeaders

# Download web page
def downloadPage(url, referer=""):
    """
    
    Arguments:
    - `url`:
    """
    USER_AGENT = '"Opera/9.63 (X11; Linux i686; U; en) Presto/2.1.1"'
    theHeaders = buildHeaders(USER_AGENT, referer)
    timeout = 300
    c = pycurl.Curl()
    c.setopt(pycurl.NOSIGNAL, 1)
    c.setopt(pycurl.URL, url)
    if timeout:
        c.setopt(pycurl.CONNECTTIMEOUT, timeout)
        c.setopt(pycurl.TIMEOUT, timeout)
    c.setopt(pycurl.USERAGENT, USER_AGENT)
    c.setopt(pycurl.HTTPHEADER, theHeaders)
    c.setopt(pycurl.COOKIEJAR, 'cookies.txt')
    c.setopt(pycurl.COOKIEFILE, 'cookies.txt')
    b = StringIO.StringIO()
    c.setopt(pycurl.WRITEFUNCTION, b.write)
    c.setopt(pycurl.FOLLOWLOCATION, 1)
    c.setopt(pycurl.MAXREDIRS, 5)
    try:
        c.perform()
        ret_code = c.getinfo(pycurl.HTTP_CODE)
        value = b.getvalue()
        b.close()
        c.close()
        return value
    except pycurl.error, exc:
        if exc[0] == 7:
            print "connect time out fetch " + url
            return None
        elif exc[0] == 28:
            print "fetch time out " + url
            return None
        elif exc[0] == 3:
            print "Error URL " + url
            return None
        else:
            print "Pycurl error " + url
            return None

# download WMA file
def downloadFile(url, refURL):
    """
    
    Arguments:
    - `url`:
    """
    print "下载 " + url + "..."
    wmaFile = downloadPage(url, refURL)
    if wmaFile is None:
        print "下载 " + url + " 失败"
        return 0
    p = re.compile('([^/]+)/([^/]+)$')
    match = p.findall(str(url))
    if not os.path.isdir(DOWNLOAD_DIR + str(match[0][0])):
        os.mkdir(DOWNLOAD_DIR + str(match[0][0]))
    fWrite = open(DOWNLOAD_DIR + str(match[0][0]) + "/" + str(match[0][1]), "w")
    fWrite.write(wmaFile)
    fWrite.close()
    
# download Player Page
def downloadPlayerPage(playerURL, refURL):
    """
    """
    playerURL = "http://www.1ting.com/player/" + playerURL
    playerPage = downloadPage(playerURL, refURL)
    if playerPage is None:
        print "下载 " + playerURL + " 失败"
        return 0
    soup = BeautifulSoup(playerPage)
    content = soup.find('div', id="MediaWrapper")
    if content is None:
        print "下载 " + playerURL + " 失败"
        return 0
    p = re.compile('<param name="url" value="(.+?)"')
    match = p.findall(str(content))
    downloadFile(match[0], playerURL)

# download Wma
def downloadWma(url):
    """
    
    Arguments:
    - `url`:
    """
    # download album page
    albumPage = downloadPage(url)
    if albumPage is None:
        return None
    soup = BeautifulSoup(albumPage)
    for script in soup("script"):
        soup.script.extract()
    # 选择一个HTML区域
    content = soup.find('tbody', id="albumSongs")
    p = re.compile('/([^/]+/[^/]+\.html)">')
    if content is None:
        content = soup.find('ul', id="list-1")
        p = re.compile('/([^/]+/[^/]+\.html)" />')
        if content is None:
            return None
    
    matchs = p.findall(str(content))
    if matchs != None:
        for playerURL in matchs:
            downloadPlayerPage(playerURL, url)
    else:
        return None
    return 1


# Main
if (len(sys.argv) is not 2):
    print u"使用专辑页URL作为唯一参数"
else:
    if downloadWma(sys.argv[1]) is not None:    
        print u"完成"
    else:
        print u"下载错误，请检查URL"
