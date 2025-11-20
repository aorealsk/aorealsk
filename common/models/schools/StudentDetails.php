<?php

namespace common\models\schools;

use yii\db\ActiveRecord;

/**
 * @property int $student_id
 * @property string $field_name
 * @property string $field_value
 * @property string created_at
 */
class StudentDetails extends ActiveRecord
{
    public const FACEBOOK = 'facebook';
    public const TWITTER = 'twitter';
    public const TIKTOK = 'tiktok';
    public const INSTAGRAM = 'instagram';
    public const SNAPCHAT = 'snapchat';
    public const LINKEDIN = 'linkedin';
    public const HEIGHT_RANGE = 'height_range';
    public const JACKET = 'jacket';
    public const PANTS = 'pants';
    public const GLOVES = 'gloves';
    public const SHOE_SIZE = 'shoe_size';
    public const SHIRT_SIZE = 'shirt_size';
    public const INTERNAT = 'internat';
    public const CANTEEN = 'canteen';
    public const PARTNER = 'partner';
    public const FIELDS = 'fields';
    public static function tableName(): string
    {
        return 'student_details';
    }

    public static function getFields()
    {
        return (new \ReflectionClass(StudentDetails::class))->getConstants();
    }
}
