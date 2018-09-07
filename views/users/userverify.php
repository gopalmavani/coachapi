<?php
/**
 * Created by PhpStorm.
 * User: Deepak
 * Date: 9/8/2018
 * Time: 12:16 AM
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
<?php
if($users->is_enabled == 0){ ?>


<div class="center">
    <div class="row" id="pass-row">
        <div class="col-lg-4"></div>
        <div class="col-lg-4 restpass">
            <h3 style>Email Verify</h3><br>
            <form action="" method="post" id="emailverify">
                <div class="row">
                    <input type="hidden" name="is_enabled" value="1">
<!--                    <button type="submit" value="submit">susb</button>-->
                    <h4>For verify your email id please <a href="" id="vrify"><button class="btn btn-primary"  type="submit">click here</button> </a></h4>
                </div>
                <br>
            </form>
        </div>
        <div class="col-lg-4"></div>
    </div>
    <?php }else{ ?>
        <div class="row">
            <div class="col-lg-4"></div>
            <div class="restpass col-lg-4">
                <span class="col-md-2"><i class="fa fa-check" style="color: green; font-size: 48px;"></i></span>
                <h3 style="color: green;">Email Verify Successfully <br style="margin-bottom: 30px;">Now you can Login </h3>
            </div>
            <div class="col-lg-4"></div>
        </div>
   <?php } ?>
    <div class="row" id="success-pass" style="display:none;">
        <div class="col-lg-4"></div>
        <div class="restpass col-lg-4">
            <span class="col-md-2"><i class="fa fa-check" style="color: green; font-size: 48px;"></i></span>
            <h3 style="color: green;">Email Verify Successfully Now you can Login </h3>
        </div>
        <div class="col-lg-4"></div>
    </div>
</div>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script>
        $("#vrify").click(function () {

            $('form').submit();
        });

</script>
