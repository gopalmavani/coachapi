<?php
namespace app\controllers;
use app\models\GroupInfo;
use app\models\GroupLikes;
use app\models\Media;
use Yii;
use yii\web\Controller;
use  yii\web\Request;
use yii\helpers\Json;
use yii\rest\ActiveController;
use yii\filters\auth\HttpBasicAuth;
use yii\web\HeaderCollection ;
use yii\web\Application;
use app\models\UserInfo;
use app\models\Posts;
use app\models\Likes;
use app\models\Comments;
use yii\web\UploadedFile;


class PostController extends ActiveController
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

    // create Post using image,text,videos

    public function actionCreatePost(){
        $result = [];

        $data = Yii::$app->request->post();
        $headers = Yii::$app->request->headers;
        $user_id = $headers['user_id'];
        if(!empty($user_id)){
            $users =  UserInfo::findOne($user_id);
            if($users){
                $model = new Posts();
                $model->attributes = $data;
                $model->user_id = $user_id;
                $model->created_date = date('Y-m-d H:i:s');
                $model->modified_date = date('Y-m-d H:i:s');
                if($model->save()){
                    if($data['post_type'] != 'text'){
                        $image = UploadedFile::getInstancesByName('url');
                        if(!empty($image)){
                            foreach ($image as $file){
                                $media = new Media();
                                $media->post_id = $model->post_id;
                                $path = Yii::getAlias('@webroot').'/uploads/media/'.$file->name; //Generate your save file path here;
                                $file->saveAs($path); //Your uploaded file is saved, you can process it further from here
                                $media->url = $path;
                                $media->created_date = date('Y-m-d H:i:s');
                                $media->modified_date = date('Y-m-d H:i:s');
                                if($media->save()){
                                    $result = [
                                        "code" => 200,
                                        "message" => "success",
                                    ];
                                }else{
                                    $result = [
                                        "code" => 500,
                                        "message" => "failed",
                                        "errors" => [$media->errors],
                                    ];
                                }
                            }
                        }
                    }else{
                        $result = [
                            "code" => 200,
                            "message" => "success",
                        ];
                    }
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
                    "error" => "user not found"
                ];
            }

        }else{
            $result = [
                "code" => 500,
                "message" => "failed",
            ];
        }
        echo JSON::encode($result);
    }

    //like post

    public function actionLikePost()
    {
        $headers = Yii::$app->request->headers;
        $user_id = $headers['user_id'];
        if(!empty($user_id)){
            $users =  UserInfo::findOne($user_id);
            if($users){
                $request = JSON::decode(Yii::$app->request->getRawBody());
                $post_id = $request['post_id'];
                if(!empty($post_id)){
                    $post =  Posts::findOne($post_id);
                    if($post){
                        $likeuser = Likes::find()->where(['user_id'=>$user_id,"post_id"=>$post_id])->one();
                        if(empty($likeuser)){
                            $model = new Likes();
                            $model->attributes = $request;
                            $model->user_id = $user_id;
                            $model->created_date = date('Y-m-d H:i:s');
                            $model->modified_date = date('Y-m-d H:i:s');
                            if($model->save()){
                                $Posts = Posts::find()->where(['post_id'=>$post_id])->one();
                                if(empty($Posts->likes_count)){ $likes = 0; }else{$likes = $Posts->likes_count;}
                                $Posts->likes_count = $likes + 1;
                                if($Posts->save()){
                                    $result = [
                                        "code" => 200,
                                        "message" => "Liked Successfully",
                                    ];
                                }else{
                                    $result = [
                                        "code" => 500,
                                        "message" => "failed",
                                        "error"=> [$Posts->errors],
                                    ];
                                }
                            }else{
                                $result = [
                                    "code" => 500,
                                    "message" => "failed",
                                ];
                            }
                        }else{
                            if($likeuser->delete()){
                                $Posts = Posts::find()->where(['post_id'=>$post_id])->one();
                                if(empty($Posts->likes_count)){ $likes = 0; }else{$likes = $Posts->likes_count;}
                                $Posts->likes_count = $likes - 1;
                                if($Posts->save()){
                                    $result = [
                                        "code" => 200,
                                        "message" => "Disliked Successfully",
                                    ];
                                }else{
                                    $result = [
                                        "code" => 500,
                                        "message" => "failed",
                                        "error"=> [$Posts->errors],
                                    ];
                                }

                            }else{
                                $result = [
                                    "code" => 500,
                                    "message" => "failed",
                                ];
                            }
//                            $result = [
//                                "code" => 500,
//                                "message" => "already Liked",
//                            ];
                        }
                    }else{
                        $result = [
                            "code" => 500,
                            "message" => "failed",
                            "error" => "post not found"
                        ];
                    }
                }else{
                    $result = [
                        "code" => 500,
                        "message" => "failed",
                        "error" => "post id can not blank"
                    ];
                }
            }else{
                $result = [
                    "code" => 500,
                    "message" => "failed",
                    "error" => "user not found"
                ];
            }
        }else{
            $result = [
                "code" => 500,
                "message" => "failed",
            ];
        }
        echo JSON::encode($result);
    }

    //like group

    public function actionLikeGroup()
    {
        $headers = Yii::$app->request->headers;
        $user_id = $headers['user_id'];
        if(!empty($user_id)){
            $users =  UserInfo::findOne($user_id);
            if($users){
                $request = JSON::decode(Yii::$app->request->getRawBody());
                $group_id = $request['group_id'];
                if(!empty($group_id)){
                    $group = GroupInfo::findOne($group_id);
                    if(!empty($group)){
                        $likeuser = GroupLikes::find()->where(['user_id'=>$user_id,"group_id"=>$group_id])->one();
                        if(empty($likeuser)){
                            $model = new GroupLikes();
                            $model->attributes = $request;
                            $model->user_id = $user_id;
                            $model->created_date = date('Y-m-d H:i:s');
                            $model->modified_date = date('Y-m-d H:i:s');
                            if($model->save()){
                                $group = GroupInfo::find()->where(['group_id'=>$group_id])->one();
                                if(empty($group->likes_count)){ $likes = 0; }else{$likes = $group->likes_count;}
                                $group->likes_count = $likes + 1;
                                if($group->save()){
                                    $result = [
                                        "code" => 200,
                                        "message" => "success",
                                    ];
                                }else{
                                    $result = [
                                        "code" => 500,
                                        "message" => "failed",
                                        "error"=> [$group->errors],
                                    ];
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
                                "message" => "already Liked",
                            ];
                        }
                    }else{
                        $result = [
                            "code" => 500,
                            "message" => "failed",
                            "error"=> "group not found",
                        ];
                    }
                }else{
                    $result = [
                        "code" => 500,
                        "message" => "failed",
                        "error"=> "group id can not blank",
                    ];
                }
            }else{
                $result = [
                    "code" => 500,
                    "message" => "failed",
                    "error"=> "user not found",
                ];
            }
        }else{
            $result = [
                "code" => 500,
                "message" => "failed",
            ];
        }
        echo JSON::encode($result);
    }

    //comment post

    public function actionComment()
    {
        $result = [];
        $headers = Yii::$app->request->headers;
        $user_id = $headers['user_id'];
        if(!empty($user_id)){
            $users =  UserInfo::findOne($user_id);
            if($users){
                $request = JSON::decode(Yii::$app->request->getRawBody());
                $post_id = $request['post_id'];
                if(!empty($post_id)) {
                    $post = Posts::findOne(["post_id" => $post_id]);
                    if(!empty($post)){
                        $model = new Comments();
                        $model->attributes = $request;
                        $model->user_id = $user_id;
                        $model->created_date = date('Y-m-d H:i:s');
                        $model->modified_date = date('Y-m-d H:i:s');
                        if($model->save()){
                            if(empty($post->comment_count)){ $likes = 0; }else{$likes = $post->comment_count;}
                            $post->comment_count = $likes + 1;
                            if($post->save()){
                                $result = [
                                    "code" => 200,
                                    "message" => "success",
                                ];
                            }else{
                                $result = [
                                    "code" => 500,
                                    "message" => "failed",
                                    "error"=> [$post->errors],
                                ];
                            }
                        }else{
                            $result = [
                                "code" => 500,
                                "message" => "failed",
                                "error"=> [$model->errors],
                            ];
                        }
                    }else{
                        $result = [
                            "code" => 500,
                            "message" => "post id not available",
                        ];
                    }
                }else{
                    $result = [
                        "code" => 500,
                        "message" => "post id not available",
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
                "message" => "user id can not blank",
            ];
        }
        echo JSON::encode($result);
    }

    //post list display

    public function actionList()
    {
        $headers = Yii::$app->request->headers;
        $user_id = $headers['user_id'];
        if(!empty($user_id)){
            $model = UserInfo::findOne(["user_id" => $user_id]);
            if(!empty($model)){
                $postListArray = [];
                $post_list = Posts::find()->select(["user_id","post_id","created_date","post_type","likes_count"])->where(["user_id" => $user_id])->all();
                foreach ($post_list as $posts){
                    //for post conetnt
                    $postcontent= [];
                    $postcont = Media::find()->select(["url"])->where(["post_id" => $posts['post_id']])->all();
                    foreach ($postcont as $postImageUrl){
//                        print_r($postImageUrl);die;
                        array_push($postcontent,array(
                            "postType"=>$posts['post_type'],
                            "post"=>$postImageUrl['url'],
                        ));
                    }
                    //for comments content
                    $comments =[];
                    $commentsCont = Comments::find()->where(["post_id" => $posts['post_id'],"user_id"=>$user_id])->all();
                    foreach ($commentsCont as $commnt){
                        array_push($comments,array(
                            "comment_id" => $commnt['id'],
                            "comments" => $commnt['comment_text'],
                            "user_id" => $user_id,
                            "user_image" => $model->image,
                        ));
                    }
                    //for time and date different
                    if(!empty($posts['created_date'])){
                        $timestamp = strtotime($posts['created_date']);
                        $date = date('m/d/Y', $timestamp);
                        $time =  date('h:i:s', $timestamp);
                    }else{
                        $date = "";
                        $time =  "";
                    }

                    array_push($postListArray,array(
                        "post_id" => $posts['post_id'],
                        "user_id" => $user_id,
                        "username" => $model->first_name,
                        "user_image" =>$model->image,
                        "date" => $date,
                        "time" => $time,
                        "postContent" => $postcontent,
                        "likes" => $posts['likes_count'],
                        "postComments" => $comments,
                    ));
                }

                $result = [
                    "code" => 200,
                    "message" => "success",
                    "postList"=>$postListArray
                ];
            }else{
                $result = [
                    "code" => 500,
                    "message" => "user not found",
                ];
            }
        }else{
            $result = [
                "code" => 500,
                "message" => "user id not available",
            ];
        }

        echo JSON::encode($result);
    }

    //like list of post id

    public function actionLikeList()
    {
        $headers = Yii::$app->request->headers;
        $user_id = $headers['user_id'];
        if(!empty($user_id)){
            $model = UserInfo::findOne(["user_id" => $user_id]);
            if(!empty($model)){
                $request = JSON::decode(Yii::$app->request->getRawBody());
                if(isset($request['post_id'])){
                    $post_id = $request['post_id'];
                    $post = Posts::findOne($post_id);
                    if($post){
                        $likeuser = Likes::find()->where(['post_id'=>$post_id])->all();
                        if(!empty($likeuser)){
                            $likesList = [];
                            foreach ($likeuser as $likes){
                                $model = UserInfo::findOne(["user_id" => $likes['user_id']]);
                                array_push($likesList,array(
                                    "userName"=>$model['first_name'].' '.$model['last_name'],
                                    "imageUrl"=>$model['image'],
                                    "about"=>$model['about_user']
                                ));
                            }
//                    echo "<pre>";
//                    print_r($likesList);die;
                            $result = [
                                "code" => 200,
                                "message" => "success",
                                "list"=>$likesList,
                            ];
                        }else{
                            $result = [
                                "code" => 200,
                                "message"=>"success",
                                "likes" => "no likes available",
                            ];
                        }
                    }else{
                        $result = [
                            "code" => 500,
                            "message" => "failed",
                            "error"=> "post not found",
                        ];
                    }
                }elseif (isset($request['group_id'])){
                    $group_id = $request['group_id'];
                    $group = GroupInfo::findOne($group_id);
                    if($group){
                        $groupLike = GroupLikes::find()->where(['group_id'=>$group_id])->all();
                        if($groupLike){
                            $likesList = [];
                            foreach ($groupLike as $likes){
                                $model = UserInfo::findOne(["user_id" => $likes['user_id']]);
                                array_push($likesList,array(
                                    "userName"=>$model['first_name'],
                                    "imageUrl"=>$model['image'],
                                    "about"=>$model['about_user']
                                ));
                            }
                            $result = [
                                "code" => 200,
                                "message" => "success",
                                "list"=>$likesList,
                            ];
                        }else{
                            $result = [
                                "code" => 200,
                                "message"=>"success",
                                "likes" => "no likes available",
                            ];
                        }
                    }else{
                        $result = [
                            "code" => 500,
                            "message" => "failed",
                            "error"=> "group not found",
                        ];
                    }
                }else{
                    $result = [
                        "code" => 500,
                        "message" => "failed",
                        "error"=> "post id or group id can not blank",
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
                "message" => "user id not available",
            ];
        }
        echo JSON::encode($result);
    }
}