<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property string $original_url
 * @property string $short_code
 * @property string $created_at
 */
class Url extends ActiveRecord
{
    public static function tableName()
    {
        return 'url';
    }

    public function rules()
    {
        return [
            [['original_url'], 'required'],
            [['original_url'], 'url'],
            [['short_code'], 'string', 'max' => 10],
            [['short_code'], 'unique'],
        ];
    }
}