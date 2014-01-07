var Grapher = function(data, slidersdiv, placeholder, canvas, textdiv, defaultdiv) {
	this.data = data;
	this.backup = data;
	this.slidersdiv = slidersdiv;
	this.placeholder = placeholder;
	this.canvas = canvas;
	this.textdiv = textdiv;
	this.defaultdiv = defaultdiv;
	this.cx = "";
	this.sliders = [];
	this.inputs = [];
	this.checkboxes = [];
	this.vars = [];
	this.__run();
};
Grapher.prototype.__run = function() {
	switch (this.data[0]) {
		case -1:
			//Forbidden
			this.defaultdiv.empty().append(this.data[2]);
			break;
		case -2:
			//Exception
			this.defaultdiv.empty().append(this.data[2]);
			break;
		case 2:
			this.textdiv.empty().append(this.data[2]);
			this.plot2d();
			this.plot3d();
			break;
		default:
			this.textdiv.empty().append(this.data[2]);
			this.appender();
			this.plot3d();
			this.placeholder.hide();
	}
};
Grapher.prototype.hideSlides = function() {
	this.slidersdiv.hide();
};
Grapher.prototype.showSlides = function() {
	this.slidersdiv.show();
};
Grapher.prototype.plot3d = function() {
	this.vars = [];
	for (var i = 0; i < this.data[4].length; i++) {
		this.vars[i] = ("Punkt" + (i + 1));
	}
	this.x = {
		"y": {
			"vars": this.vars,
			"smps": [
				"X",
				"Y",
				"Z"
			],
			"desc": [
				"Simplex method"
			],
			"data": this.data[4]
		}
	};
	this.cx = new CanvasXpress(this.canvas.attr('id'), this.x, {
		graphType: "Scatter3D",
		useFlashIE: true,
		xAxis: [
			"X"
		],
		yAxis: [
			"Y"
		],
		zAxis: [
			"Z"
		],
		scatterType: false,
		setMinX: 0,
		setMinY: 0,
		setMinZ: 0
	});
};
Grapher.prototype.plot2d = function() {
	$.plot(this.placeholder, this.data[3]);
};
Grapher.prototype.are3CheckboxesChecked = function() {
	if ($('input[type=checkbox].slider:checked').size() === 3) {
		return true;
	} else {
		return false;
	}
};
Grapher.prototype.getDimensions = function() {
	var dimensions = [];
	for (var i = 0; i < this.checkboxes.length; i++) {
		if (this.checkboxes[i].is(':checked')) {
			dimensions.push(this.checkboxes[i].data('index'));
		}
	}
	return dimensions;
};
Grapher.prototype.getSliderValues = function() {
	var values = [];
	for (var i = 0; i < this.sliders.length; i++) {
		if (this.checkboxes[i].is(':checked') && i >= this.data[0]) {
			console.log("values[" + i + "]=" + this.sliders[i].slider("value"));
			values[i] = this.sliders[i].slider("value");
		}
	}
	return values;
};
Grapher.prototype.appender = function() {
	this.slidersdiv.empty();
	this.showSlides();
	for (var i = 0; i < this.data[0]; i++) {
		this.slidersdiv.append('<label for="checkbox_' + i + '">x<sub>' + (i + 1) + '</sub></label><input type="checkbox" class="slider" id="checkbox_' + i + '" checked/><br/>');
		this.checkboxes[i] = $('#checkbox_' + i);
		this.checkboxes[i].data('index', i);
	}
	for (var i = this.data[0]; i < this.data[1].length; i++) {
		(function(i, $this) {
			var min = $this.data[1][i] / 10;
			$this.slidersdiv.append('<label for="checkbox_' + i + '">x<sub>' + (i + 1) + '</sub></label><input type="checkbox" class="slider" id="checkbox_' + i + '"/><label for="slider_' + i + '">x<sub>' + (i + 1) + '</sub>:</label><input type="text" class="sliderinput" id="slider_' + i + '_input" value="' + min + '"/><div name="slider_' + i + '" id="slider_' + i + '"></div>');
			$this.sliders[i] = $('#slider_' + i);
			$this.inputs[i] = $('#slider_' + i + '_input');
			$this.checkboxes[i] = $('#checkbox_' + i);
			$this.checkboxes[i].data('index', i);
			$this.sliders[i].slider({
				range: "max",
				min: min,
				max: $this.data[1][i],
				value: min,
				step: min,
				stop: function(event, ui) {
					$this.inputs[i].val(ui.value);
					if ($this.checkboxes[i].is(':checked')) {
						$this.redraw();
					}
				}
			});
		})(i, this);//hack by krzysiek-94
	}

	(function(i, $this) {
		$('input[type=checkbox].slider').click(function() {
			$this.redraw();
		});
	})(i, this);//hack by krzysiek-94
};
Grapher.prototype.redraw = function() {
	if (this.are3CheckboxesChecked()) {
		//alert('redraw()');
		(function($this) {
			$.ajax({
				url: "looptest.php",
				dataType: "json",
				type: "POST",
				data: {'object': $this.data[5], "dimensions": $this.getDimensions(), "values": $this.getSliderValues()},
				success: function(data) {

				}
			});
		})(this);
	}
//	this.cx.initGraph();

};