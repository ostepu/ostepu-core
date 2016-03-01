<!--
 * @file de.md
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
-->

#### Eingänge
- eid = die ID einer Aufgabe (`Exercise`)
- sub = bestimmt, ob keine Einsendungen mit zurückgegeben werden sollen ('nosubmission' = keine Einsendungen, sonst = mit Einsendungen)
- courseid = eine Veranstaltungs ID (`Course`)
- esid = die ID einer Übungsserie (`ExerciseSheet`)
- courseAmount = ???

| Bezeichnung  | Eingabetyp  | Ausgabetyp | Befehl | Beschreibung |
| :----------- |:-----------:| :---------:| :----- | :----------- |
|editExercise|Exercise|Exercise|PUT<br>/exercise(/exercise)/:eid| ??? |
|deleteExercise|-|Exercise|DELETE<br>/exercise(/exercise)/:eid| ??? |
|addExercise|Exercise|Exercise|POST<br>/exercise| ??? |
|getAllExercises|-|Exercise|GET<br>/exercise(/exercise)(/:sub)| ??? |
|getSheetExercises|-|Exercise|GET<br>/exercise/exercisesheet/:esid(/:sub)| ??? |
|getCourseExercises|-|Exercise|GET<br>/exercise/course/:courseid(/:sub)| ??? |
|getExercise|-|Exercise|GET<br>/exercise(/exercise)/:eid(/:sub)| ??? |
|addPlatform|Platform|Platform|POST<br>/platform| ??? |
|deletePlatform|-|Platform|DELETE<br>/platform| ??? |
|getExistsPlatform|-|Platform|GET<br>/link/exists/platform| ??? |
|getSamplesInfo|-|-|GET<br>/samples| ??? |
|postSamples|-|Query|POST<br>/samples/course/:courseAmount/user/:userAmount| ??? |

#### Ausgänge
- eid = die ID einer Aufgabe (`Exercise`)
- sub = bestimmt, ob keine Einsendungen mit zurückgegeben werden sollen ('nosubmission' = keine Einsendungen, sonst = mit Einsendungen)
- courseid = eine Veranstaltungs ID (`Course`)
- esid = die ID einer Übungsserie (`ExerciseSheet`)

| Bezeichnung  | Ziel  | Verwendung | Beschreibung |
| :----------- |:----- | :--------- | :----------- |
|out|DBQuery|POST<br>/query| wird für EDIT, DELETE<br>und POST<br>SQL-Templates verwendet |
|out2|DBQuery2|POST<br>/query| wird für EDIT, DELETE<br>und POST<br>SQL-Templates verwendet |
|getExercise|DBQuery2|GET<br>/query/procedure/DBExerciseGetExercise/:eid/:sub| Prozeduraufruf |
|getAllExercises|DBQuery2|GET<br>/query/procedure/DBExerciseGetAllExercises/:sub| Prozeduraufruf |
|getCourseExercises|DBQuery2|GET<br>/query/procedure/DBExerciseGetCourseExercises/:courseid/:sub| Prozeduraufruf |
|getSheetExercises|DBQuery2|GET<br>/query/procedure/DBExerciseGetSheetExercises/:esid/:sub| Prozeduraufruf |
|getExistsPlatform|DBQuery2|GET<br>/query/procedure/DBExerciseGetExistsPlatform| Prozeduraufruf |

#### Anbindungen
| Bezeichnung  | Ziel  | Priorität | Beschreibung |
| :----------- |:----- | :--------:| :------------|
|request|CLocalObjectRequest|-| damit DBExercise als lokales Objekt aufgerufen werden kann |

Stand 29.06.2015
