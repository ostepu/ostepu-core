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

## Eingänge
---------------

|||
| :----------- |:-----: |
|Beschreibung| editiert eine Komponentenverbindung|
|Befehl| put<br>/link/:linkid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Beschreibung| entfernt eine Verbindung|
|Befehl| delete<br>/link/:linkid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Beschreibung| entfernt die Komponente aus der Veranstaltung (löscht also die zugehörigen Tabellen)|
|Befehl| DELETE<br>/platform|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Beschreibung| hinterlegt eine neue Komponente|
|Befehl| post<br>/component|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Beschreibung| gibt eine einzelne Verbindung zurück|
|Befehl| get<br>/link/:linkid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Beschreibung| installiert die Komponenten anhand der Definitionen, welche sich in der Datenbank befinden|
|Befehl| get<br>/definition/send|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Beschreibung| editiert einen Komponenteneintrag in der Datenbank|
|Befehl| put<br>/component/:componentid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Beschreibung| entfernt einen Komponenteneintrag|
|Befehl| delete<br>/component/:componentid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Beschreibung| gibt einen einzelnen Komponenteneintrag aus|
|Befehl| get<br>/component/:componentid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Beschreibung| speichert eine neue Verbindung|
|Befehl| post<br>/link|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Beschreibung| gibt die vollständige Definition einer Komponente aus|
|Befehl| get<br>/definition/:componentid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Beschreibung| gibt alle Fremdschlüsselbeziehungen zwischen den Tabellen aus|
|Befehl| get<br>/tableReferences|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Beschreibung| installiert CControl in die Plattform (legt also die Tabellen an)|
|Befehl| POST<br>/platform|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Beschreibung| gibt alle Komponentendefinitionen aus|
|Befehl| get<br>/definition|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Beschreibung| prüft, ob die CControl korrekt in die Plattform installiert wurde|
|Befehl| GET<br>/link/exists/platform|
|Eingabetyp| -|
|Ausgabetyp| -|


Stand 30.06.2017
