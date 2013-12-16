var Grapher = function(data, slidersdiv, placeholder, canvas, textdiv) {
	this.data = data;
	this.backup = data;
	this.slidersdiv = slidersdiv;
	this.placeholder = placeholder;
	this.canvas = canvas;
	this.textdiv = textdiv;
	this.cx = "";
	this.sliders = [];
	this.inputs = [];
	this.vars = [];
	this.__run();
};
Grapher.prototype.__run = function() {
	switch (this.data[0]) {
		case -1:
			break;
		case -2:
			break;
		case 2:
			this.appender();
			this.plot2d();
			this.plot3d();
			break;
		default:
			this.appender();
			this.plot3d();
			this.placeholder.hide();

	}
	this.textdiv.empty().append(this.data[2]);
};
Grapher.prototype.hideSlides = function() {
	this.slidersdiv.hide();
};
Grapher.prototype.appender = function() {
	for (var i = 0; i < this.data[1].length; i++) {
		(function(i, $this) {
			$this.slidersdiv.append('<label for="slider_' + i + '">x<sub>' + (i + 1) + '</sub>:</label><input type="text" class="sliderinput" id="slider_' + i + '_input" value="' + $this.data[1][i] + '"/><div name="slider_' + i + '" id="slider_' + i + '"></div>');
			$this.sliders[i] = $('#slider_' + i);
			$this.inputs[i] = $('#slider_' + i + '_input');
			$this.sliders[i].slider({
				range: "max",
				min: 0,
				max: 10 * $this.data[1][i],
				value: $this.data[1][i],
				step: 1,
				stop: function(event, ui) {
					$this.inputs[i].val(ui.value);
					$this.redraw();
				}
			});
		})(i, this);//hack by krzysiek-94
	}
};
Grapher.prototype.plot3d = function() {
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
Grapher.prototype.redraw = function() {
	if (this.data[0] === 2) {
		for (var i = 0; i < this.data[3].length; i++) {
			if (this.data[3][i]["label"].substring(0, 1) === "A") {
				this.data[3].splice(i, 1);
				continue;
			}
		}
		for (var i = 0; i < this.data[3].length; i++) {
			if (this.data[3][i]["label"] === "gradient") {
				this.data[3][i]["data"][1][1] = this.data[3][i]["data"][1][0] * (parseInt(this.sliders[1].slider("value")) / parseInt(this.sliders[0].slider("value")));
			}
		}
		this.plot2d();
	}
	for (var i = 0; i < this.data[4].length; i++) {
		this.data[4][i][2] = (parseInt(this.sliders[0].slider("value")) * this.data[4][i][0]) + (parseInt(this.sliders[1].slider("value")) * this.data[4][i][1]);
	}
	this.cx.initGraph();
};