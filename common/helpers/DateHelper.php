<?php

namespace common\helpers;

use Yii;

final class DateHelper
{
    const SHORT_FORMAT = 'Y-m-d';
    const LONG_FORMAT = 'Y-m-d H:i:s';
    const INV_FORMAT = 'd.m.Y';

    public static function getActualYear()
    {
        return (new \DateTime('now'))->format('Y');
    }

    public static function getActualMonth()
    {
        return (new \DateTime('now'))->format('m');
    }

    public static function getToday($format = 'Y-m-d')
    {
        return (new \DateTime('now'))->format($format);
    }

    public static function formatDate($date, $format = 'Y-m-d')
    {
        return (new \DateTime($date))->format($format);
    }

    public static function getMonthText(int $month)
    {
        $months = [
            1 => Yii::t('app', 'Január'),
            2 => Yii::t('app', 'Február'),
            3 => Yii::t('app', 'Marec'),
            4 => Yii::t('app', 'Apríl'),
            5 => Yii::t('app', 'Máj'),
            6 => Yii::t('app', 'Jún'),
            7 => Yii::t('app', 'Júl'),
            8 => Yii::t('app', 'August'),
            9 => Yii::t('app', 'September'),
            10 => Yii::t('app', 'Október'),
            11 => Yii::t('app', 'November'),
            12 => Yii::t('app', 'December'),
        ];
        return $months[$month];
    }
    public static function getMonthNumber(string $month): string
    {
        $months = [
            Yii::t('app', 'Január') => '01',
            'Február' => '02',
            'Marec' => '03',
            'Apríl' => '04',
            'Máj' => '05',
            'Jún' => '06',
            'Júl' => '07',
            'August' => '08',
            'September' => '09',
            'Október' => '10',
            'November' => '11',
            'December' => '12',
        ];

        return $months[$month];
    }

    public static function getAllMonths()
    {
        return [
            '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'
        ];
    }

    public static function getDays()
    {
        return [
            '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11',
            '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22',
            '23', '24', '25', '26', '27', '28', '29', '30', '31'
        ];
    }
}
