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
        margin-top: 200px;
        margin-left: 40px;
        margin-right: 40px;
    }
</style>
<div class="center">
    <div class="row">
        <div class="col-lg-4"></div>
        <div class="col-lg-4 restpass">
        <h3 style="margin-left: -15px;">Reset Password</h3><br>
            <form action="" method="post">
            <div class="row">
                New Password <input type="password" name="password" class="form-control">
            </div>
            <br>
            <div class="row">
                Confirm Password <input type="password" name="confirmpassword" class="form-control">
            </div>
            <br>
            <div class="row">
                <input type="submit" class="btn btn-primary form-control" value="Save" name="submit">
            </div>
        </form>
        </div>
        <div class="col-lg-4"></div>
    </div>
</div>

<script>

</script>