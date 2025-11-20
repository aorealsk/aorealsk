<?php

namespace common\models;

use yii\db\ActiveRecord;

/**
 * Class Partner
 *
 * @property int         $id
 * @property string      $partner_name
 * @property string|null $address
 * @property string|null $town
 * @property string|null $zip
 * @property string|null $registration_number
 * @property string|null $ICO
 * @property string|null $DIC
 * @property string|null $DICDPH
 * @property string|null $CEO
 * @property string|null $DELEGATE
 * @property string|null $tax_number
 *
 * Convenience (virtual) properties:
 * @property-read string $fullAddress
 */
class Partner extends ActiveRecord
{
    public static function tableName(): string
    {
        // Keep it as plain 'partners' since your DB table is named that way
        return 'partners';
    }

    public function rules(): array
    {
        return [
            [['partner_name'], 'required'],

            [[
                'partner_name',
                'address',
                'town',
                'zip',
                'registration_number',
                'ICO',
                'DIC',
                'DICDPH',
                'CEO',
                'DELEGATE',
                'tax_number',
            ], 'string', 'max' => 255],

            // Normalize empty strings to NULL (nice for DB consistency)
            [[
                'address',
                'town',
                'zip',
                'registration_number',
                'ICO',
                'DIC',
                'DICDPH',
                'CEO',
                'DELEGATE',
                'tax_number',
            ], 'filter', 'filter' => static function ($v) {
                return $v === '' ? null : $v;
            }],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id'                  => 'ID',
            'partner_name'        => 'Partner Name',
            'address'             => 'Address',
            'town'                => 'Town',
            'zip'                 => 'ZIP',
            'registration_number' => 'Registration Number',
            'ICO'                 => 'IČO',
            'DIC'                 => 'DIČ',
            'DICDPH'              => 'DIČ DPH',
            'CEO'                 => 'Konateľ',
            'DELEGATE'            => 'Zástupca',
            'tax_number'          => 'Daňové číslo',
        ];
    }

    /**
     * Convenience helper for PDFs / templates.
     * Example: "Address, 81101 Bratislava"
     */
    public function getFullAddress(): string
    {
        $parts = [];

        if (!empty($this->address)) {
            $parts[] = (string)$this->address;
        }

        if (!empty($this->zip) || !empty($this->town)) {
            $parts[] = trim((string)$this->zip . ' ' . (string)$this->town);
        }

        return trim(implode(', ', $parts));
    }
}
