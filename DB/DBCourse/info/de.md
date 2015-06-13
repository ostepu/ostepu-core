#### Datenbank
Die DBCourse ermöglicht den Zugriff auf die `Course` Tabelle der Datenbank, dabei sollen
Veranstaltungen verwaltet werden.
Dazu wird bei einem `POST /platform` Aufruf die nachstehende Tabelle erzeugt.

| Spalte           | Struktur  | Beschreibung | Besonderheit |
| :------------    |:--------:| :---------------| -----: |
|C_id              |INT NOT NULL|Der Primärschlüssel einer Veranstaltung|UNIQUE,<br>AUTO_INCREMENT|
|C_name            |VARCHAR(120) NULL|Der Name der Veranstaltung|-|
|C_semester        |VARCHAR(60) NULL|Das Semester. Bsp.: SS 2015 und WS 2014/2015, dieses Format muss eingehalten werden|-|
|C_defaultGroupSize|INT NOT NULL DEFAULT 1|Die Standardgruppengröße, als Vorgabe beim erzeugen neuer Übungsserien|-|

#### Datenstruktur
Zu dieser Tabelle gehört die `Course` Datenstruktur.

#### Veranstaltungserstellung
Beim Erzeugen einer neuen Veranstaltung wird versucht, Einstellungen in die zugehörige `Setting_X` Tabelle einzutragen.
Dazu muss der `POST /course` Aufruf möglicherweise zweifach an diese Komponente gerichtet werden.

| Bezeichnung  | Typ  | Beschreibung | Vorgabewert |
| :----------- |:----:| :------------| ----------: |
|RegistrationPeriodEnd|TIMESTAMP|Wird bei der Registrierung neuer Nutzer verwendet und soll das Ende der Anmeldeperiode festlegen (danach soll kein CourseStatus mehr erstellt werden können, wird nicht durch die Datenbank geprüft). 0 = ohne Anmeldesperre, >0 (Unix-Zeitstempel) hier endet die Anmeldefrist |0|
|AllowLateSubmissions|BOOL|Soll festlegen, ob Studenten verspätet Einsendungen einreichen können. 0 = Nein, 1 = Ja|1|

#### Eingänge
| Bezeichnung  | Eingabetyp  | Ausgabetyp | Befehl | Beschreibung |
| :----------- |:-----------:| :---------:| :----- | :----------- |
|editCourse|Course|Course|PUT<br>/course(/course)/:courseid|verändert eine existierende Veranstaltung|
|deleteCourse|-|Course|DELETE<br>/course(/course)/:courseid|entfernt eine existierende Veranstaltung (auch wenn die Veranstaltung nicht existiert ist die Antwort positiv)|
|addCourse|Course|Course|POST<br>/course|erzeugt eine neue Veranstaltung (doppelte Aufrufe erzeugen die Veranstaltung mehrfach)|
|getCourse|-|Course|GET<br>/course(/course)/:courseid|liefert die Daten einer einzelnen Veranstaltung|
|getAllCourses|-|Course|GET<br>/course(/course)|liefert alle Veranstaltungen|
|getUserCourses|-|Course|GET<br>/course/user/:userid|gibt die von einem bestimmten Nutzer besuchten Veranstaltungen zurück|
|addPlatform|Platform|Platform|POST<br>/platform|installiert die zugehörige Tabelle und die Prozeduren für diese Plattform|
|deletePlatform|-|Platform|DELETE<br>/platform|entfernt die Tabelle und Prozeduren aus der Plattform|
|getExistsPlatform|-|Platform|GET<br>/link/exists/platform| prüft, ob die Tabelle und die Prozeduren existieren |
|getSamplesInfo|-|-|GET /samples|???|
|postSamples|-|Query|POST<br>/samples/course/:courseAmount<br>/user/:userAmount| erzeugt Zufallsdaten (courseAmount = Anzahl der Veranstaltungen, userAmount = Anzahl der Nutzer), anhand der Vorgabe |

#### Ausgänge
courseid = eine Veranstaltungs ID (`Course`)
userid = die ID eines Nutzers (`User`)

| Bezeichnung  | Ziel  | Verwendung | Beschreibung |
| :----------- |:----- | :--------- | :----------- |
|out2|DBQuery2|POST /query| wird für EDIT, DELETE und POST SQL-Templates verwendet |
|getCourse|DBQuery2|GET /query/procedure/DBCourseGetCourse/:courseid| Prozeduraufruf |
|getAllCourses|DBQuery2|GET /query/procedure/DBCourseGetAllCourses| Prozeduraufruf |
|getUserCourses|DBQuery2|GET /query/procedure/DBCourseGetUserCourses/:userid| Prozeduraufruf |
|getExistsPlatform|DBQuery2|GET /query/procedure/DBCourseGetExistsPlatform| Prozeduraufruf |
|getSamplesInfo|DBQuery2|GET /query/procedure/DBCourseGetExistsPlatform| Prozeduraufruf |

#### Anbindungen
| Bezeichnung  | Ziel  | Priorität | Beschreibung |
| :----------- |:----- | :--------:| :------------|
|request|CLocalObjectRequest|-| damit DBCourse als lokales Objekt aufgerufen werden kann |
|postCourse|LCourse|300| damit erzwingen wir einen erneuten POST /course Aufruf, nachdem alle mit diesem Ausgang verbundenen Verbindungen aufgerufen wurden |

Stand 13.06.2015