from django.conf import settings
from django.conf.urls.defaults import *
print 'z'
exit

urlpatterns = patterns('',
					   ('^$', 'news.views.main.index'),
					   )
