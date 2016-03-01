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
Die DBSelectedSubmission ermöglicht den Zugriff auf die `SelectedSubmission` Tabelle der Datenbank, dabei sollen
für einen Gruppeneintrag ausgewählte Einsendungen (für eine Aufgabe) verwaltet werden.
Dazu wird bei einem `POST /platform` Aufruf die nachstehende Tabelle erzeugt.

| Spalte        | Struktur  | Beschreibung | Besonderheit |
| :------       |:---------:| :------------| -----------: |
|U_id_leader|INT NOT NULL| ein Verweis auf ein Nutzerkonto, welches die Gruppe repräsentiert |-|
|S_id_selected|INT NOT NULL| ein Verweis auf die ausgewählte Einsendung |UNIQUE|
|E_id|INT NOT NULL| ein Verweis auf die zugehörige Aufgabe |-|
|ES_id|INT NULL| ein Verweis auf die Übungsserie |-|

#### Datenstruktur
Zu dieser Tabelle gehört die `SelectedSubmission` Datenstruktur.

#### Eingänge
- courseid = eine Veranstaltungs ID (`Course`)
- userid = die ID eines Nutzerkontos (`User`)
- esid = die ID einer Übungsserie (`ExerciseSheet`)
- eid = die ID einer Aufgabe (`Exercise`)
- suid = die ID einer Einsendung (`Submission`)

| Bezeichnung  | Eingabetyp  | Ausgabetyp | Befehl | Beschreibung |
| :----------- |:-----------:| :---------:| :----- | :----------- |
|editSelectedSubmission|SelectedSubmission|SelectedSubmission|PUT<br>/selectedsubmission/leader/:userid/exercise/:eid| editiert einen Auswahleintrag |
|deleteSelectedSubmission|-|SelectedSubmission|DELETE<br>/selectedsubmission/leader/:userid/exercise/:eid| entfernt eine Auswahl |
|editSubmissionSelectedSubmission|SelectedSubmission|SelectedSubmission|PUT<br>/selectedsubmission/submission/:suid| editiert einen Auswahleintrag |
|deleteSubmissionSelectedSubmission|-|SelectedSubmission|DELETE<br>/selectedsubmission/submission/:suid| entfernt eine Auswahl |
|deleteUserSheetSelectedSubmission|-|SelectedSubmission|DELETE<br>/selectedsubmission/user/:userid/exercisesheet/:esid| entfernt alle Auswahleinträge der Einsendungen eines Nutzers für eine Veranstaltung |
|addSelectedSubmission|SelectedSubmission|SelectedSubmission|POST<br>/selectedsubmission| fügt eine neue Auswahl ein |
|getExerciseSelected|-|SelectedSubmission|GET<br>/selectedsubmission/exercise/:eid| liefert alle Auswahleinträge einer Aufgabe |
|getSheetSelected|-|SelectedSubmission|GET<br>/selectedsubmission/exercisesheet/:esid| liefert alle Auswahleinträge einer Übungsserie |
|getCourseSelected|-|SelectedSubmission|GET<br>/selectedsubmission/course/:courseid| liefert alle Auswahleinträge einer Veranstaltung |
|addPlatform|Platform|Platform|POST<br>/platform|installiert dies zugehörige Tabelle und die Prozeduren für diese Plattform|
|deletePlatform|-|Platform|DELETE<br>/platform|entfernt die Tabelle und Prozeduren aus der Plattform|
|getExistsPlatform|-|Platform|GET<br>/link/exists/platform| prüft, ob die Tabelle und die Prozeduren existieren |
|getSamplesInfo|-|-|GET<br>/samples| ??? |
|postSamples|-|Query|POST<br>/samples/course/:courseAmount<br>/user/:userAmount| erzeugt Zufallsdaten (courseAmount = Anzahl der Veranstaltungen, userAmount = Anzahl der Nutzer), anhand der Vorgabe |

#### Ausgänge
- courseid = eine Veranstaltungs ID (`Course`)
- userid = die ID eines Nutzerkontos (`User`)
- esid = die ID einer Übungsserie (`ExerciseSheet`)
- eid = die ID einer Aufgabe (`Exercise`)
- suid = die ID einer Einsendung (`Submission`)

| Bezeichnung  | Ziel  | Verwendung | Beschreibung |
| :----------- |:----- | :--------- | :----------- |
|out2|DBQuery2|POST<br>/query| wird für EDIT, DELETE<br>und POST<br>SQL-Templates verwendet |
|out|DBQuery|POST<br>/query| wird für EDIT, DELETE<br>und POST<br>SQL-Templates verwendet |
|getCourseSelected|DBQuery2|GET<br>/query/procedure<br>/DBSelectedSubmissionGetCourseSelected/:courseid| Prozeduraufruf |
|getExerciseSelected|DBQuery2|GET<br>/query/procedure<br>/DBSelectedSubmissionGetExerciseSelected/:eid| Prozeduraufruf |
|getSheetSelected|DBQuery2|GET<br>/query/procedure<br>/DBSelectedSubmissionGetSheetSelected/:esid| Prozeduraufruf |
|getExistsPlatform|DBQuery2|GET<br>/query/procedure<br>/DBSelectedSubmissionGetExistsPlatform| Prozeduraufruf |
|getSamplesInfo|DBQuery2|GET<br>/query/procedure<br>/DBSelectedSubmissionGetExistsPlatform| Prozeduraufruf |

#### Anbindungen
| Bezeichnung  | Ziel  | Priorität | Beschreibung |
| :----------- |:----- | :--------:| :------------|
|request|CLocalObjectRequest|-| damit DBSelectedSubmission als lokales Objekt aufgerufen werden kann |

Stand 13.06.2015