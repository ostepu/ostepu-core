<!--
  - @file README.md
  -
  - @author Ivo Hedtke <ivo.hedtke@uni-osnabrueck.de>
  - @date 2014
 -->

# OSTEPU: Open Source Tutorial and Exercise Platform for Universities

Martin-Luther-University Halle-Wittenberg, Institute of Computer Science  
Osnabrück University, Institute of Computer Science

# Installation
Sie benötigen für die Installation einen Apache mit PHP 5.6, MySQL und Git. Die Dateien der Plattform müssen Sie in das Hauptverzeichnis Ihres Webservers entpacken oder mittels Git (empfohlen) dort einrichten. Nun finden Sie die Installationsroutine in diesem Verzeichnis, unter ``install/install.php``, welche Sie über einen Browser aufrufen müssen.

Bei der Installation werden externe Inhalte nachgeladen, sodass Sie insgesamt mit einem Umfang von maximal 200MB rechnen sollten.

Zur Unterstützung steht eine veraltete [Installationsanleitung](https://github.com/ostepu/ostepu-core-documentation/raw/master/install/Dokumentation/Installation_08_2014.pdf) bereit. (Stand: August 2014)

Wenn Sie weitere Hilfe benötigen, können Sie die [Videobeiträge zum Installationsassistenten](https://www.youtube.com/playlist?list=PLfnTtQX6vUn2CB4OhQ5cqlqDvAFPbfRr4) aufsuchen.

# Verwendung
Dazu bietet das [Dokumentationsverzeichnis](https://github.com/ostepu/ostepu-core-documentation) eine Reihe von Übersichten,
welche die Sachverhalte [Gruppenarbeit](https://github.com/ostepu/ostepu-core-documentation/raw/master/Common/Gruppen/Gruppen.pdf), 
[Einsendungsbewertungen](https://github.com/ostepu/ostepu-core-documentation/raw/master/Common/Korrektur/Korrektur.pdf), 
[Kontrolleurzuweisung](https://github.com/ostepu/ostepu-core-documentation/raw/master/Common/Kontrolleurzuweisung/Kontrolleurzuweisung.pdf) und 
[Eingabemasken](https://github.com/ostepu/ostepu-core-documentation/raw/master/logic/Dokumentation/Benutzerhandbuch.pdf) behandeln.

Zudem können Sie sich die [Videos](https://www.youtube.com/playlist?list=PLfnTtQX6vUn2lHxmo2WqLsPaEZihOEczh), zur Verwendung der Plattform, ansehen.

# Docker

Die Docker-Installtion vereinfacht viele Einrichtungs-Prozesse und ist deshalb nicht für öffentlich zugängliche oder Produktivumgebungen geeignet. Sie eignet sich aber hervorrand, den gesamten Code von einer Entwicklungsmaschine zu isolieren, so dass eine Ausführung ohne vorherigen Code-Review risoärmer ist.

0. `cp .env.example .env`
1. `docker-compose up -d app`
2. `firefox http://localhost/install/install.php`

* interne URL: http://localhost
* lokaler Pfad: /var/www/html
* externe URL: http://localhost
* temporäres Verzeichnis: /tmp
* Dateiverzeichnis: /var/www/files

* Datenbankpfad: db
* Datenbankname: ostepu

* Datenbankadministrator: root
* Passwort: change_me_root

* Plattform-Datenbanknutzer: ostepu
* Passwort: change_me_ostepu

