<?php
/**
 * Created by PhpStorm.
 * User: Deepak
 * Date: 8/16/2018
 * Time: 5:14 PM
 */
?>
<style>
    .row{
        margin-left: unset;
        margin-right: unset;
    }
    .restpass{
        margin-top: 150px;
        margin-left: 40px;
        margin-right: 40px;
    }
</style>

<div class="center">
    <div class="row" id="pass-row">
        <div class="col-lg-4"></div>
        <div class="col-lg-4 restpass">
        <h3 style="margin-left: -15px;">Reset Password</h3><br>
            <form action="" method="post" id="forgetPassword">
            <div class="row">
                New Password <input type="password" name="pass1" id="pass1" class="form-control">
            </div>
            <br>
            <div class="row">
                Confirm Password <input type="password" name="pass2" id="pass2" class="form-control">
            </div>
            <br>
            <div class="row">
                <input type="submit" class="btn btn-primary form-control" value="submit" name="submit">
            </div>
        </form>
        </div>
        <div class="col-lg-4"></div>
    </div>
    <div class="row" id="success-pass" style="display:none;">
        <div class="col-lg-4"></div>
        <div class="restpass col-lg-4">
            <span class="col-md-2"><i class="fa fa-check" style="color: green; font-size: 48px;"></i></span>
            <h3 style="color: green;">Password Change Successfully</h3>
        </div>
        <div class="col-lg-4"></div>
    </div>
</div>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script>

        $(function () {
            $("form").validate({
                rules: {
                    pass1: {
                        required: true,
                    },
                    pass2: {
                        required: true,
                        equalTo: "#pass1"
                    }
                },
                messages: {
                    pass1: {
                        required: "Please provide a password",
                    },
                    pass2: {
                        required: "Please provide a password",
                        equalTo: "Please enter the same password as above"
                    }
                },
                submitHandler: function(form) {
                    var password = $('#pass1').val();
                    $.ajax({
                        url: "<?php echo Yii::$app->urlManager->createUrl("users/changepassword/".$id);?>",
                        type: "post",
                        data : {'password':password},
                        success: function (response) {
                            var Result = JSON.parse(response);
                            if (Result.token == 1) {
                                $("#pass-row").hide();
                                $("#success-pass").show();
                            }else{
                                $("#pass-row").show();
                                $("#success-pass").hide();
                            }
                        }
                    });
                },
            });
        });

</script>