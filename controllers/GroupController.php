<?php
namespace app\controllers;
use app\models\Comments;
use app\models\FriendsList;
use app\models\Media;
use app\models\Posts;
use app\models\UsersLikes;
use Yii;
use yii\web\Controller;
use  yii\web\Request;
use yii\helpers\Json;
use yii\rest\ActiveController;
use yii\filters\auth\HttpBasicAuth;
use yii\web\HeaderCollection ;
use yii\web\Application;
use app\models\GroupInfo;
use app\models\UserInfo;
use app\models\GroupMapping;
use app\models\DeviceLocation;
use yii\web\UploadedFile;
use yii\data\Pagination;

class GroupController extends ActiveController
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

    public function actionCreateGroup()
    {
        $result = [];
//        $data = $_POST;
        $headers = Yii::$app->request->headers;
        $user_id = $headers['user_id'];
        if(!empty($user_id)){
           $users =  UserInfo::findOne($user_id);
            if($users){
                $request = JSON::decode(Yii::$app->request->getRawBody());
                if(isset($request['group_name'])){
                    $model = new GroupInfo();
                    $model->attributes = $request;
                    $model->user_id = $user_id;
//                    $image = UploadedFile::getInstancesByName('group_image');
                    $model->created_date = date('Y-m-d H:i:s');
                    $model->modified_date = date('Y-m-d H:i:s');
//                    if(!empty($image)){
//                        foreach ($image as $file){
//                            $path = Yii::getAlias('@webroot').'/uploads/group/'.$file->name; //Generate your save file path here;
//                            $file->saveAs($path); //Your uploaded file is saved, you can process it further from here
//                            $model->group_image = 'coachapi/uploads/group/'.$file->name;
//                        }
//                    }
                    if($model->save()){
                        $GroupMapping = new GroupMapping();
                        $GroupMapping->user_id = $user_id;
                        $GroupMapping->added_by_user_id = $user_id;
                        $GroupMapping->group_id = $model->group_id;
                        $GroupMapping->created_date = date('Y-m-d H:i:s');
                        $GroupMapping->modified_date = date('Y-m-d H:i:s');
                        $GroupMapping->save();
                        if(isset($request['memberList'])){
                            $datamember = explode(",",$request['memberList']);
                            foreach ($datamember as $user){
                                $users_data = UserInfo::findOne($user);
                                if($users_data){
                                    $GroupMapping = new GroupMapping();
                                    $GroupMapping->user_id = $user;
                                    $GroupMapping->added_by_user_id = $user_id;
                                    $GroupMapping->group_id = $model->group_id;
                                    $GroupMapping->created_date = date('Y-m-d H:i:s');
                                    $GroupMapping->modified_date = date('Y-m-d H:i:s');
                                    if($GroupMapping->save()){
                                        $result = [
                                            "code" => 200,
                                            "message" => "success",
                                            "group_id" => $model->group_id
                                        ];
                                    }else{
                                        $errors ='Error Occured,Please try again later';
                                        if(isset($GroupMapping->errors)){
                                            $errors = "";
                                            foreach ($GroupMapping->errors as $key => $value){
                                                if($key == 'first_name'){
                                                    $value[0] = 'Full Name cannot be blank.';
                                                }
                                                $errors .= $value[0]." and ";
                                            }
                                            $errors = rtrim($errors, ' and ');
                                            $errors = str_replace ('"', "", $errors);
                                        }
                                        $result = [
                                            "code" => 500,
                                            "message" => $errors,
//                                        "error"=>$GroupMapping->errors,
                                        ];
                                    }
                                }else{
                                    $result = [
                                        "code" => 500,
//                                    "message" => "failed",
                                        "message"=>"user id ".$user." does not exist",
                                    ];
                                }
                            }
                        }else{
                            $result = [
                                "code" => 200,
                                "message" => "success",
                            ];
                        }
                    }else{
                        $errors ='Error Occured,Please try again later';
                        if(isset($model->errors)){
                            $errors = "";
                            foreach ($model->errors as $key => $value){
                                if($key == 'first_name'){
                                    $value[0] = 'Full Name cannot be blank.';
                                }
                                $errors .= $value[0]." and ";
                            }
                            $errors = rtrim($errors, ' and ');
                            $errors = str_replace ('"', "", $errors);
                        }
                        $result = [
                            "code" => 500,
                            "message" => $errors,
//                        "errors" => [$model->errors],
                        ];
                    }
                }else{
                    $result = [
                        "code" => 500,
//                    "message" => "failed",
                        "message"=>"group name can not blank",
                    ];
                }
            }else{
                $result = [
                    "code" => 500,
//                    "message" => "failed",
                    "message"=>"user not found",
                ];
            }

        }else{
            $result = [
                "code" => 500,
//                "message" => "failed",
                "message" => "user id can not blank",
            ];
        }
    echo JSON::encode($result);
    }

    //group list display

    public function actionGroupList()
    {
        $headers = Yii::$app->request->headers;
        $user_id = $headers['user_id'];
        if(!empty($user_id)) {
            $users = UserInfo::findOne($user_id);
            if ($users) {
                $groupsmapp = GroupMapping::find('group_id')->where(['user_id'=>$user_id])->All();
                $data = [];
                if(!empty($groupsmapp)){
                    foreach ($groupsmapp as $user){
                        $model = GroupInfo::find()->select(['group_name','group_id','group_image','user_id'])->where(['group_id'=>$user->group_id])->one();
                        $countuser = GroupMapping::findAll(['group_id'=>$user->group_id]);
                        $countuser = count($countuser);
                        array_push($data,["groupId"=>$model->group_id,"groupName"=>$model->group_name,"noOfMember"=>$countuser,"imageUrl"=>$model->group_image,'created_by'=>$model->user_id]);
                    };
                    $result = [
                        "code" => 200,
                        "message" => "success",
                        "groupData" => $data,
                    ];
                }else{
                    $result = [
                        "code" => 200,
                        "message" => "success",
                        "groupData" => [],
                    ];
                }
            }else{
                $result = [
                    "code" => 500,
//                    "message" => "failed",
                    "message" => "user not found",
                ];
            }
        }else{
            $result = [
                "code" => 500,
//                "message" => "failed",
                "message" => "user id can not blank",
            ];
        }

        echo JSON::encode($result);
    }

    //get Group Details of where user is in the group

    public function actionGroupDetail()
    {
        $result = [];
        $headers = Yii::$app->request->headers;
        $user_id = $headers['user_id'];
        if(!empty($user_id)) {
            $users = UserInfo::findOne($user_id);
            if ($users) {
                $request = JSON::decode(Yii::$app->request->getRawBody());
                $group_id = $request['group_id'];

                if(isset($request['offset'])){
                    $offset =  $request['offset'];
                }else{
                    $offset = 0;
                }if (isset($request['limit'])){
                    $limit = $request['limit'];
                }else{
                    $limit = 100;
                }
                if(!empty($group_id)){
                    $group = GroupInfo::findOne($group_id);
                    if ($group) {
                        $groupUsers = GroupMapping::find()->where(["group_id"=>$group->group_id])->all();
                        $postData = [];
                        if($groupUsers){
                            foreach ($groupUsers as $GroupUser){
                                $query = Posts::find()->where(['user_id'=>$GroupUser['user_id']])->all();
                                $posts = (new \yii\db\Query())
                                    ->select('*')
                                    ->from('posts')
                                    ->where(['user_id'=>$GroupUser['user_id']])
                                    ->limit($limit)
                                    ->offset($offset)
                                    ->all();

                                if($posts){
                                    foreach ($posts as $post){

                                        // post image or video url drtails section
                                        $postMedia = Media::find()->where(['post_id'=>$post['post_id']])->all();
                                        $mediaDetails = [];
                                        if($postMedia){
                                            foreach ($postMedia as $media){
                                                array_push($mediaDetails,array("url" => $media['url']));
                                            }
                                        }

                                        //post comments details section
                                        $comments = Comments::find()->where(['post_id'=>$post['post_id']])->all();
                                        $commentDetails = [];
                                        if($comments){
                                            foreach ($comments as $comment){
                                                $user = UserInfo::findOne(["user_id" => $comment['user_id']]);
                                                // for date time to devide date and time different
                                                if(!empty($comment['created_date'])){
                                                    $timestamp = strtotime($comment['created_date']);
                                                    $date = date('d/m/Y', $timestamp);
                                                    $time =  date('h:i', $timestamp);
                                                }else{
                                                    $date = "";
                                                    $time =  "";
                                                }
                                                array_push($commentDetails,array(
                                                    "commentId" => $comment['id'],
                                                    "commentedBy" => $comment['user_id'],
                                                    "commentByName" => $user->first_name,
                                                    "comment" => $comment['comment_text'],
                                                    "commentTime" => $time,
                                                    "commentDate" => $date,
                                                ));
                                            }
                                        }
                                        $usersInfo = UserInfo::findOne($post['user_id']);
                                        $postDetails = [
                                            "postId" => $post['post_id'],
                                            "postName" => $post['post_title'],
                                            "postType" => $post['post_type'],
                                            "postDesc" => $post['post_description'],
                                            "media" => $mediaDetails,
                                            "likes" => $post['likes_count'],
                                            "comments" => $post['comment_count'],
                                            "comment" => $commentDetails,
                                            "postByUserId" => $post['user_id'],
                                            "postByUserName" => $usersInfo->first_name,
                                        ];
                                        array_push($postData,$postDetails);
                                    }
                                }
                            }
                        }
                        $groupMembers = GroupMapping::find()->where(["group_id"=>$group->group_id])->all();
                        $membersList = [];
                        if($groupMembers){
                            foreach ($groupMembers as $member){
                                $usersDetail = UserInfo::findOne($member['user_id']);
                                array_push($membersList,array(
                                    "memberId" => $member['user_id'],
                                    "memberName" => $usersDetail->first_name,
                                ));
                            }
                        }
                        $usersName = UserInfo::findOne($group->user_id);
                        $groupData = [
                            "groupId" => $group->group_id,
                            "groupName" => $group->group_name,
                            "groupImage" => $group->group_image,
                            "posts" => $postData,
                            "groupAdminId" => $group->user_id,
                            "groupAdminName" => $usersName->first_name,
                            "groupAdminUrl" => $usersName->image,
                            "groupMembersList" => $membersList,

                        ];
                        $result = [
                            "code" => 200,
                            "message" => "success",
                            "groupData"=>$groupData
                        ];
                    }else{
                        $result = [
                            "code" => 500,
//                            "message" => "failed",
                            "message"=> "group not found"
                        ];
                    }
                }else{
                    $result = [
                        "code" => 500,
//                        "message" => "failed",
                        "message"=> "group id can not blank"
                    ];
                }
            }else{
                $result = [
                    "code" => 500,
//                    "message" => "failed",
                    "message"=> "user not found"
                ];
            }
        }else{
            $result = [
                "code" => 500,
//                "message" => "failed",
                "message"=> "user id can not blank"
            ];
        }
        echo JSON::encode($result);
    }

    //add member to group

    public function actionAddMemberGroup()
    {
        $headers = Yii::$app->request->headers;
        $user_id = $headers['user_id'];
        if(!empty($user_id)){
            $users =  UserInfo::findOne($user_id);
            $request = JSON::decode(Yii::$app->request->getRawBody());
            if($users){
                if((isset($request['group_id'])) && (isset($request['userId']))){
                    $group_id = $request['group_id'];
                    $addUserId = $request['userId'];
                    $addUsers =  UserInfo::findOne($addUserId);
                    if($addUsers){
                        $group = GroupInfo::findOne($group_id);
                        if($group){
                            $grouped = GroupMapping::findOne(["user_id"=>$addUserId,"group_id"=>$group_id]);
                            if(empty($grouped)){
                                $GroupMapping = new GroupMapping();
                                $GroupMapping->group_id = $group_id;
                                $GroupMapping->user_id = $addUserId;
                                $GroupMapping->added_by_user_id = $user_id;
                                $GroupMapping->created_date = date('Y-m-d H:i:s');
                                $GroupMapping->modified_date = date('Y-m-d H:i:s');
                                if($GroupMapping->save()){
                                    $result = [
                                        "code" => 200,
                                        "message" => "success",
                                    ];
                                }else{
                                    $errors ='Error Occured,Please try again later';
                                    if(isset($GroupMapping->errors)){
                                        $errors = "";
                                        foreach ($GroupMapping->errors as $key => $value){
                                            if($key == 'first_name'){
                                                $value[0] = 'Full Name cannot be blank.';
                                            }
                                            $errors .= $value[0]." and ";
                                        }
                                        $errors = rtrim($errors, ' and ');
                                        $errors = str_replace ('"', "", $errors);
                                    }
                                    $result = [
                                        "code" => 500,
                                        "message" => $errors,
//                                    "message" => [$GroupMapping->errors],
                                    ];
                                }
                            }else{
                                $result = [
                                    "code" => 500,
                                    "message" => "Already Added Member",
                                ];
                            }
                        }else{
                            $result = [
                                "code" => 500,
                                "message" => "group not found",
                            ];
                        }
                    }else{
                        $result = [
                            "code" => 500,
                            "message" => "user not found for add to group",
                        ];
                    }

                }else{
                    $result = [
                        "code" => 500,
                        "message" => "group id can not blank ",
                    ];
                }
            }else{
                $result = [
                    "code" => 500,
                    "message" => "user does not exist",
                ];
            }

        }else{
            $result = [
                "code" => 500,
                "message" => "user id can not blank",
            ];
        }
        echo JSON::encode($result);
    }

    //Leave member to group

    public function actionLeaveMemberGroup()
    {
        $headers = Yii::$app->request->headers;
        $user_id = $headers['user_id'];
        $request = JSON::decode(Yii::$app->request->getRawBody());
        if(isset($request['group_id'])){
            $group_id = $request['group_id'];
        }else{
            $group_id = "";
        }
        if(!empty($user_id && $group_id)){
            $users =  UserInfo::findOne($user_id);
            $group = GroupInfo::findOne($group_id);
            if($users && $group){
                $grouped = GroupMapping::findOne(["user_id"=>$user_id,"group_id"=>$group_id]);
                if($grouped){
                    if($grouped->delete()){
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
//                        "message" => "failed",
                        "message"=>"user does not exist in group"
                    ];
                }
            }else{
                $result = [
                    "code" => 500,
//                    "message" => "failed",
                    "message"=>"user or group does not exist"
                ];
            }
        }else{
            $result = [
                "code" => 500,
                "message" => "failed",
                "errors" => "no data",
            ];
        }
        echo JSON::encode($result);
    }

    //add group by

    public function actionAddGroup()
    {
        $headers = Yii::$app->request->headers;
        $user_id = $headers['user_id'];
        $request = JSON::decode(Yii::$app->request->getRawBody());
        $group_id = $request['group_id'];
        if(!empty($user_id && $group_id && $request['members_id'])){
            $users =  UserInfo::findOne($user_id);
            $groups = GroupInfo::findOne($group_id);
            $member = UserInfo::findOne($user_id);
            if($users && $groups && $request['members_id']){
                $datamember = explode(",",$request['members_id']);
                foreach ($datamember as $user){
                    $grouped = GroupMapping::findOne(["user_id"=>$user,"group_id"=>$group_id]);
                    if(empty($grouped)){
                        $GroupMapping = new GroupMapping();
                        $GroupMapping->user_id = $user;
                        $GroupMapping->added_by_user_id = $user_id;
                        $GroupMapping->group_id = $group_id;
                        $GroupMapping->created_date = date('Y-m-d H:i:s');
                        $GroupMapping->modified_date = date('Y-m-d H:i:s');
                        $GroupMapping->save();
                        $result = [
                            "code" => 200,
                            "message" => "success",
                        ];
                    }
                }
            }else{
                $result = [
                    "code" => 500,
                    "message" => "failed",
                    "error"=>"user or group or meber does not exist"
                ];
            }


        }else{
            $result = [
                "code" => 500,
                "message" => "no data",
            ];
        }
        echo JSON::encode($result);
    }


    //search Group as per group names

    public function actionSearchGroup()
    {
        $headers = Yii::$app->request->headers;
        $user_id = $headers['user_id'];
        if(!empty($user_id)){
            $user = UserInfo::findOne(["user_id" => $user_id]);
            if(!empty($user)){
                $request = JSON::decode(Yii::$app->request->getRawBody());
                if(!empty($request['searchKey'])){
                    $groupDetails =[];
                    $groupdata = GroupInfo::find()->where(["like","group_name" ,$request['searchKey']])->all();
                    if($groupdata){
                        foreach ($groupdata as $group){
                            $groupMember = GroupMapping::find()->where(['group_id'=>$group->group_id])->all();
                            $userInfo = [];
                            if($groupMember){
                                foreach ($groupMember as $member){
                                    $frnds = 0;
                                    $frndsz = FriendsList::find()->where(['user_id'=>$user_id,'friend_user_id'=>$member->user_id])->one();
                                    if($frndsz){
                                        if($frndsz->status == 1){
                                            $frnds = 1;
                                        }else if($frndsz->status == 0){
                                            $frnds = 2;
                                        }
                                    }
                                    $userLike = UsersLikes::find()->where(['user_id'=>$user_id,'like_user_id'=>$member->user_id])->one();
                                    $like = 0;
                                    if($userLike) {
                                        $like = 1;
                                    }
                                    $userData = UserInfo::findOne($member->user_id);
                                    array_push($userInfo,array(
                                        "user_id"=>$userData['user_id'],
                                        "userName"=>$userData['first_name'],
                                        "userImage"=>$userData['image'],
                                        "location"=>$userData['location'],
                                        "city"=>$userData['city'],
                                        "country"=>$userData['country'],
                                        "about"=>$userData['about_user'],
                                        "is_coach"=>$userData['is_active'],
                                        "is_like"=>$like,
                                        "is_friend"=>$frnds,
                                    ));
                                }
                            }
                            array_push($groupDetails,array(
                                "groupName" => $group->group_name,
                                "imageUrl" => $group->group_image,
                                "about" => $group->group_description,
                                "userData" => $userInfo
                            ));
                        }
                    }
                    $result = [
                        "code" => 200,
                        "message"=>"success",
                        "groupData" => $groupDetails
                    ];
                }else{
                    $result = [
                        "code" => 500,
//                        "message"=>"failed",
                        "message" => "searchKey cannot blank",
                    ];
                }
            }else{
                $result = [
                    "code" => 500,
//                    "message"=>"failed",
                    "message" => "user not found",
                ];
            }
        }else{
            $result = [
                "code" => 500,
//                "message"=>"failed",
                "message" => "user id can not blank",
            ];
        }
        return $result;
        //return json_encode($result, JSON_UNESCAPED_SLASHES);
        //return JSON::encode($result);
    }

}