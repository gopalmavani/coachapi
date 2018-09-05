<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "users_likes".
 *
 * @property int $like_id
 * @property int $user_id
 * @property int $like_user_id
 * @property string $created_date
 * @property string $modified_date
 */
class UsersLikes extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'users_likes';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['like_id', 'user_id', 'like_user_id'], 'required'],
            [['like_id', 'user_id', 'like_user_id'], 'integer'],
            [['created_date', 'modified_date'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'like_id' => 'Like ID',
            'user_id' => 'User ID',
            'like_user_id' => 'Like User ID',
            'created_date' => 'Created Date',
            'modified_date' => 'Modified Date',
        ];
    }
}
