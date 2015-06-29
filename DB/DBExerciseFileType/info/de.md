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
|out|DBQuery|POST<br>/query| ??? |
|out2|DBQuery2|POST<br>/query| ??? |
|getExerciseFileType|DBQuery2|GET<br>/query/procedure/DBExerciseFileTypeGetExerciseFileType/:eftid| ??? |
|getAllExerciseFileTypes|DBQuery2|GET<br>/query/procedure/DBExerciseFileTypeGetAllExerciseFileTypes| ??? |
|getExerciseExerciseFileTypes|DBQuery2|GET<br>/query/procedure/DBExerciseFileTypeGetExerciseExerciseFileTypes/:eid| ??? |
|getSheetExerciseFileTypes|DBQuery2|GET<br>/query/procedure/DBExerciseFileTypeGetSheetExerciseFileTypes/:esid| ??? |
|getExistsPlatform|DBQuery2|GET<br>/query/procedure/DBExerciseFileTypeGetExistsPlatform| ??? |

#### Anbindungen
| Bezeichnung  | Ziel  | Priorität | Beschreibung |
| :----------- |:----- | :--------:| :------------|
|request|CLocalObjectRequest|-| ??? |

Stand 29.06.2015
