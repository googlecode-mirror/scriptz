# Useful Tut #
## exclude directory ##

```
I've got a project on my PC with some directories:
/home/myproject/trunk/foo1
/home/myproject/trunk/foo2
/home/myproject/trunk/foo3

And they are under SVN control.

I would like to exclude 'foo3' from SVN crontrol, and want SVN not to
update it.
```

If foo3 is already under version control, and now you want it not to
be, but you still want to keep the directory in your working copy,
you can do it like this:
```
$ cp -rp foo3 foo3.bak
$ svn rm foo3
$ svn propset svn:ignore foo3 .
$ svn ci -m "Removing foo3 from version control."
$ mv foo3.bak foo3
$ svn st
```