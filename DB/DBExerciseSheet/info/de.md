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
|out|DBQuery|POST<br>/query| ??? |
|out2|DBQuery2|POST<br>/query| ??? |
|getCourseExercises|DBQuery2|GET<br>/query/procedure/DBExerciseSheetGetCourseExercises/:courseid| ??? |
|getCourseSheets|DBQuery2|GET<br>/query/procedure/DBExerciseSheetGetCourseSheets/:courseid| ??? |
|getCourseSheetURLS|DBQuery2|GET<br>/query/procedure/DBExerciseSheetGetCourseSheetURLs/:courseid| ??? |
|getExerciseSheet|DBQuery2|GET<br>/query/procedure/DBExerciseSheetGetExerciseSheet/:esid| ??? |
|getExerciseSheetURL|DBQuery2|GET<br>/query/procedure/DBExerciseSheetGetExerciseSheetURL/:esid| ??? |
|getSheetExercises|DBQuery2|GET<br>/query/procedure/DBExerciseSheetGetSheetExercises/:esid| ??? |
|getExistsPlatform|DBQuery2|GET<br>/query/procedure/DBExerciseSheetGetExistsPlatform| ??? |

#### Anbindungen
| Bezeichnung  | Ziel  | Priorität | Beschreibung |
| :----------- |:----- | :--------:| :------------|
|request|CLocalObjectRequest|-| ??? |

Stand 29.06.2015
