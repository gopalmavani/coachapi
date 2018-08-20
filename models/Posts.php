<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "posts".
 *
 * @property int $post_id
 * @property int $user_id
 * @property string $post_title
 * @property string $post_subtitle
 * @property string $post_description
 * @property int $post_status
 * @property string $post_type
 * @property int $likes_count
 * @property int $comment_count
 * @property string $created_date
 * @property string $modified_date
 *
 * @property Media[] $media
 * @property PostComments[] $postComments
 * @property PostLikes[] $postLikes
 * @property UserInfo $user
 */
class Posts extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'posts';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'post_type', 'created_date', 'modified_date'], 'required'],
            [['user_id', 'post_status', 'likes_count', 'comment_count'], 'integer'],
            [['created_date', 'modified_date'], 'safe'],
            [['post_title', 'post_subtitle', 'post_description'], 'string', 'max' => 50],
            [['post_type'], 'string', 'max' => 20],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserInfo::className(), 'targetAttribute' => ['user_id' => 'user_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'post_id' => 'Post ID',
            'user_id' => 'User ID',
            'post_title' => 'Post Title',
            'post_subtitle' => 'Post Subtitle',
            'post_description' => 'Post Description',
            'post_status' => 'Post Status',
            'post_type' => 'Post Type',
            'likes_count' => 'Likes Count',
            'comment_count' => 'Comment Count',
            'created_date' => 'Created Date',
            'modified_date' => 'Modified Date',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMedia()
    {
        return $this->hasMany(Media::className(), ['post_id' => 'post_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPostComments()
    {
        return $this->hasMany(PostComments::className(), ['post_id' => 'post_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPostLikes()
    {
        return $this->hasMany(PostLikes::className(), ['post_id' => 'post_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(UserInfo::className(), ['user_id' => 'user_id']);
    }
}
