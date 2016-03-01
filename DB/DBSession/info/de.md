<!--
 * @file de.md
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
-->

#### Datenbank
Die DBSession ermöglicht den Zugriff auf die `Session` Tabelle der Datenbank, dabei sollen
Sitzungen verwaltet werden.
Dazu wird bei einem `POST /platform` Aufruf die nachstehende Tabelle erzeugt.

| Spalte     | Struktur        | Beschreibung | Besonderheit |
| :------    |:---------:      | :------------| -----------: |
|U_id        |INT NOT NULL     | die ID des zugehörigen Nutzers |UNIQUE|
|SE_sessionID|CHAR(32) NOT NULL| ein md5 Hash, welcher die Sitzung identifiziert |-|

#### Datenstruktur
Zu dieser Tabelle gehört die `Session` Datenstruktur.

#### Eingänge
- userid = die ID eines Nutzers oder ein Nuzername (`User`)
- seid = die ID einer Sitzung (`Session`)

| Bezeichnung  | Eingabetyp  | Ausgabetyp | Befehl | Beschreibung |
| :----------- |:-----------:| :---------:| :----- | :----------- |
|editSession|Session|Session|PUT<br>/session(/session)/:seid| editiert eine existierende Sitzung |
|deleteSession|-|Session|DELETE<br>/session(/session)/:seid| entfernt eine Sitzung |
|editUserSession|Session|Session|PUT<br>/session/user/:userid| editiert die Sitzung eines Nutzers |
|deleteUserSession|-|Session|DELETE<br>/session/user/:userid| entfernt die Sitzung eines Nutzers |
|addSession|Session|Session|POST<br>/session| fügt eine neue Sitzung ein (sofern bereits eine existiert, wird diese überschrieben) |
|getAllSessions|-|Session|GET<br>/session(/session)| liefert alle Sitzungen |
|getUserSession|-|Session|GET<br>/session/user/:userid| die Sitzung eines Nutzers |
|getSessionUser|-|Session|GET<br>/session(/session)/:seid| die Sitzungsdaten zu einer Sitzungs-ID |
|addPlatform|Platform|Platform|POST<br>/platform|installiert dies zugehörige Tabelle und die Prozeduren für diese Plattform|
|deletePlatform|-|Platform|DELETE<br>/platform|entfernt die Tabelle und Prozeduren aus der Plattform|
|getExistsPlatform|-|Platform|GET<br>/link/exists/platform| prüft, ob die Tabelle und die Prozeduren existieren |
|getSamplesInfo|-|-|GET<br>/samples| ??? |

#### Ausgänge
- userid = die ID eines Nutzers oder ein Nuzername (`User`)
- seid = die ID einer Sitzung (`Session`)

| Bezeichnung  | Ziel  | Verwendung | Beschreibung |
| :----------- |:----- | :--------- | :----------- |
|out2|DBQuery2|POST<br>/query| wird für EDIT, DELETE<br>und POST<br>SQL-Templates verwendet |
|out2|DBQuery|POST<br>/query| wird für EDIT, DELETE<br>und POST<br>SQL-Templates verwendet |
|getUserSession|DBQuery2|GET<br>/query/procedure<br>/DBSessionGetUserSession/:userid| Prozeduraufruf |
|getSessionUser|DBQuery2|GET<br>/query/procedure<br>/DBSessionGetSessionUser/:seid| Prozeduraufruf |
|getAllSessions|DBQuery2|GET<br>/query/procedure<br>/DBSessionGetAllSessions| Prozeduraufruf |
|getExistsPlatform|DBQuery2|GET<br>/query/procedure<br>/DBSessionGetExistsPlatform| Prozeduraufruf |
|getSamplesInfo|DBQuery2|GET<br>/query/procedure<br>/DBSessionGetExistsPlatform| Prozeduraufruf |

#### Anbindungen
| Bezeichnung  | Ziel  | Priorität | Beschreibung |
| :----------- |:----- | :--------:| :------------|
|request|CLocalObjectRequest|-| damit DBSession als lokales Objekt aufgerufen werden kann |

Stand 13.06.2015