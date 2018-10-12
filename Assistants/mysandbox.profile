noblacklist /sbin/*
noblacklist /usr/sbin/*

include /etc/firejail/default.profile

private-etc alternatives,maven,hosts

#private-bin bash,tcsh,sed,ls,cat,gcc,cut,find,dirname,basename,as,ld,unzip

noblacklist /tmp/firejail

blacklist ${PATH}/ifconfig
blacklist ${PATH}/exit
blacklist ${HOME}/.ssh

noblacklist /var/www/html
noblacklist /var/www
noblacklist /var/run
blacklist /var/*

noblacklist /srv/zubehoer/*
noblacklist /usr/bin/*
noblacklist /usr/local/bin/*
noblacklist /tmp/*
noblacklist /usr/share/*

