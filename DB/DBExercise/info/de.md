#### Eingänge
- eid = ??? 
- sub = ??? 
- esid = ??? 
- courseid = ??? 
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
- eid = ??? 
- sub = ??? 
- courseid = ??? 
- esid = ??? 

| Bezeichnung  | Ziel  | Verwendung | Beschreibung |
| :----------- |:----- | :--------- | :----------- |
|out|DBQuery|POST<br>/query| ??? |
|out2|DBQuery2|POST<br>/query| ??? |
|getExercise|DBQuery2|GET<br>/query/procedure/DBExerciseGetExercise/:eid/:sub| ??? |
|getAllExercises|DBQuery2|GET<br>/query/procedure/DBExerciseGetAllExercises/:sub| ??? |
|getCourseExercises|DBQuery2|GET<br>/query/procedure/DBExerciseGetCourseExercises/:courseid/:sub| ??? |
|getSheetExercises|DBQuery2|GET<br>/query/procedure/DBExerciseGetSheetExercises/:esid/:sub| ??? |
|getExistsPlatform|DBQuery2|GET<br>/query/procedure/DBExerciseGetExistsPlatform| ??? |

#### Anbindungen
| Bezeichnung  | Ziel  | Priorität | Beschreibung |
| :----------- |:----- | :--------:| :------------|
|request|CLocalObjectRequest|-| ??? |

Stand 29.06.2015
