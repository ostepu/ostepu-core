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

Die LSubmission bietet Aufrufe zum Handhaben von Einsendungen an (Submission's)

| Themen |
| :- |
| [Befehle/Eingänge (Commands.json)](#eingaenge) |
| [Ausgänge (Component.json => Links)](#ausgaenge) |
| [Anbindungen (Component.json => Connector)](#anbindungen) |

## <a name='eingaenge'></a>Befehle/Eingänge (Commands.json)
Diese Befehle bietet diese Komponente als Aufruf an.

|||
| :----------- |:----- |
|Beschreibung| speichert eine Einsendung|
|Befehl| post /submission|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| löscht eine Einsendung|
|Befehl| delete /submission/submission/:submissionid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| erzeugt aus den Einsendungen der Nutzergruppe eine ZIP und gibt das Datei-Objekt zurück|
|Befehl| get /submission/exercisesheet/:sheetid/user/:userid|
|Eingabetyp| -|
|Ausgabetyp| -|


## <a name='ausgaenge'></a>Ausgänge (Component.json => Links)
Wenn eine Komponente selbst noch Unteranfragen an anderen Komponenten stellen möchte, dann werden diese über die `Ausgänge` bearbeitet.
Dabei kann ein Ausgang bereits auf eine Komponente gerichtet sein (`Ziel`) oder durch die Zielkomponente selbst angebunden werden (`Connector`)

||file|
| :----------- |:----- |
|Ziel| LFile|
|Befehl| POST /file|
|Beschreibung| zum Speichern von Dateien|

||submission|
| :----------- |:----- |
|Ziel| DBSubmission|
|Befehl| GET /submission/group/user/:userid/exercisesheet/:sheetid/selected|
|Beschreibung| zum Speichern von Einsendungen|

||submission|
| :----------- |:----- |
|Ziel| DBSubmission|
|Befehl| DELETE /submission/:submissionid|
|Beschreibung| zum Speichern von Einsendungen|

||submission|
| :----------- |:----- |
|Ziel| DBSubmission|
|Befehl| POST /submission|
|Beschreibung| zum Speichern von Einsendungen|

||selectedSubmission|
| :----------- |:----- |
|Ziel| DBSelectedSubmission|
|Befehl| PUT /selectedsubmission/leader/:leaderid/exercise/:exerciseid|
|Beschreibung| zum Ändern/Erstellen der ausgewählten Einsendung|

||selectedSubmission|
| :----------- |:----- |
|Ziel| DBSelectedSubmission|
|Befehl| POST /selectedsubmission|
|Beschreibung| zum Ändern/Erstellen der ausgewählten Einsendung|

||zip|
| :----------- |:----- |
|Ziel| FSZip|
|Befehl| POST /zip/:zipname|
|Beschreibung| zum Erstellen eines Archivs|


## <a name='anbindungen'></a>Anbindungen (Component.json => Connector)
Eine Anbindung verlangt von einer anderen Komponente (`Ziel`) die Anbindung/Verbindung zu dieser Komponente.
Wenn eine Anbindung den aufzurufenden Befehl vorgibt, dann ist die Notation: METHODE URL (PRIORITÄT).

|Ausgang|request|
| :----------- |:----- |
|Ziel| CLocalObjectRequest|
|Beschreibung| damit LSubmission als lokales Objekt aufgerufen werden kann|

|Ausgang|getDescFiles|
| :----------- |:----- |
|Ziel| TDocuView|
|Beschreibung| die Entwicklerdokumentation soll unsere Beschreibungsdatei nutzen|


Stand 25.07.2017
