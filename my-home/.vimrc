" VIM Editor Config File
" $Id$
" fred@fivery.com
"
set ai nocp digraph ek hid ru sc wmnu noet nosol si
set bs=2 fo=cqrt ls=2 shm=at ww=,h,l ts=4 sw=4
set com=s1:/*,mb:*,ex:*/,://,b:# syn=on filetype=on
set vi=%,'50,\"50,:50 lcs=tab:>-,trail:.,extends:>
set pt= shm=I tm=750 nomore modelines=5 hls!
set	tabstop=4
set expandtab
set nobackup nowritebackup
syn on

" ino gj
" ino gk
" nno gj
" nno gk
 
nno :set hls!set hls?
nno :syn clear 
nno :set nu!set nu?
 
if has("gui_running")
colo darkblue
set gfn=Monaco\ 12
else
" colo ubuntu
 colo desert
" colo elflord 
endif
