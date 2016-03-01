<!--
  - @file de.md
  -
  - @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
  -
  - @package OSTEPU (https://github.com/ostepu/system)
  - @since 0.3.5
  -
  - @author Till Uhlig <till.uhlig@student.uni-halle.de>
  - @date 2015
 -->

#### Eingänge
- cid = ???
- courseid = ???

| Bezeichnung  | Eingabetyp  | Ausgabetyp | Befehl | Beschreibung |
| :----------- |:-----------:| :---------:| :----- | :----------- |
|getDaten|BEISPIEL|BEISPIEL|GET<br>/beispiel/course/:cid| ??? |
|getCourse|-|Course|GET<br>/course(/course)/:courseid| ??? |

#### Ausgänge
- abc = ???
- courseid = ???

| Bezeichnung  | Ziel  | Verwendung | Beschreibung |
| :----------- |:----- | :--------- | :----------- |
|out|DBQuery2|POST<br>/query/:abc| ??? |
|getCourse|DBQuery2|GET<br>/query/procedure/DBCourseGetCourse/:courseid| ??? |

#### Anbindungen
| Bezeichnung  | Ziel  | Priorität | Beschreibung |
| :----------- |:----- | :--------:| :------------|
|request|CLocalObjectRequest|-| ??? |

Stand 29.06.2015
