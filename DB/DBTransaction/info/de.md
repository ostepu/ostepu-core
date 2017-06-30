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

Die DBTransaction ermöglicht den Zugriff auf die `Transaction_X` Tabellen der Datenbank, dabei sollen Vorgangsnummern mit zugehörigen Inhalten verwaltet werden (welcher nur temporär zugänglich sind). Sie wird beispielsweise genutzt, wenn man zu einem Vorgang zugehörige Daten kurzfristif hinterlegen möchte (beispielsweise zur Verifizierung). Dazu wird bei einem `POST /course` Aufruf die nachstehende Tabelle erzeugt.

## Eingänge
---------------

||addSheetTransaction|
| :----------- |:-----: |
|Beschreibung| erzeugt eine Transaktion (anhand der ID einer Übungsserie)|
|Befehl| post<br>/transaction/exercisesheet/:esid|
|Eingabetyp| Transaction|
|Ausgabetyp| Transaction|
|||
||Patzhalter|
|Name|esid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID einer Übungsserie (`ExercisehSheet`)|

||deleteTransactionShort|
| :----------- |:-----: |
|Beschreibung| entfernt eine Transaktion (ohne das Sicherheitswort)|
|Befehl| delete<br>/transaction/transaction/:tid|
|Eingabetyp| -|
|Ausgabetyp| Transaction|
|||
||Patzhalter|
|Name|tid|
|Regex|%^([a-z0-9_]+)$%|
|Beschreibung|eine Transaktionsnummer|

||deleteTransaction|
| :----------- |:-----: |
|Beschreibung| entfernt eine Transaktion|
|Befehl| delete<br>/transaction/authentication/:auid/transaction/:tid|
|Eingabetyp| -|
|Ausgabetyp| Transaction|
|||
||Patzhalter|
|Name|auid|
|Regex|%^([a-zA-Z0-9_]+)$%|
|Beschreibung|das zur Transaktion gehörige Sicherheitswort (durch den Ersteller festgelegt)|
|Name|tid|
|Regex|%^([a-z0-9_]+)$%|
|Beschreibung|eine Transaktionsnummer|

||cleanTransactions|
| :----------- |:-----: |
|Beschreibung| entfernt alle abgelaufenen Transaktionsnummern aus `Transaction_X`|
|Befehl| delete<br>/clean/course/:courseid|
|Eingabetyp| -|
|Ausgabetyp| Transaction|
|||
||Patzhalter|
|Name|courseid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|eine Veranstaltungs ID (`Course`)|

||getTransaction|
| :----------- |:-----: |
|Beschreibung| liefert eine Transaktion und deren Daten+Inhalt|
|Befehl| get<br>/transaction/authentication/:auid/transaction/:tid|
|Eingabetyp| -|
|Ausgabetyp| Course|
|||
||Patzhalter|
|Name|auid|
|Regex|%^([a-zA-Z0-9_]+)$%|
|Beschreibung|das zur Transaktion gehörige Geheimwort (durch den Ersteller festgelegt)|
|Name|tid|
|Regex|%^([a-z0-9_]+)$%|

||addExerciseTransaction|
| :----------- |:-----: |
|Beschreibung| erzeugt eine Transaktion (anhand der ID einer Aufgabe)|
|Befehl| post<br>/transaction/exercise/:eid|
|Eingabetyp| Transaction|
|Ausgabetyp| Transaction|
|||
||Patzhalter|
|Name|eid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID einer Aufgabe (`Exercise`)|

||getTransactionShort|
| :----------- |:-----: |
|Beschreibung| liefert eine Transaktion und deren Daten+Inhalt (ohne das Geheimwort)|
|Befehl| get<br>/transaction/transaction/:tid|
|Eingabetyp| -|
|Ausgabetyp| Course|
|||
||Patzhalter|
|Name|tid|
|Regex|%^([a-z0-9_]+)$%|
|Beschreibung|eine Transaktionsnummer|

||getAmountOfExpiredTransactions|
| :----------- |:-----: |
|Beschreibung| ermittelt die Anzahl der abgelaufenen Transaktionsnummern in `Transaction_X`|
|Befehl| get<br>/clean/course/:courseid|
|Eingabetyp| -|
|Ausgabetyp| -|
|||
||Patzhalter|
|Name|courseid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|eine Veranstaltungs ID (`Course`)|

||addCourse|
| :----------- |:-----: |
|Beschreibung| fügt DBTransaction zur Veranstaltung hinzu (erzeugt `Transaction_X`)|
|Befehl| post<br>/course|
|Eingabetyp| Course|
|Ausgabetyp| Course|

||getExistsCourseTransactions|
| :----------- |:-----: |
|Beschreibung| prüft, ob die Tabellen zur Veranstaltung korrekt installiert sind|
|Befehl| get<br>/link/exists/course/:courseid|
|Eingabetyp| -|
|Ausgabetyp| Course|
|||
||Patzhalter|
|Name|courseid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|eine Veranstaltungs ID (`Course`)|

||addTransaction|
| :----------- |:-----: |
|Beschreibung| erzeugt eine neue Transaktion|
|Befehl| post<br>/transaction/course/:courseid|
|Eingabetyp| Transaction|
|Ausgabetyp| Transaction|
|||
||Patzhalter|
|Name|courseid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|eine Veranstaltungs ID (`Course`)|

||deleteCourse|
| :----------- |:-----: |
|Beschreibung| entfernt die `Transaction_X` Tabelle|
|Befehl| delete<br>/course|
|Eingabetyp| -|
|Ausgabetyp| Course|

||getApiProfiles|
| :----------- |:-----: |
|Beschreibung| liefert `GateProfile`-Objekte, welche unsere Befehle in die Standardprofile von CGate einsortieren|
|Befehl| GET<br>/api/profiles|
|Eingabetyp| -|
|Ausgabetyp| GateProfile|


## Ausgänge
---------------

||deleteTransaction|
| :----------- |:-----: |
|Ziel| DBQueryWrite|
|Befehl| DELETE<br>/query|
|Beschreibung| für den Befehl deleteTransaction|

||addTransaction|
| :----------- |:-----: |
|Ziel| DBQueryWrite|
|Befehl| POST<br>/query|
|Beschreibung| für den Befehl addTransaction|

||deleteTransactionShort|
| :----------- |:-----: |
|Ziel| DBQueryWrite|
|Befehl| DELETE<br>/query|
|Beschreibung| für den Befehl deleteTransactionShort|

||cleanTransactions|
| :----------- |:-----: |
|Ziel| DBQueryWrite|
|Befehl| DELETE<br>/query|
|Beschreibung| für den Befehl cleanTransactions|

||addExerciseTransaction|
| :----------- |:-----: |
|Ziel| DBQueryWrite|
|Befehl| POST<br>/query|
|Beschreibung| für den Befehl addExerciseTransaction|

||addSheetTransaction|
| :----------- |:-----: |
|Ziel| DBQueryWrite|
|Befehl| POST<br>/query|
|Beschreibung| für den Befehl addSheetTransaction|

||deleteCourse|
| :----------- |:-----: |
|Ziel| DBQuerySetup|
|Befehl| POST<br>/query|
|Beschreibung| für den Befehl deleteCourse|

||addCourse|
| :----------- |:-----: |
|Ziel| DBQuerySetup|
|Befehl| POST<br>/query|
|Beschreibung| für den Befehl addCourse|

||getTransaction|
| :----------- |:-----: |
|Ziel| DBQueryRead|
|Befehl| GET<br>/query/procedure/DBTransactionGetTransaction/:profile/:courseid/:auid/:tid/:random|
|Beschreibung| für den Befehl getTransaction|

||getTransactionShort|
| :----------- |:-----: |
|Ziel| DBQueryRead|
|Befehl| GET<br>/query/procedure/DBTransactionGetTransactionShort/:profile/:courseid/:tid/:random|
|Beschreibung| für den Befehl getTransactionShort|

||getAmountOfExpiredTransactions|
| :----------- |:-----: |
|Ziel| DBQueryRead|
|Befehl| GET<br>/query/procedure/DBTransactionGetAmountOfExpiredTransactions/:profile/:courseid|
|Beschreibung| für den Befehl getAmountOfExpiredTransactions|

||getExistsCourseTransactions|
| :----------- |:-----: |
|Ziel| DBQueryRead|
|Befehl| GET<br>/query/procedure/DBTransactionGetExistsCourseTransactions/:profile/:courseid|
|Beschreibung| für den Befehl getExsistsCourseTransactions|


## Anbindungen
---------------

|Ausgang|request|
| :----------- |:-----: |
|Ziel| CLocalObjectRequest|
|Beschreibung| damit DBTransaction als lokales Objekt aufgerufen werden kann|

|Ausgang|postCourse|
| :----------- |:-----: |
|Ziel| LCourse|
|Beschreibung| wenn eine neue Veranstaltung angelegt wird, dann wollen wir auch aufgerufen werden|

|Ausgang|deleteCourse|
| :----------- |:-----: |
|Ziel| LCourse|
|Beschreibung| wenn eine Veranstaltung gelöscht wird, dann müssen auch unsere Tabellen entfernt werden|

|Ausgang|getCleanAmount|
| :----------- |:-----: |
|Ziel| CInstall|
|Beschreibung| ermittelt die Anzahl der zu bereinigenden Tabellenzeilen|

|Ausgang|deleteClean|
| :----------- |:-----: |
|Ziel| CInstall|
|Beschreibung| bereinigt unsere Tabellen (Transaction_X)|

|Ausgang|getDescFiles|
| :----------- |:-----: |
|Ziel| TDocuView|
|Beschreibung| die Entwicklerdokumentation soll unsere Beschreibungsdatei nutzen|

|Ausgang|getComponentProfiles|
| :----------- |:-----: |
|Ziel| TApiConfiguration|
|Beschreibung| damit unsere Aufrufe in die Standardprofile der CGate einsortiert werden|


Stand 30.06.2017
