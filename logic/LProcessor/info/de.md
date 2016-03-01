<!--
 * @file de.md
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
-->



#### Eingänge
- courseid = eine Veranstaltungs ID (`Course`)

| Bezeichnung  | Eingabetyp  | Ausgabetyp | Befehl | Beschreibung |
| :----------- |:-----------:| :---------:| :----- | :----------- |
|PostSubmission|Submission|Submission|POST /submission| verarbeitet eine Einsendung oder eine Menge von Einsendungen |
|AddProcess|Process|Process|POST /process| fügt eine neue Verarbeitung hinzu (sendet die Bestandteile einer Verarbeitung an die entsprechenden Komponenten)  |
|AddCourse|Course|Course|POST /course| installiert die Komponente in dieser Veranstaltung |
|DeleteCourse|-|Course|DELETE /course/:courseid| entfernt die Komponente aus dieser Veranstaltung |
|GetExistsCourse|-|Course|GET /link/exists/course/:courseid| prüft, ob diese Komponente für diese Veranstaltung korrekt installiert ist |

#### Ausgänge
- courseid = eine Veranstaltungs ID (`Course`)
- processid = die ID eines Prozesses (`Process`)
- componentid = die ID einer Komponente (`Component`)

| Bezeichnung  | Ziel  | Verwendung | Beschreibung |
| :----------- |:----- | :--------- | :----------- |
|submission|LSubmission|POST /submission| ??? |
|marking|LMarking|POST /marking| ??? |
|processorDb|DBProcess| ??? | ??? |
|attachment|DBProcessAttachment|POST /attachment| ??? |
|workFiles|DBProcessWorkFiles|POST /attachment| ??? |
|getExerciseExerciseFileType|DBExerciseFileType|GET /exercisefiletype/exercise/:eid| ??? |
|file|LFile|POST /file| ??? |
|postCourse|DBProcess| ??? | ??? |

#### Anbindungen
| Bezeichnung  | Ziel  | Priorität | Beschreibung |
| :----------- |:----- | :--------:| :------------|
|request|CLocalObjectRequest|-| damit LProcessor als lokales Objekt aufgerufen werden kann |
|extension|LExtension|-| diese Verbindung ist notwendig, damit die LProcessor in den Veranstaltungseinstellungen und `Erweiterungen` erscheint und installierbar wird |
|deleteCourse|LCourse|150| ??? |
|postCourse|LCourse|150| ??? |
Stand 13.06.2015