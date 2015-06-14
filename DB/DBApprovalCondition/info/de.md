#### Datenbank
Die DBApprovalCondition ermöglicht den Zugriff auf die `ApprovalCondition` Tabelle der Datenbank, dabei sollen
Zulassungsbedingungen verwaltet werden.
Dazu wird bei einem `POST /platform` Aufruf die nachstehende Tabelle erzeugt.

| Spalte        | Struktur  | Beschreibung | Besonderheit |
| :------       |:---------:| :------------| -----------: |
|AC_id|INT NOT NULL| ??? |AUTO_INCREMENT,<br>UNIQUE|
|C_id|INT NOT NULL| ??? |-|
|ET_id|INT NOT NULL| ??? |-|
|AC_percentage|FLOAT NOT NULL DEFAULT 0| ??? |-|

#### Datenstruktur
Zu dieser Tabelle gehört die `ApprovalCondition` Datenstruktur.

#### Eingänge
| Bezeichnung  | Eingabetyp  | Ausgabetyp | Befehl | Beschreibung |
| :----------- |:-----------:| :---------:| :----- | :----------- |
|editApprovalCondition|ApprovalCondition|ApprovalCondition|PUT<br>/approvalcondition(/approvalcondition)/:apid| ??? |
|deleteApprovalCondition|-|ApprovalCondition|DELETE<br>/approvalcondition(/approvalcondition)/:apid| ??? |
|addApprovalCondition|ApprovalCondition|ApprovalCondition|POST<br>/approvalcondition| ??? |
|getApprovalCondition|-|ApprovalCondition|GET<br>/approvalcondition(/approvalcondition)/:apid| ??? |
|getAllApprovalConditions|-|ApprovalCondition|GET<br>/approvalcondition(/approvalcondition)| ??? |
|getCourseApprovalConditions|-|ApprovalCondition|GET<br>/approvalcondition/course/:courseid| ??? |
|addPlatform|Platform|Platform|POST<br>/platform|installiert dies zugehörige Tabelle und die Prozeduren für diese Plattform|
|deletePlatform|-|Platform|DELETE<br>/platform|entfernt die Tabelle und Prozeduren aus der Plattform|
|getExistsPlatform|-|Platform|GET<br>/link/exists/platform| prüft, ob die Tabelle und die Prozeduren existieren |
|getSamplesInfo|-|-|GET<br>/samples| ??? |
|postSamples|-|Query|POST<br>/samples/:amount| erzeugt Zufallsdaten (amount = Anzahl der Datensätze) |

#### Ausgänge
courseid = eine Veranstaltungs ID (`Course`)
apid = die ID einer Zulassungsbedingung (`ApprovalCondition`)

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