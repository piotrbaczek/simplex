var Grapher = function(data, slidersdiv, placeholder, canvas, textdiv, defaultdiv) {
	this.redrawURL = 'sources/redraw.php';
	this.x = {};
	this.cx = "";
	this.sliders = [];
	this.inputs = [];
	this.checkboxes = [];
	this.vars = [];
	this.variables = ["x1", "x2", "x3"];
};
Grapher.prototype.__run = function(data, slidersdiv, placeholder, canvas, textdiv, defaultdiv) {
	this.data = data;
	this.backup = data;
	this.slidersdiv = slidersdiv;
	this.placeholder = placeholder;
	this.canvas = canvas;
	this.textdiv = textdiv;
	this.defaultdiv = defaultdiv;
	this.hideSlides();
	if (this.data.length === 0 || this.data[0].length === 0) {
		alert('Otrzymano pustą tablicę. Error!');
	} else {
		switch (this.data[0]) {
			case -1:
				//Forbidden
				this.defaultdiv.empty().append(this.data[3]);
				break;
			case -2:
				//Exception
				this.defaultdiv.empty().append(this.data[3]);
				break;
			case 0:
			case 1:
			case 2:
				this.textdiv.empty().append(this.data[3]);
				this.plot2d();
				this.plot3d();
				break;
			case 3:
				this.textdiv.empty().append(this.data[3]);
				this.plot3d();
				this.placeholder.hide();
				break;
			default:
				this.showSlides();
				this.textdiv.empty().append(this.data[3]);
				this.appender();
				this.plot3d();
				this.placeholder.hide();
		}
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
	for (var i = 0; i < this.data[5].length; i++) {
		this.vars[i] = ("Punkt");
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
			"data": this.data[5]
		}
	};
};
Grapher.prototype.setPlot3DData = function(data) {
	this.data[5] = data;
};
Grapher.prototype.plot3d = function() {
	if (this.data[4] === undefined || this.data[5] === undefined) {
		alert('Błąd otrzymanych danych.');
	} else {
		if (this.cx instanceof CanvasXpress) {
			this.setVars();
			this.setX();
			this.cx.xAxisTitle = "x1";
			this.cx.yAxisTitle = "x2";
			this.cx.zAxisTitle = "x3";
			this.cx.updateData(this.x);
		} else {
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
		}
	}
};

Grapher.prototype.plot2d = function() {
	if (this.data[4] !== undefined && this.data[4].length > 0) {
		this.placeholder.show();
		$.plot(this.placeholder, this.data[4]);
	} else {
		this.placeholder.hide();
	}
};
Grapher.prototype.are3CheckboxesChecked = function() {
	if ($('input[type=checkbox].slider:checked').size() === 3) {
		return true;
	} else {
		return false;
	}
};
Grapher.prototype.getDimensions = function() {
	var dimensions = new Array();
	for (var i = 0; i < this.checkboxes.length; i++) {
		if (this.checkboxes[i] === undefined) {
			continue;
		}
		if (this.checkboxes[i].is(':checked')) {
			dimensions.push(this.checkboxes[i].data('index'));
		}
	}
	return dimensions;
};
Grapher.prototype.getSliderValues = function() {
	var values = new Array();
	for (var i = 0; i < this.sliders.length; i++) {
		if (this.checkboxes[i] === undefined) {
			continue;
		}
		if (!this.checkboxes[i].is(':checked')) {
			values[i] = this.sliders[i].slider("value");
		}
	}
	return values;
};
Grapher.prototype.appender = function() {
	this.slidersdiv.empty();
	this.showSlides();
	for (var i = 0; i < this.data[0]; i++) {
		if (this.data[1][i] === 0) {
			this.checkboxes[i] = undefined;
		} else {
			(function(i, $this) {
				var string = '<label for="checkbox_' + i + '">x<sub>' + (i + 1) + '</sub></label><input type="checkbox" class="slider" id="checkbox_' + i + '" ';
				if (i < 3) {
					string += 'checked';
				}
				string += '/><label for="slider_' + i + '">x<sub>' + (i + 1) + '</sub>:</label><input type="text" class="sliderinput" id="slider_' + i + '_input" value="' + ($this.data[2][i]) + '"/><div name="slider_' + i + '" id="slider_' + i + '"></div>';
				$this.slidersdiv.append(string);
				$this.sliders[i] = $('#slider_' + i);
				$this.inputs[i] = $('#slider_' + i + '_input');
				$this.checkboxes[i] = $('#checkbox_' + i);
				$this.checkboxes[i].data('index', i);
				$this.sliders[i].slider({
					range: "max",
					min: $this.data[2][i],
					max: $this.data[1][i],
					value: $this.data[2][i],
					step: $this.data[1][i] / 10,
					stop: function(event, ui) {
						$this.inputs[i].val(ui.value);
						if (!$this.checkboxes[i].is(':checked')) {
							$this.redraw();
						}
					}
				});
			})(i, this);//hack by krzysiek-94	
		}
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
				data: {'object': $this.data[6], "dimensions": $this.getDimensions(), "values": $this.getSliderValues()},
				success: function(ajaxData) {
					if (Array.isArray(ajaxData) && ajaxData.length > 0) {
						$this.setPlot3DData(ajaxData);
						$this.setVars();
						$this.setX();
						$this.cx.xAxisTitle = "x" + (1 + $this.getDimensions()[0]);
						$this.cx.yAxisTitle = "x" + (1 + $this.getDimensions()[1]);
						$this.cx.zAxisTitle = "x" + (1 + $this.getDimensions()[2]);
						$this.cx.updateData($this.x);
					} else {
						alert('Ten zbiór wartości jest pusty.');
					}
				},
				error: function(ajaxData) {
					$.unblockUI();
					alert(JSON.stringify(ajaxData));
				}
			});
		})(this);
	}
};