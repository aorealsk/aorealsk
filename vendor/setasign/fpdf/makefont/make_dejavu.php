<?php
// vendor/setasign/fpdf/makefont/make_dejavu.php

require __DIR__ . '/makefont.php';

// Convert DejaVuSansCondensed.ttf using CP1250 (Central European)
MakeFont(__DIR__ . '/DejaVuSansCondensed.ttf', 'cp1250');
