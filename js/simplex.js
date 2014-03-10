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
    var grapher = new Grapher();
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
            s = $('#solvethisform input,textarea,select').serialize();
            $.ajax({
                type: "POST",
                url: "sources/receiver.php",
                data: s,
                dataType: "json",
                success: function(data) {
                    grapher.__run(data, $('#sliders'), $('#placeholder1'), $('#canvas1'), $('#resultdiv2'), $('#defaultdiv'));
                    $('table.result td[data-dane]').tooltip({
                        delay: 0,
                        showURL: false,
                        fixPNG: true,
                        track: true,
                        bodyHandler: function() {
                            var attr = $(this).attr('data-dane');
                            if (typeof attr !== 'undefined' && attr !== false) {
                                var temp = attr.split(",");
                                return $("<img/>").attr("src", './sources/Picture.php?a=' + temp[0] + '&b=' + temp[1] + '&c=' + temp[2] + '&d=' + temp[3] + '&e=' + temp[4]).css({
                                    'background-color': 'transparent',
                                    'text-align': 'center'
                                });
                            }
                        }
                    });
                    $('#rightdiv').slideUp();
                    $('#header_leftlogo').hide();
                    $('#resultdiv').slideDown('slow');
                    $('#resultdiv2').slideDown('slow');
                },
                error: function(ajaxData) {
                    $.unblockUI();
                    alert(JSON.stringify(ajaxData));
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
    $('#generatecsv').button({
        icons: {
            primary: "ui-icon-arrowstop-1-s"
        }
    }).click(function() {
        window.open('./sources/generateCSV.php?' + encodeURI(s));
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
        if ($('form[name=form]').valid()) {
            $.ajaxFileUpload({
                url: 'sources/doajaxfileupload.php',
                secureuri: false,
                fileElementId: 'fileToUpload',
                dataType: 'json',
                data: {
                    name: 'logan',
                    id: 'id'
                },
                success: function(data1) {
                    if (typeof (data1.error) !== 'undefined') {
                        if (data1.error !== '') {
                            alert(data1.error);
                        } else {
                            $('#rightdiv').slideUp();
                            $('#header_leftlogo').hide();
                            $.ajax({
                                type: "POST",
                                url: "sources/fileProcesser.php",
                                data: {'filename': data1.msg},
                                dataType: "json",
                                success: function(data) {
                                    grapher.__run(data, $('#sliders'), $('#placeholder1'), $('#canvas1'), $('#resultdiv2'), $('#defaultdiv'));
                                    $('table.result td[data-dane]').tooltip({
                                        delay: 0,
                                        showURL: false,
                                        fixPNG: true,
                                        track: true,
                                        bodyHandler: function() {
                                            var attr = $(this).attr('data-dane');
                                            if (typeof attr !== 'undefined' && attr !== false) {
                                                var temp = attr.split(",");
                                                return $("<img/>").attr("src", './sources/Picture.php?a=' + temp[0] + '&b=' + temp[1] + '&c=' + temp[2] + '&d=' + temp[3] + '&e=' + temp[4]).css({
                                                    'background-color': 'transparent',
                                                    'text-align': 'center'
                                                });
                                            }
                                        }
                                    });
                                    if (data[7] !== undefined && data[7].length > 0) {
                                        if (data[7][0] === true) {
                                            $('#funct option:eq(0)').attr('selected',true);
                                        } else {
                                            $('#funct option:eq(1)').attr('selected',true);
                                        }
                                        if (data[7][1] === 'false') {
                                            $('#gomorryf option:eq(0)').attr('selected',true);
                                        } else {
                                            $('#gomorryf option:eq(1)').attr('selected',true);
                                        }
                                        $('#targetfunction').empty().val(data[7][2]);
                                        $('#textarea').empty().html(data[7][3]);
                                    }
                                    $('#resultdiv').slideDown('slow');
                                    $('#resultdiv2').slideDown('slow');
                                }
                            });
                        }
                    }

                },
                error: function(data) {
                    alert(JSON.stringify(data));
                }
            });
        }
        return false;
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