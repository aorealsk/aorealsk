<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Asset bundle for the Student Index page.
 */
class StudentIndexAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
        // Ide jön a DataTables CSS
        'assets/node_modules/datatables/media/css/dataTables.bootstrap4.css',
    ];

    public $js = [
        // Ide jön a DataTables JS és a mi szkriptünk
        'assets/node_modules/datatables/datatables.min.js',
        'js/student-index.js',
    ];

    public $depends = [
        // Fontos! Megmondjuk neki, hogy függ a fő RealAsset-től,
        // így biztosan azután töltődik be, hogy a jQuery és a téma betöltődött.
        'backend\assets\RealAsset',
    ];
}