<?php

namespace common\models\mailer;

use common\models\fbcharity\Customer;
use common\models\fbcharity\Guest;
use common\models\fbcharity\Order;
use Yii;

class FbCharityMailer extends AoMailer
{
    protected $sender = 'fbcharity@aoreal.sk';

    protected array $attachments = [];
    protected array $subjects = [
        'sk' => 'Vaše vstupenky na event Farsangi Bál 2024 - DK Trhová Hradská, 17. 02. 2024',
        'hu' => 'Farsangi Bál 2024 belépőjegyei - Vásárúti kultúrház, 2024. 02. 17.',
    ];


    /**
     * @throws \Exception
     */
    public function sendTicket(
        string $ticket,
        Guest $guest,
        Order $order,
        string $referralCode,
        string $referAFriendCard
    ) {
        $dueDate = (new \DateTime($order->created_at))->modify('+2 day');
        $this->setSubject($this->subjects[$guest->lang]);
        $this->setData([
            'customer' => $guest->getFullName(),
            'referralCode' => $referralCode,
            'referAFriendCard' => $referAFriendCard,
            'order' => $order,
            'dueDate' => $dueDate,
        ]);
        $this->setRecipients([$guest->email]);	
        $this->setTemplate(sprintf('%s-tickets-html', $guest->lang));
        $this->attachments = [
            $ticket,
            \Yii::getAlias('@webroot/../../media/') . "output/cards/$referAFriendCard",
        ];
        $this->sendHTMLMessage();
    }

    public function sendTickets(
        array $tickets,
        Customer $customer,
        Order $order,
        string $referralCode,
        string $referAFriendCard
    ) {
        $dueDate = (new \DateTime($order->created_at))->modify('+2 day');
        $this->setSubject($this->subjects[$customer->lang]);
        $this->setData([
            'customer' => $customer->getFullName(),
            'referralCode' => $referralCode,
            'referAFriendCard' => $referAFriendCard,
            'order' => $order,
            'dueDate' => $dueDate,
        ]);
        $this->setRecipients([$customer->email]);
        $this->setTemplate(sprintf('%s-tickets-html', $customer->lang));
        $this->attachments = $tickets;
        $this->sendHTMLMessage();
    }

    public function sendHTMLMessage(): void
    {
        try {
            $mailer = !is_null($this->message) ? Yii::$app->mailer->compose() :
                Yii::$app->mailer->compose(['html' => $this->template], $this->data);

            if (!empty($this->attachments)) {
                foreach ($this->attachments as $attachment) {
                    $mailer->attach($attachment);
                }
            }
            if (!is_null($this->message)) {
                $mailer->setHtmlBody($this->message);
            }
            $mailer->setFrom($this->sender)
                ->setTo($this->recipients)
                ->setCharset('utf-8')
                ->setSubject($this->subject)
                ->send();
        } catch (\Exception $e) {
            echo $e->getMessage();
            exit;
        }
    }
}
