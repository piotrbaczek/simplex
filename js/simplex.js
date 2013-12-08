$(document).ajaxStart(function() {
	$.blockUI({
		message: '<img src="images/icon_hd.png"/><br/><br/><img src="images/loader4.gif"/>',
		css: {
			backgroundColor: "transparent",
			border: "0px none"
		}
	});
}).ajaxStop(function() {
	$.unblockUI();
}).ajaxError(function() {
	$.unblockUI();
}).ready(function() {
	$.validator.addMethod("regex", function(value, element) {
		return this.optional(element) || /(([0-9]*x[0-9]*[+]?)+|([0-9]\/[1-9][0-9]*x[0-9]*)[+]?)(<=|>=|=)[0-9]+/g.test(value);
	}, "Wprowadzona tre\u015bć zadania jest nieprawidłowa - Tylko forma Axa+BxB+...<=C (>=C lub =C)jest dopuszczalna.");
	$.validator.addMethod("regex2", function(value, element) {
		return this.optional(element) || /(([0-9]*x[0-9]*[+]?)+|([0-9]\/[1-9][0-9]*x[0-9]*)[+]?){2,}/g.test(value);
	}, "Wprowadzona funkcja celu jest nieprawid\u0142owa - Tylko forma Axa+Bxb+... jest dopuszczalna.");
	$('#solvethisform').validate({
		rules: {
			textarea: {
				required: true,
				regex: true
			},
			targetfunction: {
				required: true,
				regex2: true
			}
		},
		messages: {
			textarea: {
				required: "Musisz wpisa\u0107 treść zadania."
			},
			targetfunction: {
				required: "Musisz wpisa\u0107 funkcję celu"
			}
		}
	});
	$('#solvethis').button().click(function() {
		if ($('#solvethisform').valid()) {
			$('#rightdiv').slideUp();
			$('#header_leftlogo').hide();
			s = $('#solvethisform input,textarea,select').serialize();
			$.ajax({
				type: "POST",
				url: "sources/receiver.php",
				data: s,
				dataType: "json",
				success: function(data) {
					if (data[0] === 2) {
						//placeholder
						$.plot($("#placeholder1"), data[1]);
					} else if (data[0] === 3) {
						//canvas
						var vars = [];
						a = data[2];
						for (var i = 0; i < a.length; i++) {
							vars.push("Punkt" + (i + 1));
						}
						var x = {
							"y": {
								"vars": vars,
								"smps": [
									"X",
									"Y",
									"Z"
								],
								"desc": [
									"Simplex method"
								],
								"data": a
							}
						};
					} else if (data[0] === -1) {
						//strona wyłączona
					} else {
						//error
					}
					$('#resultdiv2').empty().append(data[2]);
					$('table.result td[data-dane]').tooltip({
						delay: 0,
						showURL: false,
						fixPNG: true,
						track: true,
						bodyHandler: function() {
							attr = $(this).attr('data-dane');
							if (typeof attr !== 'undefined' && attr !== false) {
								temp = attr.split(",");
								return $("<img/>").attr("src", './sources/Picture.php?a=' + temp[0] + '&b=' + temp[1] + '&c=' + temp[2] + '&d=' + temp[3] + '&e=' + temp[4]).css({
									'background-color': 'transparent',
									'text-align': 'center'
								});
							}
						}
					});
					$('#resultdiv').slideDown('slow');
				}
			});
		}
		return false;
	});
	$('#backbutton').button({
		icons: {
			primary: "ui-icon-carat-1-w"
		}
	}).click(function() {
		$('#resultdiv').slideUp('fast');
		$('#header_leftlogo').show();
		$('#rightdiv').slideDown('slow');
	});
	$('form[name=form]').validate({
		rules: {
			fileToUpload: {
				required: true,
				accept: "csv"
			}
		},
		messages: {
			fileToUpload: {
				required: "Nie wskaza\u0142eś pliku. Załaduj plik a potem kliknij \"Wczytaj\".",
				accept: "Tylko pliki *.csv s\u0105 obsługiwane"
			}
		}
	});
	$('#loadfile').button({
		icons: {
			secondary: "ui-icon-transferthick-e-w"
		}
	}).click(function() {
//		if ($('form[name=form]').valid()) {
//			$.ajaxFileUpload({
//				url: 'sources/doajaxfileupload.php',
//				secureuri: false,
//				fileElementId: 'fileToUpload',
//				dataType: 'json',
//				data: {
//					name: 'logan',
//					id: 'id'
//				},
//				success: function(data, status) {
//					if (typeof (data.error) !== 'undefined') {
//						if (data.error !== '') {
//							alert(data.error);
//						} else {
//							$('#result2').load("sources/fileProcesser.php", {
//								'filename': data.msg
//							}, function() {
//								$('#fileloader').slideUp('fast');
//								$('#result').slideDown('slow');
//								$('table.result:gt(0) td').tooltip({
//									delay: 0,
//									showURL: false,
//									fixPNG: true,
//									track: true,
//									bodyHandler: function() {
//										ss = $(this).attr('data-dane');
//										temp = ss.split(",");
//										return $("<img/>").attr("src", './sources/Picture.php?a=' + temp[0] + '&b=' + temp[1] + '&c=' + temp[2] + '&d=' + temp[3] + '&e=' + temp[4]).css({
//											'background-color': 'transparent',
//											'text-align': 'center'
//										});
//									}
//
//								});
//							});
//						}
//					}
//
//				},
//				error: function(data, status, e) {
//					alert(e);
//				}
//			});
//		}
//		return false;
	});
	$('input.fake').click(function() {
		$('input[name=fileToUpload]').click();
	});
	$('input[name=fileToUpload]').bind('change', function() {
		$('input.fake').val($(this).val());
	});
	$('#firstButton').button({
		icons: {
			primary: "ui-icon-plusthick"
		}
	}).click(function() {
		var title = "Simplex";
		var href = window.location.href;
		try {
			if (window.sidebar && window.sidebar.addPanel) { // Mozilla Firefox Bookmark
				window.sidebar.addPanel(title, href, '');
			} else if (window.external && ('AddFavorite' in window.external)) { // IE Favorite
				window.external.AddFavorite(href, title);
			} else if (window.opera && window.print) { // Opera Hotlist
				this.title = title;
				return true;
			} else { // webkit - safari/chrome
				alert('Naciśnij ' + (navigator.userAgent.toLowerCase().indexOf('mac') !== -1 ? 'Command/Cmd' : 'CTRL') + ' + D aby dodać zakładkę.');
			}
		} catch (e) {
			console.log(e);
		}
	}).delay(1000).effect("bounce", 500);
});