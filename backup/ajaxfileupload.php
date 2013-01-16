<!DOCTYPE html>
<html>
    <head>
        <title>Ajax File Uploader Plugin For Jquery</title>
        <script src="js/jquery.js"></script>
        <script src="js/ajaxfileupload.js"></script>
        <script>
            $(document).ready(function(){
                $('#buttonUpload').click(function(){
                    $.ajaxFileUpload({
                        url:'doajaxfileupload.php',
                        secureuri:false,
                        fileElementId:'fileToUpload',
                        dataType: 'json',
                        data:{name:'logan', id:'id'},
                        success: function (data, status){
                            if(typeof(data.error) != 'undefined'){
                                if(data.error != ''){
                                    alert(data.error);
                                }else{
                                    alert(data.msg);
                                }
                            }
                        },
                        error: function (data, status, e){
                            alert(e);
                        }
                    });
                    return false;    
                });
            });
        </script>	
    </head>
    <body>
        <form name="form" action="" method="POST" enctype="multipart/form-data">
            <input id="fileToUpload" type="file" size="45" name="fileToUpload">
            <button class="button" id="buttonUpload">Upload</button>
        </form>    	
    </body>
</html>