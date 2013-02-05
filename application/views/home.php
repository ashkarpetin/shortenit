<!DOCTYPE html>
<html lang="en">
    <head>
        <title> Shorten It! </title>
        <link rel="stylesheet" href="http://twitter.github.com/bootstrap/assets/css/bootstrap.css">
        <link rel="stylesheet" href="http://twitter.github.com/bootstrap/assets/css/bootstrap-responsive.css">
        <script type="text/javascript" src="http://code.jquery.com/jquery.min.js"></script>
        <script src="http://twitter.github.com/bootstrap/assets/js/bootstrap-modal.js"></script>
        <script src="http://twitter.github.com/bootstrap/assets/js/bootstrap-transition.js"></script>
        <style>
            h1 
            {
                margin-bottom: 9px;
                color:#000;
                font-size: 81px;
                letter-spacing: -1px;
                line-height: 1;
            }

            a:link {color:#3A87AD;}     
            a:visited {color:#3A87AD;}  
            a:hover {color:#3A87AD;}  
            a:active {color:#3A87AD;} 

            .label 
            {
                padding: 3px 6px 5px;
                font-size: 12px;
            }        
        </style>
    </head>
    <body>
        <div class="container">
            <div class="row" style="padding-top:20px; padding-bottom:8px;">
            <div class="span12">
                <a href="index.php"><h1 class="jumbotron">Shorten It.<span id="beta" class="label label-info" style="position:absolute; top:32px;"> alpha </span></h1></a>
            </div>
            <div class="span6 offset3">
                <?php if (!isset($error)): ?>                
                <form>
                    <input class="span6 search-query" id="url" name="url" type="text">
                    <div style="padding: 16px; text-align: center;">
                        <a id="shorten" class="btn">Shorten It!</a> 
                    </div>
                    <div id="error" class="alert alert-error" style="display:none;">
                    </div>
                    <div id="modal" class="modal hide fade">
                        <div class="modal-header">
                            <a class="close" data-dismiss="modal">×</a>
                            <h3>Shorten It!</h3>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="span3">
                                    <div class="row">
                                        <div class="span3">
                                            <label>Original URL</label> 
                                            <input class="span3" type="text" id="originalUrl">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="span3">
                                            <label>Shortened URL</label>
                                            <input class="span3" type="text" id="shortenedUrl">
                                        </div>
                                    </div>
                                </div>
                                <div class="span2">
                                    <label>QR Code</label>
                                    Not Implemented
                                    <!-- <img style="margin-left:0px;" alt=""> -->
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <a href="#" class="btn" id="close">Close</a>
                        </div>
                    </div>
                    <script type="text/javascript">
                        function isUrl(u) 
                        {
                            var regexp = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/
                            return regexp.test(u);
                        } 

                        function shorten() {
                            $("#error").html("").hide();                 
                            if (isUrl($("#url").val())) {     
                                var url = $("#url").val();
                                $.ajax({
                                    type: "GET",
                                    contentType: "application/json; charset=utf-8",
                                    url: 'index.php',
                                    data:  { u: encodeURI(url) },
                                    success: function (data) {
                                        if (!data[0].return)
                                        {
                                            $("#error")
                                                .html("<strong>Oh snap!</strong> " + data[0].msg)
                                                .show();
                                        }
                                        else
                                        {
                                            $("#modal").modal();  
                                            $("#originalUrl").val(url);      
                                            $("#shortenedUrl").val(data[0].url);
                                            $("#url").val("");
                                        }
                                    }
                                });
                            }
                        }

                        $(function(){
                            $("#shorten").click(function(){
                                shorten();
                            });
                            $(".modal-footer .btn, .modal-header .close").click(function(){
                                $('#modal').modal('hide');
                            });
                            $("#modal").on("shown", function () {
                                $("#shortenedUrl").focus().select();
                            });
                            $("#modal").on("hidden", function () {
                                $("#url").focus();
                            });
                            $("#shortenedUrl").click(function() {
                                $(this).select();
                            });
                        });

                    </script    
                </form>
                <?php else: ?>
                    <div class="alert alert-error">
                        <strong>Oh snap!</strong> <?php echo $error; ?>
                    </div>
                <? endif ?>
            </div>
        </div>
        </div>
    </body>
</html>	
