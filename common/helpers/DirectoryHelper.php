<?php
namespace common\helpers;


final class DirectoryHelper
{
    public static function mediaDirectory()
    {
        return \Yii::getAlias('@web') . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR .'..' . DIRECTORY_SEPARATOR .'media';
    }
}