var Helper = Helper || {};
//=== Bibliothek um die Updates nachzuvollziehen
Helper.UpdateIndicator = new function() {
	var thisref = this;
	var display = undefined;
	$(function(){ display = $(".loading-box"); });
	//Zeigt die Ladeanzeige an
	this.ShowBox = function() {
		if (display != undefined) display.show();
	};
	//Versteckt die Ladeanzeige
	this.HideBox = function() {
		if (display != undefined) display.hide();
	};
	//Setzt die Beschreibung der Ladeanzeige
	//text: String - Der Text der gesetzt werden soll.
	this.SetText = function(text) {
		if (display != undefined) display.find(".loading-description").html(text);
	};
};