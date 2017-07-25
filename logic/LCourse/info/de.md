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

Die LCourse bietet Anfragen zum Anlegen einer neuen Veranstaltung an.

| Themen |
| :- |
| [Befehle/Eingänge (Commands.json)](#eingaenge) |
| [Ausgänge (Component.json => Links)](#ausgaenge) |
| [Anbindungen (Component.json => Connector)](#anbindungen) |

## <a name='eingaenge'></a>Befehle/Eingänge (Commands.json)
Diese Befehle bietet diese Komponente als Aufruf an.

|||
| :----------- |:----- |
|Beschreibung| zum Erstellen einer neuen Veranstaltung|
|Befehl| post /course|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| ändert eine Veranstaltung|
|Befehl| put /course/course/:courseid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| entfernt eine Veranstaltung aus dem System|
|Befehl| delete /course/course/:courseid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| trägt einen Nutzer in eine Veranstaltung|
|Befehl| post /course/course/:courseid/user/:userid/status/:status|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| ermittelt alle Nutzer einer Veranstaltung|
|Befehl| get /course/course/:courseid/user|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| ermittelt alle Veranstaltungen eines Nutzers|
|Befehl| get /course/user/:userid|
|Eingabetyp| -|
|Ausgabetyp| -|


## <a name='ausgaenge'></a>Ausgänge (Component.json => Links)
Wenn eine Komponente selbst noch Unteranfragen an andere Komponenten stellen möchte, dann werden diese über die `Ausgänge` bearbeitet.
Dabei kann ein Ausgang bereits auf eine Komponente gerichtet sein (`Ziel`) oder durch die Zielkomponente selbst angebunden werden (`Connector`)

||controller|
| :----------- |:----- |
|Ziel| LController|
|Befehl| PUT /DB/course/course/:courseid|
|Beschreibung| Über diesen Ausgang werden verschiedene Anfragen an Datenbankkomponenten gestellt. Es muss sich um eine spezielle Verteilerkomponente handeln (normalerweise LController).|

||controller|
| :----------- |:----- |
|Ziel| LController|
|Befehl| POST /DB/coursestatus|
|Beschreibung| Über diesen Ausgang werden verschiedene Anfragen an Datenbankkomponenten gestellt. Es muss sich um eine spezielle Verteilerkomponente handeln (normalerweise LController).|

||controller|
| :----------- |:----- |
|Ziel| LController|
|Befehl| GET /DB/user/course/:courseid|
|Beschreibung| Über diesen Ausgang werden verschiedene Anfragen an Datenbankkomponenten gestellt. Es muss sich um eine spezielle Verteilerkomponente handeln (normalerweise LController).|

||controller|
| :----------- |:----- |
|Ziel| LController|
|Befehl| GET /DB/course/user/:userid|
|Beschreibung| Über diesen Ausgang werden verschiedene Anfragen an Datenbankkomponenten gestellt. Es muss sich um eine spezielle Verteilerkomponente handeln (normalerweise LController).|

||postCourse|
| :----------- |:----- |
|Ziel| DBCourse|
|Befehl| POST /course|
|Beschreibung| zum Anlegen einer Veranstaltung (in postCourse)|

||deleteCourse|
| :----------- |:----- |
|Ziel| DBCourse|
|Befehl| DELETE /course/:courseid|
|Beschreibung| zum Entfernen einer Veranstaltung|


## <a name='anbindungen'></a>Anbindungen (Component.json => Connector)
Eine Anbindung verlangt von einer anderen Komponente (`Ziel`) die Anbindung/Verbindung zu dieser Komponente.
Wenn eine Anbindung den aufzurufenden Befehl vorgibt, dann ist die Notation: METHODE URL (PRIORITÄT).

|Ausgang|request|
| :----------- |:----- |
|Ziel| CLocalObjectRequest|
|Beschreibung| damit LCourse als lokales Objekt aufgerufen werden kann|


Stand 25.07.2017
