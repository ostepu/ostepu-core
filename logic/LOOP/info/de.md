<!--
  - @file de.md
  -
  - @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
  -
  - @package OSTEPU (https://github.com/ostepu/system)
  - @since 0.3.4
  -
  - @author Till Uhlig <till.uhlig@student.uni-halle.de>
  - @date 2015
 -->

Die LOOP Komponente wird beim Erstellen von Übungsserien als Verarbeitung verwendet,
dabei bietet sie im wesentlichen die Möglichkeit Java Einsendungen zu compilieren und
im Fehlerfall abzulehnen.

#### Eingänge
- courseid = eine Veranstaltungs ID (`Course`)

| Bezeichnung  | Eingabetyp  | Ausgabetyp | Befehl | Beschreibung |
| :----------- |:-----------:| :---------:| :----- | :----------- |
| AddCourse |Course|Course|POST /course| installiert diese Komponente, in die übergebene Veranstaltung |
| DeleteCourse |-|Course|DELETE /course/:courseid| deinstalliert diese Komponente für diese Veranstaltung |
| GetExistsCourse |-|Course|GET /link/exists/course/:courseid| prüft, ob ein Eintrag in der Process Tabelle, der zugehörigen Veranstaltung, exisitert. Sollte der Eintrag existieren,gilt die LOOP, als Verarbeitung für Einsendungen, als installiert.  |
| PostProcess |Process|Process|POST /process| verarbeitet die eingehende Einsendung |

#### Ausgänge
- courseid = eine Veranstaltungs ID (`Course`)
- processid = die ID eines Prozesses (`Process`)
- componentid = die ID einer Komponente (`Component`)

| Bezeichnung  | Ziel  | Verwendung | Beschreibung |
| :----------- |:----- | :--------- | :----------- |
|pdf|FSPdf| ??? | ??? |
|postProcess|DBProcessList|POST /process| ??? |
|deleteProcess|DBProcessList|DELETE /process/process/:processid| ??? |
|getProcess|DBProcessList|GET /process/course/:courseid/component/:componentid| ??? |

#### Anbindungen
| Bezeichnung  | Ziel  | Priorität | Beschreibung |
| :----------- |:----- | :--------:| :------------|
|request|CLocalObjectRequest|-| damit LOOP als lokales Objekt aufgerufen werden kann |
|extension|LExtension|150| diese Verbindung ist notwendig, damit die LOOP in den Veranstaltungseinstellungen und `Erweiterungen` erscheint und installierbar wird |

Stand 13.06.2015