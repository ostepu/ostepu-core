<!--
  - @file de.md
  -
  - @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
  -
  - @package OSTEPU (https://github.com/ostepu/ostepu-core)
  - @since -
  -
  - @author Till Uhlig <till.uhlig@student.uni-halle.de>
  - @date 2017
  -
 -->

## Eingänge
---------------

|||
| :----------- |:-----: |
|Befehl| POST<br>/exercisesheet|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Befehl| PUT<br>/exercisesheet/exercisesheet/:sheetid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Befehl| GET<br>/exercisesheet/exercisesheet/:sheetid/url|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Befehl| GET<br>/exercisesheet/exercisesheet/:sheetid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Befehl| GET<br>/exercisesheet/exercisesheet/:sheetid/exercise|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Befehl| GET<br>/exercisesheet/course/:courseid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Befehl| GET<br>/exercisesheet/course/:courseid/exercise|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Befehl| GET<br>/exercisesheet/exercisesheet/:sheetid|
|Eingabetyp| -|
|Ausgabetyp| -|


## Ausgänge
---------------

||controller|
| :----------- |:-----: |
|Ziel| LController|
|Befehl| DELETE<br>/FS/sheetFileAddress+|
|Beschreibung| für den Befehl controller|

||controller|
| :----------- |:-----: |
|Ziel| LController|
|Befehl| DELETE<br>/FS/sampleFileAddress+|
|Beschreibung| für den Befehl controller|

||controller|
| :----------- |:-----: |
|Ziel| LController|
|Befehl| DELETE<br>/DB/exercisesheet/exercisesheet/:sheetid|
|Beschreibung| für den Befehl controller|

||controller|
| :----------- |:-----: |
|Ziel| LController|
|Befehl| GET<br>/DB/exercisesheet/course/:courseid/exercise|
|Beschreibung| für den Befehl controller|

||controller|
| :----------- |:-----: |
|Ziel| LController|
|Befehl| GET<br>/DB/exercisesheet/course/:courseid|
|Beschreibung| für den Befehl controller|

||controller|
| :----------- |:-----: |
|Ziel| LController|
|Befehl| GET<br>/DB/exercisesheet/exercisesheet/:sheetid/exercise|
|Beschreibung| für den Befehl controller|

||controller|
| :----------- |:-----: |
|Ziel| LController|
|Befehl| GET<br>/DB/exercisesheet/exercisesheet/:sheetid/url|
|Beschreibung| für den Befehl controller|

||controller|
| :----------- |:-----: |
|Ziel| LController|
|Befehl| PUT<br>/DB/exercisesheet/exercisesheet/:sheetid|
|Beschreibung| für den Befehl controller|

||controller|
| :----------- |:-----: |
|Ziel| LController|
|Befehl| POST<br>/DB/exercisesheet|
|Beschreibung| für den Befehl controller|

||controller|
| :----------- |:-----: |
|Ziel| LController|
|Befehl| GET<br>/hash/:hash|
|Beschreibung| für den Befehl controller|

||controller|
| :----------- |:-----: |
|Ziel| LController|
|Befehl| POST<br>/DB/file|
|Beschreibung| für den Befehl controller|

||controller|
| :----------- |:-----: |
|Ziel| LController|
|Befehl| POST<br>/FS/file|
|Beschreibung| für den Befehl controller|

||deleteFile|
| :----------- |:-----: |
|Ziel| LFile|
|Befehl| DELETE<br>/file/:fileId|
|Beschreibung| für den Befehl deleteFile|

||getExerciseSheet|
| :----------- |:-----: |
|Ziel| DBExerciseSheet|
|Befehl| GET<br>/exercisesheet/exercisesheet/:sheetId|
|Beschreibung| für den Befehl getExerciseSheet|

||deleteExerciseSheet|
| :----------- |:-----: |
|Ziel| DBExerciseSheet|
|Befehl| DELETE<br>/exercisesheet/exercisesheet/:sheetId|
|Beschreibung| für den Befehl deleteExerciseSheet|


## Anbindungen
---------------

|Ausgang|request|
| :----------- |:-----: |
|Ziel| CLocalObjectRequest|
|Beschreibung| damit LExerciseSheet als lokales Objekt aufgerufen werden kann|


Stand 30.06.2017
