<?php

namespace common\models\documents;

use yii\base\Exception;

final class DocumentFactory
{
    public static function getDocument(string $type, int $poradie = 1)
    {
        switch ($type) {
            case DocType::NABOR_BYT:
                $result = new NaborBytDocument();
                $result->setTemplate(DocType::NABOR_BYT);
                break;
            case DocType::NABOR_DOM:
                $result = new NaborDomDocument();
                $result->setTemplate(DocType::NABOR_DOM);
                break;
            case DocType::PREDZMLUVA:
                $result = new PredZmluvaDocument();
                $result->setTemplate(DocType::PREDZMLUVA);
                break;
            case DocType::SUHLAS_OSOBNE_UDAJE:
                $result = new SuhlasOsobneUdajeDocument();
                $result->setTemplate(DocType::SUHLAS_OSOBNE_UDAJE);
                break;
            case DocType::PLNOMOCENSTVO:
                $result = new PlnomocenstvoDocument();
                $result->setTemplate(DocType::PLNOMOCENSTVO);
                break;
            case DocType::PRBERACI_PROTOKOL:
                $result = new PreberaciProtokolDocument();
                $result->setTemplate(DocType::PRBERACI_PROTOKOL);
                break;
            default:
                throw new Exception("Typ $type nema processora!!!");
        }
        return $result;
    }
}
