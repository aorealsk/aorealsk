<?php
namespace common\repositories;

use Yii;
use yii\db\Exception;

final class UserAttendanceRepository
{
    /**
     * @throws Exception
     */
    public static function getTimeSheetByUserId(int $userId, int $year, int $month)
    {
        $sql = "select uaDate,inTime,outTime from userAttendance where userId=$userId and uaDate like '$year-$month-%'";
        return Yii::$app->db->createCommand($sql)->queryAll();
    }
}