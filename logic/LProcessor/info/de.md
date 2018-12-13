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
  -
 -->

Über diese Komponente können Einsendungen im System hinterlegt werden. Sie speichert die Einsendung, Einsendungsdatei und führt nachgeschaltene Verarbeitungen aus. Die Verarbeitungen müssen für die jeweilige Aufgabe definiert sein.

| Themen |
| :- |
| [Befehle/Eingänge (Commands.json)](#eingaenge) |
| [Ausgänge (Component.json => Links)](#ausgaenge) |
| [Anbindungen (Component.json => Connector)](#anbindungen) |

## <a name='eingaenge'></a>Befehle/Eingänge (Commands.json)
Diese Befehle bietet diese Komponente als Aufruf an.

|||
| :----------- |:----- |
|Beschreibung| Dieser Befehl nimmt eine Einsendung entgeben und speichert diese im System. Dabei werden Verarbeitungen ausgeführt.|
|Befehl| post /submission|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| Zum Anlegen oder Ändern von Verarbeitungseinträgen|
|Befehl| post /process|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| Installiert diese Komponente und nich nachgeschaltenen Komponenten in eine Veranstaltung|
|Befehl| post /course|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| entfernt die Komponente und nachgeschaltene Komponenten aus der Veranstaltung|
|Befehl| delete /course/:courseid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| prüft, ob die Komponente korrekt installiert ist|
|Befehl| get /link/exists/course/:courseid|
|Eingabetyp| -|
|Ausgabetyp| -|


## <a name='ausgaenge'></a>Ausgänge (Component.json => Links)
Wenn eine Komponente selbst noch Unteranfragen an andere Komponenten stellen möchte, dann werden diese über die `Ausgänge` bearbeitet.
Dabei kann ein Ausgang bereits auf eine Komponente gerichtet sein (`Ziel`) oder durch die Zielkomponente selbst angebunden werden (`Connector`)

||submission|
| :----------- |:----- |
|Ziel| LSubmission|
|Befehl| POST /submission|
|Beschreibung| zum Speichern der Einsendung und Einsendungsdatei|

||marking|
| :----------- |:----- |
|Ziel| LMarking|
|Befehl| POST /marking|
|Beschreibung| zum Speichern der Korrekturen, falls die Verarbeitungen eine Korrektur erzeugt haben|

||processorDb|
| :----------- |:----- |
|Ziel| DBProcess|
|Befehl| POST /process|
|Beschreibung| Zum Abrufen der Verarbeitungen für diese Aufgabe. Zudem wird dieser Ausgang genutzt, um neue Verarbeitungen zu speichern und zu ändern.|

||processorDb|
| :----------- |:----- |
|Ziel| DBProcess|
|Befehl| PUT /process/process/:processid|
|Beschreibung| Zum Abrufen der Verarbeitungen für diese Aufgabe. Zudem wird dieser Ausgang genutzt, um neue Verarbeitungen zu speichern und zu ändern.|

||processorDb|
| :----------- |:----- |
|Ziel| DBProcess|
|Befehl| GET /process/exercise/:exerciseid|
|Beschreibung| Zum Abrufen der Verarbeitungen für diese Aufgabe. Zudem wird dieser Ausgang genutzt, um neue Verarbeitungen zu speichern und zu ändern.|

||attachment|
| :----------- |:----- |
|Ziel| DBProcessAttachment|
|Befehl| POST /attachment|
|Beschreibung| zum Speichern der Verarbeitunsanhänge|

||workFiles|
| :----------- |:----- |
|Ziel| DBProcessWorkFiles|
|Befehl| POST /attachment|
|Beschreibung| zum Speichern der Arbeitsdaten einer Verarbeitung|

||getExerciseExerciseFileType|
| :----------- |:----- |
|Ziel| DBExerciseFileType|
|Befehl| GET /exercisefiletype/exercise/:eid|
|Beschreibung| zum Abrufen der erlaubten Dateitypen einer Aufgabe|

||file|
| :----------- |:----- |
|Ziel| LFile|
|Befehl| POST /file|
|Beschreibung| zum Speichern von Dateien|

||postCourse|
| :----------- |:----- |
|Ziel| DBProcess|
|Befehl| GET /link/exists/course/:courseid|
|Beschreibung| um nachgeschaltene Komponenten in die Veranstaltung installieren zu können|

||postCourse|
| :----------- |:----- |
|Ziel| DBProcess|
|Befehl| POST /course|
|Beschreibung| um nachgeschaltene Komponenten in die Veranstaltung installieren zu können|

||postCourse|
| :----------- |:----- |
|Ziel| DBProcess|
|Befehl| DELETE /course/course/:courseid|
|Beschreibung| um nachgeschaltene Komponenten in die Veranstaltung installieren zu können|


## <a name='anbindungen'></a>Anbindungen (Component.json => Connector)
Eine Anbindung verlangt von einer anderen Komponente (`Ziel`) die Anbindung/Verbindung zu dieser Komponente.
Wenn eine Anbindung den aufzurufenden Befehl vorgibt, dann ist die Notation: METHODE URL (PRIORITÄT).

|Ausgang|deleteCourse|
| :----------- |:----- |
|Ziel| LCourse|
|Beschreibung| wenn eine Veranstaltung gelöscht wird, dann müssen auch unsere Tabellen entfernt werden|

|Ausgang|postCourse|
| :----------- |:----- |
|Ziel| LCourse|
|Beschreibung| wenn eine neue Veranstaltung angelegt wird, dann wollen wir auch aufgerufen werden|

|Ausgang|request|
| :----------- |:----- |
|Ziel| CLocalObjectRequest|
|Beschreibung| damit LProcessor als lokales Objekt aufgerufen werden kann|


Stand 25.07.2017
