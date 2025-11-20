<?php

namespace common\repositories;

use common\models\User;
use Yii;

final class UserRepository
{
    public static function getByUserIds(array $userIds): array
    {
        return User::find()->where(['id' => $userIds])->all();
    }

    public static function getById(int $userId): User
    {
        return User::findOne($userId);
    }

    /**
     * Elmenti a User rekordot a formból érkező adatokkal.
     * Kezeli: username, password, email, phone, cím (street, street_no, zip, city),
     * IBAN, születési dátum, opcionális ruhaméretek, és a két törvényes képviselő mezőit (ha a user táblában vannak).
     */
    public static function saveUser(User $user, array $data): bool
    {
        $toSave = false;

        // Kis segéd a "ha változott, állítsd" mintára
        $setIfChanged = static function(User $u, string $attr, $value) use (&$toSave) {
            if (!$u->canSetProperty($attr)) {
                return;
            }
            // normalizálás: stringeknél trim
            if (is_string($value)) {
                $value = trim($value);
            }
            // csak ha tényleg változik
            if ($u->$attr !== $value) {
                $u->$attr = $value;
                $toSave = true;
            }
        };

        // Alap mezők
        if (isset($data['username'])) {
            $setIfChanged($user, 'username', (string)$data['username']);
        }

        // Jelszó – FIGYELEM: a korábbi verzió hibásan a $user-ből olvasott
        if (!empty($data['password'])) {
            $user->setPassword(trim((string)$data['password']));
            $toSave = true;
        }

        if (isset($data['email'])) {
            $setIfChanged($user, 'email', (string)$data['email']);
        }

        // Telefon – ha a user táblában is tárolod
        if (array_key_exists('phone', $data)) {
            $setIfChanged($user, 'phone', (string)$data['phone']);
        }

        // Cím mezők
        foreach (['street','street_no','zip','city'] as $k) {
            if (array_key_exists($k, $data)) {
                $setIfChanged($user, $k, (string)$data[$k]);
            }
        }

        // IBAN – nagybetűre húzva
        if (array_key_exists('iban', $data)) {
            $iban = strtoupper(trim((string)$data['iban'] ?? ''));
            $setIfChanged($user, 'iban', $iban !== '' ? $iban : null);
        }

        // Születési dátum (elfogadjuk 'date_of_birth' vagy 'dob' kulccsal is)
        if (array_key_exists('date_of_birth', $data) || array_key_exists('dob', $data)) {
            $dob = (string)($data['date_of_birth'] ?? $data['dob'] ?? '');
            $setIfChanged($user, 'date_of_birth', $dob);
        }

        // Opcionális ruhaméretek (igazítsd az oszlopneveket a DB-hez)
        foreach (['pants_size','shirt_size','shoe_size'] as $k) {
            if (array_key_exists($k, $data)) {
                $setIfChanged($user, $k, (string)$data[$k]);
            }
        }

        // Törvényes képviselők – a form Guardians[] tömbjéből
        $gPosted = (array)Yii::$app->request->post('Guardian', []);
        $map = [
            'guardian1_name'      => ['idx'=>0,'f'=>'name'],
            'guardian1_relation'  => ['idx'=>0,'f'=>'relation'],
            'guardian1_phone'     => ['idx'=>0,'f'=>'phone'],
            'guardian1_email'     => ['idx'=>0,'f'=>'email'],
            'guardian1_street'    => ['idx'=>0,'f'=>'street'],
            'guardian1_street_no' => ['idx'=>0,'f'=>'street_no'],
            'guardian1_zip'       => ['idx'=>0,'f'=>'zip'],
            'guardian1_city'      => ['idx'=>0,'f'=>'city'],

            'guardian2_name'      => ['idx'=>1,'f'=>'name'],
            'guardian2_relation'  => ['idx'=>1,'f'=>'relation'],
            'guardian2_phone'     => ['idx'=>1,'f'=>'phone'],
            'guardian2_email'     => ['idx'=>1,'f'=>'email'],
            'guardian2_street'    => ['idx'=>1,'f'=>'street'],
            'guardian2_street_no' => ['idx'=>1,'f'=>'street_no'],
            'guardian2_zip'       => ['idx'=>1,'f'=>'zip'],
            'guardian2_city'      => ['idx'=>1,'f'=>'city'],
        ];
        foreach ($map as $col => $m) {
            if ($user->canSetProperty($col) && isset($gPosted[$m['idx']][$m['f']])) {
                $val = trim((string)$gPosted[$m['idx']][$m['f']]);
                if ($user->$col !== $val) {
                    $user->$col = $val;
                    $toSave = true;
                }
            }
        }

        // Ha semmi sem változott, tekintsük sikeresnek (ne dobjunk hibát a hívónak)
        if (!$toSave) {
            return true;
        }

        return $user->save(false);
    }
}
