include /etc/firejail/generic.profile

private-etc /alternatives

private-bin bash,sed,ls,cat,gcc,cut,find,dirname,basename,as,ld

noblacklist /var/www/html
noblacklist /var/www
noblacklist /tmp/firejail


blacklist /var/www/*
blacklist /var/www/html/*

blacklist ${PATH}/ifconfig
blacklist ${PATH}/exit
blacklist ${HOME}/.ssh

blacklist /var/*