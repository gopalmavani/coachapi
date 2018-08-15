<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "group_mapping".
 *
 * @property int $id
 * @property int $group_id
 * @property int $user_id
 * @property int $added_by_user_id
 * @property int $status
 * @property string $created_date
 * @property string $modified_date
 */
class GroupMapping extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'group_mapping';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['group_id', 'user_id', 'added_by_user_id', 'created_date', 'modified_date'], 'required'],
            [['group_id', 'user_id', 'added_by_user_id', 'status'], 'integer'],
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
            'group_id' => 'Group ID',
            'user_id' => 'User ID',
            'added_by_user_id' => 'Added By User ID',
            'status' => 'Status',
            'created_date' => 'Created Date',
            'modified_date' => 'Modified Date',
        ];
    }
}
