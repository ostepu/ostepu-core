<!--
  - @file de.md
  -
  - @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
  -
  - @package OSTEPU (https://github.com/ostepu/system)
  - @since 0.3.5
  -
  - @author Till Uhlig <till.uhlig@student.uni-halle.de>
  - @date 2015
 -->

#### Eingänge
- eftid = ???
- eid = ???
- esid = ???

| Bezeichnung  | Eingabetyp  | Ausgabetyp | Befehl | Beschreibung |
| :----------- |:-----------:| :---------:| :----- | :----------- |
|editExerciseFileType|ExerciseFileType|ExerciseFileType|PUT<br>/exercisefiletype(/exercisefiletype)/:eftid| ??? |
|deleteExerciseFileType|-|ExerciseFileType|DELETE<br>/exercisefiletype(/exercisefiletype)/:eftid| ??? |
|deleteExerciseExerciseFileType|-|ExerciseFileType|DELETE<br>/exercisefiletype/exercise/:eid| ??? |
|deleteExerciseSheetExerciseFileType|-|ExerciseFileType|DELETE<br>/exercisefiletype/exercisesheet/:esid| ??? |
|addExerciseFileType|ExerciseFileType|ExerciseFileType|POST<br>/exercisefiletype| ??? |
|getExerciseFileType|-|ExerciseFileType|GET<br>/exercisefiletype(/exercisefiletype)/:eftid| ??? |
|getExerciseExerciseFileTypes|-|ExerciseFileType|GET<br>/exercisefiletype(/exercisefiletype)/exercise/:eid| ??? |
|getSheetExerciseFileTypes|-|ExerciseFileType|GET<br>/exercisefiletype(/exercisefiletype)/exercisesheet/:esid| ??? |
|getAllExerciseFileTypes|-|ExerciseFileType|GET<br>/exercisefiletype(/exercisefiletype)| ??? |
|addPlatform|Platform|Platform|POST<br>/platform| ??? |
|deletePlatform|-|Platform|DELETE<br>/platform| ??? |
|getExistsPlatform|-|Platform|GET<br>/link/exists/platform| ??? |

#### Ausgänge
- eftid = ???
- eid = ???
- esid = ???

| Bezeichnung  | Ziel  | Verwendung | Beschreibung |
| :----------- |:----- | :--------- | :----------- |
|out|DBQuery|POST<br>/query| wird für EDIT, DELETE<br>und POST<br>SQL-Templates verwendet |
|out2|DBQuery2|POST<br>/query| wird für EDIT, DELETE<br>und POST<br>SQL-Templates verwendet |
|getExerciseFileType|DBQuery2|GET<br>/query/procedure/DBExerciseFileTypeGetExerciseFileType/:eftid| Prozeduraufruf |
|getAllExerciseFileTypes|DBQuery2|GET<br>/query/procedure/DBExerciseFileTypeGetAllExerciseFileTypes| Prozeduraufruf |
|getExerciseExerciseFileTypes|DBQuery2|GET<br>/query/procedure/DBExerciseFileTypeGetExerciseExerciseFileTypes/:eid| Prozeduraufruf |
|getSheetExerciseFileTypes|DBQuery2|GET<br>/query/procedure/DBExerciseFileTypeGetSheetExerciseFileTypes/:esid| Prozeduraufruf |
|getExistsPlatform|DBQuery2|GET<br>/query/procedure/DBExerciseFileTypeGetExistsPlatform| Prozeduraufruf |

#### Anbindungen
| Bezeichnung  | Ziel  | Priorität | Beschreibung |
| :----------- |:----- | :--------:| :------------|
|request|CLocalObjectRequest|-| damit DBExerciseFileType als lokales Objekt aufgerufen werden kann |

Stand 29.06.2015
