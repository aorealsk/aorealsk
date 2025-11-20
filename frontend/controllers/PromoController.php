<?php

namespace frontend\controllers;

use common\models\fbcharity\EventTicket;
use common\models\fbcharity\Guest;
use common\models\fbcharity\PromoPersonal;
use common\models\fbcharity\PromoOrder;
use common\models\fbcharity\PromoOrderItem;
use common\models\fbcharity\PromoStock;
use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\Url;
use yii\web\Response;

class PromoController extends \yii\web\Controller
{
    private $slug = null;

    // ez mar jo!!!
    public function beforeAction($action)
    {
        $this->slug = Yii::$app->request->get('slug');
        return parent::beforeAction($action);
    }

    // ez mar jo!!!
    public function actions(): array
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * @throws InvalidConfigException
     */
    // ez mar jo!!!
    public function actionIndex()
    {
        $actionID = Yii::$app->request->get('action');
        if (!empty($actionID)) {
            return $this->createAction($actionID)->runWithParams([]);
        }
        return $this->render('index');
    }

    public function actionGuests()
    {
        if (is_null(Yii::$app->user->identity)) {
            return $this->redirect(Url::to(['/promo/login']));
        }
        $this->layout = 'main-layout';
        $guests = Guest::find()
            ->andWhere(['=','promo_id', 2])
            ->orderBy("status desc, order_id asc")
            ->all();
        $guestRegistered = Guest::find()
            ->andWhere(['=','promo_id', 2])
            ->andWhere(['=','status', Guest::PENDING])
            ->all();
        return $this->render('guest_list', [
            'guests' => $guests,
            'guest_registered' => count($guestRegistered),
            'guest_confirmed' => count($guests) - count($guestRegistered),
        ]);
    }

    // ez mar jo!!!
    public function actionConfirmGuest()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $result = ['status' => 'ok'];
        $data = Yii::$app->request->post();

        /*$subject = [
            'sk' => 'Potvrdenie registrácie',
            'en' => 'Registration confirmation',
            'hu' => 'Regisztráció megerősítése'
        ];*/

        $guest = Guest::findOne($data['gid']);
        if (!$guest) {
            $result['status'] = 'error';
            $result['message'] = 'Nepodarilo sa nájsť hosťa s týmto kódom';
            return $result;
        }

        $tr = Yii::$app->db->beginTransaction();

        try {
            $guest->seat_row = $data['row'];
            $guest->seat_col = $data['seat'];
            $guest->pin = $data['pin'];
            $guest->balance += $data['credit'] == '' ? 0 : $data['credit'];
            $guest->email = $data['email'];
            $guest->phone = $data['phone'];
            $guest->status = Guest::CONFIRMED;
            $guest->save();
            $tr->commit();

            /*Yii::$app->mailer->compose(
                ['html' => 'ticketConfirm-' . $guest->lang . '-html'],
                [
                    'customer' => $guest->getFullName(),
                    'order' => $guest->order,
                    'guest' => $guest,
                ]
            )
                ->setFrom('info@aoreal.sk')
                ->setTo($guest->email)
                ->setSubject($subject[$guest->lang])
                ->send();*/
        } catch (\Exception $e) {
            $tr->rollBack();
            $result['status'] = 'error';
            $result['message'] = $e->getMessage();
        }

        return $result;
    }

    // ez mar jo!!!
    public function actionLoadOrders()
    {
        if (is_null(Yii::$app->user->identity)) {
            return $this->redirect(Url::to(['/promo/login']));
        }

        $orders = PromoOrder::find()
            ->andWhere(['=','promo_id',Yii::$app->user->getIdentity()->promo_id])
            ->andWhere(['in','status', [PromoOrder::NEW, PromoOrder::PROCESSING]])
            ->all();

        return $this->renderPartial('order_list', ['orders' => $orders]);
    }

    // ez mar jo!!!
    public function actionIdentifyQr()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $result = ['status' => 'ok'];
        $qr = Yii::$app->request->post('qr');
        $action = Yii::$app->request->post('action') ?? null;

        if ($action === 'reg') {
            $ticket = EventTicket::find()
                ->andWhere(['code' => $qr])
                ->andWhere(['status' => 1])
                ->one();
            if (!$ticket) {
                $result['status'] = 'error';
                $result['message'] = 'Nepodarilo sa nájsť lístok s týmto kódom';
                return $result;
            }
            $guest = Guest::find()
                ->andWhere(['ticket_id' => $ticket->id])
                ->andWhere(['status' => Guest::PENDING])
                ->one();
        } else {
            $guest = Guest::find()->andWhere(['badge_code' => $qr])->andWhere(['status' => Guest::CONFIRMED])->one();
        }

        if ($guest) {
            $result['guest_id'] = $guest->id;
            $result['guest_name'] = $guest->getFullName();
            $result['guest_balance'] = $guest->balance;
            $result['guest_email'] = $guest->email;
            $result['guest_phone'] = $guest->phone;
            $result['seat_row'] = $guest->seat_row;
            $result['seat_num'] = $guest->seat_col;
        } else {
            $result['status'] = 'error';
            $result['message'] = 'Nepodarilo sa nájsť hosťa s týmto kódom';
        }
        return $result;
    }

    // ez mar jo!!!
    public function actionAddCredit()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $result = ['status' => 'ok'];

        $subject = [
            'sk' => 'Kredit bol pridaný',
            'en' => 'Credit was added',
            'hu' => 'Kredit hozzáadva'
        ];

        $tr = Yii::$app->db->beginTransaction();
        try {
            $guestId = Yii::$app->request->post('guest_id');
            $credit = Yii::$app->request->post('credit');
            $guest = Guest::findOne($guestId);
            $guest->balance += $credit;
            $guest->save();
            $tr->commit();
            $result['guest_balance'] = $guest->balance;

            Yii::$app->mailer->compose(
                ['html' => 'creditAdd-' . $guest->lang . '-html'],
                [
                    'customer' => $guest->getFullName(),
                    'credit' => $credit,
                    'guest' => $guest,
                ]
            )
                ->setFrom('info@aoreal.sk')
                ->setTo($guest->email)
                ->setSubject($subject[$guest->lang])
                ->send();
        } catch (\Exception $e) {
            $tr->rollBack();
            $result['status'] = 'error';
            $result['message'] = $e->getMessage();
        }
        return $result;
    }

    // ez mar jo!!!
    public function actionHome()
    {
        if (is_null(Yii::$app->user->identity)) {
            return $this->redirect(Url::to(['/promo/login']));
        }

        $this->layout = 'main-layout';
        return $this->render('main-page', [
            'username' => Yii::$app->user->getIdentity()->user_name,
        ]);
    }

    // ez mar jo!!!
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect(Url::to(['/promo/home']));
        }
        if (Yii::$app->request->isPost) {
            $data = Yii::$app->request->post('Login');
            $personal = PromoPersonal::find()
                ->andWhere(['=','user_name', $data['username']])
                ->andWhere(['=','pin',$data['pin']])
                ->one();
            if ($personal) {
                Yii::$app->user->login($personal, 3600 * 24 * 30);
                return $this->redirect(Url::to(['/promo/home']));
            }
        }
        Yii::$app->session->setFlash('danger', 'Zadali ste zlé meno alebo zlý PIN.');
        $this->layout = 'personal';
        return $this->render('login');
    }

    // ez mar jo!!!
    public function actionLogout()
    {
        Yii::$app->user->logout();
        Yii::$app->session->set('success', 'Boli ste úspešne odhlásený!');
        return $this->redirect(Url::to(['/promo/login']));
    }

    // ez mar jo!!!
    public function actionRegTicket()
    {
        if (is_null(Yii::$app->user->identity)) {
            return $this->redirect(Url::to(['/promo/login']));
        }
        $this->layout = "main-layout";
        return $this->render('reg-ticket', [
            'username' => Yii::$app->user->getIdentity()->user_name,
        ]);
    }

    // ez mar jo!!!
    public function actionCredits()
    {
        if (is_null(Yii::$app->user->identity)) {
            return $this->redirect(Url::to(['/promo/login']));
        }
        $this->layout = "main-layout";
        return $this->render('credits', [
            'username' => Yii::$app->user->getIdentity()->user_name,
        ]);
    }

    // ez mar jo!!!
    public function actionOrders()
    {
        if (is_null(Yii::$app->user->identity)) {
            return $this->redirect(Url::to(['/promo/login']));
        }
        $this->layout = 'promo_order';
        $orders = PromoOrder::find()
            ->andWhere(['=', 'promo_id', Yii::$app->user->getIdentity()->promo_id])
            ->andWhere(['in', 'status', [PromoOrder::NEW, PromoOrder::PROCESSING]])
            ->all();

        return $this->render('orders', [
            'orders' => $orders,
            'promoId' => Yii::$app->user->getIdentity()->promo_id,
        ]);
    }

    // ez mar jo !!!
    public function actionNewOrder()
    {
        if (is_null(Yii::$app->user->identity)) {
            return $this->controller->redirect(Url::to(['/promo/login']));
        }
        $this->layout = "main-layout";

        return $this->render('new-order', [
            'list' => PromoStock::find()->where('combo=0')->andWhere('amount > 0')->all(),
        ]);
    }

    // ez mar jo!!!
    public function actionProcessOrder()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $result = ['status' => 'ok'];
        $data = Yii::$app->request->post();

        $tr = Yii::$app->db->beginTransaction();
        try {
            $order = new PromoOrder();
            $order->promo_id = Yii::$app->user->getIdentity()->promo_id; // TODO: check if this is correct
            $order->guest_id = $data['gid'];
            $order->status = PromoOrder::PAID;
            $order->total = 0;
            $order->tax = 0;
            $order->promo_personal_id = Yii::$app->user->getIdentity()->id; // TODO: check if this is correct
            $order->note = '';
            $order->created_at = date('Y-m-d H:i:s');
            $order->save();

            foreach ($data['items'] as $item) {
                if ($item['amount'] > 0) {
                    $orderItem = new PromoOrderItem();
                    $orderItem->promo_order_id = $order->id;
                    $orderItem->promo_stock_id = $item['id'];
                    $orderItem->unit = $item['unit'];
                    $orderItem->unit_price = $item['price'];
                    $orderItem->amount = $item['amount'];
                    $orderItem->price = $item['price'] * $item['amount'];
                    $orderItem->tax = 0;
                    $orderItem->combo_item = 0;
                    $orderItem->created_at = date('Y-m-d H:i:s');
                    $orderItem->save();

                    $order->total += $orderItem->price;
                }
            }
            $order->save();
            $guest = Guest::findOne($data['gid']);
            //$guest->reservation = $order->total;
            $guest->balance -= $order->total;
            $guest->save();
            $tr->commit();
        } catch (\Exception $e) {
            $tr->rollBack();
            $result['status'] = 'error';
            $result['message'] = $e->getMessage();
        }
        return $result;
    }

    // ez mar jo!!!
    public function actionOrderStatusChange()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $orderText = [
            'new' => 'Nová',
            'processing' => 'Spracovaná',
            'paid' => 'Zaplatená',
            'completed' => 'Skompletizovaná',
        ];
        $orderColor = [
            'new' => 'text-bg-success',
            'processing' => 'text-bg-warning',
            'paid' => 'text-bg-danger',
            'completed' => 'text-bg-info',
        ];
        $orderId = Yii::$app->request->post('oid');
        $stat = Yii::$app->request->post('stat');

        $order = PromoOrder::findOne(['id' => $orderId]);
        $result['old_class'] = $orderColor[$order->status];
        if ($stat === PromoOrder::PROCESSING) {
            $order->promo_personal_id = Yii::$app->user->getIdentity()->id;
            $order->status = PromoOrder::PROCESSING;
        }
        if ($stat === PromoOrder::COMPLETED) {
            $order->status = PromoOrder::COMPLETED;
            // odpocitajme objednavku zo skladu
            //$this->updatePromoStock($order);
        }
        $order->save();
        $result['status'] = 'ok';
        $result['status_text'] = $orderText[$order->status];
        $result['status_color'] = $orderColor[$order->status];
        $result['oid'] = $orderId;

        return $result;
    }

    // ez mar jo!!!
    public function actionLoadOrder()
    {
        $orderText = [
            'new' => 'Nová',
            'processing' => 'Spracovaná'
        ];
        $orderColor = [
            'new' => 'text-bg-success',
            'processing' => 'text-bg-warning'
        ];
        Yii::$app->response->format = Response::FORMAT_JSON;
        $orderId = Yii::$app->request->post('oid');
        $order = PromoOrder::findOne(['id' => $orderId]);
        $result = ['status' => 'ok'];
        $totalSum = 0;

        $result['oid'] = $orderId;
        $result['title'] = '#' . $orderId . ' - ' . $order->created_at;
        $result['status_text'] = $orderText[$order->status];
        $result['status_color'] = $orderColor[$order->status];
        $result['locked'] = $order->personal->user_name;
        $result['ostatus'] = $order->status;

        $rows = PromoOrderItem::find()
            ->andWhere(['=', 'promo_order_id', $orderId])
            ->all();
        $items = [];
        foreach ($rows as $row) {
            $title = $row->detail->stockDetail->getTitle();
            $items[] = [
                'title' => $title,
                'mj' => $row->unit,
                'mnozstvo' => $row->amount,
                'jedcena' => $row->unit_price,
                'cena' =>  $row->price,
            ];
            $totalSum += $row->price;
        }
        $result['items_tbody'] = $this->renderPartial('orders_tbody', ['items' => $items]);
        $result['items_tfoot'] = $this->renderPartial('orders_tfoot', ['totalSum' => $totalSum]);
        return $result;
    }

    // ez mar jo!!!
    public function actionPersonalRegister()
    {
        $this->layout = false;
        $pro = Yii::$app->request->get('pro');
        return $this->render('personal-register', [
            'pro' => $pro ?? 1,
        ]);
    }

    // ez mar jo!!!
    public function actionFinishPersonal()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $result = ['status' => 'ok'];

        $tr = Yii::$app->db->beginTransaction();
        try {
            $data = Yii::$app->request->post();
            $personal = new PromoPersonal();
            $personal->promo_id = $data['promo_id'];
            $personal->name_first = $data['name_first'];
            $personal->name_last = $data['name_last'];
            $personal->email = $data['email'];
            $personal->phone = $data['phone'];
            $personal->user_name = $data['user_name'];
            $personal->pin = $data['pin'];
            $personal->lang = implode(',', $data['lang']);
            $personal->created_at = date('Y-m-d H:i:s');
            $personal->save();
            $tr->commit();
        } catch (\Exception $e) {
            $tr->rollBack();
            $result['status'] = 'error';
            $result['message'] = $e->getMessage();
        }

        return $result;
    }

    // ez mar jo!!!
    public function actionMyOrders()
    {
        if (is_null(Yii::$app->user->identity)) {
            return $this->redirect(Url::to(['/promo/login']));
        }
        $this->layout = 'main-layout';
        $orders = PromoOrder::find()
            ->andWhere(['=', 'promo_id', Yii::$app->user->getIdentity()->promo_id])
            ->andWhere(['=', 'promo_personal_id', Yii::$app->user->getIdentity()->id])
            ->andWhere(['in', 'status', [PromoOrder::NEW, PromoOrder::COMPLETED]])
            ->all();
        return $this->render('my-orders', [
            'orders' => $orders,
        ]);
    }

    // ez mar jo!!!
    public function actionFinishOrder()
    {
        if (is_null(Yii::$app->user->identity)) {
            return $this->redirect(Url::to(['/promo/login']));
        }
        $this->layout = 'main-layout';
        $orderId = Yii::$app->request->get('pro');
        $order = PromoOrder::findOne(['id' => $orderId]);

        return $this->render('finish-order', [
            'order' => $order,
        ]);
    }

    public function actionPayOrder()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $result = ['status' => 'ok'];
        $orderId = Yii::$app->request->post('order_id');

        $subject = [
            'sk' => 'Objednávka bola zaplatená',
            'en' => 'Order was paid',
            'hu' => 'Rendelés kifizetve'
        ];

        $tr = Yii::$app->db->beginTransaction();
        try {
            $order = PromoOrder::findOne(['id' => $orderId]);
            $order->status = PromoOrder::PAID;
            $order->save();

            // znizit rezervacie od zakaznika
            $guest = Guest::findOne(['id' => $order->guest_id]);
            $guest->reservation -= $order->total;
            $guest->save();

            $this->updatePromoStock($order);

            Yii::$app->mailer->compose(
                ['html' => 'orderPaid-' . $guest->lang . '-html'],
                [
                    'customer' => $guest->getFullName(),
                    'order' => $order,
                    'guest' => $guest,
                ]
            )
                ->setFrom('info@aoreal.sk')
                ->setTo($guest->email)
                ->setSubject($subject[$guest->lang])
                ->send();

            $tr->commit();
        } catch (\Exception $e) {
            $tr->rollBack();
            $result['status'] = 'error';
            $result['message'] = $e->getMessage();
        }
        return $result;
    }

    public function actionChangeStatus()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $result = ['status' => 'ok'];
        $guestId = Yii::$app->request->post('guest_id');
        $status = Yii::$app->request->post('status');

        $guest = Guest::findOne($guestId);
        $guest->status = $status;
        $guest->save();

        return $result;
    }
    public function actionSearchGuests()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $result = ['status' => 'ok'];
        $qname = Yii::$app->request->post('qname');
        $guests = Guest::find()
            ->andWhere(['like', 'concat(name_first," ",name_last)', '%' . $qname . '%', false])
            ->andWhere(['=', 'promo_id', 2])
            ->orderBy('status desc, order_id asc')
            ->all();
        if ($guests) {
            $result['tbody'] = $this->renderPartial('guests_tbody', ['guests' => $guests]);
        } else {
            $result['status'] = 'error';
            $result['message'] = 'Nincs találat';
        }
        return $result;
    }

    public function actionGetGuest()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $result = ['status' => 'ok'];
        $gname = Yii::$app->request->post('gname');
        if ($gname === '') {
            $result['status'] = 'error';
            $result['message'] = 'Nepodarilo sa nájsť hosťa s týmto menom';
            return $result;
        }
        $guest = Guest::find()
            ->andWhere(['like', 'concat(name_first," ",name_last)', '%' . $gname . '%', false])
            ->andWhere(['=', 'status', Guest::CONFIRMED])
            ->one();
        if ($guest) {
            $result['guest'] = $guest;
            $result['name'] = $guest->getFullName();
        } else {
            $result['status'] = 'error';
            $result['message'] = 'Nepodarilo sa nájsť hosťa s týmto menom: ' . $gname;
        }
        return $result;
    }

    public function actionReturnMoney(int $pro)
    {
        $guest = Guest::findOne($pro);
        print_r($guest);
        exit;
    }

    // ez mar jo!!!
    private function updatePromoStock(PromoOrder $order)
    {
        $unitMap = [
            '1dl' => 0.1,
            '0.04dl' => 0.04,
            '0.5l' => 0.5,
            '1l' => 1,
            '0.75dl' => 0.75,
        ];
        $items = $order->items;
        foreach ($items as $item) {
            $details = $item->detail->stockDetail;
            if (!in_array($item->unit, ['fl.', 'fl'])) {
                $unit = $unitMap[$item->unit];
                $item->detail->amount -= $unit * $item->amount;
                $item->detail->save();
            } else {
                $unit = $details->bottle_size;
                $item->detail->amount -= $unit * $item->amount;
                $item->detail->save();
            }
            unset($unit);
        }
    }
    public function actionRefundMoney(int $pro)
    {
        $this->layout = false;
        $guest = Guest::findOne($pro);
        return $this->render('refund-money-' . $guest->lang, [
           'guest' => $guest,
        ]);
    }

    public function actionRefundDone()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $result = ['status' => 'ok'];
        $data = Yii::$app->request->post();
        $guest = Guest::findOne($data['gid']);
        $fdata = json_decode($data['fdata'], true);
        $subject = [
            'sk' => 'Vrátenie peňazí',
            'en' => 'Refund',
            'hu' => 'Pénzvisszatérítés'
        ];

        if ($fdata['iban'] !== '') {
            $guest->iban = $fdata['iban'];
            $guest->save();
        }

        $guest->refund = $data['fdata'];
        $guest->save();

        Yii::$app
            ->mailer
            ->compose(
                [
                    'html' => 'refundDone-' . $guest->lang . '-html',
                ],
                [
                    'guest' => $guest,
                    'fdata' => $fdata,
                ]
            )
            ->setFrom('info@aoreal.sk')
            ->setTo($guest->email)
            //->setBcc(['fbcharity@aoreal.sk','sksja1981@gmail.com'])
            ->setSubject($subject[$guest->lang])
            ->send();

        return $result;
    }
}
