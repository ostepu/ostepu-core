#### Weiterleitungslinks ####

Sie können hier Weiterleitungslinks auf externe Inhalte einrichten, dabei sind diese Links stets für alle Nutzergruppen sichtbar und können in der Navigationsleiste der Veranstaltung oder direkt an den Übungsserien platziert werden.

### Vorlagen verwenden

Damit Sie eine neue Weiterleitung einrichten können, müssen Sie eine der Vorlagen wählen (`Leer`ist eine zum eigenen Ausfüllen).

![](courseRedirectsD.png)

Dann können Sie diese über `Hinzufuegen` Ihrer Liste der Weiterleitungen hinzufügen und bearbeiten (das Speichern nicht vergessen!).

> Hinweis: Die Templates befinden sich auf dem Server unter UI\include\CourseManagement\CourseRedirect\templates

##### Beispiel: instituteOfComputerScience.json (der Dateiname wird dann in der Auswahlliste angezeigt)
```json
{
    "title": "Institute of Computer Science",
    "url":"http://www.informatik.uni-halle.de",
    "authentication":"none",
    "location":"course"
}
```

### Eine Weiterleitung eintragen

![](courseRedirectsE.png)

##### Titel

Der Titel ist die Bezeichnung des Links, welche also dem Nutzer später angezeigt werden soll.

##### URL

Die URL gibt das Ziel der Weiterleitung an (hier wären auch relative Pfade zu uns möglich).

#### Ort

Hier können Sie auswählen, wo der Link angezeigt werden soll (Beispiele sehen Sie unter `Die Ansicht eines Nutzers`).

### Transaktionsnummern verwenden

![](courseRedirectsF.png)

Es gibt externe Erweiterungen, welche mit Informationen versorgt werden müssen. Dazu können Nutzerdaten und Veranstaltungsdaten in einer abfragbaren Transaktion
hinterlegt werden. Dabei wird der Aufruf-URL ein `tid=...` angehängt.

##### Beispiel 
Der Aufruf unserer `test.php` würde dann erweitert zu `test.php?tid=1_74_17050a5720574c7384c78969a2b21880`
Diese kann nun über `DB/DBTransaction/transaction/authentication/redirect/transaction/1_74_17050a5720574c7384c78969a2b21880` abgefragt werden und liefert dann:
```json
{
"transactionId": "1_74_17050a5720574c7384c78969a2b21880",
"durability": "1484663014",
"authentication": "redirect",
"content": "{\"user\":{\"id\":\"4\",\"userName\":\"till\",\"email\":\"30d70.1acae@uni.de\",\"firstName\":\"Till\",\"lastName\":\"Uhlig\",\"courses\":[{\"course\":{\"id\":\"1\",\"name\":\"\\u00d6step\\u00fcTest2\",\"semester\":\"WS 2014\\\/2015\",\"defaultGroupSize\":\"1\",\"settings\":[{\"id\":\"1_1\",\"name\":\"RegistrationPeriodEnd\",\"state\":\"0\",\"type\":\"TIMESTAMP\",\"category\":\"userManagement\"},{\"id\":\"1_2\",\"name\":\"AllowLateSubmissions\",\"state\":\"1\",\"type\":\"BOOL\",\"category\":\"submissions\"},{\"id\":\"1_5\",\"name\":\"MaxStudentUploadSize\",\"state\":\"2097152\",\"type\":\"INT\",\"category\":\"submissions\"},{\"id\":\"1_12\",\"name\":\"GenerateDummyCorrectionsForTutorArchives\",\"state\":\"0\",\"type\":\"BOOL\",\"category\":\"markings\"},{\"id\":\"1_20\",\"name\":\"InsertStudentNamesIntoTutorArchives\",\"state\":\"0\",\"type\":\"BOOL\",\"category\":\"markings\"}]},\"status\":\"3\"}],\"flag\":\"1\",\"studentNumber\":\"\",\"isSuperAdmin\":\"0\",\"lang\":\"de\"},\"session\":{\"user\":\"4\",\"session\":\"aae3b9d0d189a0be379f7b8b97daefd9\"}}"
}
```



### Speichern

Sie müssen jede Änderung an den Einträgen (auch ein das Löschen eines Links) über die Schaltfläche `speichern` bestätigen.

![](courseRedirectsC.png)

Ss sollte anschließend ein grüner Hinweis erscheinen, welcher Bescheinigt, dass alle Änderungen erfolgreich durchgeführt wurden.




### Die Ansicht eines Nutzers

Eine Weiterleitung welche den Ort `Veranstaltung` besitzt, wird den Nutzern in der Navigationsleiste angezeigt.

![](courseRedirectsA.png)

Der Ort `Uebungsserie` führt dazu, dass die Nutzer in der Navigation einer Übungsserie den Link sehen.

![](courseRedirectsB.png)