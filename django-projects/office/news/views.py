# Create your views here.
from django.http import HttpResponse
import datetime

def homepage(request):
	now = datetime.datetime.now()
	body = 'Django project <a href="http://www.djangobook.com/en/1.0/">(book)</a>'
	html = "<html><body><h1>", body,"</h1><em>time is %s.</em></body></html>" % now
	return HttpResponse(html)
