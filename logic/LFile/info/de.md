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

Die LFile bietet Aufrufe an, welche das Speichern und Löschen von Dateien erlauben

| Themen |
| :- |
| [Befehle/Eingänge (Commands.json)](#eingaenge) |
| [Ausgänge (Component.json => Links)](#ausgaenge) |
| [Anbindungen (Component.json => Connector)](#anbindungen) |

## <a name='eingaenge'></a>Befehle/Eingänge (Commands.json)
Diese Befehle bietet diese Komponente als Aufruf an.

|||
| :----------- |:----- |
|Beschreibung| über diesen Befehl können Dateien in der Plattform hinterlegt werden (Datenbank+Dateisystem)|
|Befehl| POST /file(/)|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| über diesen Befehl können Dateien sauber aus der Plattform entfernt werden (Datenbank+Dateisystem)|
|Befehl| DELETE /file/:fileid(/)|
|Eingabetyp| -|
|Ausgabetyp| -|


## <a name='ausgaenge'></a>Ausgänge (Component.json => Links)
Wenn eine Komponente selbst noch Unteranfragen an andere Komponenten stellen möchte, dann werden diese über die `Ausgänge` bearbeitet.
Dabei kann ein Ausgang bereits auf eine Komponente gerichtet sein (`Ziel`) oder durch die Zielkomponente selbst angebunden werden (`Connector`)

||fileDb|
| :----------- |:----- |
|Ziel| DBFile|
|Befehl| GET /file/hash/:hash|
|Beschreibung| zum Speichern/Löschen von Dateien in der Datenbank|

||fileDb|
| :----------- |:----- |
|Ziel| DBFile|
|Befehl| DELETE /file/file/:fileid|
|Beschreibung| zum Speichern/Löschen von Dateien in der Datenbank|

||fileDb|
| :----------- |:----- |
|Ziel| DBFile|
|Befehl| POST /file|
|Beschreibung| zum Speichern/Löschen von Dateien in der Datenbank|

||file|
| :----------- |:----- |
|Ziel| FSFile|
|Befehl| POST /file|
|Beschreibung| zum Speichern/Löschen von Dateien im Dateisystem|

||file|
| :----------- |:----- |
|Ziel| FSFile|
|Befehl| DELETE /file/:adress|
|Beschreibung| zum Speichern/Löschen von Dateien im Dateisystem|


## <a name='anbindungen'></a>Anbindungen (Component.json => Connector)
Eine Anbindung verlangt von einer anderen Komponente (`Ziel`) die Anbindung/Verbindung zu dieser Komponente.
Wenn eine Anbindung den aufzurufenden Befehl vorgibt, dann ist die Notation: METHODE URL (PRIORITÄT).

|Ausgang|request|
| :----------- |:----- |
|Ziel| CLocalObjectRequest|
|Beschreibung| damit LFile als lokales Objekt aufgerufen werden kann|

|Ausgang|getDescFiles|
| :----------- |:----- |
|Ziel| TDocuView|
|Beschreibung| die Entwicklerdokumentation soll unsere Beschreibungsdatei nutzen|


Stand 25.07.2017
