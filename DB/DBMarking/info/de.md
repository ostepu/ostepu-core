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

#### Datenbank
Die DBMarking ermöglicht den Zugriff auf die `Marking` Tabelle der Datenbank, dabei sollen
Korrekturen, zu Einsendungen, verwaltet werden.
Dazu wird bei einem `POST /platform` Aufruf die nachstehende Tabelle erzeugt.

| Spalte        | Struktur  | Beschreibung | Besonderheit |
| :------       |:---------:| :------------| -----------: |
|M_id|INT NOT NULL| die ID der Korrektur |AUTO_INCREMENT,<br>UNIQUE|
|U_id_tutor|INT NOT NULL| ein Verweis auf das Nutzerkonto des Kontrolleurs (bei automatisch kontrollierten steht hier der Einsender) |-|
|F_id_file|INT NULL| ein Verweis auf den Korrektureintrag |-|
|S_id|INT NOT NULL| ein Verweis auf die Einsendung |-|
|M_tutorComment|VARCHAR(255) NULL| ein Kommentar des Kontrolleurs |-|
|M_outstanding|TINYINT(1) NULL DEFAULT 0| hier kann die Einsendung als "besonders" markiert werden (1 = besonders, 0 = normal) |-|
|M_status|TINYINT NOT NULL DEFAULT 0| der Korrekturstatus (siehe Marking::getStatusDefinition()) |-|
|M_points|FLOAT NULL DEFAULT 0| die durch den Kontrolleur vergebenen Punkte |-|
|M_date|INT UNSIGNED NULL DEFAULT 0| das Datum der Korrektur (als Unix-Zeitstempel) |-|
|E_id|INT NULL| ein Verweis auf die zugehörige Aufgabe |-|
|ES_id|INT NULL| ein Verweis auf die Übungsserie |-|
|M_hideFile|TINYINT NOT NULL DEFAULT 0| ein Korrektur kann ausgeblendet werden, wenn beispielweise ein manuelle Nachkorrektur vorgenommen wurde (1 = ausgeblendet, 0 = sichtbar) |-|

#### Datenstruktur
Zu dieser Tabelle gehört die `Marking` Datenstruktur.

#### Eingänge
- courseid = eine Veranstaltungs ID (`Course`)
- userid = eine Veranstaltungs ID (`User`)
- sub = bestimmt, ob keine Einsendungen mit zurückgegeben werden sollen ('nosubmission' = keine Einsendungen, sonst = mit Einsendungen)
- mid = die ID einer Korrektur (`Marking`)
- eid = die ID einer Aufgabe (`Exercise`)
- esid = die ID einer Übungsserie (`ExerciseSheet`)
- suid = die ID einer Einsendung (`Submission`)

| Bezeichnung  | Eingabetyp  | Ausgabetyp | Befehl | Beschreibung |
| :----------- |:-----------:| :---------:| :----- | :----------- |
|editMarking|Marking|Marking|PUT<br>/marking(/marking)/:mid| editiert eine Korrektur |
|deleteMarking|-|Marking|DELETE<br>/marking(/marking)/:mid| entfernt eine Korrektur (hat auch Auswirkungen auf Gruppenmitglieder) |
|deleteSheetMarkings|-|Marking|DELETE<br>/marking(/marking)/exercisesheet/:esid| entfernt alle Korrekturen einer Übungsserie ((at auch Auswirkungen auf Gruppenmitglieder) |
|addMarking|Marking|Marking|POST<br>/marking| fügt eine neue Korrektur ein |
|getSubmissionMarking|-|Marking|GET<br>/marking/submission/:suid(/:sub)| liefert alle Korrekturen zu einer Einsendung |
|getExerciseMarkings|-|Marking|GET<br>/marking/exercise/:eid(/:sub)| liefert alle Korrekturen einer Aufgabe |
|getSheetMarkings|-|Marking|GET<br>/marking/exercisesheet/:esid(/:sub)| liefert alle Korrekturen einer Übungsserie |
|getCourseMarkings|-|Marking|GET<br>/marking/course/:courseid(/:sub)| liefert alle Korrekturen einer Veranstaltung |
|getUserGroupMarkings|-|Marking|GET<br>/marking/exercisesheet/:esid/user/:userid(/:sub)| gibt alle Korrekturen eines Nutzers und seiner zugehörigen Gruppe aus |
|getCourseUserGroupMarkings|-|Marking|GET<br>/marking/course/:courseid/user/:userid(/:sub)| gibt alle Korrekturen eines Nutzers und seiner zugehörigen Gruppe aus |
|getTutorSheetMarkings|-|Marking|GET<br>/marking/exercisesheet/:esid/tutor/:userid(/:sub)| liefert Korrekturen eines Kontrolleurs |
|getTutorExerciseMarkings|-|Marking|GET<br>/marking/exercise/:eid/tutor/:userid(/:sub)| liefert Korrekturen eines Kontrolleurs |
|getMarking|-|Marking|GET<br>/marking(/marking)/:mid(/:sub)| liefert eine einzelne Korrektur |
|getAllMarkings|-|Marking|GET<br>/marking(/marking)(/:sub)| liefert alle Korrekturen (aller Veranstaltungen) |
|addPlatform|Platform|Platform|POST<br>/platform|installiert dies zugehörige Tabelle und die Prozeduren für diese Plattform|
|deletePlatform|-|Platform|DELETE<br>/platform|entfernt die Tabelle und Prozeduren aus der Plattform|
|getExistsPlatform|-|Platform|GET<br>/link/exists/platform| prüft, ob die Tabelle und die Prozeduren existieren |
|getSamplesInfo|-|-|GET<br>/samples| ??? |
|postSamples|-|Query|POST<br>/samples/course/:courseAmount<br>/user/:userAmount| erzeugt Zufallsdaten (courseAmount = Anzahl der Veranstaltungen, userAmount = Anzahl der Nutzer), anhand der Vorgabe |

#### Ausgänge
- courseid = eine Veranstaltungs ID (`Course`)
- userid = eine Veranstaltungs ID (`User`)
- sub = bestimmt, ob keine Einsendungen mit zurückgegeben werden sollen ('nosubmission' = keine Einsendungen, sonst = mit Einsendungen)
- mid = die ID einer Korrektur (`Marking`)
- eid = die ID einer Aufgabe (`Exercise`)
- esid = die ID einer Übungsserie (`ExerciseSheet`)
- suid = die ID einer Einsendung (`Submission`)

| Bezeichnung  | Ziel  | Verwendung | Beschreibung |
| :----------- |:----- | :--------- | :----------- |
|out2|DBQuery2|POST<br>/query| wird für EDIT, DELETE<br>und POST<br>SQL-Templates verwendet |
|out|DBQuery|POST<br>/query| wird für EDIT, DELETE<br>und POST<br>SQL-Templates verwendet |
|getMarking|DBQuery2|GET<br>/query/procedure<br>/DBMarkingGetMarking/:mid/:sub| Prozeduraufruf |
|getSubmissionMarking|DBQuery2|GET<br>/query/procedure<br>/DBMarkingGetSubmissionMarking/:suid/:sub| Prozeduraufruf |
|getAllMarkings|DBQuery2|GET<br>/query/procedure<br>/DBExerciseGetAllMarkings/:sub| Prozeduraufruf |
|getCourseMarkings|DBQuery2|GET<br>/query/procedure<br>/DBMarkingGetCourseMarkings/:courseid/:sub| Prozeduraufruf |
|getExerciseMarkings|DBQuery2|GET<br>/query/procedure<br>/DBMarkingGetExerciseMarkings/:eid/:sub| Prozeduraufruf |
|getSheetMarkings|DBQuery2|GET<br>/query/procedure<br>/DBMarkingGetSheetMarkings/:esid/:sub| Prozeduraufruf |
|getTutorCourseMarkings|DBQuery2|GET<br>/query/procedure<br>/DBMarkingGetTutorCourseMarkings/:courseid/:userid/:sub| Prozeduraufruf |
|getTutorExerciseMarkings|DBQuery2|GET<br>/query/procedure<br>/DBMarkingGetTutorExerciseMarkings/:eid/:userid/:sub| Prozeduraufruf |
|getTutorSheetMarkings|DBQuery2|GET<br>/query/procedure<br>/DBMarkingGetTutorSheetMarkings/:esid/:userid/:sub| Prozeduraufruf |
|getUserGroupMarkings|DBQuery2|GET<br>/query/procedure<br>/DBMarkingGetUserGroupMarkings/:esid/:userid/:sub| Prozeduraufruf |
|getCourseUserGroupMarkings|DBQuery2|GET<br>/query/procedure<br>/DBMarkingGetCourseUserGroupMarkings/:courseid/:userid/:sub| Prozeduraufruf |
|getExistsPlatform|DBQuery2|GET<br>/query/procedure<br>/DBFileGetExistsPlatform| Prozeduraufruf |

#### Anbindungen
| Bezeichnung  | Ziel  | Priorität | Beschreibung |
| :----------- |:----- | :--------:| :------------|
|request|CLocalObjectRequest|-| damit DBMarking als lokales Objekt aufgerufen werden kann |

Stand 13.06.2015