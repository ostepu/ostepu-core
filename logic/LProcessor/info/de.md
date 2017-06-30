<!--
  - @file de.md
  -
  - @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
  -
  - @package OSTEPU (https://github.com/ostepu/ostepu-core)
  - @since 0.3.4
  -
  - @author Till Uhlig <till.uhlig@student.uni-halle.de>
  - @date 2015
  -
 -->

## Eingänge
---------------

|||
| :----------- |:-----: |
|Befehl| post<br>/submission|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Befehl| post<br>/process|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Befehl| post<br>/course|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Befehl| delete<br>/course/:courseid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Befehl| get<br>/link/exists/course/:courseid|
|Eingabetyp| -|
|Ausgabetyp| -|


## Ausgänge
---------------

||submission|
| :----------- |:-----: |
|Ziel| LSubmission|
|Befehl| POST<br>/submission|
|Beschreibung| für den Befehl submission|

||marking|
| :----------- |:-----: |
|Ziel| LMarking|
|Befehl| POST<br>/marking|
|Beschreibung| für den Befehl marking|

||processorDb|
| :----------- |:-----: |
|Ziel| DBProcess|
|Befehl| POST<br>/process|
|Beschreibung| für den Befehl processorDb|

||processorDb|
| :----------- |:-----: |
|Ziel| DBProcess|
|Befehl| GET<br>/process/exercise/:exerciseid|
|Beschreibung| für den Befehl processorDb|

||attachment|
| :----------- |:-----: |
|Ziel| DBProcessAttachment|
|Befehl| POST<br>/attachment|
|Beschreibung| für den Befehl attachment|

||workFiles|
| :----------- |:-----: |
|Ziel| DBProcessWorkFiles|
|Befehl| POST<br>/attachment|
|Beschreibung| für den Befehl workFiles|

||getExerciseExerciseFileType|
| :----------- |:-----: |
|Ziel| DBExerciseFileType|
|Befehl| GET<br>/exercisefiletype/exercise/:eid|
|Beschreibung| für den Befehl getExerciseExerciseFileType|

||file|
| :----------- |:-----: |
|Ziel| LFile|
|Befehl| POST<br>/file|
|Beschreibung| für den Befehl file|

||postCourse|
| :----------- |:-----: |
|Ziel| DBProcess|
|Befehl| GET<br>/link/exists/course/:courseid|
|Beschreibung| für den Befehl postCourse|

||postCourse|
| :----------- |:-----: |
|Ziel| DBProcess|
|Befehl| POST<br>/course|
|Beschreibung| für den Befehl postCourse|

||postCourse|
| :----------- |:-----: |
|Ziel| DBProcess|
|Befehl| DELETE<br>/course/course/:courseid|
|Beschreibung| für den Befehl postCourse|


## Anbindungen
---------------

|Ausgang|deleteCourse|
| :----------- |:-----: |
|Ziel| LCourse|
|Beschreibung| wenn eine Veranstaltung gelöscht wird, dann müssen auch unsere Tabellen entfernt werden|

|Ausgang|postCourse|
| :----------- |:-----: |
|Ziel| LCourse|
|Beschreibung| wenn eine neue Veranstaltung angelegt wird, dann wollen wir auch aufgerufen werden|

|Ausgang|request|
| :----------- |:-----: |
|Ziel| CLocalObjectRequest|
|Beschreibung| damit LProcessor als lokales Objekt aufgerufen werden kann|


Stand 30.06.2017
