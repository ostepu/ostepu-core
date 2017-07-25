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

(DEPRECATED) Über diese Komponente können verschiedene angehangene Komponenten angesprochen werden. Diese Methode sollte nicht mehr verwendet werden, weil sie fehleranfällig ist.

| Themen |
| :- |
| [Befehle/Eingänge (Commands.json)](#eingaenge) |
| [Ausgänge (Component.json => Links)](#ausgaenge) |
| [Anbindungen (Component.json => Connector)](#anbindungen) |

## <a name='eingaenge'></a>Befehle/Eingänge (Commands.json)
Diese Befehle bietet diese Komponente als Aufruf an.

|||
| :----------- |:----- |
|Beschreibung| Dieser Befehl behandelt POST anfragen und leitet sie an die entsprechende Komponente weiter.|
|Befehl| POST /:string+|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| Dieser Befehl behandelt PUT anfragen und leitet sie an die entsprechende Komponente weiter.|
|Befehl| PUT /:string+|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| Dieser Befehl behandelt GET anfragen und leitet sie an die entsprechende Komponente weiter.|
|Befehl| GET /:string+|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| Dieser Befehl behandelt DELETE anfragen und leitet sie an die entsprechende Komponente weiter.|
|Befehl| DELETE /:string+|
|Eingabetyp| -|
|Ausgabetyp| -|


## <a name='ausgaenge'></a>Ausgänge (Component.json => Links)
Wenn eine Komponente selbst noch Unteranfragen an anderen Komponenten stellen möchte, dann werden diese über die `Ausgänge` bearbeitet.
Dabei kann ein Ausgang bereits auf eine Komponente gerichtet sein (`Ziel`) oder durch die Zielkomponente selbst angebunden werden (`Connector`)

||database|
| :----------- |:----- |
|Ziel| DBControl|
|Befehl| POST /path+|
|Beschreibung| über diesen Ausgang werden DB-Komponenten (Datenbank) angefragt|

||database|
| :----------- |:----- |
|Ziel| DBControl|
|Befehl| GET /path+|
|Beschreibung| über diesen Ausgang werden DB-Komponenten (Datenbank) angefragt|

||database|
| :----------- |:----- |
|Ziel| DBControl|
|Befehl| PUT /path+|
|Beschreibung| über diesen Ausgang werden DB-Komponenten (Datenbank) angefragt|

||database|
| :----------- |:----- |
|Ziel| DBControl|
|Befehl| DELETE /path+|
|Beschreibung| über diesen Ausgang werden DB-Komponenten (Datenbank) angefragt|

||filesystem|
| :----------- |:----- |
|Ziel| FSControl|
|Befehl| POST /path+|
|Beschreibung| über diesen Ausgang werden FS-Komponenten (Dateisystem) angefragt|

||filesystem|
| :----------- |:----- |
|Ziel| FSControl|
|Befehl| GET /path+|
|Beschreibung| über diesen Ausgang werden FS-Komponenten (Dateisystem) angefragt|

||filesystem|
| :----------- |:----- |
|Ziel| FSControl|
|Befehl| PUT /path+|
|Beschreibung| über diesen Ausgang werden FS-Komponenten (Dateisystem) angefragt|

||filesystem|
| :----------- |:----- |
|Ziel| FSControl|
|Befehl| DELETE /path+|
|Beschreibung| über diesen Ausgang werden FS-Komponenten (Dateisystem) angefragt|


## <a name='anbindungen'></a>Anbindungen (Component.json => Connector)
Eine Anbindung verlangt von einer anderen Komponente (`Ziel`) die Anbindung/Verbindung zu dieser Komponente.
Wenn eine Anbindung den aufzurufenden Befehl vorgibt, dann ist die Notation: METHODE URL (PRIORITÄT).

|Ausgang|request|
| :----------- |:----- |
|Ziel| CLocalObjectRequest|
|Beschreibung| damit LController als lokales Objekt aufgerufen werden kann|


Stand 25.07.2017
