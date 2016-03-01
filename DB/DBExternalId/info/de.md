<!--
 * @file de.md
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
-->

#### Datenbank
Die DBExternalId ermöglicht den Zugriff auf die `ExternalId` Tabelle der Datenbank, dabei sollen
externe Zuordnungsnummern für Nutzerkonten verwaltet werden.
Dazu wird bei einem `POST /platform` Aufruf die nachstehende Tabelle erzeugt.

| Spalte        | Struktur  | Beschreibung | Besonderheit |
| :------       |:---------:| :------------| -----------: |
|EX_id|VARCHAR(255) NOT NULL| die externe ID, Bsp.: die StudIP ID |UNIQUE|
|C_id|INT NOT NULL| ein Verweis auf die zugehörige Veranstaltung (sodass in jeder Veranstaltung eine eigene externe ID vergeben werden kann) |-|

Die externe ID wird für den Zugang übers StudIP verwendet.

#### Datenstruktur
Zu dieser Tabelle gehört die `ExternalId` Datenstruktur.

#### Eingänge
- courseid = die ID einer Veranstlatung (`Course`)
- exid = die ID einer externen ID (`ExternalId`)

| Bezeichnung  | Eingabetyp  | Ausgabetyp | Befehl | Beschreibung |
| :----------- |:-----------:| :---------:| :----- | :----------- |
|editExternalId|ExternalId|ExternalId|PUT<br>/externalid(/externalid)/:exid| editiert eine existierende ID |
|deleteExternalId|-|ExternalId|DELETE<br>/externalid(/externalid)/:exid| entfernt die ID |
|addExternalId|ExternalId|ExternalId|POST<br>/externalid| fügt eine neue ein |
|getExternalId|-|ExternalId|GET<br>/externalid(/externalid)/:exid| gibt eine einzelne ID zurück |
|getAllExternalIds|-|ExternalId|GET<br>/externalid(/externalid)| gibt alle IDs zurück (für alle Veranstaltungen) |
|getCourseExternalIds|-|ExternalId|GET<br>/externalid/course/:courseid| die die IDs einer bestimmten Veranstaltung aus |
|addPlatform|Platform|Platform|POST<br>/platform|installiert dies zugehörige Tabelle und die Prozeduren für diese Plattform|
|deletePlatform|-|Platform|DELETE<br>/platform|entfernt die Tabelle und Prozeduren aus der Plattform|
|getExistsPlatform|-|Platform|GET<br>/link/exists/platform| prüft, ob die Tabelle und die Prozeduren existieren |

#### Ausgänge
- courseid = die ID einer Veranstlatung (`Course`)
- exid = die ID einer externen ID (`ExternalId`)

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