<?php
namespace backend\components;

use Yii;
use yii\helpers\FileHelper;

/**
 * Dokumentumsablon-regiszter automatikus XML felismeréssel.
 *
 * Motorok:
 *  - HTML → PDF (mPDF):   ['engine' => self::ENGINE_HTML, 'view' => '@backend/...']
 *  - XML → overlay (FPDI):['engine' => self::ENGINE_XML,  'xml'  => '@backend/.../template.xml']
 */
class DocTemplateRegistry
{
    public const ENGINE_HTML = 'html';
    public const ENGINE_XML  = 'xml';

    /** Hova tesszük az XML sablonokat */
    private const XML_DIR_ALIAS = '@backend/templates/contracts';

    /** Egyszerű memória cache, hogy ne olvassunk fájlt minden híváskor */
    private static array $_cache = [];

    /**
     * Az összes elérhető sablon metaadatai (kézzel felvett + automatikusan felismert XML-ek).
     * @return array<string,array>
     */
    public static function all(): array
    {
        if (self::$_cache) {
            return self::$_cache;
        }

        // 1) Kézzel regisztráltak (meglévő nézeted)
        $manual = [
            'ttsk_dual_1r_html' => [
                'label'  => 'Žiadosť – klónozott űrlap (mPDF, kitölthető)',
                'engine' => self::ENGINE_HTML,
                'view'   => Yii::getAlias('@backend/views/documents/templates/zupan_dual_1r_fillable.php'),
            ],

            'dual_vycvik_zmluva' => [
                'label'  => 'Zmluva o duálnom vzdelávaní',
                'engine' => self::ENGINE_XML,
                'xml'    => Yii::getAlias('@backend/templates/contracts/dual_vycvik_zmluva.xml'),
                ],
        ];

        // 2) Automatikus XML felismerés
        $auto = self::scanXmlTemplates(self::XML_DIR_ALIAS);

        // Ütközés esetén a kézi bejegyzés élvez elsőbbséget
        self::$_cache = $manual + $auto;
        return self::$_cache;
    }

    /**
     * Egy sablon metaadatainak lekérdezése azonosító alapján.
     * @param string $id
     * @return array|null
     */
    public static function get(string $id): ?array
    {
        $all = self::all();
        return $all[$id] ?? null;
    }

    /**
     * Létezik-e sablon.
     */
    public static function exists(string $id): bool
    {
        return self::get($id) !== null;
    }

    /**
     * Legördülő opciók (id => label).
     * @return array<string,string>
     */
    public static function dropdownOptions(): array
    {
        $out = [];
        foreach (self::all() as $id => $meta) {
            $out[$id] = $meta['label'] ?? $id;
        }
        return $out;
    }

    /**
     * XML sablonok beszkennelése egy mappából.
     * - ID: xml_<fájlnév_kiterjesztés_nélkül> (pl. xml_stipendium_overlay)
     * - Label: <meta><title> vagy fájlnévből képezett barátságos cím
     *
     * @param string $dirAlias
     * @return array<string,array>
     */
    private static function scanXmlTemplates(string $dirAlias): array
    {
        $out = [];
        $dir = Yii::getAlias($dirAlias);
        if (!is_dir($dir)) {
            return $out;
        }

        $files = FileHelper::findFiles($dir, [
            'only' => ['*.xml'],
            'recursive' => true,
        ]);

        foreach ($files as $path) {
            $id = 'xml_' . self::idFromFilename($path);
            $label = self::labelFromXmlOrFilename($path);

            $out[$id] = [
                'label'  => $label,
                'engine' => self::ENGINE_XML,
                'xml'    => $path,
            ];
        }

        return $out;
    }

    /**
     * Címke kinyerése: először XML <meta><title>, ha nincs, akkor fájlnévből.
     */
    private static function labelFromXmlOrFilename(string $path): string
    {
        try {
            $xml = @simplexml_load_file($path);
            if ($xml && isset($xml->meta->title) && trim((string)$xml->meta->title) !== '') {
                return (string)$xml->meta->title;
            }
        } catch (\Throwable $e) {
            // ha hibás XML, esünk vissza a fájlnévre
        }
        return self::friendlyTitleFromFilename($path);
    }

    /**
     * ID-hoz alap: fájlnév kiterjesztés nélkül, nem alfanumerikus karakterek '_' lesznek.
     */
    private static function idFromFilename(string $path): string
    {
        $base = pathinfo($path, PATHINFO_FILENAME);
        $base = strtolower(preg_replace('/[^a-z0-9]+/i', '_', $base));
        return trim($base, '_');
    }

    /**
     * Szép cím a fájlnévből (pl. stipendium_overlay → "Stipendium overlay").
     */
    private static function friendlyTitleFromFilename(string $path): string
    {
        $name = pathinfo($path, PATHINFO_FILENAME);
        $name = preg_replace('/[_\-]+/', ' ', $name);
        $name = trim($name);
        return mb_convert_case($name, MB_CASE_TITLE, 'UTF-8');
    }
}
