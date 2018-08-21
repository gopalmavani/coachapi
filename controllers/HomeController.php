<?php
/**
 * Created by PhpStorm.
 * User: Deepak
 * Date: 8/13/2018
 * Time: 2:28 PM
 */


namespace app\controllers;
use Yii;
use yii\web\Controller;
use  yii\web\Request;
use yii\helpers\Json;
use yii\rest\ActiveController;
use yii\filters\auth\HttpBasicAuth;
use yii\web\HeaderCollection ;
use yii\web\Application;
use app\models\Users;
use app\models\UserInfo;
use app\models\DeviceLocation;
use yii\web\UploadedFile;
use yii\helpers\Url;

class HomeController extends ActiveController
{

    public $modelClass = 'app\models\Employee';
    // public $token = 'abc@1234'; //decrypted token
    public $token = '31528198109743225ff9d0cf04d1fdd1'; //md5 token encrypted

    public function beforeAction($action)
    {
        header('Access-Control-Allow-Origin: *');
        header('Content-type: application/json');
        if (isset(getallheaders()['Token']) && $this->token === getallheaders()['Token']) {
            return parent::beforeAction($action); // TODO: Change the autogenerated stub
        } else {
            http_response_code(401);
            echo Json::encode(array(
                "result" => false,
                "error" => "Unauthorized",
            ));
        }
    }

    //Registration Api As per Required Parameters.
    public function actionRegister()
    {
        $result = [];
        $request = JSON::decode(Yii::$app->request->getRawBody());
        if(!empty($request)){
            if(isset($request['registerType'])){
                $registerType = strtolower($request['registerType']);
                if(($registerType == "facebook" || $registerType == "email" || $registerType == "google" || $registerType == "insta" || $registerType == "linkedin")){
                    if(isset($request['userType'])){
                        $userType = strtolower($request['userType']);
                        if(($userType == "coach" || $userType == "user" )){
                            if(!empty($request['fullname'])){
                                if(!empty($request['email']) && !empty($request['password'])){
                                    $model = new UserInfo();
                                    $model->attributes = $request;
                                    if(!empty($request['socialId'])) { $model->social_id = $request['socialId'];}
                                    if(!empty($request['fullname'])) { $model->first_name = $request['fullname'];}
                                    if(!empty($request['focusArea'])) { $model->focus_areas = $request['focusArea'];}
                                    if(!empty($request['aboutme'])){ $model->about_user = $request['aboutme'];}
                                    $model->user_type = $request['userType'];
                                    $model->password = md5($request['password']);
                                    $model->date_of_registration = date('Y-m-d H:i:s');
                                    $model->created_date = date('Y-m-d H:i:s');
                                    $model->modified_date = date('Y-m-d H:i:s');
                                    //          MD5 hash for admin@123 is : e6e061838856bf47e1de730719fb2609
                                    //          $model->user_token = md5(uniqid($model->user_id, true));
                                    if ($model->save()) {
                                        $device = new DeviceLocation();
                                        $device->attributes = $request;
                                        if(isset($request['deviceId'])){
                                            $model->device_token = $request['deviceId'];
                                        }
                                        $device->event = "register";
                                        $device->user_id = $model->user_id;
                                        $device->created_date = date('Y-m-d H:i:s');
                                        $device->modified_date = date('Y-m-d H:i:s');
                                        $device->save();
                                        $result = [
                                            "code" => 200,
                                            "message" => "success",
                                            "userId" => $model->user_id,
                                            //              "userToken" => $model->user_token,
                                        ];
                                    }else{
                                        $result = [
                                            "code" => 500,
                                            "message" => "failed",
                                            "errors" => [$model->errors],
                                        ];
                                    }

                                }else{
                                    $result = [
                                        "code" => 500,
                                        "message" => "failed",
                                        "error" => "Invalid email or password",
                                    ];
                                }
                            }else{
                                $result = [
                                    "code" => 500,
                                    "message" => "failed",
                                    "error" => "fullname cannot be blank",
                                ];
                            }

                        }else{
                            $result = [
                                "code" => 500,
                                "message" => "failed",
                                "error" => "Invalid userType",
                            ];
                        }
                    }else{
                        $result = [
                            "code" => 500,
                            "message" => "failed",
                            "error" => "userType not defined",
                        ];
                    }
                }else{
                    $result = [
                        "code" => 500,
                        "message" => "failed",
                        "error" => "Invalid registerType",
                    ];
                }

            }else{
                $result = [
                    "code" => 500,
                    "message" => "failed",
                    "error" => "registerType not defined",
                ];
            }
        }else{
            $result = [
                "code" => 500,
                "message" => "failed",
                "error" => "data not available",
            ];
        }
        echo JSON::encode($result);
    }

    //Login Api As per Required Parameters.
    public function actionLogin()
    {
        $result = [];
        $request = JSON::decode(Yii::$app->request->getRawBody());
        if(!empty($request)){
            if(isset($request['userType'])){
                $userType = strtolower($request['userType']);
                if(($userType == "coach") || ($userType == "user")){
                    if(isset($request['deviceType'])){
                        $deviceType = strtolower($request['deviceType']);
                        if(($deviceType == "ios" )|| ($deviceType == "android")){
                            if(!empty($request['deviceId'])){
                                if (!empty($request['email']) && !empty($request['password'])) {
                                    $user = UserInfo::findOne(["email" => $request['email'], "password" => md5($request['password'])]);
                                    if (!empty($user)) {
                                        $user->last_logged_in = date('Y-m-d H:i:s');
                                        $user->save();
                    //                    if($user->gender == 1){
                    //                        $user->gender = "Female";
                    //                    }else{
                    //                        $user->gender = "male";
                    //                    }
                    //                    if($user->is_enabled == 1){
                    //                        $user->is_enabled = "yes";
                    //                    }else{
                    //                        $user->is_enabled = "no";
                    //                    }
                                        $userDetails = [
                                            "userId"=>$user['user_id'],
                                            "userType"=>$user['user_type'],
                                            "fullname"=>$user['first_name'].' '.$user['last_name'],
                                            "email"=>$user['email'],
                                            "gender"=> $user->gender,
                                            "dob"=>$user->dob,
                                            "aboutme"=>$user->about_user,
                                            "goals"=>$user->goals,
                                            "focus_area"=>$user->focus_areas,
                                            "location"=>$user->location,
                                            "city"=>$user->city,
                                            "country"=>$user->country,
                                            "profession"=>$user->profession,
                                            "is_verified"=> $user->is_enabled,
                                            "user_profile_image" => $user->image
                                        ];

                                        $result = [
                                            "code" => 200,
                                            "status" => "success",
//                        "userToken"=>$user->user_token,
                                            "userDetails" => $userDetails,
                                        ];
                                    } else {
                                        $result = [
                                            "code" => 500,
                                            "message" => "failed",
                                            "error" => "Invalid user/Password",
                                        ];
                                    }
                                } else {
                                    $result = [
                                        "code" => 500,
                                        "message" => "failed",
                                        "error" => "email and password required",
                                    ];
                                }
                            }else{
                                $result = [
                                    "code" => 500,
                                    "message" => "failed",
                                    "error" => "deviceId not defined",
                                ];
                            }
                        }else{
                            $result = [
                                "code" => 500,
                                "message" => "failed",
                                "error" => "Invalid deviceType",
                            ];
                        }
                    }else{
                        $result = [
                            "code" => 500,
                            "message" => "failed",
                            "error" => "deviceType not defined",
                        ];
                    }
                }else{
                    $result = [
                        "code" => 500,
                        "message" => "failed",
                        "error" => "Invalid userType",
                    ];
                }
            }else{
                $result = [
                    "code" => 500,
                    "message" => "failed",
                    "error" => "userType not defined",
                ];
            }
        }else{
            $result = [
                "code" => 500,
                "message" => "failed",
                "error" => "data not available",
            ];
        }
        echo JSON::encode($result);
    }

    //Social Login Api As per Required Parameters.

    public function actionSocialLogin()
    {
        $result = [];
        $request = JSON::decode(Yii::$app->request->getRawBody());
        if(!empty($request)){
            if(isset($request['loginType'])){
//                ($request['loginType'] == "facebook" || $request['loginType'] == "google" || $request['loginType'] == "insta" || $request['loginType'] == "linkedin")
                $loginType = strtolower($request['loginType']);
                if(($loginType == "facebook") || ($loginType == "email") || ($loginType == "google") || ($loginType == "insta") || ($loginType == "linkedin")){
                    if(isset($request['deviceType'])){
                        $deviceType = strtolower($request['deviceType']);
                        if(($deviceType == "ios" )|| ($deviceType == "android")){
                            if(!empty($request['deviceId'])){
                                if (!empty($request['email']) && !empty($request['socialId'])) {
                                    $user = UserInfo::findOne(["email" => $request['email'], "social_id"=>$request['socialId']]);
                                    if (!empty($user)) {
                                        $user->last_logged_in = date('Y-m-d H:i:s');
                                        $user->save();
                                        //                    if($user->gender == 1){
                                        //                        $user->gender = "Female";
                                        //                    }else{
                                        //                        $user->gender = "male";
                                        //                    }
                                        //                    if($user->is_enabled == 1){
                                        //                        $user->is_enabled = "yes";
                                        //                    }else{
                                        //                        $user->is_enabled = "no";
                                        //                    }
                                        $userDetails = [
                                            "userId"=>$user['user_id'],
                                            "userType"=>$user['user_type'],
                                            "fullname"=>$user['first_name'].' '.$user['last_name'],
                                            "email"=>$user['email'],
                                            "gender"=> $user->gender,
                                            "dob"=>$user->dob,
                                            "aboutme"=>$user->about_user,
                                            "goals"=>$user->goals,
                                            "focus_area"=>$user->focus_areas,
                                            "location"=>$user->location,
                                            "city"=>$user->city,
                                            "country"=>$user->country,
                                            "profession"=>$user->profession,
                                            "is_verified"=> $user->is_enabled,
                                            "user_profile_image" => $user->image
                                        ];

                                        $result = [
                                            "code" => 200,
                                            "status" => "success",
//                        "userToken"=>$user->user_token,
                                            "userDetails" => $userDetails,
                                        ];
                                    } else {
                                        $result = [
                                            "code" => 500,
                                            "message" => "failed",
                                            "error" => "Invalid user/socialId",
                                        ];
                                    }
                                } else {
                                    $result = [
                                        "code" => 500,
                                        "message" => "failed",
                                        "error" => "email and password required",
                                    ];
                                }
                            }else{
                                $result = [
                                    "code" => 500,
                                    "message" => "failed",
                                    "error" => "deviceId not defined",
                                ];
                            }
                        }else{
                            $result = [
                                "code" => 500,
                                "message" => "failed",
                                "error" => "Invalid deviceType",
                            ];
                        }
                    }else{
                        $result = [
                            "code" => 500,
                            "message" => "failed",
                            "error" => "deviceType not defined",
                        ];
                    }
                }else{
                    $result = [
                        "code" => 500,
                        "message" => "failed",
                        "error" => "Invalid loginType",
                    ];
                }
            }else{
                $result = [
                    "code" => 500,
                    "message" => "failed",
                    "error" => "userType not defined",
                ];
            }
        }else{
            $result = [
                "code" => 500,
                "message" => "failed",
                "error" => "data not available",
            ];
        }
        echo JSON::encode($result);
    }




    public function actionSocialLoginbkp()
    {
        $result = [];
        $request = JSON::decode(Yii::$app->request->getRawBody());
        if((!empty($request['loginType'])) && (!empty($request['deviceId'])) && (!empty($request['email'])) && (!empty($request['deviceType'])) && (!empty($request['socialId']))){
            if(($request['loginType'] == "facebook" || $request['loginType'] == "google" || $request['loginType'] == "insta" || $request['loginType'] == "linkedin") && ($request['deviceType'] == "ios" || $request['deviceType'] == "android")){
                if (!empty($request['email']) && (!empty($request['socialId']))){
                    $user = UserInfo::findOne(["email" => $request['email'],"social_id"=>$request['socialId']]);
                    if (!empty($user)) {
                        $user->last_logged_in = date('Y-m-d H:i:s');
                        $user->save();

                        $userDetails = [
                            "userId"=>$user['user_id'],
                            "userType"=>$user['user_type'],
                            "fullname"=>$user['first_name'].' '.$user['last_name'],
                            "email"=>$user['email'],
                            "gender"=> $user->gender,
                            "dob"=>$user->dob,
                            "aboutme"=>$user->about_user,
                            "goals"=>$user->goals,
                            "focus_area"=>$user->focus_areas,
                            "location"=>$user->location,
                            "city"=>$user->city,
                            "country"=>$user->country,
                            "profession"=>$user->profession,
                            "is_verified"=> $user->is_enabled,
                            "user_profile_image"=>$user->image
                        ];
                        $result = [
                            "code" => 200,
                            "status" => "success",
                            "user Id" => $user['user_id'],
                            "userDetails" => $userDetails,
                        ];
                    } else {
                        $result = [
                            "code" => 500,
                            "message" => "failed",
                            "error" => "Invalid user/social id",
                        ];
                    }
                } else {
                    $result = [
                        "code" => 500,
                        "message" => "failed",
                        "error" => "email and social id required",
                    ];
                }
            } else {
                $result = [
                    "code" => 500,
                    "message" => "failed",
                ];
            }
        } else {
            $result = [
                "code" => 500,
                "message" => "failed",
                "error" => "data not available",
            ];
        }
        echo JSON::encode($result);
    }

    //get splash Api As per Required Parameters.

    public function actionSplash()
    {
        $result = [];
        $request = JSON::decode(Yii::$app->request->getRawBody());
        $result = [
            "code" => "200",
            "splash" => "http://localhost/asd/img/influ.png",
        ];
        echo JSON::encode($result);
    }


    // update profile using id and update device location

    public function actionEditProfile(){
        $result = [];
        $headers = Yii::$app->request->headers;
        $id = $headers['user_id'];
        if(!empty($id)){
            $user = UserInfo::findOne(["user_id" => $id]);
            if(!empty($user)){
                $request = JSON::decode(Yii::$app->request->getRawBody());
                if(!empty($request)){
                    $user->attributes = $request;
                    if(!empty($request['socialId'])) { $user->social_id = $request['socialId'];}
                    if(!empty($request['fullname'])) { $user->first_name = $request['fullname'];}
                    if(!empty($request['focusArea'])) { $user->focus_areas = $request['focusArea'];}
                    if(!empty($request['aboutme'])){ $user->about_user = $request['aboutme'];}
                    $user->modified_date = date('Y-m-d H:i:s');
                    if($user->save()){
                        $device = DeviceLocation::findOne(["user_id" => $id]);
                        if(empty($device)){
                            $device = new DeviceLocation();
                            $device->attributes = $request;
                            $device->event = "register";
                            $device->user_id = $user->user_id;
                            $device->created_date = date('Y-m-d H:i:s');
                            $device->modified_date = date('Y-m-d H:i:s');
                            if($device->save()) {
                                $result = [
                                    "code" => 200,
                                    "message" => "success",
                                ];
                            }else{
                                $result = [
                                    "code" => 500,
                                    "message" => "failed",
                                    "errors" => [$device->errors],
                                ];
                            }
                        }else{
                            $device->event = "register";
                            $device->attributes = $request;
//                            $device->latitude = $request['latitude'];
//                            $device->longitude = $request['longitude'];
                            $device->created_date = date('Y-m-d H:i:s');
                            $device->modified_date = date('Y-m-d H:i:s');
                            if($device->save()) {
                                $result = [
                                    "code" => 200,
                                    "message" => "success",
                                ];
                            }else{
                                $result = [
                                    "code" => 500,
                                    "message" => "failed",
                                    "errors" => [$device->errors],
                                ];
                            }
                        }
                    }else{
                        $result = [
                            "code" => 500,
                            "message" => "failed",
                            "errors" =>[$user->errors],
                        ];
                    }
                }else{
                    $result = [
                        "code" => 500,
                        "message" => "data cannot be blank",
                    ];
                }
            }else{
                $result = [
                    "code" => 500,
                    "message" => "user not found",
                ];
            }
        }else{
            $result = [
                "code" => 500,
                "message" => "cant get id of user",
            ];
        }
        echo JSON::encode($result);
    }

    // update profile image

    public function actionUpdateProfileImage()
    {
        $result = [];
        $headers = Yii::$app->request->headers;
        $id = $headers['user_id'];
        if(!empty($id)){
            $model = UserInfo::findOne(["user_id" => $id]);
            if(!empty($model)){
                $image = UploadedFile::getInstancesByName('image');
                if(!empty($image)){
                    foreach ($image as $file){
                        $path = Yii::getAlias('@webroot').'/uploads/'.$file->name; //Generate your save file path here;
                        $file->saveAs($path); //Your uploaded file is saved, you can process it further from here
                        $model->image = $path;
                        if($model->save()){
                            $result = [
                                "code" => 200,
                                "message" => "success",
                            ];
                        }else{
                            $result = [
                                "code" => 500,
                                "message" => "failed",
                                "errors" => [$model->errors],
                            ];
                        }
                    }
                }else{
                    $result = [
                        "code" => 500,
                        "message" => "image not available",
                    ];
                }
            }else{
                $result = [
                    "code" => 500,
                    "message" => "user not found",
                ];
            }
        }else{
            $result = [
                "code" => 500,
                "message" => "cant get id of user",
            ];
        }
        echo JSON::encode($result);
    }

    //deactive account

    public function actionDeactiveAccount()
    {
        $result = [];
        $headers = Yii::$app->request->headers;
        $user_id = $headers['user_id'];
        if(!empty($user_id)){
            $model = UserInfo::findOne(["user_id" => $user_id]);
            if(!empty($model)){
                if($model->is_active == 0 ){
                    $result = [
                        "code" => 500,
                        "message" => "failed",
                        "errors" => "already deactive account",
                    ];
                }else{
                    $model->is_active = 0;
                    if($model->save()){
                        $result = [
                            "code" => 200,
                            "message" => "success",
                        ];
                    }else{
                        $result = [
                            "code" => 500,
                            "message" => "failed",
                            "errors" => [$model->errors],
                        ];
                    }
                }
            }else{
                $result = [
                    "code" => 500,
                    "message" => "user not found",
                ];
            }
        }else{
            $result = [
                "code" => 500,
                "message" => "cant get id of user",
            ];
        }
        echo JSON::encode($result);
    }

    //update Location

    public function actionRegisterLocation()
    {
        $result = [];
        $headers = Yii::$app->request->headers;
        $user_id = $headers['user_id'];
        if(!empty($user_id)){
            $user = UserInfo::findOne(["user_id" => $user_id]);
            if(!empty($user)){
                $request = JSON::decode(Yii::$app->request->getRawBody());
                if(isset($request['latitude']) && isset($request['longitude']) && isset($request['deviceToken'])){
                    $model = DeviceLocation::findOne(["user_id" => $user_id]);
                    if(!empty($model)){
                        $model->attributes = $request;
                        $model->user_id = $user_id;
                        $model->event = "register";
                        $model->device_token = $request['deviceToken'];
                        $model->created_date = date('Y-m-d H:i:s');
                        $model->modified_date = date('Y-m-d H:i:s');
                        if ($model->save()) {
                            $result = [
                                "code" => 200,
                                "message" => "success",
                            ];
                        } else {
                            $result = [
                                "code" => 500,
                                "message" => "failed",
                                "error" => [$model->errors],
                            ];
                        }
                    }else{
                        $device = new DeviceLocation();
                        $device->attributes = $request;
                        $model->device_token = $request['deviceToken'];
                        $device->user_id = $user_id;
                        $model->event = "register";
                        $device->created_date = date('Y-m-d H:i:s');
                        $device->modified_date = date('Y-m-d H:i:s');
                        if ($device->save()) {
                            $result = [
                                "code" => 200,
                                "message" => "success",
                            ];
                        } else {
                            $result = [
                                "code" => 500,
                                "message" => "failed",
                                "error" => [$device->errors],
                            ];
                        }
                    }
                }else{
                    $result = [
                        "code" => 500,
                        "message" => "failed",
                    ];
                }
            }else{
                $result = [
                    "code" => 500,
                    "message" => "failed",
                    "erorr" => "user not found",
                ];
            }
        }else{
            $result = [
                "code" => 500,
                "message" => "failed",
                "erorr" => "user id can not blank",
            ];
        }

        echo JSON::encode($result);
    }

    //forget password for the reser password

    public function actionForgetPassword()
    {
        $result = [];
        $request = JSON::decode(Yii::$app->request->getRawBody());
        if (!empty($request['emailId'])) {
            $user = UserInfo::findOne(["email" => $request['emailId']]);
            $url = "http://scrumwheel.com".Yii::$app->urlManager->createUrl("users/resetpassword/".$user["user_id"]);

            if (!empty($user)) {
                $to = $user['email'];
                $subject = "user password reset";
                $headers = "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                $headers .= "From: support@coach.in" . "\r\n";
                $message = '
                        <html>
                            <body>
                                <table style="margin:50px auto;width:500px;">
                                    <thead>
                                        <tr>
                                            <td><h2>CoachApi</h2></td>
                                        </tr>
                                        <tr><p>Reset password Details</p></tr>
                                    </thead>
                                    <tbody>
                                        <tr width="100%">
                                           <td><h4>password reset link <a href="'.$url.'" target="_blank">click here</a></h4></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </body>
                        </html>';

                if(mail($to, $subject, $message, $headers)){
                    $result = [
                        "code" => 200,
                        "message" => "success",
                    ];
                }else{
                    $result = [
                        "code" => 500,
                        "message" => "failed",
                    ];
                }
            }else{
                $result = [
                    "code" => 500,
                    "message" => "user not found",
                ];
            }
        }else{
            $result = [
                "code" => 500,
                "message" => "user not available",
            ];
        }
        echo JSON::encode($result);
    }

    //search api from user name parameters

    public function actionSearch()
    {
        $result = [];
        $headers = Yii::$app->request->headers;
        $user_id = $headers['user_id'];
        if(!empty($user_id)){
            $user = UserInfo::findOne(["user_id" => $user_id]);
            if(!empty($user)){
                $request = JSON::decode(Yii::$app->request->getRawBody());
                if(!empty($request['seachKey'])){
                    $userData = UserInfo::find()->select(['user_id','first_name','image','location','city','country','about_user'])->where(["like","first_name" ,$request['seachKey']])->all();
                    if($userData){
                        $data = [];
                        foreach ($userData as $usersInfo){
                            array_push($data,array(
                                "userId"=>$usersInfo['user_id'],
                                "userName"=>$usersInfo['first_name'],
                                "userImage"=>$usersInfo['image'],
                                "location"=>$usersInfo['location'],
                                "city"=>$usersInfo['city'],
                                "country"=>$usersInfo['country'],
                                "aboutme"=>$usersInfo['about_user']
                            ));
                        }
                        $result = [
                            "code" => 200,
                            "message" => "success",
                            "userData"=>$data
                        ];
                    }else{
                        $result = [
                            "code" => 500,
                            "message"=>"failed",
                            "error" => "seachKey can not blank",
                        ];
                    }

                }else{
                    $result = [
                        "code" => 500,
                        "message"=>"failed",
                        "error" => "seachKey can not blank",
                    ];
                }
            }else{
                $result = [
                    "code" => 500,
                    "message"=>"failed",
                    "error" => "user not found",
                ];
            }
        }else{
            $result = [
                "code" => 500,
                "message"=>"failed",
                "error" => "user id can not blank",
            ];
        }
        echo JSON::encode($result);
    }
}