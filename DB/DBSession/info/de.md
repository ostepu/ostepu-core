#### Datenbank
Die DBSession ermöglicht den Zugriff auf die `Session` Tabelle der Datenbank, dabei sollen
Sitzungen verwaltet werden.
Dazu wird bei einem `POST /platform` Aufruf die nachstehende Tabelle erzeugt.

| Spalte        | Struktur  | Beschreibung | Besonderheit |
| :------       |:---------:| :------------| -----------: |
|U_id|INT NOT NULL| ??? |UNIQUE|
|SE_sessionID|CHAR(32) NOT NULL| ??? |-|

#### Datenstruktur
Zu dieser Tabelle gehört die `Session` Datenstruktur.

#### Eingänge
| Bezeichnung  | Eingabetyp  | Ausgabetyp | Befehl | Beschreibung |
| :----------- |:-----------:| :---------:| :----- | :----------- |
|editSession|Session|Session|PUT /session(/session)/:seid| ??? |
|deleteSession|-|Session|DELETE /session(/session)/:seid| ??? |
|editUserSession|Session|Session|PUT /session/user/:userid| ??? |
|deleteUserSession|-|Session|DELETE /session/user/:userid| ??? |
|addSession|Session|Session|POST /session| ??? |
|getAllSessions|-|Session|GET /session(/session)| ??? |
|getUserSession|-|Session|GET /session/user/:userid| ??? |
|getSessionUser|-|Session|GET /session(/session)/:seid| ??? |
|addPlatform|Platform|Platform|POST<br>/platform|installiert dies zugehörige Tabelle und die Prozeduren für diese Plattform|
|deletePlatform|-|Platform|DELETE<br>/platform|entfernt die Tabelle und Prozeduren aus der Plattform|
|getExistsPlatform|-|Platform|GET<br>/link/exists/platform| prüft, ob die Tabelle und die Prozeduren existieren |

#### Ausgänge
userid = die ID eines Nutzers oder ein Nuzername (`User`)
seid = ??? (`Session`)

| Bezeichnung  | Ziel  | Verwendung | Beschreibung |
| :----------- |:----- | :--------- | :----------- |
|out2|DBQuery2|POST<br>/query| wird für EDIT, DELETE und POST SQL-Templates verwendet |
|out2|DBQuery|POST<br>/query| wird für EDIT, DELETE und POST SQL-Templates verwendet |
|getUserSession|DBQuery2|GET /query/procedure/DBSessionGetUserSession/:userid| ??? |
|getSessionUser|DBQuery2|GET /query/procedure/DBSessionGetSessionUser/:seid| ??? |
|getAllSessions|DBQuery2|GET /query/procedure/DBSessionGetAllSessions| ??? |
|getExistsPlatform|DBQuery2|GET /query/procedure/DBFileGetExistsPlatform| Prozeduraufruf |
|getSamplesInfo|DBQuery2|GET /query/procedure/DBFileGetExistsPlatform| Prozeduraufruf |

#### Anbindungen
| Bezeichnung  | Ziel  | Priorität | Beschreibung |
| :----------- |:----- | :--------:| :------------|
|request|CLocalObjectRequest|-| damit DBSession als lokales Objekt aufgerufen werden kann |

Stand 13.06.2015