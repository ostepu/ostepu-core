<!--
  - @file de.md
  -
  - @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
  -
  - @package OSTEPU (https://github.com/ostepu/ostepu-core)
  - @since 0.3.4
  -
  - @author Till Uhlig <till.uhlig@student.uni-halle.de>
  - @date 2015
  -
 -->

Die DBSession ermöglicht den Zugriff auf die `Session` Tabelle der Datenbank, dabei sollen Sitzungen verwaltet werden. Dazu wird bei einem `POST /platform` Aufruf die nachstehende Tabelle erzeugt. Zu dieser Tabelle gehört die `Session` Datenstruktur.

| Spalte     | Struktur        | Beschreibung | Besonderheit |
| :------    |:---------:      | :------------| -----------: |
|U_id        |INT NOT NULL     | die ID des zugehörigen Nutzers |UNIQUE|
|SE_sessionID|CHAR(32) NOT NULL| ein md5 Hash, welcher die Sitzung identifiziert |-|

## Eingänge
---------------

||editSession|
| :----------- |:-----: |
|Beschreibung| editiert eine Session|
|Befehl| PUT<br>/session/session/:seid|
|Eingabetyp| Session|
|Ausgabetyp| Session|
|||
||Patzhalter|
|Name|seid|
|Regex|%^[0-9A-Fa-f]{32}$%|
|Beschreibung|die ID einer Sitzung (`Session`)|

||deleteSession|
| :----------- |:-----: |
|Beschreibung| entfernt eine Session|
|Befehl| DELETE<br>/session/session/:seid|
|Eingabetyp| -|
|Ausgabetyp| Session|
|||
||Patzhalter|
|Name|seid|
|Regex|%^[0-9A-Fa-f]{32}$%|
|Beschreibung|die ID einer Sitzung (`Session`)|

||postSamples|
| :----------- |:-----: |
|Beschreibung| erzeugt Zufallsdaten (courseAmount = Anzahl der Veranstaltungen, userAmount = Anzahl der Nutzer), anhand der Vorgabe|
|Befehl| POST<br>/samples/:amount|
|Eingabetyp| -|
|Ausgabetyp| -|

||getUserSession|
| :----------- |:-----: |
|Beschreibung| liefert die Session eines Nutzers|
|Befehl| GET<br>/session/user/:userid|
|Eingabetyp| -|
|Ausgabetyp| Session|
|||
||Patzhalter|
|Name|userid|
|Regex|%^[a-zA-Z0-9äöüÄÖÜß]+$%|
|Beschreibung|die ID eines Nutzers oder ein Nuzername (`User`)|

||deleteUserSession|
| :----------- |:-----: |
|Beschreibung| entfernt die Session eines Nutzers|
|Befehl| entfernt die Session eines Nutzers<br>/session/user/:userid|
|Eingabetyp| -|
|Ausgabetyp| Session|
|||
||Patzhalter|
|Name|userid|
|Regex|%^[a-zA-Z0-9äöüÄÖÜß]+$%|
|Beschreibung|die ID eines Nutzers oder ein Nuzername (`User`)|

||getExistsPlatform|
| :----------- |:-----: |
|Beschreibung| prüft, ob die Tabelle und die Prozeduren existieren und die Komponente generell vollständig installiert ist|
|Befehl| GET<br>/link/exists/platform|
|Eingabetyp| -|
|Ausgabetyp| Platform|

||addSession|
| :----------- |:-----: |
|Beschreibung| fügt eine Session ein (wenn der Eintrag schon existiert, dann wird er aktualisiert/überschrieben)|
|Befehl| POST<br>/session|
|Eingabetyp| Session|
|Ausgabetyp| Session|

||getAllSessions|
| :----------- |:-----: |
|Beschreibung| ermittelt alle Sessions|
|Befehl| GET<br>/session|
|Eingabetyp| -|
|Ausgabetyp| Session|

||getValidSession|
| :----------- |:-----: |
|Beschreibung| gibt einen Eintrag zurück, wenn die Session und der Nutzer zusammen passen|
|Befehl| GET<br>/session/session/:seid/user/:userid|
|Eingabetyp| -|
|Ausgabetyp| Session|
|||
||Patzhalter|
|Name|seid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID einer Sitzung (`Session`)|
|Name|userid|
|Regex|%^[a-zA-Z0-9äöüÄÖÜß]+$%|
|Beschreibung|die ID eines Nutzers oder ein Nuzername (`User`)|

||editUserSession|
| :----------- |:-----: |
|Beschreibung| editiert die Session eines Nutzers|
|Befehl| PUT<br>/session/user/:userid|
|Eingabetyp| Session|
|Ausgabetyp| Session|
|||
||Patzhalter|
|Name|userid|
|Regex|%^[a-zA-Z0-9äöüÄÖÜß]+$%|
|Beschreibung|die ID eines Nutzers oder ein Nuzername (`User`)|

||deletePlatform|
| :----------- |:-----: |
|Beschreibung| entfernt die Komponente und ihre installierten Bestandteile aus der Plattform|
|Befehl| DELETE<br>/platform|
|Eingabetyp| -|
|Ausgabetyp| Platform|

||getSessionUser|
| :----------- |:-----: |
|Beschreibung| liefert die Session anhand der Session-ID|
|Befehl| GET<br>/session/session/:seid|
|Eingabetyp| -|
|Ausgabetyp| Session|
|||
||Patzhalter|
|Name|seid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID einer Sitzung (`Session`)|

||getSamplesInfo|
| :----------- |:-----: |
|Beschreibung| liefert die Bezeichner der betroffenen Tabellen|
|Befehl| GET<br>/samples|
|Eingabetyp| -|
|Ausgabetyp| -|

||addPlatform|
| :----------- |:-----: |
|Beschreibung| installiert die zugehörige Tabelle und die Prozeduren für diese Plattform|
|Befehl| POST<br>/platform|
|Eingabetyp| Platform|
|Ausgabetyp| Platform|

||getApiProfiles|
| :----------- |:-----: |
|Beschreibung| liefert `GateProfile`-Objekte, welche unsere Befehle in die Standardprofile von CGate einsortieren|
|Befehl| GET<br>/api/profiles|
|Eingabetyp| -|
|Ausgabetyp| GateProfile|


## Ausgänge
---------------

||editSession|
| :----------- |:-----: |
|Ziel| DBQueryWrite|
|Befehl| POST<br>/query|
|Beschreibung| für den Befehl editSession|

||deleteSession|
| :----------- |:-----: |
|Ziel| DBQueryWrite|
|Befehl| POST<br>/query|
|Beschreibung| für den Befehl deleteSession|

||editUserSession|
| :----------- |:-----: |
|Ziel| DBQueryWrite|
|Befehl| POST<br>/query|
|Beschreibung| für den Befehl editUserSession|

||deleteUserSession|
| :----------- |:-----: |
|Ziel| DBQueryWrite|
|Befehl| POST<br>/query|
|Beschreibung| für den Befehl deleteUserSession|

||addSession|
| :----------- |:-----: |
|Ziel| DBQueryWrite|
|Befehl| POST<br>/query|
|Beschreibung| für den Befehl addSession|

||deletePlatform|
| :----------- |:-----: |
|Ziel| DBQuerySetup|
|Befehl| POST<br>/query|
|Beschreibung| für den Befehl deletePlatform|

||addPlatform|
| :----------- |:-----: |
|Ziel| DBQuerySetup|
|Befehl| POST<br>/query|
|Beschreibung| für den Befehl addPlatform|

||postSamples|
| :----------- |:-----: |
|Ziel| DBQueryWrite|
|Befehl| POST<br>/query|
|Beschreibung| für den Befehl postSamples|

||getUserSession|
| :----------- |:-----: |
|Ziel| DBQueryRead|
|Befehl| GET<br>/query/procedure/DBSessionGetUserSession/:profile/:userid|
|Beschreibung| für den Befehl getUserSession|

||getSessionUser|
| :----------- |:-----: |
|Ziel| DBQueryRead|
|Befehl| GET<br>/query/procedure/DBSessionGetSessionUser/:profile/:seid|
|Beschreibung| für den Befehl getSessionUser|

||getValidSession|
| :----------- |:-----: |
|Ziel| DBQueryRead|
|Befehl| GET<br>/query/procedure/DBSessionGetValidSession/:profile/:seid/:userid|
|Beschreibung| für den Befehl getValidSession|

||getAllSessions|
| :----------- |:-----: |
|Ziel| DBQueryRead|
|Befehl| GET<br>/query/procedure/DBSessionGetAllSessions/:profile|
|Beschreibung| für den Befehl getAllSessions|

||getExistsPlatform|
| :----------- |:-----: |
|Ziel| DBQueryRead|
|Befehl| GET<br>/query/procedure/DBSessionGetExistsPlatform/:profile|
|Beschreibung| für den Befehl getExistsPlatform|

||getSamplesInfo|
| :----------- |:-----: |
|Ziel| DBQueryRead|
|Befehl| GET<br>/query/procedure/DBSessionGetExistsPlatform/:profile|
|Beschreibung| für den Befehl getSamplesInfo|


## Anbindungen
---------------

|Ausgang|request|
| :----------- |:-----: |
|Ziel| CLocalObjectRequest|
|Beschreibung| damit DBSession als lokales Objekt aufgerufen werden kann|

|Ausgang|postPlatform|
| :----------- |:-----: |
|Ziel| CInstall|
|Beschreibung| der Installationsassistent soll uns bei der Plattforminstallation aufrufen|

|Ausgang|getDescFiles|
| :----------- |:-----: |
|Ziel| TDocuView|
|Beschreibung| die Entwicklerdokumentation soll unsere Beschreibungsdatei nutzen|

|Ausgang|getComponentProfiles|
| :----------- |:-----: |
|Ziel| TApiConfiguration|
|Beschreibung| damit unsere Aufrufe in die Standardprofile der CGate einsortiert werden|


Stand 30.06.2017
