#### Datenbank
Die DBSelectedSubmission ermöglicht den Zugriff auf die `SelectedSubmission` Tabelle der Datenbank, dabei sollen
für einen Gruppeneintrag ausgewählte Einsendungen (für eine Aufgabe) verwaltet werden.
Dazu wird bei einem `POST /platform` Aufruf die nachstehende Tabelle erzeugt.

| Spalte        | Struktur  | Beschreibung | Besonderheit |
| :------       |:---------:| :------------| -----------: |
|U_id_leader|INT NOT NULL| ??? |-|
|S_id_selected|INT NOT NULL| ??? |UNIQUE|
|E_id|INT NOT NULL| ??? |-|
|ES_id|INT NULL| ??? |-|

#### Datenstruktur
Zu dieser Tabelle gehört die `SelectedSubmission` Datenstruktur.

#### Eingänge
| Bezeichnung  | Eingabetyp  | Ausgabetyp | Befehl | Beschreibung |
| :----------- |:-----------:| :---------:| :----- | :----------- |
|editSelectedSubmission|SelectedSubmission|SelectedSubmission|PUT<br>/selectedsubmission/leader/:userid/exercise/:eid| ??? |
|deleteSelectedSubmission|-|SelectedSubmission|DELETE<br>/selectedsubmission/leader/:userid/exercise/:eid| ??? |
|editSubmissionSelectedSubmission|SelectedSubmission|SelectedSubmission|PUT<br>/selectedsubmission/submission/:suid| ??? |
|deleteSubmissionSelectedSubmission|-|SelectedSubmission|DELETE<br>/selectedsubmission/submission/:suid| ??? |
|deleteUserSheetSelectedSubmission|-|SelectedSubmission|DELETE<br>/selectedsubmission/user/:userid/exercisesheet/:esid| ??? |
|addSelectedSubmission|SelectedSubmission|SelectedSubmission|POST<br>/selectedsubmission| ??? |
|getExerciseSelected|-|SelectedSubmission|GET<br>/selectedsubmission/exercise/:eid| ??? |
|getSheetSelected|-|SelectedSubmission|GET<br>/selectedsubmission/exercisesheet/:esid| ??? |
|getCourseSelected|-|SelectedSubmission|GET<br>/selectedsubmission/course/:courseid| ??? |
|addPlatform|Platform|Platform|POST<br>/platform|installiert dies zugehörige Tabelle und die Prozeduren für diese Plattform|
|deletePlatform|-|Platform|DELETE<br>/platform|entfernt die Tabelle und Prozeduren aus der Plattform|
|getExistsPlatform|-|Platform|GET<br>/link/exists/platform| prüft, ob die Tabelle und die Prozeduren existieren |
|getSamplesInfo|-|-|GET<br>/samples| ??? |
|postSamples|-|Query|POST<br>/samples/course/:courseAmount<br>/user/:userAmount| erzeugt Zufallsdaten (courseAmount = Anzahl der Veranstaltungen, userAmount = Anzahl der Nutzer), anhand der Vorgabe |

#### Ausgänge
courseid = eine Veranstaltungs ID (`Course`)
userid = die ID eines Nutzerkontos (`User`)
esid = die ID einer Übungsserie (`ExerciseSheet`)
eid = die ID einer Aufgabe (`Exercise`)
suid = die ID einer Einsendung (`Submission`)

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