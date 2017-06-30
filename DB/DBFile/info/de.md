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

Die DBFile ermöglicht den Zugriff auf die `File` Tabelle der Datenbank, dabei sollen Dateien (nicht der Dateiinhalt) verwaltet werden. Dazu wird bei einem `POST /platform` Aufruf die nachstehende Tabelle erzeugt. Zu dieser Tabelle gehört die `File` Datenstruktur.

| Spalte        | Struktur  | Beschreibung | Besonderheit |
| :------       |:---------:| :------------| -----------: |
|F_id|INT NOT NULL| die ID der Datei |AUTO_INCREMENT,<br>UNIQUE|
|F_displayName|VARCHAR(255) NULL| der Anzeigename, bekommt der Nutzer beim Herunterladen so angezeigt Bsp.: MeineDatei.txt |-|
|F_address|CHAR(55) NOT NULL| der Pfad im Dateisystem (zum Abrufen über `FSBinder`), Bsp.: pdf/3/e/1/288361a6d62aa394feee355bd8779269a5977, die Adresse entspricht nicht immer dem Hash des Dateiinhalts |UNIQUE|
|F_timeStamp|INT UNSIGNED NULL DEFAULT 0| der Unix-Zeitstempel der Speicherung/des Hochladens, wird meist direkt nach dem Eingang im System gesetzt |-|
|F_fileSize|INT NULL DEFAULT 0| die Dateigröße in Byte (entsprechend dem Dateiinhalt) |-|
|F_hash|CHAR(40) NULL| der md5 hash |UNIQUE|
|F_comment|VARCHAR(255) NULL| ein möglicher Dateikommentar (wird nicht verwendet) |-|
|F_mimeType|VARCHAR(255) NULL| der mimType (siehe `Assistants/MimeReader.php`), Bsp.: image/png, application/pdf |-|

## Eingänge
---------------

||getExistsPlatform|
| :----------- |:-----: |
|Beschreibung| prüft, ob die Tabelle und die Prozeduren existieren und die Komponente generell vollständig installiert ist|
|Befehl| GET<br>/link/exists/platform|
|Eingabetyp| -|
|Ausgabetyp| Platform|

||removeFile|
| :----------- |:-----: |
|Beschreibung| entfernt einen Eintrag anhand der ID|
|Befehl| DELETE<br>/file/file/:fileid|
|Eingabetyp| -|
|Ausgabetyp| File|
|||
||Patzhalter|
|Name|fileid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID einer Datei (`File`)|

||editFile|
| :----------- |:-----: |
|Beschreibung| editiert einen Eintrag|
|Befehl| PUT<br>/file/file/:fileid|
|Eingabetyp| File|
|Ausgabetyp| File|
|||
||Patzhalter|
|Name|fileid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID einer Datei (`File`)|

||getFileByMimeType|
| :----------- |:-----: |
|Beschreibung| ermittelt Einträge anhand des MIME-Type|
|Befehl| GET<br>/file/mimetype/:base(/:type)(/timestamp/begin/:beginStamp/end/:endStamp)|
|Eingabetyp| -|
|Ausgabetyp| File|
|||
||Patzhalter|
|Name|base|
|Regex|%^[a-zA-Z]+$%|
|Beschreibung|die Basis eines MimeType Bsp.: text, application|
|Name|type|
|Regex|%^[a-zA-Z]+$%|
|Beschreibung|der explizite Typ eines MimeType Bsp.: c++, pdf|
|Name|beginStamp|
|Regex|%^([0-9_]+)$%|
|Beschreibung|der Anfangsstempel (Unix-Zeitstempel)|
|Name|endStamp|
|Regex|%^([0-9_]+)$%|
|Beschreibung|der Endstempel (Unix-Zeitstempel)|

||getFile|
| :----------- |:-----: |
|Beschreibung| ermittelt einen Eintrag anhand der ID|
|Befehl| GET<br>/file/file/:fileid|
|Eingabetyp| -|
|Ausgabetyp| File|
|||
||Patzhalter|
|Name|fileid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID einer Datei (`File`)|

||postSamples|
| :----------- |:-----: |
|Beschreibung| erzeugt Zufallsdaten (courseAmount = Anzahl der Veranstaltungen, userAmount = Anzahl der Nutzer), anhand der Vorgabe|
|Befehl| POST<br>/samples/course/:courseAmount/user/:userAmount|
|Eingabetyp| -|
|Ausgabetyp| Query|

||getFileByHash|
| :----------- |:-----: |
|Beschreibung| ermittelt einen Eintrag anhand des Hash (`F_hash`)|
|Befehl| GET<br>/file/hash/:hash|
|Eingabetyp| -|
|Ausgabetyp| File|
|||
||Patzhalter|
|Name|hash|
|Regex|%^[0-9a-fA-F]{40}$%|
|Beschreibung|der Hashwert einer Datei|

||getAllFiles|
| :----------- |:-----: |
|Beschreibung| liefert alle Dateien|
|Befehl| GET<br>/file(/timestamp/begin/:beginStamp/end/:endStamp)|
|Eingabetyp| -|
|Ausgabetyp| File|
|||
||Patzhalter|
|Name|beginStamp|
|Regex|%^([0-9_]+)$%|
|Beschreibung|der Anfangsstempel (Unix-Zeitstempel)|
|Name|endStamp|
|Regex|%^([0-9_]+)$%|
|Beschreibung|der Endstempel (Unix-Zeitstempel)|

||deletePlatform|
| :----------- |:-----: |
|Beschreibung| entfernt die Komponente und ihre installierten Bestandteile aus der Plattform|
|Befehl| DELETE<br>/platform|
|Eingabetyp| -|
|Ausgabetyp| Platform|

||addFile|
| :----------- |:-----: |
|Beschreibung| trägt eine neue Datei ein|
|Befehl| POST<br>/file|
|Eingabetyp| File|
|Ausgabetyp| File|

||getSamplesInfo|
| :----------- |:-----: |
|Beschreibung| liefert die Bezeichner der betroffenen Tabellen|
|Befehl| GET<br>/samples|
|Eingabetyp| -|
|Ausgabetyp| -|

||addPlatform|
| :----------- |:-----: |
|Beschreibung| installiert die zugehörige Tabelle und die Prozeduren für diese Plattform|
|Befehl| POST<br>/platform|
|Eingabetyp| Platform|
|Ausgabetyp| Platform|

||getApiProfiles|
| :----------- |:-----: |
|Beschreibung| liefert `GateProfile`-Objekte, welche unsere Befehle in die Standardprofile von CGate einsortieren|
|Befehl| GET<br>/api/profiles|
|Eingabetyp| -|
|Ausgabetyp| GateProfile|


## Ausgänge
---------------

||editFile|
| :----------- |:-----: |
|Ziel| DBQueryWrite|
|Befehl| POST<br>/query|
|Beschreibung| für den Befehl editFile|

||removeFile|
| :----------- |:-----: |
|Ziel| DBQueryWrite|
|Befehl| POST<br>/query|
|Beschreibung| für den Befehl removeFile|

||addFile|
| :----------- |:-----: |
|Ziel| DBQueryWrite|
|Befehl| POST<br>/query|
|Beschreibung| für den Befehl addFile|

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

||postSamples|
| :----------- |:-----: |
|Ziel| DBQueryWrite|
|Befehl| POST<br>/query|
|Beschreibung| für den Befehl postSamples|

||getFile|
| :----------- |:-----: |
|Ziel| DBQueryRead|
|Befehl| GET<br>/query/procedure/DBFileGetFile/:profile/:fileid|
|Beschreibung| für den Befehl getFile|

||getAllFiles|
| :----------- |:-----: |
|Ziel| DBQueryRead|
|Befehl| GET<br>/query/procedure/DBFileGetAllFiles/:profile/:beginStamp/:endStamp|
|Beschreibung| für den Befehl getAllFiles|

||getFileByHash|
| :----------- |:-----: |
|Ziel| DBQueryRead|
|Befehl| GET<br>/query/procedure/DBFileGetFileByHash/:profile/:hash|
|Beschreibung| für den Befehl getFileByHash|

||getFileByMimeType|
| :----------- |:-----: |
|Ziel| DBQueryRead|
|Befehl| GET<br>/query/procedure/DBFileGetFileByMimeType/:profile/:base/:type/:beginStamp/:endStamp|
|Beschreibung| für den Befehl getFileByMimeType|

||getExistsPlatform|
| :----------- |:-----: |
|Ziel| DBQueryRead|
|Befehl| GET<br>/query/procedure/DBFileGetExistsPlatform/:profile|
|Beschreibung| für den Befehl getExistsPlatform|

||getSamplesInfo|
| :----------- |:-----: |
|Ziel| DBQueryRead|
|Befehl| GET<br>/query/procedure/DBFileGetExistsPlatform/:profile|
|Beschreibung| für den Befehl getSamplesInfo|


## Anbindungen
---------------

|Ausgang|request|
| :----------- |:-----: |
|Ziel| CLocalObjectRequest|
|Beschreibung| damit DBFile als lokales Objekt aufgerufen werden kann|

|Ausgang|postPlatform|
| :----------- |:-----: |
|Ziel| CInstall|
|Beschreibung| der Installationsassistent soll uns bei der Plattforminstallation aufrufen|

|Ausgang|postSamples|
| :----------- |:-----: |
|Ziel| CInstall|
|Beschreibung| wir wollen bei Bedarf Beispieldaten erzeugen|

|Ausgang|getDescFiles|
| :----------- |:-----: |
|Ziel| TDocuView|
|Beschreibung| die Entwicklerdokumentation soll unsere Beschreibungsdatei nutzen|

|Ausgang|getComponentProfiles|
| :----------- |:-----: |
|Ziel| TApiConfiguration|
|Beschreibung| damit unsere Aufrufe in die Standardprofile der CGate einsortiert werden|


Stand 30.06.2017
