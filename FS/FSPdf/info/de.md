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

Die FSPdf erzeugt PDF's. Dabei kann man mittels HTML und CSS die Ausgabe formatieren.

| Themen |
| :- |
| [Befehle/Eingänge (Commands.json)](#eingaenge) |
| [Anbindungen (Component.json => Connector)](#anbindungen) |

## <a name='eingaenge'></a>Befehle/Eingänge (Commands.json)
Diese Befehle bietet diese Komponente als Aufruf an.

||addPlatform|
| :----------- |:----- |
|Beschreibung| installiert die zugehörige Tabelle und die Prozeduren für diese Plattform|
|Befehl| POST /platform|
|Eingabetyp| Platform|
|Ausgabetyp| Platform|

||addPdfPermanent|
| :----------- |:----- |
|Beschreibung| erzeugt eine PDF dauerhaft|
|Befehl| POST /:folder|
|Eingabetyp| Pdf|
|Ausgabetyp| File|

||addPdfFromFile|
| :----------- |:----- |
|Beschreibung| nutzt den Inhalt der Datei, um daraus eine PDF zu erzeugen|
|Befehl| POST /:folder/:type/:a/:b/:c/:file|
|Eingabetyp| -|
|Ausgabetyp| File|

||addPdfFromFile2|
| :----------- |:----- |
|Beschreibung| wandelt alle übergebenen Dateien in eine PDF um (einzelne PDF's) (KEINE NUTZUNG)|
|Befehl| POST /:folder/file|
|Eingabetyp| File|
|Ausgabetyp| File|

||addPdfFromFile3|
| :----------- |:----- |
|Beschreibung| vereint alle übergebenen Dateien in einer einzelnen PDF (merge)|
|Befehl| POST /:folder/file/merge|
|Eingabetyp| File|
|Ausgabetyp| File|

||addPdfTemporary|
| :----------- |:----- |
|Beschreibung| erzeugt eine PDF vorrübergehend|
|Befehl| POST /:folder/:filename|
|Eingabetyp| Pdf|
|Ausgabetyp| binary|

||deletePdf|
| :----------- |:----- |
|Beschreibung| entfernt eine PDF aus dem Dateisystem|
|Befehl| DELETE /:folder/:a/:b/:c/:file|
|Eingabetyp| -|
|Ausgabetyp| File|

||getPdfdata|
| :----------- |:----- |
|Beschreibung| liefert die Metadaten einer PDF|
|Befehl| GET /:folder/:a/:b/:c/:file|
|Eingabetyp| -|
|Ausgabetyp| File|

||getPdfDocument|
| :----------- |:----- |
|Beschreibung| gibt den Inhalt einer PDF zurück|
|Befehl| GET /:folder/:a/:b/:c/:file/:filename|
|Eingabetyp| -|
|Ausgabetyp| binary|

||deletePlatform|
| :----------- |:----- |
|Beschreibung| entfernt die Komponente und ihre installierten Bestandteile aus der Plattform|
|Befehl| DELETE /platform|
|Eingabetyp| -|
|Ausgabetyp| Platform|

||getExistsPlatform|
| :----------- |:----- |
|Beschreibung| prüft, ob die Tabelle und die Prozeduren existieren und die Komponente generell vollständig installiert ist|
|Befehl| GET /link/exists/platform|
|Eingabetyp| -|
|Ausgabetyp| Platform|

||getApiProfiles|
| :----------- |:----- |
|Beschreibung| liefert `GateProfile`-Objekte, welche unsere Befehle in die Standardprofile von CGate einsortieren|
|Befehl| GET /api/profiles|
|Eingabetyp| -|
|Ausgabetyp| GateProfile|


## <a name='anbindungen'></a>Anbindungen (Component.json => Connector)
Eine Anbindung verlangt von einer anderen Komponente (`Ziel`) die Anbindung/Verbindung zu dieser Komponente.
Wenn eine Anbindung den aufzurufenden Befehl vorgibt, dann ist die Notation: METHODE URL (PRIORITÄT).

|Ausgang|request|
| :----------- |:----- |
|Ziel| CLocalObjectRequest|
|Beschreibung| damit FSPdf als lokales Objekt aufgerufen werden kann|

|Ausgang|postPlatform|
| :----------- |:----- |
|Ziel| CInstall|
|Beschreibung| der Installationsassistent soll uns bei der Plattforminstallation aufrufen|

|Ausgang|getComponentProfiles|
| :----------- |:----- |
|Ziel| TApiConfiguration|
|Beschreibung| damit unsere Aufrufe in die Standardprofile der CGate einsortiert werden|

|Ausgang|getDescFiles|
| :----------- |:----- |
|Ziel| TDocuView|
|Beschreibung| die Entwicklerdokumentation soll unsere Beschreibungsdatei nutzen|


Stand 25.07.2017
