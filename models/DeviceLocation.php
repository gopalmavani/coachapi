<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "device_location".
 *
 * @property int $device_id
 * @property int $user_id
 * @property string $device_type
 * @property double $latitude
 * @property double $longitude
 * @property string $event
 * @property string $created_date
 * @property string $modified_date
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
            [['user_id', 'latitude', 'longitude', 'created_date', 'modified_date'], 'required'],
            [['user_id'], 'integer'],
            [['latitude', 'longitude'], 'number'],
            [['created_date', 'modified_date'], 'safe'],
            [['device_type', 'event'], 'string', 'max' => 50],
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
            'device_type' => 'Device Type',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'event' => 'Event',
            'created_date' => 'Created Date',
            'modified_date' => 'Modified Date',
        ];
    }
}
