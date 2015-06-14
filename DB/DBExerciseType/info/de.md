#### Datenbank
Die DBExerciseType ermöglicht den Zugriff auf die `ExerciseType` Tabelle der Datenbank, dabei sollen
Typen von Aufgaben (Punktearten) verwaltet werden.
Dazu wird bei einem `POST /platform` Aufruf die nachstehende Tabelle erzeugt.

| Spalte        | Struktur  | Beschreibung | Besonderheit |
| :------       |:---------:| :------------| -----------: |
|ET_id|INT NOT NULL| die ID des Bewertungstyps |AUTO_INCREMENT,<br>UNIQUE|
|ET_name|VARCHAR(45) NOT NULL| ein Bezeichner, Bsp.: Theorie, Praxis |-|

#### Datenstruktur
Zu dieser Tabelle gehört die `ExerciseType` Datenstruktur.

#### Eingänge
etid = eine Aufgabetyp ID (`ExerciseType`)

| Bezeichnung  | Eingabetyp  | Ausgabetyp | Befehl | Beschreibung |
| :----------- |:-----------:| :---------:| :----- | :----------- |
|editExerciseType|ExerciseType|ExerciseType|PUT<br>/exercisetype(/exercisetype)/:etid| ??? |
|deleteExerciseType|-|ExerciseType|DELETE<br>/exercisetype(/exercisetype)/:etid| ??? |
|addExerciseType|ExerciseType|ExerciseType|POST<br>/exercisetype| ??? |
|getExerciseType|-|ExerciseType|GET<br>/exercisetype(/exercisetype)/:etid| ??? |
|getAllExerciseTypes|-|ExerciseType|GET<br>/exercisetype(/exercisetype)| ??? |
|addPlatform|Platform|Platform|POST<br>/platform|installiert dies zugehörige Tabelle und die Prozeduren für diese Plattform|
|deletePlatform|-|Platform|DELETE<br>/platform|entfernt die Tabelle und Prozeduren aus der Plattform|
|getExistsPlatform|-|Platform|GET<br>/link/exists/platform| prüft, ob die Tabelle und die Prozeduren existieren |
|getSamplesInfo|-|-|GET<br>/samples| ??? |
|postSamples|-|Query|POST<br>/samples/:amount| erzeugt Zufallsdaten (amount = Anzahl der Einträge) |

#### Ausgänge
etid = eine Aufgabetyp ID (`ExerciseType`)

| Bezeichnung  | Ziel  | Verwendung | Beschreibung |
| :----------- |:----- | :--------- | :----------- |
|out2|DBQuery2|POST<br>/query| wird für EDIT, DELETE<br>und POST<br>SQL-Templates verwendet |
|out|DBQuery|POST<br>/query| wird für EDIT, DELETE<br>und POST<br>SQL-Templates verwendet |
|getExerciseType|DBQuery2|GET<br>/query/procedure<br>/DBExerciseTypeGetExerciseType/:etid| Prozeduraufruf |
|getAllExerciseTypes|DBQuery2|GET<br>/query/procedure<br>/DBExerciseTypeGetAllExerciseTypes| Prozeduraufruf |
|getExistsPlatform|DBQuery2|GET<br>/query/procedure<br>/DBExerciseTypeGetExistsPlatform| Prozeduraufruf |
|getSamplesInfo|DBQuery2|GET<br>/query/procedure<br>/DBExerciseTypeGetExistsPlatform| Prozeduraufruf |

#### Anbindungen
| Bezeichnung  | Ziel  | Priorität | Beschreibung |
| :----------- |:----- | :--------:| :------------|
|request|CLocalObjectRequest|-| damit DBExerciseType als lokales Objekt aufgerufen werden kann |

Stand 13.06.2015