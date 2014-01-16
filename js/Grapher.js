var Grapher = function(data, slidersdiv, placeholder, canvas, textdiv, defaultdiv) {
	this.data = data;
	this.redrawURL = 'sources/redraw.php';
	this.backup = data;
	this.slidersdiv = slidersdiv;
	this.placeholder = placeholder;
	this.canvas = canvas;
	this.textdiv = textdiv;
	this.defaultdiv = defaultdiv;
	this.x = {};
	this.cx = "";
	this.sliders = [];
	this.inputs = [];
	this.checkboxes = [];
	this.vars = [];
	this.variables = ["x1", "x2", "x3"];
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
Grapher.prototype.setVars = function() {
	this.vars = [];
	for (var i = 0; i < this.data[4].length; i++) {
		this.vars[i] = ("Punkt" + (i + 1));
	}
};
Grapher.prototype.setX = function() {
	this.x = {};
	this.x = {
		"y": {
			"vars": this.vars,
			"smps": [
				this.variables[0],
				this.variables[1],
				this.variables[2]
			],
			"desc": [
				"Simplex method"
			],
			"data": this.data[4]
		}
	};
};
Grapher.prototype.plot3d = function() {
	this.setVars();
	this.setX();
	this.cx = new CanvasXpress(this.canvas.attr('id'), this.x, {
		graphType: "Scatter3D",
		useFlashIE: true,
		xAxis: [
			this.variables[0]
		],
		yAxis: [
			this.variables[1]
		],
		zAxis: [
			this.variables[2]
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
			values[i] = this.sliders[i].slider("value");
		}
	}
	return values;
};
Grapher.prototype.appender = function() {
	console.log(this.data[1].toString());
	this.slidersdiv.empty();
	this.showSlides();
	for (var i = 0; i < this.data[1].length; i++) {
		(function(i, $this) {
			var min = $this.data[1][i] / 10;
			var string = '<label for="checkbox_' + i + '">x<sub>' + (i + 1) + '</sub></label><input type="checkbox" class="slider" id="checkbox_' + i + '" ';
			if (i < 3) {
				string += 'checked';
			}
			string += '/><label for="slider_' + i + '">x<sub>' + (i + 1) + '</sub>:</label><input type="text" class="sliderinput" id="slider_' + i + '_input" value="' + min + '"/><div name="slider_' + i + '" id="slider_' + i + '"></div>';
			$this.slidersdiv.append(string);
			$this.sliders[i] = $('#slider_' + i);
			$this.inputs[i] = $('#slider_' + i + '_input');
			$this.checkboxes[i] = $('#checkbox_' + i);
			$this.checkboxes[i].data('index', i);
			$this.sliders[i].slider({
				range: "max",
				min: 0,
				max: $this.data[1][i],
				value: $this.data[1][i],
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

	(function($this) {
		$('input[type=checkbox].slider').click(function() {
			$this.redraw();
		});
	})(this);
};
Grapher.prototype.redraw = function() {
	if (this.are3CheckboxesChecked()) {
		(function($this) {
			$.ajax({
				url: $this.redrawURL,
				dataType: "json",
				type: "POST",
				data: {'object': $this.data[5], "dimensions": $this.getDimensions(), "values": $this.getSliderValues()},
				success: function(data) {
					if (data.length !== 0) {
						$this.data[4] = data;
						$this.variables = ["x" + (1 + $this.getDimensions()[0]), "x" + (1 + $this.getDimensions()[1]), "x" + (1 + $this.getDimensions()[2])];
						$this.plot3d();
					} else {
						alert('Ten zbiór wartości jest pusty.');
					}
				}
			});
		})(this);
	}
//	this.cx.initGraph();
};