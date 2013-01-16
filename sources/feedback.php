<script>
    $(document).ready(function(){
        $('#feedbackform').validate({
            rules:{
                opinion_textarea:{
                    required:true,
                    minlength:10,
                    maxlength:200
                }
            },
            messages:{
                opinion_textarea:{
                    required:'Proszę podać parę słów komentarza o aplikacji',
                    minlength:'Twój komentarz nie może być krótszy niż 10 znaków',
                    maxlength: 'Twój komentarz nie może być dłuższy niż 200 znaków'
                }
            },
            invalidHandler:function(e,validator){
                $('div.error').show();
            },
            submitHandler:function(){
                $('div.error').hide();
            }
        });
        $("#slider-range-max").slider({
            range: "min",
            min: 1,
            max: 5,
            value: 3,
            slide: function(event, ui){
                $("#amount").val(ui.value);
            },
            animate:"true"
        });
        $("#amount").val($("#slider-range-max").slider("value"));
        $("#sendopinion_button").button({
            icons:{
                primary:"ui-icon-mail-closed"
            }
        }).click(function(){
            if($('#feedbackform').valid()){
                $.ajax({
                    type:"POST",
                    dataType:"json",
                    url:"sources/feedbackresponse.php",
                    data: {"range":$("#amount").val(),"version":$("#bver").val(),"opinion":$("#opinion_textarea").val()},
                    error:function(){
                        alert("Serwer chwiliowo niedost\u0119pny");
                    },
                    success:function(data){
                        if(data.result=='1'){
                            $("#feedback1").hide();
                            $("#feedback2").show();
                        }else{
                            alert('Wszystkie pola musz\u0105 być wypełnione.');
                        }
                    }
                });
            }
            return false;
        });
    });
</script>
<?php
include '../classes/verDetect.class.php';
$detect = new verDetect();
echo '<div id="feedback1">';
echo '<form id="feedbackform">';
echo 'Ocena:<div id="slider-range-max"></div><br/><br/><br/>';
echo '<label for="amount">Twoja ocena:</label>
	<input type="text" id="amount" maxlength="1" disabled="true"/><br/>';
echo 'Wersja przeglądarki:<input type="text" disabled="true" id="bver" value="' . $detect->returnDetect() . '"/><br/>';
echo '<label for="opinion_textarea">Twoja Opinia:</label><br/><textarea id="opinion_textarea" name="opinion_textarea" rows="10"></textarea><br/>';
echo '<button id="sendopinion_button">Wyślij</button>';
echo '</form>';
echo '</div>';
echo '<div id="feedback2" class="hidden">Dziękuję za wyrażenie opinii.</div>';
?>