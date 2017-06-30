<!--
  - @file de.md
  -
  - @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
  -
  - @package OSTEPU (https://github.com/ostepu/ostepu-core)
  - @since -
  -
  - @author Till Uhlig <till.uhlig@student.uni-halle.de>
  - @date 2017
  -
 -->

Die FSCsv ermöglicht das Erstellen von CSV-Dateien.

## Eingänge
---------------

||addPlatform|
| :----------- |:-----: |
|Beschreibung| installiert die zugehörige Tabelle und die Prozeduren für diese Plattform|
|Befehl| POST<br>/platform|
|Eingabetyp| Platform|
|Ausgabetyp| Platform|

||addCsvPermanent|
| :----------- |:-----: |
|Beschreibung| erzeug eine CSV dauerhaft|
|Befehl| POST<br>/:folder|
|Eingabetyp| Csv|
|Ausgabetyp| File|

||addCsvTemporary|
| :----------- |:-----: |
|Beschreibung| erzeugt die CSV, gibt ihren Inhalt aber nur zurück|
|Befehl| POST<br>/:folder/:filename|
|Eingabetyp| Csv|
|Ausgabetyp| binary|

||deleteCsv|
| :----------- |:-----: |
|Beschreibung| entfernt eine CSV aus dem Dateisystem|
|Befehl| DELETE<br>/:folder/:a/:b/:c/:file|
|Eingabetyp| -|
|Ausgabetyp| File|

||getCsvdata|
| :----------- |:-----: |
|Beschreibung| liefert die Metadaten einer CSV|
|Befehl| GET<br>/:folder/:a/:b/:c/:file|
|Eingabetyp| -|
|Ausgabetyp| File|

||getCsvDocument|
| :----------- |:-----: |
|Beschreibung| gibt den Inhalt der CSV zurück|
|Befehl| GET<br>/:folder/:a/:b/:c/:file/:filename|
|Eingabetyp| -|
|Ausgabetyp| binary|

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
|Beschreibung| damit FSCsv als lokales Objekt aufgerufen werden kann|

|Ausgang|postPlatform|
| :----------- |:-----: |
|Ziel| CInstall|
|Beschreibung| der Installationsassistent soll uns bei der Plattforminstallation aufrufen|

|Ausgang|getComponentProfiles|
| :----------- |:-----: |
|Ziel| TApiConfiguration|
|Beschreibung| damit unsere Aufrufe in die Standardprofile der CGate einsortiert werden|


Stand 30.06.2017
