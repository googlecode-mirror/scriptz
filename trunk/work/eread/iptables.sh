#!/bin/sh
#防止SYN攻击 轻量
/sbin/iptables -N syn-flood
/sbin/iptables -A INPUT -p tcp --syn -j syn-flood
/sbin/iptables -I syn-flood -p tcp -m limit --limit 3/s --limit-burst 6 -j RETURN
/sbin/iptables -A syn-flood -j REJECT

#为了防止DOS太多连接进来,那么可以允许最多15个初始连接,超过的丢弃
/sbin/iptables -A INPUT -s $INNET -p tcp -m state --state ESTABLISHED,RELATED -j ACCEPT
/sbin/iptables -A INPUT -i $EXTIF -p tcp --syn -m connlimit --connlimit-above 15 -j DROP
/sbin/iptables -A INPUT -s $INNET -p tcp --syn -m connlimit --connlimit-above 15 -j DROP

#设置icmp阔值 ,并对攻击者记录在案
/sbin/iptables -A INPUT -p icmp -m limit --limit 3/s -j LOG --log-level INFO --log-prefix "ICMP packet IN:"
/sbin/iptables -A INPUT -p icmp -m limit --limit 6/m -j ACCEPT
/sbin/iptables -A INPUT -p icmp -j DROP

# 防止端口扫描
/sbin/iptables -I INPUT -p icmp --icmp-type echo-request -m state --state NEW -j DROP
#禁止PING

/sbin/iptables -A INPUT -p tcp --tcp-flags ALL FIN,URG,PSH -j DROP
#标志为FIN，URG，PSH拒绝

/sbin/iptables -A INPUT -p tcp --tcp-flags SYN,RST SYN,RST -j DROP
/sbin/iptables -A INPUT -p tcp --tcp-flags SYN,FIN SYN,FIN -j DROP
/sbin/iptables -A INPUT -p tcp --tcp-flags ALL ALL -j DROP
/sbin/iptables -A INPUT -p tcp --tcp-flags ALL SYN,RST,ACK,FIN,URG -j DROP
/sbin/iptables -A INPUT -p tcp --tcp-flags ALL NONE -j DROP

# 防止SYN FLOOD
/sbin/iptables -N synfoold
/sbin/iptables -A synfoold -p tcp --syn -m limit --limit 1/s -j RETURN
/sbin/iptables -A synfoold -p tcp -j REJECT --reject-with tcp-reset
/sbin/iptables -A INPUT -p tcp -m state --state NEW -j synfoold
# 禁止PING

#iptables -N ping
#iptables -A ping -p icmp --icmp-type echo-request -m limit --limit 1/second -j RETURN
#iptables -A ping -p icmp -j REJECT
#iptables -I INPUT -p icmp --icmp-type echo-request -m state --state NEW -j ping 
/sbin/iptables -A INPUT -i eth0 -p tcp --dport 22 -j ACCEPT
/sbin/iptables -A INPUT -i eth0 -p tcp --dport 80 -j ACCEPT
