$(document).ajaxStart(function() {
//	$.blockUI({
//		message: '<img src="../images/icon_hd.png"/><br/><br/><img src="../images/loader4.gif"/>',
//		css: {
//			backgroundColor: "transparent",
//			border: "0px none"
//		}
//	});
}).ajaxStop(function() {
//	$.unblockUI();
}).ready(function() {
	$('#tabs').tabs();
	$('#logout').button({
		icons: {
			primary: "ui-icon-power"
		}
	}).toggleClass('ui-state-error');
	$.ajax({
		url: "adminsources/checkactivity.php",
		method: "POST",
		success: function(data) {
			if (data === 1) {
				$('#standby').button({
					icons: {
						primary: "ui-icon-stop"
					}
				}).button({'label': 'Wyłącz'});
			} else {
				$('#standby').button({
					icons: {
						primary: "ui-icon-play"
					}
				}).button({'label': 'Włącz'}).toggleClass('ui-state-error');
			}
		},
		error: function(data) {
			alert('Błąd:' + data);
		}
	});

	$('#standby').click(function() {
		$.ajax({
			url: "adminsources/toggleActivity.php",
			dataType: "json",
			method: "POST",
			success: function(data) {
				if (data.active === 'false') {
					$('#standby').button({'label': 'Wyłącz', 'icons': {'primary': 'ui-icon-stop'}}).toggleClass('ui-state-error');
				} else {
					$('#standby').button({'label': 'Włącz', 'icons': {'primary': 'ui-icon-play'}}).toggleClass('ui-state-error');
				}
				$('#standby').button("refresh");
			}
		});
	});
});