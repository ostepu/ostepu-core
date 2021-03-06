<!--
  - @file de.md
  -
  - @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
  -
  - @package OSTEPU (https://github.com/ostepu/system)
  - @since 0.3.4
  -
  - @author Till Uhlig <till.uhlig@student.uni-halle.de>
  - @date 2015
 -->

#### Datenbank
Die DBFile ermöglicht den Zugriff auf die `File` Tabelle der Datenbank, dabei sollen
Dateien (nicht der Dateiinhalt) verwaltet werden.
Dazu wird bei einem `POST /platform` Aufruf die nachstehende Tabelle erzeugt.

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

#### Datenstruktur
Zu dieser Tabelle gehört die `File` Datenstruktur.

#### Eingänge
- courseid = eine Veranstaltungs ID (`Course`)
- fileid = die ID einer Datei (`File`)
- beginStamp = der Anfangsstempel (Unix-Zeitstempel)
- endStamp = der Endstempel (Unix-Zeitstempel)
- hash = der Hashwert einer Datei
- base = die Basis eines MimeType Bsp.: text, application
- type = der explizite Typ eines MimeType Bsp.: c++, pdf

| Bezeichnung  | Eingabetyp  | Ausgabetyp | Befehl | Beschreibung |
| :----------- |:-----------:| :---------:| :----- | :----------- |
|editFile|File|File|PUT<br>/file(/file)/:fileid| editiert einen Dateieintrag |
|removeFile|-|File|DELETE<br>/file(/file)/:fileid| entfernt einen Dateieintrag (geht nur, wenn nichts auf diesen Eintrag verweist). Achtung: entfernt die Datei nicht physisch aus dem Dateisystem. |
|addFile|File|File|POST<br>/file| fügt einen neuen Eintrag ein |
|getFile|-|File|GET<br>/file(/file)/:fileid| liefert eine einzelne Datei |
|getFileByHash|-|File|GET<br>/file/hash/:hash| liefert eine Datei anhand ihres md5 Hash |
|getAllFiles|-|File|GET<br>/file(/timestamp/begin/:beginStamp/end/:endStamp)| liefert alle Dateien (für alle Veranstaltungen), es kann ein Zeitraum angegeben werden |
|getFileByMimeType|-|File|GET<br>/file/mimetype/:base(/:type)(/timestamp/begin/:beginSttamp/end/:endStamp)| es werden Dateien anhand eines mimeType gewählt (siehe `Assistans/MimeReader.php`), zusätzlich kann ein Zeitraum angegeben werden |
|addPlatform|Platform|Platform|POST<br>/platform|installiert dies zugehörige Tabelle und die Prozeduren für diese Plattform|
|deletePlatform|-|Platform|DELETE<br>/platform|entfernt die Tabelle und Prozeduren aus der Plattform|
|getExistsPlatform|-|Platform|GET<br>/link/exists/platform| prüft, ob die Tabelle und die Prozeduren existieren |
|getSamplesInfo|-|-|GET<br>/samples| ??? |
|postSamples|-|Query|POST<br>/samples/course/:courseAmount<br>/user/:userAmount| erzeugt Zufallsdaten (courseAmount = Anzahl der Veranstaltungen, userAmount = Anzahl der Nutzer), anhand der Vorgabe |

#### Ausgänge
- courseid = eine Veranstaltungs ID (`Course`)
- fileid = die ID einer Datei (`File`)
- beginStamp = der Anfangsstempel (Unix-Zeitstempel)
- endStamp = der Endstempel (Unix-Zeitstempel)
- hash = der Hashwert einer Datei
- base = die Basis eines MimeType Bsp.: text, application
- type = der explizite Typ eines MimeType Bsp.: c++, pdf

| Bezeichnung  | Ziel  | Verwendung | Beschreibung |
| :----------- |:----- | :--------- | :----------- |
|out2|DBQuery2|POST<br>/query| wird für EDIT, DELETE<br>und POST<br>SQL-Templates verwendet |
|out|DBQuery|POST<br>/query| wird für EDIT, DELETE<br>und POST<br>SQL-Templates verwendet |
|getFile|DBQuery2|GET<br>/query/procedure<br>/DBFileGetFile/:fileid| Prozeduraufruf |
|getAllFiles|DBQuery2|GET<br>/query/procedure<br>/DBFileGetAllFiles/:beginStamp/:endStamp| Prozeduraufruf |
|getFileByHash|DBQuery2|GET<br>/query/procedure<br>/DBFileGetFileByHash/:hash| Prozeduraufruf |
|getFileByMimeType|DBQuery2|GET<br>/query/procedure<br>/DBFileGetFileByMimeType/:base/:type/:beginStamp/:endStamp| Prozeduraufruf |
|getExistsPlatform|DBQuery2|GET<br>/query/procedure<br>/DBFileGetExistsPlatform| Prozeduraufruf |
|getSamplesInfo|DBQuery2|GET<br>/query/procedure<br>/DBFileGetExistsPlatform| Prozeduraufruf |

#### Anbindungen
| Bezeichnung  | Ziel  | Priorität | Beschreibung |
| :----------- |:----- | :--------:| :------------|
|request|CLocalObjectRequest|-| damit DBFile als lokales Objekt aufgerufen werden kann |

Stand 13.06.2015