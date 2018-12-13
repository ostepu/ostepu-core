<!--
  - @file de.md
  -
  - @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
  -
  - @package OSTEPU (https://github.com/ostepu/ostepu-core)
  - @since 0.3.4
  -
  - @author Till Uhlig <till.uhlig@student.uni-halle.de>
  - @date 2015,2017
  -
 -->

Die DBSession ermöglicht den Zugriff auf die `Session` Tabelle der Datenbank, dabei sollen Sitzungen verwaltet werden. Dazu wird bei einem `POST /platform` Aufruf die nachstehende Tabelle erzeugt. Zu dieser Tabelle gehört die `Session` Datenstruktur.

| Spalte     | Struktur        | Beschreibung | Besonderheit |
| :------    |:---------:      | :------------| -----------: |
|U_id        |INT NOT NULL     | die ID des zugehörigen Nutzers |UNIQUE|
|SE_sessionID|CHAR(32) NOT NULL| ein md5 Hash, welcher die Sitzung identifiziert |-|

| Themen |
| :- |
| [Befehle/Eingänge (Commands.json)](#eingaenge) |
| [Ausgänge (Component.json => Links)](#ausgaenge) |
| [Anbindungen (Component.json => Connector)](#anbindungen) |

## <a name='eingaenge'></a>Befehle/Eingänge (Commands.json)
Diese Befehle bietet diese Komponente als Aufruf an.

||editSession|
| :----------- |:----- |
|Beschreibung| editiert eine Session|
|Befehl| PUT /session/session/:seid|
|Eingabetyp| Session|
|Ausgabetyp| Session|
|||
||Patzhalter|
|Name|seid|
|Regex|%^[0-9A-Fa-f]{32}$%|
|Beschreibung|die ID einer Sitzung (`Session`)|

||deleteSession|
| :----------- |:----- |
|Beschreibung| entfernt eine Session|
|Befehl| DELETE /session/session/:seid|
|Eingabetyp| -|
|Ausgabetyp| Session|
|||
||Patzhalter|
|Name|seid|
|Regex|%^[0-9A-Fa-f]{32}$%|
|Beschreibung|die ID einer Sitzung (`Session`)|

||postSamples|
| :----------- |:----- |
|Beschreibung| erzeugt Zufallsdaten (courseAmount = Anzahl der Veranstaltungen, userAmount = Anzahl der Nutzer), anhand der Vorgabe|
|Befehl| POST /samples/:amount|
|Eingabetyp| -|
|Ausgabetyp| -|

||getUserSession|
| :----------- |:----- |
|Beschreibung| liefert die Session eines Nutzers|
|Befehl| GET /session/user/:userid|
|Eingabetyp| -|
|Ausgabetyp| Session|
|||
||Patzhalter|
|Name|userid|
|Regex|%^[a-zA-Z0-9äöüÄÖÜß]+$%|
|Beschreibung|die ID eines Nutzers oder ein Nuzername (`User`)|

||deleteUserSession|
| :----------- |:----- |
|Beschreibung| entfernt die Session eines Nutzers|
|Befehl| entfernt die Session eines Nutzers /session/user/:userid|
|Eingabetyp| -|
|Ausgabetyp| Session|
|||
||Patzhalter|
|Name|userid|
|Regex|%^[a-zA-Z0-9äöüÄÖÜß]+$%|
|Beschreibung|die ID eines Nutzers oder ein Nuzername (`User`)|

||getExistsPlatform|
| :----------- |:----- |
|Beschreibung| prüft, ob die Tabelle und die Prozeduren existieren und die Komponente generell vollständig installiert ist|
|Befehl| GET /link/exists/platform|
|Eingabetyp| -|
|Ausgabetyp| Platform|

||addSession|
| :----------- |:----- |
|Beschreibung| fügt eine Session ein (wenn der Eintrag schon existiert, dann wird er aktualisiert/überschrieben)|
|Befehl| POST /session|
|Eingabetyp| Session|
|Ausgabetyp| Session|

||getAllSessions|
| :----------- |:----- |
|Beschreibung| ermittelt alle Sessions|
|Befehl| GET /session|
|Eingabetyp| -|
|Ausgabetyp| Session|

||getValidSession|
| :----------- |:----- |
|Beschreibung| gibt einen Eintrag zurück, wenn die Session und der Nutzer zusammen passen|
|Befehl| GET /session/session/:seid/user/:userid|
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
| :----------- |:----- |
|Beschreibung| editiert die Session eines Nutzers|
|Befehl| PUT /session/user/:userid|
|Eingabetyp| Session|
|Ausgabetyp| Session|
|||
||Patzhalter|
|Name|userid|
|Regex|%^[a-zA-Z0-9äöüÄÖÜß]+$%|
|Beschreibung|die ID eines Nutzers oder ein Nuzername (`User`)|

||deletePlatform|
| :----------- |:----- |
|Beschreibung| entfernt die Komponente und ihre installierten Bestandteile aus der Plattform|
|Befehl| DELETE /platform|
|Eingabetyp| -|
|Ausgabetyp| Platform|

||getSessionUser|
| :----------- |:----- |
|Beschreibung| liefert die Session anhand der Session-ID|
|Befehl| GET /session/session/:seid|
|Eingabetyp| -|
|Ausgabetyp| Session|
|||
||Patzhalter|
|Name|seid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID einer Sitzung (`Session`)|

||getSamplesInfo|
| :----------- |:----- |
|Beschreibung| liefert die Bezeichner der betroffenen Tabellen|
|Befehl| GET /samples|
|Eingabetyp| -|
|Ausgabetyp| -|

||addPlatform|
| :----------- |:----- |
|Beschreibung| installiert die zugehörige Tabelle und die Prozeduren für diese Plattform|
|Befehl| POST /platform|
|Eingabetyp| Platform|
|Ausgabetyp| Platform|

||getApiProfiles|
| :----------- |:----- |
|Beschreibung| liefert `GateProfile`-Objekte, welche unsere Befehle in die Standardprofile von CGate einsortieren|
|Befehl| GET /api/profiles|
|Eingabetyp| -|
|Ausgabetyp| GateProfile|


## <a name='ausgaenge'></a>Ausgänge (Component.json => Links)
Wenn eine Komponente selbst noch Unteranfragen an andere Komponenten stellen möchte, dann werden diese über die `Ausgänge` bearbeitet.
Dabei kann ein Ausgang bereits auf eine Komponente gerichtet sein (`Ziel`) oder durch die Zielkomponente selbst angebunden werden (`Connector`)

||editSession|
| :----------- |:----- |
|Ziel| DBQueryWrite|
|Befehl| POST /query|
|Beschreibung| für den Befehl editSession|

||deleteSession|
| :----------- |:----- |
|Ziel| DBQueryWrite|
|Befehl| POST /query|
|Beschreibung| für den Befehl deleteSession|

||editUserSession|
| :----------- |:----- |
|Ziel| DBQueryWrite|
|Befehl| POST /query|
|Beschreibung| für den Befehl editUserSession|

||deleteUserSession|
| :----------- |:----- |
|Ziel| DBQueryWrite|
|Befehl| POST /query|
|Beschreibung| für den Befehl deleteUserSession|

||addSession|
| :----------- |:----- |
|Ziel| DBQueryWrite|
|Befehl| POST /query|
|Beschreibung| für den Befehl addSession|

||deletePlatform|
| :----------- |:----- |
|Ziel| DBQuerySetup|
|Befehl| POST /query|
|Beschreibung| für den Befehl deletePlatform|

||addPlatform|
| :----------- |:----- |
|Ziel| DBQuerySetup|
|Befehl| POST /query|
|Beschreibung| für den Befehl addPlatform|

||postSamples|
| :----------- |:----- |
|Ziel| DBQueryWrite|
|Befehl| POST /query|
|Beschreibung| für den Befehl postSamples|

||getUserSession|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBSessionGetUserSession/:profile/:userid|
|Beschreibung| für den Befehl getUserSession|

||getSessionUser|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBSessionGetSessionUser/:profile/:seid|
|Beschreibung| für den Befehl getSessionUser|

||getValidSession|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBSessionGetValidSession/:profile/:seid/:userid|
|Beschreibung| für den Befehl getValidSession|

||getAllSessions|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBSessionGetAllSessions/:profile|
|Beschreibung| für den Befehl getAllSessions|

||getExistsPlatform|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBSessionGetExistsPlatform/:profile|
|Beschreibung| für den Befehl getExistsPlatform|

||getSamplesInfo|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBSessionGetExistsPlatform/:profile|
|Beschreibung| für den Befehl getSamplesInfo|


## <a name='anbindungen'></a>Anbindungen (Component.json => Connector)
Eine Anbindung verlangt von einer anderen Komponente (`Ziel`) die Anbindung/Verbindung zu dieser Komponente.
Wenn eine Anbindung den aufzurufenden Befehl vorgibt, dann ist die Notation: METHODE URL (PRIORITÄT).

|Ausgang|request|
| :----------- |:----- |
|Ziel| CLocalObjectRequest|
|Beschreibung| damit DBSession als lokales Objekt aufgerufen werden kann|

|Ausgang|postPlatform|
| :----------- |:----- |
|Ziel| CInstall|
|Beschreibung| der Installationsassistent soll uns bei der Plattforminstallation aufrufen|

|Ausgang|getDescFiles|
| :----------- |:----- |
|Ziel| TDocuView|
|Beschreibung| die Entwicklerdokumentation soll unsere Beschreibungsdatei nutzen|

|Ausgang|getComponentProfiles|
| :----------- |:----- |
|Ziel| TApiConfiguration|
|Beschreibung| damit unsere Aufrufe in die Standardprofile der CGate einsortiert werden|


Stand 25.07.2017
