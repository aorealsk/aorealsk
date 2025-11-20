<?php

namespace common\models\idcardreader;

use common\models\reader\SlovakInvoiceReader;

class CardProcessorFactory
{
    public static function getDocument(int $documentId)
    {
        switch ($documentId) {
            case 20 : {
                return new SlovakIdCardProcessor();
                break;
            }
        }
    }
}
