<?php

namespace common\models\fbcharity;

final class ImgBox
{
    public int $top;
    public int $left;
    public int $width;
    public int $height;

    public function __construct(int $top, int $left, int $width, int $height)
    {
        $this->top = $top;
        $this->left = $left;
        $this->width = $width;
        $this->height = $height;
    }

    public function addBWText(
        &$image,
        string $text,
        int $fontSize = 42
    ) {
        $black = imagecolorallocate($image, 0, 0, 0);
        $white = imagecolorallocate($image, 255, 255, 255);

        $fontFile = \Yii::getAlias('@webroot/../../media/') . "Kontora Regular.otf";

        $i = -1;
        $fSize = $fontSize;
        do {
            ++$i;
            $textWidth =
                imagettfbbox($fSize, 0, $fontFile, $text)[2] -
                imagettfbbox($fSize, 0, $fontFile, $text)[0];
            $fSize -= 2;
        } while ($textWidth > $this->width - 5);

        $fontSize -= $i * 2;
        $textHeight =
            imagettfbbox($fontSize, 0, $fontFile, $text)[1] -
            imagettfbbox($fontSize, 0, $fontFile, $text)[7];
        $textX = $this->left + ( $this->width - $textWidth) / 2;
        $textY = $this->top + ($textHeight + $this->height ) / 2;
        // Draw the text on the image
        imagettftext($image, $fontSize, 0, $textX, $textY, $white, $fontFile, $text);
        imagettftext($image, $fontSize, 0, $textX - 4, $textY, $black, $fontFile, $text);
    }
}
