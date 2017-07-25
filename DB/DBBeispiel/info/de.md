<!--
  - @file de.md
  -
  - @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
  -
  - @package OSTEPU (https://github.com/ostepu/ostepu-core)
  - @since 0.3.5
  -
  - @author Till Uhlig <till.uhlig@student.uni-halle.de>
  - @date 2015,2017
  -
 -->

Die DBBeispiel ist eine Beispielkomponente.

| Themen |
| :- |
| [Befehle/Eingänge (Commands.json)](#eingaenge) |
| [Ausgänge (Component.json => Links)](#ausgaenge) |
| [Anbindungen (Component.json => Connector)](#anbindungen) |

## <a name='eingaenge'></a>Befehle/Eingänge (Commands.json)
Diese Befehle bietet diese Komponente als Aufruf an.

||getDaten|
| :----------- |:----- |
|Beschreibung| diese Beispielanfrage hat keinen Nutzen|
|Befehl| GET /beispiel/course/:cid|
|Eingabetyp| BEISPIEL|
|Ausgabetyp| BEISPIEL|

||getCourse|
| :----------- |:----- |
|Beschreibung| fragt den Eintrag einer Veranstaltung ab|
|Befehl| GET /course(/course)/:courseid|
|Eingabetyp| -|
|Ausgabetyp| Course|


## <a name='ausgaenge'></a>Ausgänge (Component.json => Links)
Wenn eine Komponente selbst noch Unteranfragen an anderen Komponenten stellen möchte, dann werden diese über die `Ausgänge` bearbeitet.
Dabei kann ein Ausgang bereits auf eine Komponente gerichtet sein (`Ziel`) oder durch die Zielkomponente selbst angebunden werden (`Connector`)

||out|
| :----------- |:----- |
|Ziel| DBQuery2|
|Befehl| POST /query/:abc|
|Beschreibung| über diesen Ausgang werden Anfragen an die Datenbank gestellt|

||getCourse|
| :----------- |:----- |
|Ziel| DBQuery2|
|Befehl| GET /query/procedure/DBCourseGetCourse/:courseid|
|Beschreibung| für den Befehl getCourse|


## <a name='anbindungen'></a>Anbindungen (Component.json => Connector)
Eine Anbindung verlangt von einer anderen Komponente (`Ziel`) die Anbindung/Verbindung zu dieser Komponente.
Wenn eine Anbindung den aufzurufenden Befehl vorgibt, dann ist die Notation: METHODE URL (PRIORITÄT).

|Ausgang|request|
| :----------- |:----- |
|Ziel| CLocalObjectRequest|
|Beschreibung| damit DBBeispiel als lokales Objekt aufgerufen werden kann|


Stand 25.07.2017
