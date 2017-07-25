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

Die LOOP Komponente wird beim Erstellen von Übungsserien als Verarbeitung verwendet, dabei bietet sie im wesentlichen die Möglichkeit Java Einsendungen zu compilieren und im Fehlerfall abzulehnen.

| Themen |
| :- |
| [Befehle/Eingänge (Commands.json)](#eingaenge) |
| [Ausgänge (Component.json => Links)](#ausgaenge) |
| [Anbindungen (Component.json => Connector)](#anbindungen) |

## <a name='eingaenge'></a>Befehle/Eingänge (Commands.json)
Diese Befehle bietet diese Komponente als Aufruf an.

|||
| :----------- |:----- |
|Beschreibung| behandelt den Verarbeitungsaufruf von LOOP (also einen Einsenevorgang)|
|Befehl| post /process|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| erstellt Testcases|
|Befehl| post /postprocess|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| fügt LOOP einer Veranstaltung hinzu|
|Befehl| post /course|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| entfernt LOOP aus einer Veranstaltung|
|Befehl| delete /course/:courseid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| prüft, ob LOOP in einer Veranstaltung installiert ist|
|Befehl| get /link/exists/course/:courseid|
|Eingabetyp| -|
|Ausgabetyp| -|

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

|||
| :----------- |:----- |
|Beschreibung| zum n-maligen Starten der Compute Funktion|
|Befehl| get /start/:count|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| zum n-maligen Starten der Compute Funktion|
|Befehl| get /compute/:count|
|Eingabetyp| -|
|Ausgabetyp| -|


## <a name='ausgaenge'></a>Ausgänge (Component.json => Links)
Wenn eine Komponente selbst noch Unteranfragen an andere Komponenten stellen möchte, dann werden diese über die `Ausgänge` bearbeitet.
Dabei kann ein Ausgang bereits auf eine Komponente gerichtet sein (`Ziel`) oder durch die Zielkomponente selbst angebunden werden (`Connector`)

||pdf|
| :----------- |:----- |
|Ziel| FSPdf|
|Befehl| POST /pdf|
|Beschreibung| zum Erzeugen einer PDF|

||postProcess|
| :----------- |:----- |
|Ziel| DBProcessList|
|Befehl| POST /process|
|Beschreibung| um LOOP als Verarbeitung einzutragen|

||deleteProcess|
| :----------- |:----- |
|Ziel| DBProcessList|
|Befehl| DELETE /process/process/:processid|
|Beschreibung| um LOOP als Verarbeitung zu entfernen|

||getProcess|
| :----------- |:----- |
|Ziel| DBProcessList|
|Befehl| GET /process/course/:courseid/component/:componentid|
|Beschreibung| um den eigenen Verarbeitungseintrag abzurufen|

||postCourse|
| :----------- |:----- |
|Ziel| DBOOP|
|Befehl| POST /course|
|Beschreibung| fügt LOOP einer Veranstaltung hinzu|

||deleteCourse|
| :----------- |:----- |
|Ziel| DBOOP|
|Befehl| DELETE /course/:courseid|
|Beschreibung| entfernt die Komponente aus einer Veranstaltung|

||postTestcase|
| :----------- |:----- |
|Ziel| DBOOP|
|Befehl| POST /insert|
|Beschreibung| um einen Testfall zu registrieren|

||popTestcase|
| :----------- |:----- |
|Ziel| DBOOP|
|Befehl| GET /pop|
|Beschreibung| ruft den nächsten Testfall ab|

||editTestcase|
| :----------- |:----- |
|Ziel| DBOOP|
|Befehl| POST /testcase/testcase/:testcaseid|
|Beschreibung| um einen Testfall zu bearbeiten|

||getTestcase|
| :----------- |:----- |
|Ziel| DBOOP|
|Befehl| GET /testcase/submission/:sid/course/:cid|
|Beschreibung| ruft alle Testfälle einer Veranstaltung und einer Einsendung ab|

||getExercise|
| :----------- |:----- |
|Ziel| DBExercise|
|Befehl| GET /exercise/exercise/:eid/nosubmission|
|Beschreibung| ruft eine Aufgabe ab|

||marking|
| :----------- |:----- |
|Ziel| LMarking|
|Befehl| POST /marking|
|Beschreibung| um eine Korrektur zu speichern|


## <a name='anbindungen'></a>Anbindungen (Component.json => Connector)
Eine Anbindung verlangt von einer anderen Komponente (`Ziel`) die Anbindung/Verbindung zu dieser Komponente.
Wenn eine Anbindung den aufzurufenden Befehl vorgibt, dann ist die Notation: METHODE URL (PRIORITÄT).

|Ausgang|extension|
| :----------- |:----- |
|Ziel| LExtension|
|Beschreibung| diese Komponente soll als Veranstaltungserweiterung wählbar sein|

|Ausgang|request|
| :----------- |:----- |
|Ziel| CLocalObjectRequest|
|Beschreibung| damit LOOP als lokales Objekt aufgerufen werden kann|

|Ausgang|request|
| :----------- |:----- |
|Ziel| CHelp|
|Beschreibung| hier werden Hilfedateien beim zentralen Hilfesystem angemeldet, sodass sie über ihre globale Adresse abgerufen werden können|
|| GET /help/:language/extension/LOOP/LOOP.md|
|| GET /help/:language/extension/LOOP/LOOPDesc.md|
|| GET /help/:language/extension/LOOP/LOOPA.png|
|| GET /help/:language/extension/LOOP/LOOPB.png|
|| GET /help/:language/extension/LOOP/LOOPC.png|
|| GET /help/:language/extension/LOOP/LOOPD.png|
|| GET /help/:language/extension/LOOP/LOOPE.png|
|| GET /help/:language/extension/LOOP/LOOPF.png|
|| GET /help/:language/extension/LOOP/LOOPG.png|

|Ausgang|postPlatform|
| :----------- |:----- |
|Ziel| CInstall|
|Beschreibung| der Installationsassistent soll uns bei der Plattforminstallation aufrufen|


Stand 25.07.2017
