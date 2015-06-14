#### Datenbank
Die DBSubmission ermöglicht den Zugriff auf die `Submission` Tabelle der Datenbank, dabei sollen
studentische Einsendungen verwaltet werden.
Dazu wird bei einem `POST /platform` Aufruf die nachstehende Tabelle erzeugt.

| Spalte        | Struktur  | Beschreibung | Besonderheit |
| :------       |:---------:| :------------| -----------: |
|U_id|INT NOT NULL| ??? |-|
|S_id|INT NOT NULL| ??? |AUTO_INCREMENT,<br>UNIQUE|
|F_id_file|INT NULL| ??? |-|
|S_comment|VARCHAR(255) NULL| ??? |-|
|S_date|INT UNSIGNED NOT NULL DEFAULT 0| ??? |-|
|S_accepted|TINYINT(1) NOT NULL DEFAULT false| ??? |-|
|E_id|INT NOT NULL| ??? |-|
|ES_id|INT NULL| ??? |-|
|S_flag|TINYINT NOT NULL DEFAULT 1| ??? |-|
|S_leaderId|INT NULL| ??? |-|
|S_hideFile|TINYINT NOT NULL DEFAULT 0| ??? |-|

#### Datenstruktur
Zu dieser Tabelle gehört die `Submission` Datenstruktur.

#### Eingänge
| Bezeichnung  | Eingabetyp  | Ausgabetyp | Befehl | Beschreibung |
| :----------- |:-----------:| :---------:| :----- | :----------- |
|editSubmission|Submission|Submission|PUT<br>/submission(/submission)/:suid| ??? |
|deleteSubmission|-|Submission|DELETE<br>/submission(/submission)/:suid| ??? |
|addSubmission|Submission|Submission|POST<br>/submission| ??? |
|getExerciseSubmissions|-|Submission|GET<br>/submission/exercise/:eid| ??? |
|getUserExerciseSubmissions|-|Submission|GET<br>/submission/user/:userid/exercise/:eid| ??? |
|getUserSheetSubmissions|-|Submission|GET<br>/submission/user/:userid/exercisesheet/:esid| ??? |
|getGroupSubmissions|-|Submission|GET<br>/submission/group/user/:userid/exercisesheet/:esid| ??? |
|getGroupSelectedSubmissions|-|Submission|GET<br>/submission/group/user/:userid/exercisesheet/:esid/selected| ??? |
|getGroupExerciseSubmissions|-|Submission|GET<br>/submission/group/user/:userid/exercise/:eid| ??? |
|getGroupSelectedExerciseSubmissions|-|Submission|GET<br>/submission/group/user/:userid/exercise/:eid/selected| ??? |
|getGroupSelectedCourseSubmissions|-|Submission|GET<br>/submission/group/user/:userid/course/:courseid/selected| ??? |
|getGroupCourseSubmissions|-|Submission|GET<br>/submission/group/user/:userid/course/:courseid| ??? |
|getSelectedSheetSubmissions|-|Submission|GET<br>/submission/exercisesheet/:esid/selected| ??? |
|getAllSubmissions|-|Submission|GET<br>/submission(/submission)(/:selected)(/date/begin/:beginStamp/end/:endStamp)| ??? |
|getSelectedExerciseSubmissions|-|Submission|GET<br>/submission/exercise/:eid/selected| ??? |
|getSheetSubmissions|-|Submission|GET<br>/submission/exercisesheet/:esid| ??? |
|getSubmission|-|Submission|GET<br>/submission(/submission)/:suid| ??? |
|getCourseSubmissions|-|Submission|GET<br>/submission/course/:courseid| ??? |
|getCourseUserSubmissions|-|Submission|GET<br>/submission/course/:courseid/user/:userid| ??? |
|getSelectedCourseUserSubmissions|-|Submission|GET<br>| ??? |
||-|Submission|GET<br>/submission/course/:courseid/user/:userid/selected| ??? |
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
beginStamp = der Anfangsstempel (Unix-Zeitstempel)
endStamp = der Endstempel (Unix-Zeitstempel)
selected = bestimmt, ob nur selektierte (`SelectedSubmission`) zurückgegeben werden sollen ('selected' = Ja, sonst = Nein)

| Bezeichnung  | Ziel  | Verwendung | Beschreibung |
| :----------- |:----- | :--------- | :----------- |
|out2|DBQuery2|POST<br>/query| wird für EDIT, DELETE<br>und POST<br>SQL-Templates verwendet |
|out|DBQuery|POST<br>/query| wird für EDIT, DELETE<br>und POST<br>SQL-Templates verwendet |
|getAllSubmissions|DBQuery2|GET<br>/query/procedure<br>/DBSubmissionGetAllSubmissions/:selected/:beginStamp/:endStamp| Prozeduraufruf |
|getCourseSubmissions|DBQuery2|GET<br>/query/procedure<br>/DBSubmissionGetCourseSubmissions/:courseid| Prozeduraufruf |
|getCourseUserSubmissions|DBQuery2|GET<br>/query/procedure<br>/DBSubmissionGetCourseUserSubmissions/:courseid/:userid| Prozeduraufruf |
|getExerciseSubmissions|DBQuery2|GET<br>/query/procedure<br>/DBSubmissionGetExerciseSubmissions/:eid| Prozeduraufruf |
|getGroupCourseSubmissions|DBQuery2|GET<br>/query/procedure<br>/DBSubmissionGetGroupCourseSubmissions/:userid/:courseid| Prozeduraufruf |
|getGroupExerciseSubmissions|DBQuery2|GET<br>/query/procedure<br>/DBSubmissionGetGroupExerciseSubmissions/:userid/:eid| Prozeduraufruf |
|getGroupSelectedCourseSubmissions|DBQuery2|GET<br>/query/procedure<br>/DBSubmissionGetGroupSelectedCourseSubmissions/:userid/:courseid| Prozeduraufruf |
|getGroupSelectedExerciseSubmissions|DBQuery2|GET<br>/query/procedure<br>/DBSubmissionGetGroupSelectedExerciseSubmissions/:userid/:eid| Prozeduraufruf |
|getGroupSelectedSubmissions|DBQuery2|GET<br>/query/procedure<br>/DBSubmissionGetGroupSelectedSubmissions/:userid/:esid| Prozeduraufruf |
|getGroupSubmissions|DBQuery2|GET<br>/query/procedure<br>/DBSubmissionGetGroupSubmissions/:userid/:esid| Prozeduraufruf |
|getSelectedCourseUserSubmissions|DBQuery2|GET<br>/query/procedure<br>/DBSubmissionGetSelectedCourseUserSubmissions/:courseid/:userid| Prozeduraufruf |
|getSelectedExerciseSubmissions|DBQuery2|GET<br>/query/procedure<br>/DBSubmissionGetSelectedExerciseSubmissions/:eid| Prozeduraufruf |
|getSelectedSheetSubmissions|DBQuery2|GET<br>/query/procedure<br>/DBSubmissionGetSelectedSheetSubmissions/:esid| Prozeduraufruf |
|getSheetSubmissions|DBQuery2|GET<br>/query/procedure<br>/DBSubmissionGetSheetSubmissions/:esid| Prozeduraufruf |
|getSubmission|DBQuery2|GET<br>/query/procedure<br>/DBSubmissionGetSubmission/:suid| Prozeduraufruf |
|getUserExerciseSubmissions|DBQuery2|GET<br>/query/procedure<br>/DBSubmissionGetUserExerciseSubmissions/:userid/:eid| Prozeduraufruf |
|getUserSheetSubmissions|DBQuery2|GET<br>/query/procedure<br>/DBSubmissionGetUserSheetSubmissions/:userid/:esid| Prozeduraufruf |
|getExistsPlatform|DBQuery2|GET<br>/query/procedure<br>/DBSubmissionGetExistsPlatform| Prozeduraufruf |

#### Anbindungen
| Bezeichnung  | Ziel  | Priorität | Beschreibung |
| :----------- |:----- | :--------:| :------------|
|request|CLocalObjectRequest|-| damit DBSubmission als lokales Objekt aufgerufen werden kann |

Stand 13.06.2015