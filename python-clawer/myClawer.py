#!/usr/bin/python
# $Id$
# fred@localhost
import pycurl, StringIO, time, datetime, re, sys, os, urllib, stat, md5
import time, random
from BeautifulSoup import BeautifulSoup, Comment

def buildHeaders(browser, referer=''):
    """
    Build HTTP Headers
    Arguments:
    - `browser`: User Agent
    - `referer`: HTTP Referer
    """
    if referer != "":
        buildHeaders = ['User-agent: ' + browser,'Accept-Language: en-us', 'Accept-Encoding:gzip,compress;q=0.9,deflate;q=0', 'Referer:' + referer]
    else:
        buildHeaders = ['User-agent: ' + browser,'Accept-Language: en-us', 'Accept-Encoding:gzip,compress;q=0.9,deflate;q=0']
    return buildHeaders

def getFile(url):
    """
    Get file function here.
    Arguments:
    - `url`: This is a file URL you must put
    """
    if (re.match('^http://', url) is None):
        USER_AGENT = '"Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.0.6) Gecko/20060728 Firefox/1.5.0.6"'
        theHeaders = buildHeaders(USER_AGENT)
        timeout = 300
        c = pycurl.Curl()
        c.setopt(pycurl.NOSIGNAL, 1)
        c.setopt(pycurl.URL, url)
        if timeout:
            c.setop(pycurl.CONNECTTIMEOUT, timeout)
            c.setop(pycurl.TIMEOUT, timeout)
        c.setopt(pycurl.USERAGENT, USER_AGENT)
        c.setopt(pycurl.HTTPHEADER, theHeaders)
        c.setopt(pycurl.COOKIEFILE, '')
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
                print 'Connect time out fetch ' + url
                return None
            elif exc[0] == 28:
                print 'Fetch file time out ' + url
                return None
            elif exc[0] == 3:
                print 'Error URL format ' + url
                return None
            else:
                print exc[0]
                print 'Pycurl error ' + url
                return None
