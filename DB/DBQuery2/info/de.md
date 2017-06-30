<!--
  - @file de.md
  -
  - @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
  -
  - @package OSTEPU (https://github.com/ostepu/ostepu-core)
  - @since 0.3.5
  -
  - @author Till Uhlig <till.uhlig@student.uni-halle.de>
  - @date 2015
  -
 -->

Die DBQuery2 ermöglicht das Ausführen von SQL-Anfragen. Dabei nutzt sie die mysqli-Erweiterung von PHP.

## Eingänge
---------------

||postQuery|
| :----------- |:-----: |
|Beschreibung| führt eine Datenbankanfrage aus und gibt das Ergebnis zurück|
|Befehl| POST<br>/query|
|Eingabetyp| Query|
|Ausgabetyp| Query|

||postMultiGetRequest|
| :----------- |:-----: |
|Beschreibung| führt eine Menge von Anfragen aus (als Array)|
|Befehl| POST<br>/multiGetRequest|
|Eingabetyp| -|
|Ausgabetyp| -|

||getProcedureQuery|
| :----------- |:-----: |
|Beschreibung| ruft eine Prozedur in der Datenbank auf|
|Befehl| GET<br>/query/procedure/:procedure(/:params+)|
|Eingabetyp| -|
|Ausgabetyp| Query|
|||
||Patzhalter|
|Name|procedure|
|Regex|%^([a-zA-Z]+)$%|

||addPlatform|
| :----------- |:-----: |
|Beschreibung| installiert die zugehörige Tabelle und die Prozeduren für diese Plattform|
|Befehl| POST<br>/platform|
|Eingabetyp| Platform|
|Ausgabetyp| Platform|

||deletePlatform|
| :----------- |:-----: |
|Beschreibung| entfernt die Komponente und ihre installierten Bestandteile aus der Plattform|
|Befehl| DELETE<br>/platform|
|Eingabetyp| -|
|Ausgabetyp| Platform|

||getExistsPlatform|
| :----------- |:-----: |
|Beschreibung| prüft, ob die Tabelle und die Prozeduren existieren und die Komponente generell vollständig installiert ist|
|Befehl| GET<br>/link/exists/platform|
|Eingabetyp| -|
|Ausgabetyp| Platform|

||getApiProfiles|
| :----------- |:-----: |
|Beschreibung| liefert `GateProfile`-Objekte, welche unsere Befehle in die Standardprofile von CGate einsortieren|
|Befehl| GET<br>/api/profiles|
|Eingabetyp| -|
|Ausgabetyp| GateProfile|


## Anbindungen
---------------

|Ausgang|request|
| :----------- |:-----: |
|Ziel| CLocalObjectRequest|
|Beschreibung| damit DBQuery2 als lokales Objekt aufgerufen werden kann|

|Ausgang|getDescFiles|
| :----------- |:-----: |
|Ziel| TDocuView|
|Beschreibung| die Entwicklerdokumentation soll unsere Beschreibungsdatei nutzen|

|Ausgang|getComponentProfiles|
| :----------- |:-----: |
|Ziel| TApiConfiguration|
|Beschreibung| damit unsere Aufrufe in die Standardprofile der CGate einsortiert werden|


Stand 30.06.2017
