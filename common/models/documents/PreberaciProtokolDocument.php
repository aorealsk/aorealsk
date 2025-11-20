<?php

namespace common\models\documents;

class PreberaciProtokolDocument extends Documents implements IContractDocument
{
    private ?string $contractNumber = null;

    public function getTemplateData()
    {
        $this->templateData = [
            'seller_full_name' => '',
            'seller_full_address' => '',
            'seller_birth_date_tax_nr' => '',
            'seller_phone' => '',
            'seller_email' => '',
            'buyer_full_name' => '',
            'buyer_full_address' => '',
            'buyer_birth_date_tax_nr' => '',
            'buyer_phone' => '',
            'buyer_email' => '',
            'property' => '',
            'electricity_meter_status' => 0,
            'electricity_meter_number' => '',
            'electricity_meter_note' => '',
            'water_cold_meter_status' => 0,
            'water_cold_meter_number' => '',
            'water_cold_meter_note' => '',
            'water_hot_meter_status' => 0,
            'water_hot_meter_number' => '',
            'water_hot_meter_note' => '',
            'gas_meter_status' => 0,
            'gas_meter_number' => '',
            'gas_meter_note' => '',
            'heating_meter_status' => 0,
            'heating_meter_number' => '',
            'heating_meter_note' => '',
            'gate_count' => 0,
            'entrance_door_count' => 0,
            'chip_card_count' => 0,
            'chip_count' => 0,
            'remote_control_count' => 0,
            'flat_entrance_door_count' => 0,
            'security_insert_count' => 0,
            'cellar_count' => 0,
            'cellar_space_count' => 0,
            'mailbox_count' => 0,
            'outside_garage_count' => 0,
            'inside_garage_count' => 0,
            'laundry_count' => 0,
            'other_count' => 0,
            'note' => '',
            'place' => '',
            'date' => '',
            'common_spaces_count' => '',
        ];
    }

    public function setContractNumber($number)
    {
        $this->contractNumber = $number;
    }

    public function create()
    {
        parent::create();
        $this->writeToFile($this->contractNumber);
        $this->writeToDatabase($this->contractNumber, DocType::PRBERACI_PROTOKOL);
    }
}
