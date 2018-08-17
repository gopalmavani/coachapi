<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "post_comments".
 *
 * @property int $id
 * @property int $post_id
 * @property int $user_id
 * @property string $comment_text
 * @property int $comment_status
 * @property string $comment_type
 * @property string $created_date
 * @property string $modified_date
 */
class Comments extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'post_comments';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['post_id', 'user_id', 'comment_text', 'created_date', 'modified_date'], 'required'],
            [['post_id', 'user_id', 'comment_status'], 'integer'],
            [['created_date', 'modified_date'], 'safe'],
            [['comment_text'], 'string', 'max' => 255],
            [['comment_type'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'post_id' => 'Post ID',
            'user_id' => 'User ID',
            'comment_text' => 'Comment Text',
            'comment_status' => 'Comment Status',
            'comment_type' => 'Comment Type',
            'created_date' => 'Created Date',
            'modified_date' => 'Modified Date',
        ];
    }
}
