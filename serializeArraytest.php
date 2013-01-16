<!DOCTYPE html>
<html>
<head>
  <style>
  body, select { font-size:14px; }
  form { margin:5px; }
  p { color:red; margin:5px; }
  b { color:blue; }
  </style>
  <script src="http://code.jquery.com/jquery-latest.js"></script>
</head>
<body>
  <p><b>Results:</b> <span id="results"></span></p>

  <form>
    <select name="single">
      <option>Single</option>
      <option>Single2</option>

    </select>
    <select name="multiple" multiple="multiple">
      <option selected="selected">Multiple</option>
      <option>Multiple2</option>

      <option selected="selected">Multiple3</option>
    </select><br/>
    <input type="checkbox" name="check" value="check1" id="ch1"/>

    <label for="ch1">check1</label>
    <input type="checkbox" name="check" value="check2" checked="checked" id="ch2"/>

    <label for="ch2">check2</label>
    <input type="radio" name="radio" value="radio1" checked="checked" id="r1"/>

    <label for="r1">radio1</label>
    <input type="radio" name="radio" value="radio2" id="r2"/>

    <label for="r2">radio2</label>
    <input type="submit" id="przycisk">
  </form>
<script>

    function showValues() {
      var fields = $(":input").serializeArray();
      $("#results").empty();
      jQuery.each(fields, function(i, field){
        $("#results").append(field.value + " ");
      });
    }

//    $(":checkbox, :radio").click(showValues);
//    $("select").change(showValues);
//    showValues();
$('#przycisk').click(function(){
    var fields = $(":input").serializeArray();
    alert(fields);
});
</script>

</body>
</html>