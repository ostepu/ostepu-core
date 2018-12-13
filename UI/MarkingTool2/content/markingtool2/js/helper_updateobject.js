//require helper_updateproperty.js
//require helper_events.js

var Helper = Helper || {};
//Ein Objekt welches selbst verfolgen kann, ob sich sein Zustand ändert.
//data: Objekt - Ein Objekt welches durch Schlüssel- und Wertepaare alle Daten enthält. Alle Daten aus 
//               diesem Objekt werden auch hier wieder verfügbar gemacht. Obwohl dieses Objekt als Daten-
//               speicher dient, sollten alle Zugriffe nur über das UpdateObject erfolgen
//path: Objekt - Eindeutige Informationen, die das Objekt identifizieren
Helper.UpdateObject = function (data, path) {
	var propertys = {};
	var thisref = this;
	var updadedEvent = new Helper.Event();
	var raiseHandler = function(sender) {
		thisref.UpdatedEvent.invoke(thisref);
	};
	for (var key in data) {
		if (data.hasOwnProperty(key)) {
			propertys[key] = new Helper.UpdateProperty(data, key, path);
			Object.defineProperty(this, key, {
				get: (function(data, key) { return function() { return data[key]; }; })(data, key),
				set: (function(data, key, propertys) { return function(value) { propertys[key].changeValue(value); }; })(data, key, propertys)
			});
			propertys[key].UpdatedEvent.add(raiseHandler);
		}
		else this[key] = data[key];
	}
	//Überprüft ob eine Property schon exisitiert und fügt sie gegebenfalls hinzu.
	//key:          String - Der Name der Property
	//defaultValue: Wert   - Der Wert den die Property annehmen soll, falls sie noch nicht existierte.
	this.checkProperty = function(key, defaultValue) {
		if (propertys[key] == undefined) {
			propertys[key] = new Helper.UpdateProperty(data, key, path);
			propertys[key].changeValue(defaultValue);
			propertys[key].setDefaultValue(defaultValue);
			Object.defineProperty(this, key, {
				get: (function(data, key) { return function() { return data[key]; }; })(data, key),
				set: (function(data, key, propertys) { return function(value) { propertys[key].changeValue(value); }; })(data, key, propertys)
			});
			propertys[key].UpdatedEvent.add(raiseHandler);
		}
	};
	//Objekt - Die eindeitige Information, die dieses Objekt identifiziert.
	this.path = path;
	//[EVENT] Dieses Event wird ausgelöst, wenn sich der Wert einer Variable ändert.
	//[function(sender)]
	//    sender: Objekt - dieses Objekt
	Object.defineProperty(this, "UpdatedEvent", {get: function(){ return updadedEvent; } });
	//Setzt alle Propertys auf ihren Standartwert
	this.resetValues = function() {
		for (var key in propertys)
			if (propertys.hasOwnProperty(key))
				propertys[key].resetValue();
		thisref.UpdatedEvent.invoke(thisref);
	};
	//Überprüft ob die aktuellen Werte aller Propertys mit dem Standartwerten übereinstimmen. Es erfolgt keine
	//Typprüfung.
	//return: Boolean - Ergebnis der Überprüfung
	this.isValueChanged = function() {
		for (var key in propertys)
			if (propertys.hasOwnProperty(key))
				if (propertys[key].isValueChanged())
					return true;
		return false;
	};
	//Setzt den Standartwert für eine Property
	//key:   String - Der Name der Property
	//value: Wert   - Der neue Standartwert der Property
	this.setDefaultValue = function(key, value) {
		propertys[key].setDefaultValue(value);
		thisref.UpdatedEvent.invoke(thisref);
	};
	//Setzt die aktuellen Werte aller Propertys als Standartwerte
	this.setAllValuesAsDefault = function() {
		for (var key in propertys)
			if (propertys.hasOwnProperty(key))
				propertys[key].setDefaultValue(data[key]);
		thisref.UpdatedEvent.invoke(thisref);
	};
	//Ruft die Liste alle Propertys zu diesen Objekt ab
	//return: Objekt - Eine Liste aus Schlüssel-Werte-Paaren mit UpdateProperty als Werten.
	this.getPropertys = function() {
		return propertys;
	};
};
