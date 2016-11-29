include /etc/firejail/default.profile

private-etc alternatives

private-bin bash,sed,ls,cat,gcc,cut,find,dirname,basename,as,ld

noblacklist /tmp/firejail

blacklist ${PATH}/ifconfig
blacklist ${PATH}/exit
blacklist ${HOME}/.ssh

noblacklist /var/www/html
noblacklist /var/www
noblacklist /var/run
blacklist /var/*