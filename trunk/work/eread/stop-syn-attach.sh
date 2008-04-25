#!/bin/bash

# Set default policies

/sbin/iptables -P INPUT ACCEPT

/sbin/iptables -P OUTPUT ACCEPT

/sbin/iptables -P FORWARD DROP



/sbin/iptables -F

/sbin/iptables -F INPUT

/sbin/iptables -F OUTPUT

/sbin/iptables -F FORWARD

/sbin/iptables -F -t mangle

/sbin/iptables -X



/sbin/iptables -A INPUT -i lo -j ACCEPT

/sbin/iptables -A INPUT -d 127.0.0.0/8 -j REJECT



/sbin/iptables -A INPUT -i eth0 -j ACCEPT



/sbin/iptables -A INPUT -m state --state INVALID -j DROP



### chains to DROP too many SYN-s ######

/sbin/iptables -N syn-flood

/sbin/iptables -A syn-flood -m limit --limit 100/second --limit-burst 150 -j RETURN

/sbin/iptables -A syn-flood -j LOG --log-prefix "SYN flood: "

/sbin/iptables -A syn-flood -j DROP