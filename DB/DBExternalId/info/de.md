#### Datenbank
Die DBExternalId ermöglicht den Zugriff auf die `ExternalId` Tabelle der Datenbank, dabei sollen
externe Zuordnungsnummer für Nutzerkonten verwaltet werden.
Dazu wird bei einem `POST /platform` Aufruf die nachstehende Tabelle erzeugt.

| Spalte        | Struktur  | Beschreibung | Besonderheit |
| :------       |:---------:| :------------| -----------: |
|EX_id|VARCHAR(255) NOT NULL| ??? |-|
|C_id|INT NOT NULL| ??? |-|

Die externe ID wird für den Zugang übers StudIP verwendet.

#### Datenstruktur
Zu dieser Tabelle gehört die `ExternalId` Datenstruktur.

#### Eingänge
| Bezeichnung  | Eingabetyp  | Ausgabetyp | Befehl | Beschreibung |
| :----------- |:-----------:| :---------:| :----- | :----------- |
|editExternalId|ExternalId|ExternalId|PUT /externalid(/externalid)/:exid| ??? |
|deleteExternalId|-|ExternalId|DELETE /externalid(/externalid)/:exid| ??? |
|addExternalId|ExternalId|ExternalId|POST /externalid| ??? |
|getExternalId|-|ExternalId|GET /externalid(/externalid)/:exid| ??? |
|getAllExternalIds|-|ExternalId|GET /externalid(/externalid)| ??? |
|getCourseExternalIds|-|ExternalId|GET /externalid/course/:courseid| ??? |
|addPlatform|Platform|Platform|POST<br>/platform|installiert dies zugehörige Tabelle und die Prozeduren für diese Plattform|
|deletePlatform|-|Platform|DELETE<br>/platform|entfernt die Tabelle und Prozeduren aus der Plattform|
|getExistsPlatform|-|Platform|GET<br>/link/exists/platform| prüft, ob die Tabelle und die Prozeduren existieren |

#### Ausgänge
courseid = die ID einer Veranstlatung (`Course`)
exid = die ID einer externen ID (`ExternalId`)

| Bezeichnung  | Ziel  | Verwendung | Beschreibung |
| :----------- |:----- | :--------- | :----------- |
|out2|DBQuery2|POST<br>/query| wird für EDIT, DELETE und POST SQL-Templates verwendet |
|out2|DBQuery|POST<br>/query| wird für EDIT, DELETE und POST SQL-Templates verwendet |
|getExternalId|DBQuery2|GET /query/procedure/DBExternalIdGetExternalId/:exid| ??? |
|getAllExternalIds|DBQuery2|GET /query/procedure/DBExternalIdGetAllExternalIds| ??? |
|getCourseExternalIds|DBQuery2|GET /query/procedure/DBExternalIdGetCourseExternalIds/:courseid| ??? |
|getExistsPlatform|DBQuery2|GET /query/procedure/DBFileGetExistsPlatform| Prozeduraufruf |

#### Anbindungen
| Bezeichnung  | Ziel  | Priorität | Beschreibung |
| :----------- |:----- | :--------:| :------------|
|request|CLocalObjectRequest|-| damit DBExternalId als lokales Objekt aufgerufen werden kann |

Stand 13.06.2015