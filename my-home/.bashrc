# Add my path
export PATH="$HOME/bin:$HOME/scripts:/usr/sbin:/sbin:$PATH"
# System-wide .bashrc file for interactive bash(1) shells.
#
 
shopt -s checkwinsize
 
### In order to have consistent feel over accounts, 
### I customize this file instead of ~/.bashrc
 
PS1='\u@\h:\W\$ '
 
# Some more alias to avoid making mistakes:
alias rm='rm -i'
alias cp='cp -i'
alias mv='mv -i'
 
# short commands
 
alias h='history'
alias j="jobs -l"
alias c="clear"
alias m="less"
alias pu="pushd"
alias po="popd"
# You may comment the following lines if you do not want `ls' to be 
export LS_OPTIONS='--color=auto'
eval `dircolors` # set LS_COLORS
alias ll='ls $LS_OPTIONS -lbF'
alias l='ls $LS_OPTIONS -AbF'
alias ls='ls $LS_OPTIONS'
# useful functions for mc
 
# does not do ctrl-Z
# mc() { cd $(/usr/bin/mc -P "$@"); }
# use secured temp-file (This is for Potato)
#mc ()
#{
# mkdir -p ~/.mc/tmp 2> /dev/null
# chmod 700 ~/.mc/tmp
# MC=~/.mc/tmp/mc-$$
# /usr/bin/mc -P "$@" > "$MC"
# cd "$(cat $MC)"
# rm -f "$MC"
# unset MC;
#}
# Sid
#

# fix SSH hang wait?
# shopt -s huponexit

if [ -f /usr/share/mc/bin/mc.sh ] ; then
. /usr/share/mc/bin/mc.sh
fi
 
# Woody
if [ -f /usr/lib/mc/bin/mc.sh ] ; then
. /usr/lib/mc/bin/mc.sh
fi

# term to rxvt
if [ "$TERM" = "rxvt-unicode" ]; then
		TERM="rxvt"
		export TERM
fi		

# Set up the LS_COLORS environment:
if [ -f $HOME/.dir_colors ]; then
  eval `/bin/dircolors -b $HOME/.dir_colors`
elif [ -f /etc/DIR_COLORS ]; then
  eval `/bin/dircolors -b /etc/DIR_COLORS`
else
  eval `/bin/dircolors -b`
fi

export PATH=$PATH:"/opt/UNA-1.0"
