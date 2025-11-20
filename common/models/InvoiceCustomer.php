<?php
namespace common\models;

use yii\db\ActiveRecord;

class InvoiceCustomer extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'faktura_odberatel';
    }

    public function attributeLabels(): array
    {
        return [
            'id'    => 'ID',
            'faktura_id'  =>  'Faktura ID',
            'nazov'  =>  'Nazov',
            'kontaktna_osoba'  =>  'Kontaktna osoba',
            'ulica'  =>  'Ulica',
            'mesto'  =>  'Mesto',
            'psc'  =>  'PSC',
            'stat'  =>  'Stat',
            'ico'  =>  'ICO',
            'dic'  =>  'DIC',
            'icdph'  =>  'IC DPH',
            'poznamka' => 'Poznamka',
            'dodacia'   => 'Dodacia',
            'dodacia_nazov' => 'Dodacia Nazov',
            'dodacia_kontaktna_osoba' => 'Dodacia - kontaktna osoba',
            'dodacia_ulica' => 'Dodacia - ulica',
            'dodacia_mesto' => 'Dodacia - mesto',
            'dodacia_psc' => 'Dodacia - psc',
            'dodacia_stat' => 'Dodacia - stat',
            'status'  =>  'Status',
            'email' => 'Email',
            'phone' => 'Telefón',
            'web' => 'Web',
        ];
    }

    public function getCustomerList(): array
    {
        $finalList = [];
        $customers = self::find()->orderBy('id desc')->asArray()->all();

        foreach ($customers as $customer) {
            $key = [];

            if (isset($customer['nazov']) && ($customer['nazov'] !== '')) {
                $this->getKey($customer, $key, 'nazov');
                $this->getKey($customer, $key, 'ico');
            } else {
                $this->getKey($customer, $key, 'kontaktna_osoba');
                $this->getKey($customer, $key, 'mesto');
            }

            if (empty($key)) {
                continue;
            }

            $finalList[implode(' / ', $key)] = json_encode($customer);
        }

        return $finalList;
    }

    private function getKey (array $item, array &$keys, string $key): void
    {
        if (isset($item[$key]) && ($item[$key] !== '')) {
            $keys[] = $item[$key];
        }
    }

}