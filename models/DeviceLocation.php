<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "device_location".
 *
 * @property int $device_id
 * @property int $user_id
 * @property string $device_token
 * @property string $device_type
 * @property double $latitude
 * @property double $longitude
 * @property string $event
 * @property string $created_date
 * @property string $modified_date
 *
 * @property UserInfo $user
 */
class DeviceLocation extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'device_location';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'created_date', 'modified_date'], 'required'],
            [['user_id'], 'integer'],
            [['latitude', 'longitude'], 'number'],
            [['created_date', 'modified_date'], 'safe'],
            [['device_token', 'device_type', 'event'], 'string', 'max' => 50],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserInfo::className(), 'targetAttribute' => ['user_id' => 'user_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'device_id' => 'Device ID',
            'user_id' => 'User ID',
            'device_token' => 'Device Token',
            'device_type' => 'Device Type',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'event' => 'Event',
            'created_date' => 'Created Date',
            'modified_date' => 'Modified Date',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(UserInfo::className(), ['user_id' => 'user_id']);
    }
}
