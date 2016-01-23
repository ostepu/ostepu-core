include /etc/firejail/generic.profile

private-etc /alternatives

private-bin bash,sed,ls,cat,gcc

noblacklist /var/www/html
noblacklist /var/www


blacklist /var/www/*
blacklist /var/www/html/*

blacklist ${PATH}/ifconfig
blacklist ${PATH}/exit
blacklist ${HOME}/.ssh

blacklist /var/*