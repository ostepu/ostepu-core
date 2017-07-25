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

Diese Komponente ermöglicht das einfache Speichern von Korrekturobjekten. Dabei prüft sie selbständig, ob die Korrekturdatei in der Plattform hinterlegt werden muss und ob es sich um eine Änderung oder eine Neuerstellung des Korrekturobjekts handelt.

| Themen |
| :- |
| [Befehle/Eingänge (Commands.json)](#eingaenge) |
| [Ausgänge (Component.json => Links)](#ausgaenge) |
| [Anbindungen (Component.json => Connector)](#anbindungen) |

## <a name='eingaenge'></a>Befehle/Eingänge (Commands.json)
Diese Befehle bietet diese Komponente als Aufruf an.

|||
| :----------- |:----- |
|Beschreibung| speichert/ändert das Korrekturobjekt (wenn die ID gesetzt ist, dann wird eine Änderung ausgeführt)|
|Befehl| POST /marking|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| löscht den Korrektureintrag aus der Datenbank|
|Befehl| DELETE /marking/marking/:markingid|
|Eingabetyp| -|
|Ausgabetyp| -|


## <a name='ausgaenge'></a>Ausgänge (Component.json => Links)
Wenn eine Komponente selbst noch Unteranfragen an andere Komponenten stellen möchte, dann werden diese über die `Ausgänge` bearbeitet.
Dabei kann ein Ausgang bereits auf eine Komponente gerichtet sein (`Ziel`) oder durch die Zielkomponente selbst angebunden werden (`Connector`)

||file|
| :----------- |:----- |
|Ziel| LFile|
|Befehl| POST /file|
|Beschreibung| zum Speichern von Dateien|

||marking|
| :----------- |:----- |
|Ziel| DBMarking|
|Befehl| DELETE /marking/marking/:markingid|
|Beschreibung| zum Speichern, Ändern und Löschen von Korrekturen|

||marking|
| :----------- |:----- |
|Ziel| DBMarking|
|Befehl| POST /marking|
|Beschreibung| zum Speichern, Ändern und Löschen von Korrekturen|

||marking|
| :----------- |:----- |
|Ziel| DBMarking|
|Befehl| PUT /marking/marking/:markingid|
|Beschreibung| zum Speichern, Ändern und Löschen von Korrekturen|


## <a name='anbindungen'></a>Anbindungen (Component.json => Connector)
Eine Anbindung verlangt von einer anderen Komponente (`Ziel`) die Anbindung/Verbindung zu dieser Komponente.
Wenn eine Anbindung den aufzurufenden Befehl vorgibt, dann ist die Notation: METHODE URL (PRIORITÄT).

|Ausgang|request|
| :----------- |:----- |
|Ziel| CLocalObjectRequest|
|Beschreibung| damit LMarking als lokales Objekt aufgerufen werden kann|

|Ausgang|getDescFiles|
| :----------- |:----- |
|Ziel| TDocuView|
|Beschreibung| die Entwicklerdokumentation soll unsere Beschreibungsdatei nutzen|


Stand 25.07.2017
