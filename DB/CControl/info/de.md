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

Diese Komponente stellt wichtige Aufrufe bereit, um die Installation der Komponenten durchzuführen. Zudem legt sie die Tabellen `Component` und `ComponentLinkage` an und verwaltet diese.

| Themen |
| :- |
| [Befehle/Eingänge (Commands.json)](#eingaenge) |
| [Anbindungen (Component.json => Connector)](#anbindungen) |

## <a name='eingaenge'></a>Befehle/Eingänge (Commands.json)
Diese Befehle bietet diese Komponente als Aufruf an.

|||
| :----------- |:----- |
|Beschreibung| editiert eine Komponentenverbindung|
|Befehl| put /link/:linkid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| entfernt eine Verbindung|
|Befehl| delete /link/:linkid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| entfernt die Komponente aus der Veranstaltung (löscht also die zugehörigen Tabellen)|
|Befehl| DELETE /platform|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| hinterlegt eine neue Komponente|
|Befehl| post /component|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| gibt eine einzelne Verbindung zurück|
|Befehl| get /link/:linkid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| installiert die Komponenten anhand der Definitionen, welche sich in der Datenbank befinden|
|Befehl| get /definition/send|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| editiert einen Komponenteneintrag in der Datenbank|
|Befehl| put /component/:componentid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| entfernt einen Komponenteneintrag|
|Befehl| delete /component/:componentid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| gibt einen einzelnen Komponenteneintrag aus|
|Befehl| get /component/:componentid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| speichert eine neue Verbindung|
|Befehl| post /link|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| gibt die vollständige Definition einer Komponente aus|
|Befehl| get /definition/:componentid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| gibt alle Fremdschlüsselbeziehungen zwischen den Tabellen aus|
|Befehl| get /tableReferences|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| installiert CControl in die Plattform (legt also die Tabellen an)|
|Befehl| POST /platform|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| gibt alle Komponentendefinitionen aus|
|Befehl| get /definition|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| prüft, ob die CControl korrekt in die Plattform installiert wurde|
|Befehl| GET /link/exists/platform|
|Eingabetyp| -|
|Ausgabetyp| -|


## <a name='anbindungen'></a>Anbindungen (Component.json => Connector)
Eine Anbindung verlangt von einer anderen Komponente (`Ziel`) die Anbindung/Verbindung zu dieser Komponente.
Wenn eine Anbindung den aufzurufenden Befehl vorgibt, dann ist die Notation: METHODE URL (PRIORITÄT).

|Ausgang|getDescFiles|
| :----------- |:----- |
|Ziel| TDocuView|
|Beschreibung| die Entwicklerdokumentation soll unsere Beschreibungsdatei nutzen|


Stand 25.07.2017
