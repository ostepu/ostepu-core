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

Diese Komponente bietet Befehle zum Anlegen, Abrufen, Löschen und Ändern von Aufgabenanhängen.

| Themen |
| :- |
| [Befehle/Eingänge (Commands.json)](#eingaenge) |
| [Ausgänge (Component.json => Links)](#ausgaenge) |

## <a name='eingaenge'></a>Befehle/Eingänge (Commands.json)
Diese Befehle bietet diese Komponente als Aufruf an.

|||
| :----------- |:----- |
|Beschreibung| speichert einen neuen Anhang|
|Befehl| POST /attachment|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| ruft einen Anhang ab|
|Befehl| GET '/attachment/attachment/:attachmentid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| entfernt einen Anhang aus dem System|
|Befehl| DELETE /attachment/attachment/:attachmentid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| ändert einen Anhang|
|Befehl| PUT /attachment/attachment/:attachmentid|
|Eingabetyp| -|
|Ausgabetyp| -|


## <a name='ausgaenge'></a>Ausgänge (Component.json => Links)
Wenn eine Komponente selbst noch Unteranfragen an anderen Komponenten stellen möchte, dann werden diese über die `Ausgänge` bearbeitet.
Dabei kann ein Ausgang bereits auf eine Komponente gerichtet sein (`Ziel`) oder durch die Zielkomponente selbst angebunden werden (`Connector`)

||controller|
| :----------- |:----- |
|Ziel| LController|
|Befehl| POST /DB/attachment|
|Beschreibung| über diesen Ausgang werden die Anhänge behandelt|

||controller|
| :----------- |:----- |
|Ziel| LController|
|Befehl| GET /DB/attachment/attachment/:attachmentid|
|Beschreibung| über diesen Ausgang werden die Anhänge behandelt|

||controller|
| :----------- |:----- |
|Ziel| LController|
|Befehl| DELETE /DB/attachment/:attachmentid|
|Beschreibung| über diesen Ausgang werden die Anhänge behandelt|

||controller|
| :----------- |:----- |
|Ziel| LController|
|Befehl| PUT /DB/attachment/:attachmentid|
|Beschreibung| über diesen Ausgang werden die Anhänge behandelt|

||postFile|
| :----------- |:----- |
|Ziel| LFile|
|Befehl| POST /file|
|Beschreibung| zum Speichern von Dateien beim Anlegen/Ändern eines Anhangs|

||postAttachment|
| :----------- |:----- |
|Ziel| DBAttachment|
|Befehl| POST /attachment|
|Beschreibung| zum Speichern eines Anhangs|


Stand 25.07.2017
