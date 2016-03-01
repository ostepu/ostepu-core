<!--
 * @file de.md
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
-->

#### Datenbank
Die DBSetting ermöglicht den Zugriff auf die `SETTING_X` Tabellen der Datenbank, dabei sollen
Veranstaltungseinstellungen verwaltet werden.
Dazu wird bei einem `POST /course` Aufruf die nachstehende Tabelle erzeugt (X = ID der Veranstaltung).

| Spalte  | Struktur  | Beschreibung | Besonderheit |
| :------ |:---------:| :------------| -----------: |
|SET_id   |INT NOT NULL| die ID der Einstellung |AUTO_INCREMENT,<br>UNIQUE|
|SET_name |VARCHAR(255) NOT NULL| ein Bezeichner, wird exakt so auch zur Bestimmung des Wertes benötigt |UNIQUE|
|SET_state|VARCHAR(255) NOT NULL DEFAULT ''| der Zustand/Wert der Einstellung (Bsp.: 1) ' |-|
|SET_type |VARCHAR(255) NOT NULL DEFAULT 'TEXT'| der erwartete Typ (wird in der Oberfläche benötigt, damit der Nutzer den Wert entsprechend eingeben kann). Erlaubt sind: TEXT (Text), INT (Zahl/Integer), BOOL (Wahrheitswert als 0/1), TIMESTAMP (Unix-Zeitstempel)  |-|
|SET_category|VARCHAR(255) NOT NULL DEFAULT ''| Ein Bezeichner für die Kategorie (submissions, markings, userManagement...) ' |-|

Möglicherweise tragen andere Komponenten selbstständig hier Einstellungen für sich ein.

#### Datenstruktur
Zu dieser Tabelle gehört die `Session` Datenstruktur.

#### Eingänge
- courseid = eine Veranstaltungs ID (`Course`)

| Bezeichnung  | Eingabetyp  | Ausgabetyp | Befehl | Beschreibung |
| :----------- |:-----------:| :---------:| :----- | :----------- |
||||||
||||||
||||||
||||||
||||||
||||||
||||||
||||||
||||||
||||||
||||||

#### Ausgänge
- courseid = eine Veranstaltungs ID (`Course`)

| Bezeichnung  | Ziel  | Verwendung | Beschreibung |
| :----------- |:----- | :--------- | :----------- |
|||||
|||||
|||||
|||||
|||||
|||||
|||||
|||||
|||||

#### Anbindungen
| Bezeichnung  | Ziel  | Priorität | Beschreibung |
| :----------- |:----- | :--------:| :------------|
|request|CLocalObjectRequest|-| damit DBSetting als lokales Objekt aufgerufen werden kann |
|postCourse|LCourse|150| damit wir beim Erstellen einer neuen Veranstaltung aufgerufen werden |
|deleteCourse|LCourse|150| damit wir beim Entfernen einer Veranstaltung aufgerufen werden |

Stand 13.06.2015