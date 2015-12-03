var accordion = function() {
	var tm = sp = 10;
	function slider(t, c, k) { 
		var self = this;
		if (!(self instanceof slider)) {
			return new slider(t, c, k);
		} 
		self.nm = t; 
		self.arr = []; 
		self.init(t, c, k);
	}
	slider.prototype.pro = function(d) {
		for (var i = 0; i < this.l; i++) {
			var h = this.arr[i], s = h.nextSibling; s = s.nodeType != 1 ? s.nextSibling : s;
			clearInterval(s.tm);
			if (h == d && s.style.display == 'none') {
				s.style.display = '';
				su(s, 1);
				h.className = this.sl
			} else if (s.style.display == '') {
				su(s, -1);
				h.className = '';
			} 
		}
	}
	slider.prototype.init = function(t, c, k) {
		var a, h, s, l, i, self = this; a = T$(t); self.sl = k ? k : '';
		h = T$$('dt', a); s = T$$('dd', a); self.l = h.length;
		for (i = 0; i < self.l; i++) {
			var d = h[i]; self.arr[i] = d;
			d.onclick = function() { self.pro(this); }
			if (c == i) { d.className = self.sl; } 
		}
		l = s.length;
		for (i = 0; i < l; i++) {
			var d = s[i]; d.mh = d.offsetHeight;
			if (c != i) {
				d.style.height = 0;
				d.style.display = 'none';
			} 
		}
	}
	
	function T$(o) { return document.getElementById(o); }
	function T$$(t, o) { return (o || document).getElementsByTagName(t); }
	function su(c, f) { c.tm = setInterval(function() { sl(c, f)}, tm); }
	function sl(c, f) {
		var h = c.offsetHeight, m = c.mh, d = f == 1 ? m - h : h;
		c.style.height = h + (Math.ceil(d / sp) * f) +  'px';
		c.style.opacity = h / m; c.style.filter = 'alpha(opacity=' + h * 100 / m + ')';
		if (f == 1 && h >= m) { clearInterval(c.tm)}
		else if (f != 1 && h == 1) {
			c.style.display = 'none';
			clearInterval(c.tm);
		} 
	}
	return {slider: slider}
}();