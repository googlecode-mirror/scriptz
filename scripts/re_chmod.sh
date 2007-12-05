#!/bin/sh

for i in `find $HOME/$1`
	do
		if [ -d $i ]
			then
			chmod 744 $i
		else if [ -f $i ]
			then
			chmod 644 $i
			fi
		fi
	done	
