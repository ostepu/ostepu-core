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

Die LExtension verwaltet Komponenten welche an eine Veranstaltung angehangen werden können. Dazu müssen sich diese Komponenten am 'extension'-Ausgang registrieren und selbst über POST /course installierbar sein.

| Themen |
| :- |
| [Befehle/Eingänge (Commands.json)](#eingaenge) |
| [Ausgänge (Component.json => Links)](#ausgaenge) |
| [Anbindungen (Component.json => Connector)](#anbindungen) |

## <a name='eingaenge'></a>Befehle/Eingänge (Commands.json)
Diese Befehle bietet diese Komponente als Aufruf an.

|||
| :----------- |:----- |
|Beschreibung| installiert eine Veranstaltungserweiterung|
|Befehl| post /link/course/:courseid/extension/:name|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| entfernt eine Erweiterung aus einer Veranstaltung|
|Befehl| delete /link/course/:courseid/extension/:name|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| entfernt die Komponente aus der Veranstaltung|
|Befehl| delete /link/course/:courseid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| prüft, ob eine Erweiterung in einer Veranstatung installiert ist|
|Befehl| get /link/exists/course/:courseid/extension/:name|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| ermittelt alle installierten Erweiterungen einer Veranstaltung|
|Befehl| get /link/course/:courseid/extension|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| ermittelt alle installierbaren Erweiterungen|
|Befehl| get /link/extension|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| prüft, ob eine bestimmte Erweiterung existiert|
|Befehl| get /link/exists/extension/:name|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| ermittelt die Komponentendaten einer Erweiterung|
|Befehl| get /link/extension/:name|
|Eingabetyp| -|
|Ausgabetyp| -|


## <a name='ausgaenge'></a>Ausgänge (Component.json => Links)
Wenn eine Komponente selbst noch Unteranfragen an andere Komponenten stellen möchte, dann werden diese über die `Ausgänge` bearbeitet.
Dabei kann ein Ausgang bereits auf eine Komponente gerichtet sein (`Ziel`) oder durch die Zielkomponente selbst angebunden werden (`Connector`)

||extension|
| :----------- |:----- |
|Ziel| |
|Befehl| DELETE /course/:courseid|
|Beschreibung| An diesen Ausgang können sich die Komponenten hängen, welche als Veranstaltungserweiterung nutzbar sein wollen.|

||extension|
| :----------- |:----- |
|Ziel| |
|Befehl| POST /course|
|Beschreibung| An diesen Ausgang können sich die Komponenten hängen, welche als Veranstaltungserweiterung nutzbar sein wollen.|

||extension|
| :----------- |:----- |
|Ziel| |
|Befehl| GET /link/exists/course/:courseid|
|Beschreibung| An diesen Ausgang können sich die Komponenten hängen, welche als Veranstaltungserweiterung nutzbar sein wollen.|


## <a name='anbindungen'></a>Anbindungen (Component.json => Connector)
Eine Anbindung verlangt von einer anderen Komponente (`Ziel`) die Anbindung/Verbindung zu dieser Komponente.
Wenn eine Anbindung den aufzurufenden Befehl vorgibt, dann ist die Notation: METHODE URL (PRIORITÄT).

|Ausgang|request|
| :----------- |:----- |
|Ziel| CLocalObjectRequest|
|Beschreibung| damit LExtension als lokales Objekt aufgerufen werden kann|


Stand 25.07.2017
