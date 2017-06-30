Die FSPdf erzeugt PDF's. Dabei kann man mittels HTML und CSS die Ausgabe formatieren.

## Eingänge
---------------

||addPlatform|
| :----------- |:-----: |
|Beschreibung| installiert die zugehörige Tabelle und die Prozeduren für diese Plattform|
|Befehl| POST<br>/platform|
|Eingabetyp| Platform|
|Ausgabetyp| Platform|

||addPdfPermanent|
| :----------- |:-----: |
|Beschreibung| erzeugt eine PDF dauerhaft|
|Befehl| POST<br>/:folder|
|Eingabetyp| Pdf|
|Ausgabetyp| File|

||addPdfFromFile|
| :----------- |:-----: |
|Beschreibung| nutzt den Inhalt der Datei, um daraus eine PDF zu erzeugen|
|Befehl| POST<br>/:folder/:type/:a/:b/:c/:file|
|Eingabetyp| -|
|Ausgabetyp| File|

||addPdfFromFile2|
| :----------- |:-----: |
|Beschreibung| wandelt alle übergebenen Dateien in eine PDF um (einzelne PDF's) (KEINE NUTZUNG)|
|Befehl| POST<br>/:folder/file|
|Eingabetyp| File|
|Ausgabetyp| File|

||addPdfFromFile3|
| :----------- |:-----: |
|Beschreibung| vereint alle übergebenen Dateien in einer einzelnen PDF (merge)|
|Befehl| POST<br>/:folder/file/merge|
|Eingabetyp| File|
|Ausgabetyp| File|

||addPdfTemporary|
| :----------- |:-----: |
|Beschreibung| erzeugt eine PDF vorrübergehend|
|Befehl| POST<br>/:folder/:filename|
|Eingabetyp| Pdf|
|Ausgabetyp| binary|

||deletePdf|
| :----------- |:-----: |
|Beschreibung| entfernt eine PDF aus dem Dateisystem|
|Befehl| DELETE<br>/:folder/:a/:b/:c/:file|
|Eingabetyp| -|
|Ausgabetyp| File|

||getPdfdata|
| :----------- |:-----: |
|Beschreibung| liefert die Metadaten einer PDF|
|Befehl| GET<br>/:folder/:a/:b/:c/:file|
|Eingabetyp| -|
|Ausgabetyp| File|

||getPdfDocument|
| :----------- |:-----: |
|Beschreibung| gibt den Inhalt einer PDF zurück|
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
|Beschreibung| damit FSPdf als lokales Objekt aufgerufen werden kann|

|Ausgang|postPlatform|
| :----------- |:-----: |
|Ziel| CInstall|
|Beschreibung| der Installationsassistent soll uns bei der Plattforminstallation aufrufen|

|Ausgang|getComponentProfiles|
| :----------- |:-----: |
|Ziel| TApiConfiguration|
|Beschreibung| damit unsere Aufrufe in die Standardprofile der CGate einsortiert werden|


Stand 30.06.2017
