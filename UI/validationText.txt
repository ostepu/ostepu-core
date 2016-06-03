
#Verwendung
```PHP
include_once dirname(__FILE__) . '/../Assistants/vendor/Validation/Validation.php';

// ab hier wird ein neuer Regelsatz für $_POST['action'] definiert,
// wobei das Feld nur die Werte 'SetPassword' und 'SetAccountInfo' haben darf
// oder nicht existieren darf (sodass es zu 'noAction' wird)
$val = Validation::open($_POST)
  ->addSet('action',
           array('set_default'=>'noAction',
                 'satisfy_in_list'=>array('noAction', 'SetPassword', 'SetAccountInfo'),
                 'on_error'=>array('type'=>'error',
                                   'text'=>'unbekannte Aktion'))); 
                                   
$result = $val->validate(); // liefert die Ergebnismenge

if ($val->isValid()){
    // $_POST['action'] erfüllt die Regelmenge und kann genutzt werden
    echo $result['action'];
} else {
  // wenn die Eingabe nicht validiert werden konnte, können hier die
  // Fehlermeldungen behandelt werden
  $notifications = $val->getNotifications();
}
```

# Selektoren
Sie können mit diesen Funktionen die Elemente der Eingabe auswählen, welche die definierten Regeln
erfüllen sollen.

| Übersicht | | | |
| :-: | :-: | :-: | :-: |
|[key](#key)|[key_list](#key_list)|[key_all](#key_all)|[key_regex](#key_regex)|
|[key_numeric](#key_numeric)|[key_integer](#key_integer)|[key_min_numeric](#key_min_numeric)|[key_max_numeric](#key_max_numeric)|
|[key_starts_with](#key_starts_with)|[key_union](#key_union)|[key_intersection](#key_intersection)|

#### key

====================================================================================

#### key_list

====================================================================================

#### key_all

====================================================================================

#### key_regex

====================================================================================

#### key_numeric

====================================================================================

#### key_integer

====================================================================================

#### key_min_numeric

====================================================================================

#### key_max_numeric

====================================================================================

#### key_starts_with

====================================================================================

#### key_union

====================================================================================

#### key_intersection

====================================================================================


# Regeln

| Übersicht | | | |
| :-: | :-: | :-: | :-: |
|[satisfy_exists](#satisfy_exists)|[satisfy_not_exists](#satisfy_not_exists)|[satisfy_required](#satisfy_required)|[satisfy_isset](#satisfy_isset)|
|[satisfy_not_isset](#satisfy_not_isset)|[satisfy_not_empty](#satisfy_not_empty)|[satisfy_empty](#satisfy_empty)|[satisfy_equals_field](#satisfy_equals_field)|
|[satisfy_not_equals_field](#satisfy_not_equals_field)|[satisfy_regex](#satisfy_regex)|[satisfy_equalTo](#satisfy_equalto)|[satisfy_min_numeric](#satisfy_min_numeric)|
|[satisfy_max_numeric](#satisfy_max_numeric)|[satisfy_exact_numeric](#satisfy_exact_numeric)|[satisfy_min_len](#satisfy_min_len)|[satisfy_max_len](#satisfy_max_len)|
|[satisfy_exact_len](#satisfy_exact_len)|[satisfy_in_list](#satisfy_in_list)|[satisfy_not_in_list](#satisfy_not_in_list)|[satisfy_value](#satisfy_value)|
|[satisfy_file_exists](#satisfy_file_exists)|[satisfy_file_isset](#satisfy_file_isset)|[satisfy_file_error](#satisfy_file_error)|[satisfy_file_no_error](#satisfy_file_no_error)|
|[satisfy_file_extension](#satisfy_file_extension)|[satisfy_file_mime](#satisfy_file_mime)|[satisfy_file_size](#satisfy_file_size)|[satisfy_file_name](#satisfy_file_name)|
|[satisfy_file_name_strict](#satisfy_file_name_strict)|[to_float](#to_float)|[to_string](#to_string)|[to_lower](#to_lower)|
|[to_upper](#to_upper)|[to_integer](#to_integer)|[to_boolean](#to_boolean)|[to_md5](#to_md5)|
|[to_sha1](#to_sha1)|[to_base64](#to_base64)|[to_string_from_base64](#to_string_from_base64)|[to_object_from_json](#to_object_from_json)|
|[to_array_from_json](#to_array_from_json)|[to_json](#to_json)|[to_timestamp](#to_timestamp)|[on_error](#on_error)|
|[on_no_error](#on_no_error)|[on_success](#on_success)|[logic_or](#logic_or)|[perform_this_foreach](#perform_this_foreach)|
|[perform_foreach](#perform_foreach)|[perform_this_array](#perform_this_array)|[perform_array](#perform_array)|[perform_switch_case](#perform_switch_case)|
|[sanitize_url](#sanitize_url)|[sanitize](#sanitize)|[set_default](#set_default)|[set_copy](#set_copy)|
|[set_value](#set_value)|[set_field_value](#set_field_value)|[set_error](#set_error)|[valid_email](#valid_email)|
|[valid_url](#valid_url)|[valid_url_query](#valid_url_query)|[valid_regex](#valid_regex)|[valid_hash](#valid_hash)|
|[valid_md5](#valid_md5)|[valid_sha1](#valid_sha1)|[valid_identifier](#valid_identifier)|[valid_user_name](#valid_user_name)|
|[valid_userName](#valid_username)|[valid_timestamp](#valid_timestamp)|[valid_alpha](#valid_alpha)|[valid_alpha_space](#valid_alpha_space)|
|[valid_integer](#valid_integer)|[valid_alpha_numeric](#valid_alpha_numeric)|[valid_alpha_space_numeric](#valid_alpha_space_numeric)|[valid_json](#valid_json)|
|[to_structure](#to_structure)|[is_float](#is_float)|[is_boolean](#is_boolean)|[is_integer](#is_integer)|
|[is_string](#is_string)|[is_array](#is_array)|

#### satisfy_exists

siehe [satisfy_isset](#satisfy_isset)

====================================================================================

#### satisfy_not_exists

siehe [satisfy_not_isset](#satisfy_not_isset)

====================================================================================

#### satisfy_required

siehe [satisfy_isset](#satisfy_isset)

====================================================================================

#### satisfy_isset

```PHP
// das Feld $_POST['action'] muss gesetzt sein
$val = Validation::open($_POST);
$val->addSet('action',
             array('satisfy_isset'));
```

====================================================================================

#### satisfy_not_isset

```PHP
// das Feld $_POST['action'] darf nicht gesetzt sein
$val = Validation::open($_POST);
$val->addSet('action',
             array('satisfy_not_isset'));

```

====================================================================================

#### satisfy_not_empty

```PHP
// das Feld $_POST['action'] darf nicht leer sein
$val = Validation::open($_POST);
$val->addSet('action',
             array('satisfy_not_empty'));
```
siehe [php:empty](http://php.net/manual/de/function.empty.php)

====================================================================================

#### satisfy_empty

```PHP
// das Feld $_POST['action'] muss leer sein
$val = Validation::open($_POST);
$val->addSet('action',
             array('satisfy_empty'));
```
siehe [php:empty](http://php.net/manual/de/function.empty.php)

====================================================================================

#### satisfy_equals_field

| Aufbau      |
| :---------- |
| satisfy_equals_field=>param  |

| Parameter   | Typ | Beschreibung |
| :---------: | :--: | :----------- |
| param | string | der Bezeichner des Feldes, welches gleich sein soll |

```PHP
// das Feld $_POST['newPasswordRepeat'] soll den selben Inhalt
// wie das Feld $_POST['newPassword'] haben
$val = Validation::open($_POST);
$val->addSet('newPasswordRepeat',
             array('satisfy_equals_field'=>'newPassword'));

```

====================================================================================

#### satisfy_not_equals_field

| Aufbau      |
| :---------- |
| satisfy_not_equals_field=>param  |

| Parameter   | Typ | Beschreibung |
| :---------: | :--: | :----------- |
| param | string | der Bezeichner des Feldes, welches nicht gleich sein soll |

```PHP
// das Feld $_POST['deleteSheetWarning'] darf nicht den selben
// Inhalt wie das Feld $_POST['deleteSheet'] haben
$val = Validation::open($_POST);
$val->addSet('deleteSheetWarning',
             array('satisfy_not_equals_field'=>'deleteSheet'));

```

====================================================================================

#### satisfy_regex

| Aufbau      |
| :---------- |
| satisfy_regex => param  |

| Parameter   | Typ | Beschreibung |
| :---------: | :--: | :----------- |
| param | string | regulärer Ausdruck |

```PHP
// das Feld $_POST['key'] muss den regulären Ausdruck
// %^([a-zA-Z0-9_]+)$% erfüllen
$val = Validation::open($_POST);
$val->addSet('key',
             array('satisfy_regex'=>'%^([a-zA-Z0-9_]+)$%'));
```
siehe [php:PCRE](http://php.net/manual/de/reference.pcre.pattern.syntax.php)

====================================================================================

#### satisfy_equalTo

siehe [satisfy_value](#satisfy_value)

====================================================================================

#### satisfy_min_numeric

| Aufbau      |
| :---------- |
| satisfy_min_numeric => param  |

| Parameter   | Typ | Beschreibung |
| :---------: | :--: | :----------- |
| param | string/integer/float | Minimalwert |

```PHP
// das Feld $_POST['field'] soll >= 0 sein
$val = Validation::open($_POST);
$val->addSet('field',
             array('satisfy_min_numeric'=>0));
```

====================================================================================

#### satisfy_max_numeric

| Aufbau      |
| :---------- |
| satisfy_max_numeric => param  |

| Parameter   | Typ | Beschreibung |
| :---------: | :--: | :----------- |
| param | string/integer/float | Maximalwert |

```PHP
// das Feld $_POST['field'] soll <= 100 sein
$val = Validation::open($_POST);
$val->addSet('field',
             array('satisfy_max_numeric'=>100));
```

====================================================================================

#### satisfy_exact_numeric

| Aufbau      |
| :---------- |
| satisfy_exact_numeric => param  |

| Parameter   | Typ | Beschreibung |
| :---------: | :--: | :----------- |
| param | string/integer/float | Vergleichswert |

```PHP
// das Feld $_POST['field'] soll genau 50 sein
$val = Validation::open($_POST);
$val->addSet('field',
             array('satisfy_exact_numeric'=>50));
```

====================================================================================

#### satisfy_min_len

| Aufbau      |
| :---------- |
| satisfy_min_len => param  |

| Parameter   | Typ | Beschreibung |
| :---------: | :--: | :----------- |
| param | string/integer/float | Mindestlänge |

```PHP
// die Länge des Feldes $_POST['newPassword']
// soll >= 6 sein
$val = Validation::open($_POST);
$val->addSet('newPassword',
             array('satisfy_min_len'=>6));

```

====================================================================================

#### satisfy_max_len

| Aufbau      |
| :---------- |
| satisfy_max_len => param  |

| Parameter   | Typ | Beschreibung |
| :---------: | :--: | :----------- |
| param | string/integer/float | Maximallänge |

```PHP
// die Länge des Feldes $_POST['newPassword']
// soll <= 255 sein
$val = Validation::open($_POST);
$val->addSet('newPassword',
             array('satisfy_max_len'=>255));
```

====================================================================================

#### satisfy_exact_len

| Aufbau      |
| :---------- |
| satisfy_exact_len => param  |

| Parameter   | Typ | Beschreibung |
| :---------: | :--: | :----------- |
| param | string/integer/float | Vergleichswert |

```PHP
// die Länge des Feldes $_POST['newPassword']
// soll genau 8 sein
$val = Validation::open($_POST);
$val->addSet('newPassword',
             array('satisfy_exact_len'=>8));
```

====================================================================================

#### satisfy_in_list

| Aufbau      |
| :---------- |
| satisfy_in_list => param  |

| Parameter   | Typ | Beschreibung |
| :---------: | :--: | :----------- |
| param | array(val0,val1, ... ) | Liste der Vergleichswerte |
| val | mixed | Vergleichswerte |

```PHP
// das Feld $_POST['action'] soll einen der
// Werte 'SetPassword' oder 'SetAccountInfo' enthalten
// und wenn es nicht gesetzt ist 'noAction'
$val = Validation::open($_POST);
$val->addSet('action',
             array('satisfy_in_list'=>array('noAction', 'SetPassword', 'SetAccountInfo')));

```

====================================================================================

#### satisfy_not_in_list

| Aufbau      |
| :---------- |
| satisfy_not_in_list => param  |

| Parameter   | Typ | Beschreibung |
| :---------: | :--: | :----------- |
| param | array(val0,val1, ... ) | Liste der Vergleichswerte |
| val | mixed | Vergleichswerte |

```PHP
// das Feld $_POST['action'] darf nicht die Werte
// 'SetPassword' oder 'SetAccountInfo' haben
$val = Validation::open($_POST);
$val->addSet('action',
             array('satisfy_not_in_list'=>array('SetPassword', 'SetAccountInfo')));

```

====================================================================================

#### satisfy_value

| Aufbau      |
| :---------- |
| satisfy_value => param  |

| Parameter   | Typ | Beschreibung |
| :---------: | :--: | :----------- |
| param | mixed | Vergleichswert |

```PHP
// das Feld $_POST['action'] muss den
// Wert -1 haben
$val = Validation::open($_POST);
$val->addSet('action',
             array('satisfy_value'=>'-1'));
```

====================================================================================

#### satisfy_file_exists

```PHP
// die hochgeladene Datei in $_FILES['MarkingFile']
// soll existieren
$val = Validation::open($_FILES);
$val->addSet('MarkingFile',
             ['satisfy_file_exists']);

```
siehe [php:file_exists](http://php.net/manual/de/function.file-exists.php)

====================================================================================

#### satisfy_file_isset

```PHP
// die notwendigen Felder der hochgeladenen Datei
// sollen in $_FILES['MarkingFile'] gesetzt sein
$val = Validation::open($_FILES);
$val->addSet('MarkingFile',
             ['satisfy_file_isset']);

```

====================================================================================

#### satisfy_file_error
```PHP

```

====================================================================================

#### satisfy_file_no_error
```PHP

```

====================================================================================

#### satisfy_file_extension

| Aufbau      |
| :---------- |
| satisfy_file_extension => param oder satisfy_file_extension => array(param,param,...) |

| Parameter   | Typ | Beschreibung |
| :---------: | :--: | :----------- |
| param | string | Dateiendung |

```PHP
// die hochgeladene Datei in $_FILES['MarkingFile'] soll
// die Dateiendung .zip besitzen
$val = Validation::open($_FILES);
$val->addSet('MarkingFile',
             ['satisfy_file_extension'=>'zip']);

```

====================================================================================

#### satisfy_file_mime

| Aufbau      |
| :---------- |
| satisfy_file_mime => param  oder satisfy_file_mime => array(param,param,...) |

| Parameter   | Typ | Beschreibung |
| :---------: | :--: | :----------- |
| param | string | Strukturtyp |

```PHP
// die hochgeladene Datei in $_FILES['MarkingFile'] soll
// den Strukturtyp application/zip haben
$val = Validation::open($_FILES);
$val->addSet('MarkingFile',
             ['satisfy_file_mime'=>'application/zip']);

```
siehe [mime-Typen](https://wiki.selfhtml.org/wiki/Referenz:MIME-Typen)

====================================================================================

#### satisfy_file_size

```PHP
// nicht implementiert
```

====================================================================================

#### satisfy_file_name

| Aufbau      |
| :---------- |
| satisfy_file_name => param oder satisfy_file_name => array(param,param,...) |

| Parameter   | Typ | Beschreibung |
| :---------: | :--: | :----------- |
| param | string | Dateiname |

```PHP
// die hochgeladene Datei in $_FILES['MarkingFile'] soll
// den Dateiname upload.zip haben
$val = Validation::open($_FILES);
$val->addSet('MarkingFile',
             ['satisfy_file_name'=>'upload.zip']);
```

====================================================================================

#### satisfy_file_name_strict

```PHP
// die hochgeladene Datei in $_FILES['MarkingFile'] darf
// im Dateinamen nur die Zeichen a-z,A-z,0-9 und .-_ enthalten
$val = Validation::open($_FILES);
$val->addSet('MarkingFile',
             ['satisfy_file_name_strict']);
```

====================================================================================

#### to_float

```PHP
// wandelt $_POST['field'] in eine Gleitkommazahl um
$val = Validation::open($_POST);
$val->addSet('field',
             ['to_float']);
```

====================================================================================

#### to_string

```PHP
// wandelt $_POST['field'] in einen String um
$val = Validation::open($_POST);
$val->addSet('field',
             ['to_string']);
```

====================================================================================

#### to_lower

```PHP
// wandelt $_POST['externalTypeName'] in
// Kleinbuchstaben um
$val = Validation::open($_POST);
$val->addSet('externalTypeName',
             ['to_lower']);
```

====================================================================================

#### to_upper

```PHP
// wandelt $_POST['externalTypeName'] in
// Großbuchstaben um
$val = Validation::open($_POST);
$val->addSet('externalTypeName',
             ['to_upper']);
```

====================================================================================

#### to_integer

```PHP
$val = Validation::open($_POST);
$val->addSet('externalType',
             ['to_integer',
              'satisfy_in_list' => [1,2]]);
```

====================================================================================

#### to_boolean
```PHP
$val = Validation::open($_POST);
$val->addSet('field',
             ['to_boolean']);
```

====================================================================================

#### to_md5
```PHP
// kodiert $_POST['field'] mittels md5
$val->addSet('field',
             ['to_md5');
```
siehe [php:md5](http://php.net/manual/de/function.md5.php)

====================================================================================

#### to_sha1
```PHP
// kodiert $_POST['field'] mittels sha1
$val->addSet('field',
             ['to_sha1');
```
siehe [php:sha1](http://php.net/manual/de/function.sha1.php)

====================================================================================

#### to_base64
```PHP
// kodiert $_POST['field'] mittels base64
$val->addSet('field',
             ['to_base64');
```
siehe [php:base64_encode](http://php.net/manual/de/function.base64-encode.php)

====================================================================================

#### to_string_from_base64
```PHP
// wandelt das base64 kodierte Feld $_POST['field']
// in einen String um
$val->addSet('field',
             ['to_string_from_base64');
```
siehe [php:base64_decode](http://php.net/manual/de/function.base64-decode.php)

====================================================================================

#### to_object_from_json
```PHP

```

====================================================================================

#### to_array_from_json
```PHP

```

====================================================================================

#### to_json

```PHP
// $_POST['elem'] soll im json-Format serialisiert werden
$val = Validation::open($_POST);
$val->addSet('elem',
             array('to_json'));
```
siehe [php:json_encode](http://php.net/manual/de/function.json-encode.php)

====================================================================================

#### to_timestamp

```PHP
// $_POST['startDate'] soll in einen unix-Zeitstempel umgewandelt werden
$val = Validation::open($_POST);
$val->addSet('startDate',
             array('satisfy_exists',
                   'to_timestamp'));
```

====================================================================================

#### on_error

| Aufbau      |
| :---------- |
| on_error => array(type,text,abortSet)  |

| Parameter   | Typ | Beschreibung | Vorgabewert |
| :---------: | :--: | :----------- | :--: |
| type (optional)| string | Bezeichner für den Meldungstyp (Bsp.: warning, error oder message) | message |
| text (optional)| string | Meldungstext | |
| abortSet (optional) | bool | true = im Fehlerfall die Validierung beenden, false = sonst | true |

```PHP
// das Feld $_POST['action'] soll existieren, ansonsten
// soll eine Fehlermeldung generiert werden
$val = Validation::open($_POST);
$val->addSet('action',
             array('satisfy_exists',
                   'on_error'=>array('type'=>'error',
                                     'text'=>'unbekannte Aktion')));
```

====================================================================================

#### on_no_error

siehe [on_success](#on_success)

====================================================================================

#### on_success

| Aufbau      |
| :---------- |
| on_error => array(type,text,abortSet)  |

| Parameter   | Typ | Beschreibung | Vorgabewert |
| :---------: | :--: | :----------- | :--: |
| type (optional)| string | Bezeichner für den Meldungstyp (Bsp.: warning, error oder message) | message |
| text (optional)| string | Meldungstext | |
| abortSet (optional) | bool | true = im Fehlerfall die Validierung beenden, false = sonst | false |

```PHP
// wenn das Feld $_POST['action'] existiert, soll eine
// Erfolgsmeldung erzeugt werden (kein Abbruch)
$val = Validation::open($_POST);
$val->addSet('action',
             array('satisfy_exists',
                   'on_success'=>array('text'=>'Aktion existiert')));
```

====================================================================================

#### logic_or

```PHP
// das Feld $_POST['key'] darf entweder ein gültiger identifier
// oder der leere String sein
$val = Validation::open($_POST);
$val->addSet('key',
             array('logic_or'=>[['satisfy_value'=>''],
                                ['valid_identifier']]));
```

====================================================================================

#### perform_this_foreach

```PHP
// alle Schlüssel des Arrays $_POST['approvalCondition'] sollen gültige
// identifiert sein und alle darin enthaltenen Elemente zwischen
// 0 und 100 liegen
$val = Validation::open($_POST);
$val->addSet('approvalCondition',
             array('set_default'=>array(),
                   'perform_this_foreach'=>[['key',
                                             ['valid_identifier']],
                                            ['elem',
                                             ['to_integer',
                                              'satisfy_min_numeric'=>0,
                                              'satisfy_max_numeric'=>100]]]));
```

====================================================================================

#### perform_foreach
```PHP

```

====================================================================================

#### perform_this_array

```PHP
// die Elemente des Arrays $_POST['proposal'] sollen
// gültige identifiert sein
$val = Validation::open($_POST);
$val->addSet('proposal',
             ['perform_this_array'=>[[['key_all'],
                                      ['valid_identifier']]]]);
```

====================================================================================

#### perform_array
```PHP

```

====================================================================================

#### perform_switch_case

```PHP
// es sollen die Felder $_POST['elem']['proposal'] und
// $_POST['elem']['marking'] geprüft werden
$val = Validation::open($_POST);
$val->addSet('elem',
             ['perform_switch_case'=>[['proposal',
                                       [...]],
                                      ['marking',
                                       [...]]]]);
```

====================================================================================

#### sanitize_url
```PHP

```

====================================================================================

#### sanitize
```PHP

```

====================================================================================

#### set_default
```PHP
// wenn der Wert $_POST['action'] nicht gesetzt ist
// soll er 'noAction' sein
$val = Validation::open($_POST);
$val->addSet('action',
             array('set_default'=>'noAction'));
```

====================================================================================

#### set_copy
```PHP
// erstellt das Feld $_POST['newField'] und kopiert
// dort $_POST['oldField'] hinein
$val = Validation::open($_POST);
$val->addSet('oldField',
             array('set_copy'=>'newField'));
```

====================================================================================

#### set_value
```PHP
// setzt den Wert des Feldes $_POST['field']
// auf 1234
$val = Validation::open($_POST);
$val->addSet('field',
             array('set_value'=>'1234'));
```

====================================================================================

#### set_field_value
```PHP
// setzt den Wert des Feldes $_POST['field']
// auf $_POST['otherField']
$val = Validation::open($_POST);
$val->addSet('field',
             array('set_field_value'=>'otherField'));
```

====================================================================================

#### set_error
```PHP

```

====================================================================================

#### valid_email
```PHP

```

====================================================================================

#### valid_url
```PHP

```

====================================================================================

#### valid_url_query
```PHP

```

====================================================================================

#### valid_regex
```PHP

```

====================================================================================

#### valid_hash
```PHP

```

====================================================================================

#### valid_md5
```PHP

```

====================================================================================

#### valid_sha1
```PHP

```

====================================================================================

#### valid_identifier

```PHP
// das Feld $_POST['sortId'] darf nur 0-9 und _ enthalten
$val = Validation::open($_POST);
$val->addSet('sortId',
             array('valid_identifier'));

```

====================================================================================

#### valid_user_name
```PHP

```

====================================================================================

#### valid_userName
```PHP

```

====================================================================================

#### valid_timestamp
```PHP

```

====================================================================================

#### valid_alpha
```PHP

```

====================================================================================

#### valid_alpha_space
```PHP

```

====================================================================================

#### valid_integer
```PHP

```

====================================================================================

#### valid_alpha_numeric
```PHP

```

====================================================================================

#### valid_alpha_space_numeric
```PHP

```

====================================================================================

#### valid_json
```PHP

```

====================================================================================

#### to_structure
```PHP

```

====================================================================================

#### is_float
```PHP

```

====================================================================================

#### is_boolean
```PHP

```

====================================================================================

#### is_integer
```PHP

```

====================================================================================

#### is_string
```PHP

```

====================================================================================

#### is_array

```PHP
// das Feld $_POST['rights'] muss ein Array sein
$val = Validation::open($_POST);
$val->addSet('rights',
             array('is_array'));
```

====================================================================================
