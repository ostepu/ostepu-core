Die CGate stellt zugangskontrollierte Zugänge zu internen Komponenten bereit.

## Eingänge
---------------

||call|
| :----------- |:-----: |
|Beschreibung| über diese Anfrage werden alle Interface-Anfragen behandelt|
|Befehl| PUT,GET,HEAD,OPTIONS,POST,DELETE<br>/interface/:profile/:component/:path+|
|Eingabetyp| binary|
|Ausgabetyp| binary|


## Ausgänge
---------------

||getComponentProfileWithAuthLogin|
| :----------- |:-----: |
|Ziel| DBGate|
|Befehl| GET<br>/gateprofile/gateprofile/:profName/auth/:authType/component/:component/login/:login|
|Beschreibung| für den Befehl getComponentProfileWithAuthLogin|

||getComponentProfileWithAuth|
| :----------- |:-----: |
|Ziel| DBGate|
|Befehl| GET<br>/gateprofile/gateprofile/:profName/auth/:authType/component/:component|
|Beschreibung| für den Befehl getComponentProfileWithAuth|


## Anbindungen
---------------

|Ausgang|request|
| :----------- |:-----: |
|Ziel| CLocalObjectRequest|
|Beschreibung| damit CGate als lokales Objekt aufgerufen werden kann|


Stand 30.06.2017
