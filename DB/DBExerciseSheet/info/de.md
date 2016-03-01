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
- esid = ???
- courseid = ???
- courseAmount = ???

| Bezeichnung  | Eingabetyp  | Ausgabetyp | Befehl | Beschreibung |
| :----------- |:-----------:| :---------:| :----- | :----------- |
|editExerciseSheet|ExerciseSheet|ExerciseSheet|PUT<br>/exercisesheet(/exercisesheet)/:esid| ??? |
|deleteExerciseSheet|-|ExerciseSheet|DELETE<br>/exercisesheet(/exercisesheet)/:esid| ??? |
|addExerciseSheet|ExerciseSheet|ExerciseSheet|POST<br>/exercisesheet| ??? |
|getExerciseSheetURL|-|File|GET<br>/exercisesheet(/exercisesheet)/:esid/url| ??? |
|getCourseSheetURLs|-|File|GET<br>/exercisesheet/course/:courseid/url| ??? |
|getCourseSheets|-|ExerciseSheet|GET<br>/exercisesheet/course/:courseid(/:exercise)| ??? |
|getExerciseSheet|-|ExerciseSheet|GET<br>/exercisesheet(/exercisesheet)/:esid(/:exercise)| ??? |
|addPlatform|Platform|Platform|POST<br>/platform| ??? |
|deletePlatform|-|Platform|DELETE<br>/platform| ??? |
|getSamplesInfo|-|-|GET<br>/samples| ??? |
|postSamples|-|Query|POST<br>/samples/course/:courseAmount/user/:userAmount| ??? |

#### Ausgänge
- courseid = ???
- esid = ???

| Bezeichnung  | Ziel  | Verwendung | Beschreibung |
| :----------- |:----- | :--------- | :----------- |
|out|DBQuery|POST<br>/query| wird für EDIT, DELETE<br>und POST<br>SQL-Templates verwendet |
|out2|DBQuery2|POST<br>/query| wird für EDIT, DELETE<br>und POST<br>SQL-Templates verwendet |
|getCourseExercises|DBQuery2|GET<br>/query/procedure/DBExerciseSheetGetCourseExercises/:courseid| Prozeduraufruf |
|getCourseSheets|DBQuery2|GET<br>/query/procedure/DBExerciseSheetGetCourseSheets/:courseid| Prozeduraufruf |
|getCourseSheetURLS|DBQuery2|GET<br>/query/procedure/DBExerciseSheetGetCourseSheetURLs/:courseid| Prozeduraufruf |
|getExerciseSheet|DBQuery2|GET<br>/query/procedure/DBExerciseSheetGetExerciseSheet/:esid| Prozeduraufruf |
|getExerciseSheetURL|DBQuery2|GET<br>/query/procedure/DBExerciseSheetGetExerciseSheetURL/:esid| Prozeduraufruf |
|getSheetExercises|DBQuery2|GET<br>/query/procedure/DBExerciseSheetGetSheetExercises/:esid| Prozeduraufruf |
|getExistsPlatform|DBQuery2|GET<br>/query/procedure/DBExerciseSheetGetExistsPlatform| Prozeduraufruf |

#### Anbindungen
| Bezeichnung  | Ziel  | Priorität | Beschreibung |
| :----------- |:----- | :--------:| :------------|
|request|CLocalObjectRequest|-| damit DBExerciseSheet als lokales Objekt aufgerufen werden kann |

Stand 29.06.2015
