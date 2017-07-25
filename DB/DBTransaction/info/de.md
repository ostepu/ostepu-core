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

| Themen |
| :- |
| [Befehle/Eingänge (Commands.json)](#eingaenge) |
| [Ausgänge (Component.json => Links)](#ausgaenge) |
| [Anbindungen (Component.json => Connector)](#anbindungen) |

## <a name='eingaenge'></a>Befehle/Eingänge (Commands.json)
Diese Befehle bietet diese Komponente als Aufruf an.

||addSheetTransaction|
| :----------- |:----- |
|Beschreibung| erzeugt eine Transaktion (anhand der ID einer Übungsserie)|
|Befehl| post /transaction/exercisesheet/:esid|
|Eingabetyp| Transaction|
|Ausgabetyp| Transaction|
|||
||Patzhalter|
|Name|esid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID einer Übungsserie (`ExercisehSheet`)|

||deleteTransactionShort|
| :----------- |:----- |
|Beschreibung| entfernt eine Transaktion (ohne das Sicherheitswort)|
|Befehl| delete /transaction/transaction/:tid|
|Eingabetyp| -|
|Ausgabetyp| Transaction|
|||
||Patzhalter|
|Name|tid|
|Regex|%^([a-z0-9_]+)$%|
|Beschreibung|eine Transaktionsnummer|

||deleteTransaction|
| :----------- |:----- |
|Beschreibung| entfernt eine Transaktion|
|Befehl| delete /transaction/authentication/:auid/transaction/:tid|
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
| :----------- |:----- |
|Beschreibung| entfernt alle abgelaufenen Transaktionsnummern aus `Transaction_X`|
|Befehl| delete /clean/course/:courseid|
|Eingabetyp| -|
|Ausgabetyp| Transaction|
|||
||Patzhalter|
|Name|courseid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|eine Veranstaltungs ID (`Course`)|

||getTransaction|
| :----------- |:----- |
|Beschreibung| liefert eine Transaktion und deren Daten+Inhalt|
|Befehl| get /transaction/authentication/:auid/transaction/:tid|
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
| :----------- |:----- |
|Beschreibung| erzeugt eine Transaktion (anhand der ID einer Aufgabe)|
|Befehl| post /transaction/exercise/:eid|
|Eingabetyp| Transaction|
|Ausgabetyp| Transaction|
|||
||Patzhalter|
|Name|eid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID einer Aufgabe (`Exercise`)|

||getTransactionShort|
| :----------- |:----- |
|Beschreibung| liefert eine Transaktion und deren Daten+Inhalt (ohne das Geheimwort)|
|Befehl| get /transaction/transaction/:tid|
|Eingabetyp| -|
|Ausgabetyp| Course|
|||
||Patzhalter|
|Name|tid|
|Regex|%^([a-z0-9_]+)$%|
|Beschreibung|eine Transaktionsnummer|

||getAmountOfExpiredTransactions|
| :----------- |:----- |
|Beschreibung| ermittelt die Anzahl der abgelaufenen Transaktionsnummern in `Transaction_X`|
|Befehl| get /clean/course/:courseid|
|Eingabetyp| -|
|Ausgabetyp| -|
|||
||Patzhalter|
|Name|courseid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|eine Veranstaltungs ID (`Course`)|

||addCourse|
| :----------- |:----- |
|Beschreibung| fügt DBTransaction zur Veranstaltung hinzu (erzeugt `Transaction_X`)|
|Befehl| post /course|
|Eingabetyp| Course|
|Ausgabetyp| Course|

||getExistsCourseTransactions|
| :----------- |:----- |
|Beschreibung| prüft, ob die Tabellen zur Veranstaltung korrekt installiert sind|
|Befehl| get /link/exists/course/:courseid|
|Eingabetyp| -|
|Ausgabetyp| Course|
|||
||Patzhalter|
|Name|courseid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|eine Veranstaltungs ID (`Course`)|

||addTransaction|
| :----------- |:----- |
|Beschreibung| erzeugt eine neue Transaktion|
|Befehl| post /transaction/course/:courseid|
|Eingabetyp| Transaction|
|Ausgabetyp| Transaction|
|||
||Patzhalter|
|Name|courseid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|eine Veranstaltungs ID (`Course`)|

||deleteCourse|
| :----------- |:----- |
|Beschreibung| entfernt die `Transaction_X` Tabelle|
|Befehl| delete /course|
|Eingabetyp| -|
|Ausgabetyp| Course|

||getApiProfiles|
| :----------- |:----- |
|Beschreibung| liefert `GateProfile`-Objekte, welche unsere Befehle in die Standardprofile von CGate einsortieren|
|Befehl| GET /api/profiles|
|Eingabetyp| -|
|Ausgabetyp| GateProfile|


## <a name='ausgaenge'></a>Ausgänge (Component.json => Links)
Wenn eine Komponente selbst noch Unteranfragen an andere Komponenten stellen möchte, dann werden diese über die `Ausgänge` bearbeitet.
Dabei kann ein Ausgang bereits auf eine Komponente gerichtet sein (`Ziel`) oder durch die Zielkomponente selbst angebunden werden (`Connector`)

||deleteTransaction|
| :----------- |:----- |
|Ziel| DBQueryWrite|
|Befehl| DELETE /query|
|Beschreibung| für den Befehl deleteTransaction|

||addTransaction|
| :----------- |:----- |
|Ziel| DBQueryWrite|
|Befehl| POST /query|
|Beschreibung| für den Befehl addTransaction|

||deleteTransactionShort|
| :----------- |:----- |
|Ziel| DBQueryWrite|
|Befehl| DELETE /query|
|Beschreibung| für den Befehl deleteTransactionShort|

||cleanTransactions|
| :----------- |:----- |
|Ziel| DBQueryWrite|
|Befehl| DELETE /query|
|Beschreibung| für den Befehl cleanTransactions|

||addExerciseTransaction|
| :----------- |:----- |
|Ziel| DBQueryWrite|
|Befehl| POST /query|
|Beschreibung| für den Befehl addExerciseTransaction|

||addSheetTransaction|
| :----------- |:----- |
|Ziel| DBQueryWrite|
|Befehl| POST /query|
|Beschreibung| für den Befehl addSheetTransaction|

||deleteCourse|
| :----------- |:----- |
|Ziel| DBQuerySetup|
|Befehl| POST /query|
|Beschreibung| für den Befehl deleteCourse|

||addCourse|
| :----------- |:----- |
|Ziel| DBQuerySetup|
|Befehl| POST /query|
|Beschreibung| für den Befehl addCourse|

||getTransaction|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBTransactionGetTransaction/:profile/:courseid/:auid/:tid/:random|
|Beschreibung| für den Befehl getTransaction|

||getTransactionShort|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBTransactionGetTransactionShort/:profile/:courseid/:tid/:random|
|Beschreibung| für den Befehl getTransactionShort|

||getAmountOfExpiredTransactions|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBTransactionGetAmountOfExpiredTransactions/:profile/:courseid|
|Beschreibung| für den Befehl getAmountOfExpiredTransactions|

||getExistsCourseTransactions|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBTransactionGetExistsCourseTransactions/:profile/:courseid|
|Beschreibung| für den Befehl getExsistsCourseTransactions|


## <a name='anbindungen'></a>Anbindungen (Component.json => Connector)
Eine Anbindung verlangt von einer anderen Komponente (`Ziel`) die Anbindung/Verbindung zu dieser Komponente.
Wenn eine Anbindung den aufzurufenden Befehl vorgibt, dann ist die Notation: METHODE URL (PRIORITÄT).

|Ausgang|request|
| :----------- |:----- |
|Ziel| CLocalObjectRequest|
|Beschreibung| damit DBTransaction als lokales Objekt aufgerufen werden kann|

|Ausgang|postCourse|
| :----------- |:----- |
|Ziel| LCourse|
|Beschreibung| wenn eine neue Veranstaltung angelegt wird, dann wollen wir auch aufgerufen werden|

|Ausgang|deleteCourse|
| :----------- |:----- |
|Ziel| LCourse|
|Beschreibung| wenn eine Veranstaltung gelöscht wird, dann müssen auch unsere Tabellen entfernt werden|

|Ausgang|getCleanAmount|
| :----------- |:----- |
|Ziel| CInstall|
|Beschreibung| ermittelt die Anzahl der zu bereinigenden Tabellenzeilen|

|Ausgang|deleteClean|
| :----------- |:----- |
|Ziel| CInstall|
|Beschreibung| bereinigt unsere Tabellen (Transaction_X)|

|Ausgang|getDescFiles|
| :----------- |:----- |
|Ziel| TDocuView|
|Beschreibung| die Entwicklerdokumentation soll unsere Beschreibungsdatei nutzen|

|Ausgang|getComponentProfiles|
| :----------- |:----- |
|Ziel| TApiConfiguration|
|Beschreibung| damit unsere Aufrufe in die Standardprofile der CGate einsortiert werden|


Stand 25.07.2017
