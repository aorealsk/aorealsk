<?php

namespace common\models\documents\templatedocuments;

final class TemplateFactory
{
    public static function getDocument(int $type)
    {
        switch ($type) {
            case 12: {
                    $result = new MesacnyVykazTemplate();
                    break;
                }
            case 13: {
                    $result = new HlasenieOAbsenciiZiakaTemplate();
                    break;
                }
            case 14: {
                    $result = new EvidenciaDochadzkyZiakaTemplate();
                    break;
                }
            case 15: {
                    $result = new HodnotiaciListZiakaTemplate();
                    break;
                }
            case 476: {
                    $result = new SuhlasVlastnikaTemplate();
                    break;
                }
            case 477: {
                    $result = new SplnomocnenieZalozenieFirmyTemplate();
                    break;
                }
            case 478: {
                    $result = new CestneVyhlasenieSpolocnikaTemplate();
                    break;
                }
            case 479: {
                    $result = new PodpisovyVzorKonateliaTemplate();
                    break;
                }
            case 481: {
                    $result = new SplnomocnenieZalozenieFirmyPravnikTemplate();
                    break;
                }
            case 482: {
                $result = new VyhlasenieSpravcuVkladuTemplate();
                break;
            }
            case 483: {
                $result = new ZmluvaOPoskitovaniSluziebTemplate();
                break;
            }
            case 484: {
                $result = new InformacieOSpracovaniUdajovTemplate();
                break;
            }
            case 485: {
                $result = new SuhlasOSpracovaniUdajovTemplate();
                break;
            }
            case 505: {
                $result = new ZakladatelskaListinaTemplate();
                break;
            }
            case 506: {
                $result = new ZiadostPoVydanieZivOpravneniaTemplate();
                break;
            }
            case 507: {
                $result = new CestneVyhlasenieKonecnyUzivatelVyhodTemplate();
                break;
            }
        }

        return $result;
    }
}
