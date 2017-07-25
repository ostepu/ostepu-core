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

Die FSBinder erlaubte die allgemeine Nutzung von Dateien (es können also Dateien von FSZip, FSFile oder auch FSPdf genutzt werden).

| Themen |
| :- |
| [Befehle/Eingänge (Commands.json)](#eingaenge) |
| [Anbindungen (Component.json => Connector)](#anbindungen) |

## <a name='eingaenge'></a>Befehle/Eingänge (Commands.json)
Diese Befehle bietet diese Komponente als Aufruf an.

||addFile|
| :----------- |:----- |
|Beschreibung| fügt eine Datei dem Dateisystem hinzu|
|Befehl| POST /:folder/:a/:b/:c/:file|
|Eingabetyp| File|
|Ausgabetyp| File|

||deleteFile|
| :----------- |:----- |
|Beschreibung| entfernt eine Datei aus dem Dateisystem|
|Befehl| DELETE /:folder/:a/:b/:c/:file|
|Eingabetyp| -|
|Ausgabetyp| File|

||getFiledata|
| :----------- |:----- |
|Beschreibung| ermittelt die Metadaten der Datei|
|Befehl| GET,INFO /:folder/:a/:b/:c/:file|
|Eingabetyp| -|
|Ausgabetyp| File|

||getFileDocument|
| :----------- |:----- |
|Beschreibung| liefert den Inhalt der Datei|
|Befehl| GET /:folder/:a/:b/:c/:file/:filename|
|Eingabetyp| -|
|Ausgabetyp| binary|

||getFileDocumentWithSignature|
| :----------- |:----- |
|Beschreibung| liefert den Inhalt einer Datei, wobei die Signatur korrekt sein muss|
|Befehl| GET /:signature/:folder/:a/:b/:c/:file/:filename|
|Eingabetyp| -|
|Ausgabetyp| binary|

||addPlatform|
| :----------- |:----- |
|Beschreibung| installiert die zugehörige Tabelle und die Prozeduren für diese Plattform|
|Befehl| POST /platform|
|Eingabetyp| Platform|
|Ausgabetyp| Platform|

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
|Beschreibung| damit FSBinder als lokales Objekt aufgerufen werden kann|

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
