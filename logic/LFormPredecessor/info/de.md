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

Diese Komponente gehört zu den Formuareingaben um LForm und DBForm. Mit LFormPredecessor werden die Eingaben der Studenten in eine PDF umgewandelt und diese als Einsendung behandelt.

| Themen |
| :- |
| [Befehle/Eingänge (Commands.json)](#eingaenge) |
| [Ausgänge (Component.json => Links)](#ausgaenge) |
| [Anbindungen (Component.json => Connector)](#anbindungen) |

## <a name='eingaenge'></a>Befehle/Eingänge (Commands.json)
Diese Befehle bietet diese Komponente als Aufruf an.

|||
| :----------- |:----- |
|Beschreibung| prüft, ob die Komponente korrekt in der Veranstaltung installiert ist|
|Befehl| GET /link/exists/course/:courseid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| behandelt den Verarbeitungsaufruf|
|Befehl| post /process|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| fügt die Komponente einer Veranstaltung hinzu|
|Befehl| post /course|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| entfernt die Komponente aus einer Veranstaltung|
|Befehl| delete /course/:courseid|
|Eingabetyp| -|
|Ausgabetyp| -|


## <a name='ausgaenge'></a>Ausgänge (Component.json => Links)
Wenn eine Komponente selbst noch Unteranfragen an andere Komponenten stellen möchte, dann werden diese über die `Ausgänge` bearbeitet.
Dabei kann ein Ausgang bereits auf eine Komponente gerichtet sein (`Ziel`) oder durch die Zielkomponente selbst angebunden werden (`Connector`)

||formDb|
| :----------- |:----- |
|Ziel| DBForm|
|Befehl| GET /form/exercise/:exerciseid|
|Beschreibung| um die Formulare einer Aufgabe abzurufen|

||pdf|
| :----------- |:----- |
|Ziel| FSPdf|
|Befehl| POST /pdf|
|Beschreibung| zum Anlegen einer PDF|

||postProcess|
| :----------- |:----- |
|Ziel| DBProcessList|
|Befehl| POST /process|
|Beschreibung| über diesen Ausgang trägt sich LFormPredecessor als Verarbeitung in eine Veranstaltung ein|

||deleteProcess|
| :----------- |:----- |
|Ziel| DBProcessList|
|Befehl| DELETE /process/process/:processid|
|Beschreibung| entfernt LFormPredecessor als Verarbeitung aus einer Veranstaltung|

||getProcess|
| :----------- |:----- |
|Ziel| DBProcessList|
|Befehl| GET /process/course/:courseid/component/:componentid|
|Beschreibung| um den eigenen Verarbeitungseintrag zu ermitteln|


## <a name='anbindungen'></a>Anbindungen (Component.json => Connector)
Eine Anbindung verlangt von einer anderen Komponente (`Ziel`) die Anbindung/Verbindung zu dieser Komponente.
Wenn eine Anbindung den aufzurufenden Befehl vorgibt, dann ist die Notation: METHODE URL (PRIORITÄT).

|Ausgang|postCourse|
| :----------- |:----- |
|Ziel| LForm|
|Beschreibung| wenn LForm zu einer Veranstaltung hinzugefügt wird, dann wollen auch aufgerufen werden|

|Ausgang|request|
| :----------- |:----- |
|Ziel| CLocalObjectRequest|
|Beschreibung| damit LFormPredecessor als lokales Objekt aufgerufen werden kann|

|Ausgang|request|
| :----------- |:----- |
|Ziel| CHelp|
|Beschreibung| hier werden Hilfedateien beim zentralen Hilfesystem angemeldet, sodass sie über ihre globale Adresse abgerufen werden können|
|| GET /help/:language/extension/LFormPredecessor/LFormPredecessor.md|


Stand 25.07.2017
