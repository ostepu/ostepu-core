#### Datenbank
Die DBApprovalCondition ermöglicht den Zugriff auf die `ApprovalCondition` Tabelle der Datenbank, dabei sollen
Zulassungsbedingungen verwaltet werden.
Dazu wird bei einem `POST /platform` Aufruf die nachstehende Tabelle erzeugt.

| Spalte        | Struktur  | Beschreibung | Besonderheit |
| :------       |:---------:| :------------| -----------: |
|AC_id|INT NOT NULL| die ID der Zulassungsbedingung |AUTO_INCREMENT,<br>UNIQUE|
|C_id|INT NOT NULL| ein Verweis auf die zugehörige Veranstaltung (`Course`) |-|
|ET_id|INT NOT NULL| ein Verweis auf einen Aufgabentyp (`ExerciseType`) |-|
|AC_percentage|FLOAT NOT NULL DEFAULT 0| die zu erreichenden Punkte in Prozent. Bsp.: 0.5 für 50% |-|

#### Datenstruktur
Zu dieser Tabelle gehört die `ApprovalCondition` Datenstruktur.

#### Eingänge
- courseid = eine Veranstaltungs ID (`Course`)
- apid = die ID einer Zulassungsbedingung (`ApprovalCondition`)

| Bezeichnung  | Eingabetyp  | Ausgabetyp | Befehl | Beschreibung |
| :----------- |:-----------:| :---------:| :----- | :----------- |
|editApprovalCondition|ApprovalCondition|ApprovalCondition|PUT<br>/approvalcondition(/approvalcondition)/:apid| verändert eine existierende Zulassungsbedingung |
|deleteApprovalCondition|-|ApprovalCondition|DELETE<br>/approvalcondition(/approvalcondition)/:apid| entfernt einen Zulassungseintrag |
|addApprovalCondition|ApprovalCondition|ApprovalCondition|POST<br>/approvalcondition| fügt eine neue Zulassungsbedingung ein |
|getApprovalCondition|-|ApprovalCondition|GET<br>/approvalcondition(/approvalcondition)/:apid| gibt eine einzelne Zulassungsbedingung aus |
|getAllApprovalConditions|-|ApprovalCondition|GET<br>/approvalcondition(/approvalcondition)| gibt alle Zulassungsbedingungen aus (für alle Veranstaltungen) |
|getCourseApprovalConditions|-|ApprovalCondition|GET<br>/approvalcondition/course/:courseid| liefert die Zulassungsbedingungen einer einzelnen Veranstaltung |
|addPlatform|Platform|Platform|POST<br>/platform|installiert dies zugehörige Tabelle und die Prozeduren für diese Plattform|
|deletePlatform|-|Platform|DELETE<br>/platform|entfernt die Tabelle und Prozeduren aus der Plattform|
|getExistsPlatform|-|Platform|GET<br>/link/exists/platform| prüft, ob die Tabelle und die Prozeduren existieren |
|getSamplesInfo|-|-|GET<br>/samples| ??? |
|postSamples|-|Query|POST<br>/samples/:amount| erzeugt Zufallsdaten (amount = Anzahl der Datensätze) |

#### Ausgänge
- courseid = eine Veranstaltungs ID (`Course`)
- apid = die ID einer Zulassungsbedingung (`ApprovalCondition`)

| Bezeichnung  | Ziel  | Verwendung | Beschreibung |
| :----------- |:----- | :--------- | :----------- |
|out2|DBQuery2|POST<br>/query| wird für EDIT, DELETE<br>und POST<br>SQL-Templates verwendet |
|out|DBQuery|POST<br>/query| wird für EDIT, DELETE<br>und POST<br>SQL-Templates verwendet |
|getApprovalCondition|DBQuery2|GET<br>/query/procedure<br>/DBApprovalConditionGetApprovalCondition/:apid| Prozeduraufruf |
|getAllApprovalConditions|DBQuery2|GET<br>/query/procedure<br>/DBApprovalConditionGetAllApprovalConditions| Prozeduraufruf |
|getCourseApprovalConditions|DBQuery2|GET<br>/query/procedure<br>/DBApprovalConditionGetCourseApprovalConditions/:courseid| Prozeduraufruf |
|getExistsPlatform|DBQuery2|GET<br>/query/procedure<br>/DBApprovalConditionGetExistsPlatform| Prozeduraufruf |
|getSamplesInfo|DBQuery2|GET<br>/query/procedure<br>/DBApprovalConditionGetExistsPlatform| Prozeduraufruf |

#### Anbindungen
| Bezeichnung  | Ziel  | Priorität | Beschreibung |
| :----------- |:----- | :--------:| :------------|
|request|CLocalObjectRequest|-| damit DBApprovalCondition als lokales Objekt aufgerufen werden kann |

Stand 13.06.2015