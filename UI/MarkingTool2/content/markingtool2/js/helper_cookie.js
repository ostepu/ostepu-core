var Helper = Helper || {};
//Ein Cookiehandler, welcher Cookies verwalten kann.
Helper.Cookie = function () {
	//Ruft alle Cookies ab, die im Browser gespeichert worden
	this.GetAllCookies = function() {
		var c = decodeURIComponent(document.cookie);
		c = c.split(';');
		var l = {};
		for (var i = 0; i<c.length; ++i) {
			var p = c[i].split('=');
			for (var j = 0; j<p.length; ++j)
				p[j] = decodeURIComponent(p[j].trim());
			if (p.length == 1)
				l[""] = JSON.parse(p[0]);
			else l[p[0]] = JSON.parse(p[1]);
		}
		return l;
	};
	//Ruft einen einzelnen Cookie ab.
	this.GetCookie = function(name) {
		var c = decodeURIComponent(document.cookie);
		name = encodeURIComponent(name);
		var result = c.match(new RegExp(name + "=([^;]+)"));
		result && (result = JSON.parse(decodeURIComponent(result[1])));
		return result;
	};
	//Eine Zeitkonstante für einen Tag
	this.OneDay = 86400000;
	//Fügt einen neuen Cookie hinzu
	this.SetCookie = function(name, value, exp) {
		var d = new Date();
		d.setTime(d.getTime() + exp);
		document.cookie = 
			encodeURIComponent(name) + "=" +
			encodeURIComponent(JSON.stringify(value)) + 
			";expires=" + d.toUTCString() +
			";path=/";
	};
	//löscht ein Cookie
	this.DeleteCookie = function(name) {
		var d = new Date();
		d.setTime(0);
		document.cookie =
			encodeURIComponent(name) + "=;expires=" +
			d.toUTCString() + ";path=/";
	};
};