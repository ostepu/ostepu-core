#### Datenbank
Die DBMarking ermöglicht den Zugriff auf die `Marking` Tabelle der Datenbank, dabei sollen
Korrekturen, zu Einsendungen, verwaltet werden.
Dazu wird bei einem `POST /platform` Aufruf die nachstehende Tabelle erzeugt.

| Spalte        | Struktur  | Beschreibung | Besonderheit |
| :------       |:---------:| :------------| -----------: |
|M_id|INT NOT NULL| ??? |AUTO_INCREMENT,<br>UNIQUE|
|U_id_tutor|INT NOT NULL| ??? |-|
|F_id_file|INT NULL| ??? |-|
|S_id|INT NOT NULL| ??? |-|
|M_tutorComment|VARCHAR(255) NULL| ??? |-|
|M_outstanding|TINYINT(1) NULL DEFAULT false| ??? |-|
|M_status|TINYINT NOT NULL DEFAULT 0| ??? |-|
|M_points|FLOAT NULL DEFAULT 0| ??? |-|
|M_date|INT UNSIGNED NULL DEFAULT 0| ??? |-|
|E_id|INT NULL| ??? |-|
|ES_id|INT NULL| ??? |-|
|M_hideFile|TINYINT NOT NULL DEFAULT 0| ??? |-|

#### Datenstruktur
Zu dieser Tabelle gehört die `Marking` Datenstruktur.

#### Eingänge
| Bezeichnung  | Eingabetyp  | Ausgabetyp | Befehl | Beschreibung |
| :----------- |:-----------:| :---------:| :----- | :----------- |
|editMarking|Marking|Marking|PUT<br>/marking(/marking)/:mid| ??? |
|deleteMarking|-|Marking|DELETE<br>/marking(/marking)/:mid| ??? |
|deleteSheetMarkings|-|Marking|DELETE<br>/marking(/marking)/exercisesheet/:esid| ??? |
|addMarking|Marking|Marking|POST<br>/marking| ??? |
|getSubmissionMarking|-|Marking|GET<br>/marking/submission/:suid(/:sub)| ??? |
|getExerciseMarkings|-|Marking|GET<br>/marking/exercise/:eid(/:sub)| ??? |
|getSheetMarkings|-|Marking|GET<br>/marking/exercisesheet/:esid(/:sub)| ??? |
|getCourseMarkings|-|Marking|GET<br>/marking/course/:courseid(/:sub)| ??? |
|getUserGroupMarkings|-|Marking|GET<br>/marking/exercisesheet/:esid/user/:userid(/:sub)| ??? |
|getCourseUserGroupMarkings|-|Marking|GET<br>/marking/course/:courseid/user/:userid(/:sub)| ??? |
|getTutorSheetMarkings|-|Marking|GET<br>/marking/exercisesheet/:esid/tutor/:userid(/:sub)| ??? |
|getTutorExerciseMarkings|-|Marking|GET<br>/marking/exercise/:eid/tutor/:userid(/:sub)| ??? |
|getMarking|-|Marking|GET<br>/marking(/marking)/:mid(/:sub)| ??? |
|getAllMarkings|-|Marking|GET<br>/marking(/marking)(/:sub)| ??? |
|addPlatform|Platform|Platform|POST<br>/platform|installiert dies zugehörige Tabelle und die Prozeduren für diese Plattform|
|deletePlatform|-|Platform|DELETE<br>/platform|entfernt die Tabelle und Prozeduren aus der Plattform|
|getExistsPlatform|-|Platform|GET<br>/link/exists/platform| prüft, ob die Tabelle und die Prozeduren existieren |
|getSamplesInfo|-|-|GET<br>/samples| ??? |
|postSamples|-|Query|POST<br>/samples/course/:courseAmount<br>/user/:userAmount| erzeugt Zufallsdaten (courseAmount = Anzahl der Veranstaltungen, userAmount = Anzahl der Nutzer), anhand der Vorgabe |

#### Ausgänge
courseid = eine Veranstaltungs ID (`Course`)
userid = eine Veranstaltungs ID (`User`)
sub = bestimmt, ob keine Einsendungen mit zurückgegeben werden sollen ('nosubmission' = keine Einsendungen, sonst = mit Einsendungen)
mid = die ID einer Korrektur (`Marking`)
eid = die ID einer Aufgabe (`Exercise`)
esid = die ID einer Übungsserie (`ExerciseSheet`)
suid = die ID einer Einsendung (`Submission`)

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