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
 -->

Die DBExternalId ermöglicht den Zugriff auf die `ExternalId` Tabelle der Datenbank, dabei sollen externe Zuordnungsnummern für Nutzerkonten verwaltet werden. Dazu wird bei einem `POST /platform` Aufruf die nachstehende Tabelle erzeugt. Zu dieser Tabelle gehört die `ExternalId` Datenstruktur.

| Spalte        | Struktur  | Beschreibung | Besonderheit |
| :------       |:---------:| :------------| -----------: |
|EX_id|VARCHAR(255) NOT NULL| die externe ID, Bsp.: die StudIP ID |UNIQUE|
|C_id|INT NOT NULL| ein Verweis auf die zugehörige Veranstaltung (sodass in jeder Veranstaltung eine eigene externe ID vergeben werden kann) |-|

## Eingänge
---------------

||getExistsPlatform|
| :----------- |:-----: |
|Beschreibung| prüft, ob die Tabelle und die Prozeduren existieren und die Komponente generell vollständig installiert ist|
|Befehl| GET<br>/link/exists/platform|
|Eingabetyp| -|
|Ausgabetyp| Platform|

||getExternalId|
| :----------- |:-----: |
|Beschreibung| liefert einen einzelnen Eintrag anhand der ID|
|Befehl| GET<br>/externalid/externalid/:exid|
|Eingabetyp| -|
|Ausgabetyp| ExternalId|
|||
||Patzhalter|
|Name|exid|
|Regex|%^[0-9a-zA-Z_]+$%|
|Beschreibung|die ID einer externen ID (`ExternalId`)|

||addPlatform|
| :----------- |:-----: |
|Beschreibung| installiert die zugehörige Tabelle und die Prozeduren für diese Plattform|
|Befehl| POST<br>/platform|
|Eingabetyp| Platform|
|Ausgabetyp| Platform|

||addExternalId|
| :----------- |:-----: |
|Beschreibung| fügt einen neuen Eintrag ein|
|Befehl| POST<br>/externalid|
|Eingabetyp| ExternalId|
|Ausgabetyp| ExternalId|

||editExternalId|
| :----------- |:-----: |
|Beschreibung| editiert einen Eintrag|
|Befehl| PUT<br>/externalid/externalid/:exid|
|Eingabetyp| ExternalId|
|Ausgabetyp| ExternalId|
|||
||Patzhalter|
|Name|exid|
|Regex|%^[0-9a-zA-Z_]+$%|
|Beschreibung|die ID einer externen ID (`ExternalId`)|

||getCourseExternalIds|
| :----------- |:-----: |
|Beschreibung| liefert alle Einträge einer Veranstaltung|
|Befehl| GET<br>/externalid/course/:courseid|
|Eingabetyp| -|
|Ausgabetyp| ExternalId|
|||
||Patzhalter|
|Name|courseid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|eine Veranstaltungs ID (`Course`)|

||getAllExternalIds|
| :----------- |:-----: |
|Beschreibung| ermittelt alle Einträge|
|Befehl| GET<br>/externalid|
|Eingabetyp| -|
|Ausgabetyp| ExternalId|

||deletePlatform|
| :----------- |:-----: |
|Beschreibung| entfernt die Komponente und ihre installierten Bestandteile aus der Plattform|
|Befehl| DELETE<br>/platform|
|Eingabetyp| -|
|Ausgabetyp| Platform|

||deleteExternalId|
| :----------- |:-----: |
|Beschreibung| entfernt einen Eintrag anhand der ID|
|Befehl| DELETE<br>/externalid/externalid/:exid|
|Eingabetyp| -|
|Ausgabetyp| ExternalId|
|||
||Patzhalter|
|Name|exid|
|Regex|%^[0-9a-zA-Z_]+$%|
|Beschreibung|die ID einer externen ID (`ExternalId`)|

||getApiProfiles|
| :----------- |:-----: |
|Beschreibung| liefert `GateProfile`-Objekte, welche unsere Befehle in die Standardprofile von CGate einsortieren|
|Befehl| GET<br>/api/profiles|
|Eingabetyp| -|
|Ausgabetyp| GateProfile|


## Ausgänge
---------------

||editExternalId|
| :----------- |:-----: |
|Ziel| DBQueryWrite|
|Befehl| POST<br>/query|
|Beschreibung| für den Befehl editExternalId|

||deleteExternalId|
| :----------- |:-----: |
|Ziel| DBQueryWrite|
|Befehl| POST<br>/query|
|Beschreibung| für den Befehl deleteExternalId|

||addExternalId|
| :----------- |:-----: |
|Ziel| DBQueryWrite|
|Befehl| POST<br>/query|
|Beschreibung| für den Befehl addExternalId|

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

||getExternalId|
| :----------- |:-----: |
|Ziel| DBQueryRead|
|Befehl| GET<br>/query/procedure/DBExternalIdGetExternalId/:exid|
|Beschreibung| für den Befehl getExternalId|

||getAllExternalIds|
| :----------- |:-----: |
|Ziel| DBQueryRead|
|Befehl| GET<br>/query/procedure/DBExternalIdGetAllExternalIds|
|Beschreibung| für den Befehl getAllExternalIds|

||getCourseExternalIds|
| :----------- |:-----: |
|Ziel| DBQueryRead|
|Befehl| GET<br>/query/procedure/DBExternalIdGetCourseExternalIds/:courseid|
|Beschreibung| für den Befehl getCourseExternalIds|

||getExistsPlatform|
| :----------- |:-----: |
|Ziel| DBQueryRead|
|Befehl| GET<br>/query/procedure/DBCourseGetExistsPlatform|
|Beschreibung| für den Befehl getExistsPlatform|


## Anbindungen
---------------

|Ausgang|request|
| :----------- |:-----: |
|Ziel| CLocalObjectRequest|
|Beschreibung| damit DBExternalId als lokales Objekt aufgerufen werden kann|

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
