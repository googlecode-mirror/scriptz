from django.conf.urls.defaults import *
from news.views import homepage

urlpatterns = patterns('',
    # Example:
    # (r'^office/', include('office.foo.urls')),
	(r'^$', homepage),
    # Uncomment this for admin:
    (r'^admin/', include('django.contrib.admin.urls')),
)
