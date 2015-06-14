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
|editExternalId|ExternalId|ExternalId|PUT<br>/externalid(/externalid)/:exid| ??? |
|deleteExternalId|-|ExternalId|DELETE<br>/externalid(/externalid)/:exid| ??? |
|addExternalId|ExternalId|ExternalId|POST<br>/externalid| ??? |
|getExternalId|-|ExternalId|GET<br>/externalid(/externalid)/:exid| ??? |
|getAllExternalIds|-|ExternalId|GET<br>/externalid(/externalid)| ??? |
|getCourseExternalIds|-|ExternalId|GET<br>/externalid/course/:courseid| ??? |
|addPlatform|Platform|Platform|POST<br>/platform|installiert dies zugehörige Tabelle und die Prozeduren für diese Plattform|
|deletePlatform|-|Platform|DELETE<br>/platform|entfernt die Tabelle und Prozeduren aus der Plattform|
|getExistsPlatform|-|Platform|GET<br>/link/exists/platform| prüft, ob die Tabelle und die Prozeduren existieren |

#### Ausgänge
courseid = die ID einer Veranstlatung (`Course`)
exid = die ID einer externen ID (`ExternalId`)

| Bezeichnung  | Ziel  | Verwendung | Beschreibung |
| :----------- |:----- | :--------- | :----------- |
|out2|DBQuery2|POST<br>/query| wird für EDIT, DELETE<br>und POST<br>SQL-Templates verwendet |
|out2|DBQuery|POST<br>/query| wird für EDIT, DELETE<br>und POST<br>SQL-Templates verwendet |
|getExternalId|DBQuery2|GET<br>/query/procedure<br>/DBExternalIdGetExternalId/:exid| Prozeduraufruf |
|getAllExternalIds|DBQuery2|GET<br>/query/procedure<br>/DBExternalIdGetAllExternalIds| Prozeduraufruf |
|getCourseExternalIds|DBQuery2|GET<br>/query/procedure<br>/DBExternalIdGetCourseExternalIds/:courseid| Prozeduraufruf |
|getExistsPlatform|DBQuery2|GET<br>/query/procedure<br>/DBExternalIdGetExistsPlatform| Prozeduraufruf |

#### Anbindungen
| Bezeichnung  | Ziel  | Priorität | Beschreibung |
| :----------- |:----- | :--------:| :------------|
|request|CLocalObjectRequest|-| damit DBExternalId als lokales Objekt aufgerufen werden kann |

Stand 13.06.2015