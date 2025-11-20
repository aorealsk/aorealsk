<?php
namespace backend\components;

/**
 * PDF-biztos szöveg előkészítés ékezetes nevekhez.
 * Nem használ FPDI/tFPDF-et – csak a sztringeket alakítja.
 */
class PdfTextSanitizer
{
    /** Egy szöveg „latinítása” (ékezetek → közelítő ASCII). */
    public static function latinize(string $s): string
    {
        $s = trim($s);

        // 1) Közép-európai betűk gyors cseréje (iconv előtt/után is hasznos)
        $map = [
            // magyar
            'Á'=>'A','á'=>'a','É'=>'E','é'=>'e','Í'=>'I','í'=>'i','Ó'=>'O','ó'=>'o',
            'Ö'=>'O','ö'=>'o','Ő'=>'O','ő'=>'o','Ú'=>'U','ú'=>'u','Ü'=>'U','ü'=>'u','Ű'=>'U','ű'=>'u',
            // szlovák/cseh
            'Ľ'=>'L','ľ'=>'l','Ĺ'=>'L','ĺ'=>'l','Š'=>'S','š'=>'s','Ž'=>'Z','ž'=>'z','Č'=>'C','č'=>'c',
            'Ň'=>'N','ň'=>'n','Ŕ'=>'R','ŕ'=>'r','Ť'=>'T','ť'=>'t','Ď'=>'D','ď'=>'d','Ý'=>'Y','ý'=>'y',
            'Ů'=>'U','ů'=>'u',
            // román/horvát – pár gyakori
            'Ă'=>'A','ă'=>'a','Â'=>'A','â'=>'a','Î'=>'I','î'=>'i','Ş'=>'S','ş'=>'s','Ț'=>'T','ț'=>'t',
        ];
        $s = strtr($s, $map);

        // 2) Általános transzliteráció (ami maradt)
        $t = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $s);
        if ($t === false) {
            $t = $s; // iconv hiba esetén marad az eredeti
        }

        // 3) Nem nyomtatható/PDF-ben problémás karakterek kiszűrése
        $t = preg_replace('/[^\x09\x0A\x0D\x20-\x7E]/', '', $t) ?? '';

        // 4) Többszörös szóköz összehúzása
        $t = preg_replace('/\s{2,}/', ' ', $t) ?? $t;

        return trim($t);
    }

    /** Tömbös változat: neveket „latinítja”, üreseket kidobja. */
    public static function latinizeList(array $items): array
    {
        $out = [];
        foreach ($items as $v) {
            if (!is_string($v)) continue;
            $t = self::latinize($v);
            if ($t !== '') $out[] = $t;
        }
        return $out;
    }
}
