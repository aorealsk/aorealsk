<?php

namespace common\models\fbcharity;

use common\helpers\GeneratorHelper;
use common\models\mailer\FbCharityMailer;

final class OrderTicket
{
    private Order $order;
    private array $labels = [
        'hu' => [
            'price' => 'Ár',
            'dresscode' => 'Öltözködés:',
            'date' => 'Dátum:',
            'name' => 'Név:',
            'ticket_type' => 'Jegy típusa:',
            'place' => 'Helyszín:',
            'seat' => 'Ülőhely:',
            'wish' => 'Jó szórakozást kívánunk!',
            'building' => 'Vásárúti kultúrház',
            'time' => '2024. 02. 17.',
            'referral' => 'Ajánló kód:',
            'invitor' => 'Meghívó neve:',
        ],
        'sk' => [
            'price' => 'Cena:',
            'dresscode' => 'Dresscode:',
            'date' => 'Dátum:',
            'name' => 'Meno:',
            'ticket_type' => 'Typ vstupenky:',
            'place' => 'Miesto:',
            'seat' => 'Sedadlo:',
            'wish' => 'Prajeme Vám príjemnú zábavu!',
            'building' => 'DK Trhová Hradská',
            'time' => '17. 02. 2024',
            'referral' => 'Odporúčací kód:',
            'invitor' => 'Meno odporúčajúcej osoby:',
        ],
    ];

    private array $ticketTypes = [
        1 => 'normal',
        2 => 'stage',
        3 => 'ultra',
    ];
    private string $imageName;
    private string $outputDir;
    private string $ticketDir;
    private string $cardsDir;
    private array $tickets = [];

    public function __construct(Order $order)
    {
        $this->order = $order;
        $this->imageName = \Yii::getAlias('@webroot/../../media/') . "ticket_sablona.png";
        $this->outputDir = \Yii::getAlias('@webroot/../../media/') . "output/tickets/";
        $this->ticketDir = \Yii::getAlias('@webroot/../../media/') . "tickets/";
        $this->cardsDir = \Yii::getAlias('@webroot/../../media/') . "output/cards/";
    }

    private function createTicket(&$image, Guest $guest, Seat $seat): void
    {
        $lang = $guest->lang;

        $box = new ImgBox(1100, 556, 474, 31);
        $box->addBWText($image, $this->labels[$lang]['seat'], 26);

        $box = new ImgBox(1131, 556, 474, 65);
        $box->addBWText($image, $seat->seat_row . $seat->seat_num);

        $box = new ImgBox(1100, 69, 474, 31);
        $box->addBWText($image, $this->labels[$lang]['ticket_type'], 26);

        $box = new ImgBox(1131, 69, 474, 65);
        $box->addBWText($image, $guest->product->getDetails($lang)->one()->name);

        $box = new ImgBox(1222, 69, 967, 31);
        $box->addBWText($image, $this->labels[$lang]['name'], 26);

        $box = new ImgBox(1245, 69, 967, 65);
        $box->addBWText($image, strtoupper($guest->getFullName()));

        $box = new ImgBox(1340, 69, 474, 31);
        $box->addBWText($image, $this->labels[$lang]['date'], 26);

        $box = new ImgBox(1363, 69, 474, 65);
        $box->addBWText($image, $this->labels[$lang]['time']);

        $box = new ImgBox(1340, 556, 474, 31);
        $box->addBWText($image, $this->labels[$lang]['place'], 26);

        $box = new ImgBox(1363, 556, 474, 65);
        $box->addBWText($image, $this->labels[$lang]['building']);

        $box = new ImgBox(1458, 556, 474, 31);
        $box->addBWText($image, $this->labels[$lang]['dresscode'], 26);

        $box = new ImgBox(1481, 556, 474, 65);
        $box->addBWText($image, "SEMI-FORMAL");

        $box = new ImgBox(1458, 69, 474, 31);
        $box->addBWText($image, $this->labels[$lang]['price'], 26);

        $box = new ImgBox(1481, 69, 474, 65);
        $box->addBWText($image, (int)$guest->product->unit_price . ',- €');

        $box = new ImgBox(1566, 69, 967, 104);
        $box->addBWText($image, $this->labels[$lang]['wish']);
    }

    private function addQrCode(&$image, Guest $guest, string $ticketNumber)
    {
        $qrcode = sprintf($this->ticketDir . '/%s/%s.png', $this->ticketTypes[$guest->product_id], $ticketNumber);
        $resizedImage = imagecreatetruecolor(400, 400);

        $imgqr = imagecreatefrompng($qrcode);
        imagecopyresampled(
            $resizedImage,
            $imgqr,
            0,
            0,
            0,
            0,
            400,
            400,
            200,
            200
        );
        imagecopy($image, $resizedImage, 105, 647, 0, 0, 400, 400);
    }

    private function saveTicket($image, string $ticketNumber): string
    {
        $outputFile = $this->outputDir . $ticketNumber . '.png';
        imagepng($image, $outputFile);
        return $outputFile;
    }

    private function getOrderSeat(Guest $guest): OrderSeat
    {
        $orderSeat = OrderSeat::find()
            ->where([
                'order_id' => $this->order->id,
                'status' => 0,
                'product_id' => $guest->product_id
            ])
            ->one();

        $orderSeat->status = 1;
        $orderSeat->save();

        return $orderSeat;
    }

    private function updateSeat(OrderSeat $orderSeat, Guest $guest): Seat
    {
        $seat = Seat::findOne(['id' => $orderSeat->seat_id]);

        $guest->seat_row = $seat->seat_row;
        $guest->seat_col = $seat->seat_num;
        $guest->save();

        return $seat;
    }

    private function getTicketNumber(Guest $guest)
    {
        return EventTicket::find()->where([
            'status' => 0,
            'product_id' => $guest->product_id,
        ])
            ->orderBy('id')
            ->one();
    }

    public function generateTickets()
    {
        foreach ($this->order->getGuests()->all() as $guest) {
            $ticketImage = imagecreatefrompng($this->imageName);
            $orderSeat = $this->getOrderSeat($guest);
            $seat = $this->updateSeat($orderSeat, $guest);
            $ticket = $this->getTicketNumber($guest);

            $ticket->status = 1;
            $ticket->save();

            $this->createTicket($ticketImage, $guest, $seat);
            $this->addQrCode($ticketImage, $guest, $ticket->code);
            $clientTicket = $this->saveTicket($ticketImage, $ticket->code);

            $guest->ticket_id = $ticket->id;
            $guest->save();

            $referralCode = PromoCode::codeExistsForCustomer($guest->getFullName());
            if (is_null($referralCode)) {
                $code = GeneratorHelper::promoCodeGenerator([4, 4, 4], 'B2024', '-', 'REF', 0);

                $done = false;
                do {
                    PromoCode::find()
                        ->where(['code' => $code])
                        ->andWhere(['code_type' => 'referral'])
                        ->one() ?
                        $code = GeneratorHelper::promoCodeGenerator([4, 4, 4], 'B2024', '-', 'REF', 0) :
                        $done = true;
                } while (!$done);

                $referralCode = new PromoCode();
                $referralCode->code = $code;
                $referralCode->code_type = PromoCode::REFERRAL;
                $referralCode->assigned_to = $guest->getFullName();
                $referralCode->created_at = date('Y-m-d H:i:s');
                $referralCode->save();
            }

            $referAFriendCard = $this->generateReferAFriendCard(
                $referralCode->code,
                $guest->getFullName(),
                $this->order->code,
                $guest->lang
            );

            $mail = new FbCharityMailer();
            $mail->sendTicket($clientTicket, $guest, $this->order, $referralCode->code, $referAFriendCard);

            $this->tickets[] = $clientTicket;
            imagedestroy($ticketImage);
        }

        $referralCode = PromoCode::find()
            ->where(['code_type' => 'referral'])
            ->andWhere(['assigned_to' => $this->order->customer->getFullName()])
            ->one();
        $referralCode = !is_null($referralCode) ? $referralCode->code : '';
        $referAFriendCard = '';
        if ($referralCode != '') {
            $referAFriendCard = $this->generateReferAFriendCard(
                $referralCode,
                $this->order->customer->getFullName(),
                $this->order->code,
                $this->order->customer->lang
            );
            $this->tickets[] = $this->cardsDir . $referAFriendCard;
        }

        $mail->sendTickets($this->tickets, $this->order->customer, $this->order, $referralCode, $referAFriendCard);
    }

    public function generateReferAFriendCard(
        string $code,
        string $name,
        string $orderNumber,
        string $lang = 'sk'
    ): string {
        $fileName = $orderNumber . '_' . uniqid() . '.png';
        $file = \Yii::getAlias('@webroot/../../media/') . 'output/cards/' . $fileName;
        $image = imagecreatefrompng(\Yii::getAlias('@webroot/../../media/') . 'refer_friend_card.png');
        $nameTitleBox = new ImgBox(573, 127, 954, 30);
        $nameTitleBox->addBWText($image, $this->labels[$lang]['invitor'], 16);
        $nameBox = new ImgBox(585, 127, 954, 81);
        $nameBox->addBWText($image, $name);
        $codeTitleBox = new ImgBox(686, 127, 954, 30);
        $codeTitleBox->addBWText($image, $this->labels[$lang]['referral'], 16);
        $codeBox = new ImgBox(680, 127, 954, 105);
        $codeBox->addBWText($image, $code);
        imagepng($image, $file);
        return $fileName;
    }
}
