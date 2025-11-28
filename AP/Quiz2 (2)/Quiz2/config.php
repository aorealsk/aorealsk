<?php

// config.php
session_start();

define("DATA_FILE", __DIR__ . "/data/munkafuzet_epiteszet_quiz.json");

$languages = [
    'hu' => 'Magyar',
    'sk' => 'Slovenčina',
    'en' => 'English'
];

if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'sk';
}
if (isset($_GET['lang']) && isset($languages[$_GET['lang']])) {
    $_SESSION['lang'] = $_GET['lang'];
}
