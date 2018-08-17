<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "group_likes".
 *
 * @property int $id
 * @property int $user_id
 * @property int $group_id
 * @property string $created_date
 * @property string $modified_date
 */
class GroupLikes extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'group_likes';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'group_id'], 'required'],
            [['user_id', 'group_id'], 'integer'],
            [['created_date', 'modified_date'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'group_id' => 'Group ID',
            'created_date' => 'Created Date',
            'modified_date' => 'Modified Date',
        ];
    }
}
