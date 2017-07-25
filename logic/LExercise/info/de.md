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

Die LExercise bietet Anfragen für Übungsaufgaben an. Dabei enthält sie zunächst nur einen Befehl zum Speichern neuer Übungsaufgaben.

| Themen |
| :- |
| [Befehle/Eingänge (Commands.json)](#eingaenge) |
| [Ausgänge (Component.json => Links)](#ausgaenge) |

## <a name='eingaenge'></a>Befehle/Eingänge (Commands.json)
Diese Befehle bietet diese Komponente als Aufruf an.

|||
| :----------- |:----- |
|Beschreibung| Dieser Befehl erlaubt das Anlegen neuer Übungsaufgaben|
|Befehl| POST /exercise|
|Eingabetyp| -|
|Ausgabetyp| -|


## <a name='ausgaenge'></a>Ausgänge (Component.json => Links)
Wenn eine Komponente selbst noch Unteranfragen an anderen Komponenten stellen möchte, dann werden diese über die `Ausgänge` bearbeitet.
Dabei kann ein Ausgang bereits auf eine Komponente gerichtet sein (`Ziel`) oder durch die Zielkomponente selbst angebunden werden (`Connector`)

||controller|
| :----------- |:----- |
|Ziel| LController|
|Befehl| POST /FS/file|
|Beschreibung| über diesen Ausgang werden diverse Anfragen behandelt|

||controller|
| :----------- |:----- |
|Ziel| LController|
|Befehl| POST /DB/file|
|Beschreibung| über diesen Ausgang werden diverse Anfragen behandelt|

||controller|
| :----------- |:----- |
|Ziel| LController|
|Befehl| GET /hash/:hash|
|Beschreibung| über diesen Ausgang werden diverse Anfragen behandelt|

||controller|
| :----------- |:----- |
|Ziel| LController|
|Befehl| POST /DB/exercise|
|Beschreibung| über diesen Ausgang werden diverse Anfragen behandelt|

||controller|
| :----------- |:----- |
|Ziel| LController|
|Befehl| POST /DB/exercisefiletype|
|Beschreibung| über diesen Ausgang werden diverse Anfragen behandelt|

||controller|
| :----------- |:----- |
|Ziel| LController|
|Befehl| GET /DB/exercise/:exerciseid|
|Beschreibung| über diesen Ausgang werden diverse Anfragen behandelt|

||controller|
| :----------- |:----- |
|Ziel| LController|
|Befehl| DELETE /DB/exercise/exercise/:$exerciseid|
|Beschreibung| über diesen Ausgang werden diverse Anfragen behandelt|

||controller|
| :----------- |:----- |
|Ziel| LController|
|Befehl| PUT /DB/exercise/:exerciseid|
|Beschreibung| über diesen Ausgang werden diverse Anfragen behandelt|

||postAttachment|
| :----------- |:----- |
|Ziel| LAttachment|
|Befehl| POST /attachment|
|Beschreibung| zum Speichern von Übungsanhängen|


Stand 25.07.2017
