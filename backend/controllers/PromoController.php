<?php

namespace backend\controllers;

use common\helpers\GeneratorHelper;
use common\models\fbcharity\Guest;
use common\models\fbcharity\OrderStatus;
use common\models\fbcharity\OrderTicket;
use common\models\fbcharity\PromoOrder;
use common\models\fbcharity\PromoOrderItem;
use common\models\fbcharity\PromoPersonal;
use common\models\fbcharity\PromoPlace;
use common\models\fbcharity\StockItemGroup;
use common\models\fbcharity\StockItemMedia;
use common\models\mailer\FbCharityMailer;
use common\models\promo\PromoGuest;
use DateTimeImmutable;
use Exception;
use yii\web\Response;
use Yii;
use yii\web\Controller;
use common\models\fbcharity\PromoCode;
use common\models\fbcharity\Order;

class PromoController extends Controller
{
    public function actions(): array
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'index' => [
                'class' => 'backend\actions\promo\IndexAction',
            ],
            'personal' => [
                'class' => 'backend\actions\promo\PersonalAction',
            ],
            'stock' => [
                'class' => 'backend\actions\promo\StockAction',
            ],
            'orders' => [
                'class' => 'backend\actions\promo\OrderAction',
            ],
            'detail' => [
                'class' => 'backend\actions\promo\DetailAction',
            ],
            'stock-edit' => [
                'class' => 'backend\actions\promo\StockEditAction',
            ],
            'group-new' => [
                'class' => 'backend\actions\promo\NewGroupAction',
            ],
            'stock-new' => [
                'class' => 'backend\actions\promo\StockNewAction',
            ],
            'personal-new' => [
                'class' => 'backend\actions\promo\PersonalNewAction',
            ],
            'promo-price-list' => [
                'class' => 'backend\actions\promo\PriceListAction',
            ],
            'personal-edit' => [
                'class' => 'backend\actions\promo\PersonalEditAction',
            ],
            'add-personal' => [
                'class' => 'backend\actions\promo\AssignPersonalAction',
            ],
            'places' => [
                'class' => 'backend\actions\promo\PlacesAction',
            ],
            'add-promo-place' => [
                'class' => 'backend\actions\promo\AddPromoPlaceAction',
            ],
            'promo-place-edit' => [
                'class' => 'backend\actions\promo\EditPromoPlaceAction',
            ],
            'codes' => [
                'class' => 'backend\actions\promo\CodesAction',
            ],
            'codes-edit' => [
                'class' => 'backend\actions\promo\CodesEditAction',
            ],
            'codes-add' => [
                'class' => 'backend\actions\promo\CodesAddAction',
            ],
            'stock-groups' => [
                'class' => 'backend\actions\promo\StockGroupsAction',
            ],
            'order-edit' => [
                'class' => 'backend\actions\promo\OrderEditAction',
            ],
            'stock-group-edit' => [
                'class' => 'backend\actions\promo\StockGroupEditAction',
            ],
            'add-stock-group' => [
                'class' => 'backend\actions\promo\AddStockGroupAction',
            ],
            'guest-detail' => [
                'class' => 'backend\actions\promo\GuestDetailAction',
            ],
            'referrals' => [
                'class' => 'backend\actions\promo\ReferralsAction',
            ],
            'add-referral' => [
                'class' => 'backend\actions\promo\AddReferralAction',
            ],
            'edit-referral' => [
                'class' => 'backend\actions\promo\EditReferralAction',
            ],
            'edit-personal' => [
                'class' => 'backend\actions\promo\EditPersonalAction',
            ],
            'guest-orders' => [
                'class' => 'backend\actions\promo\GuestOrdersAction',
            ],
            'promo-closure' => [
                'class' => 'backend\actions\promo\PromoClosureAction',
            ],
        ];
    }

    public function actionRefund()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $result = ['status' => 'ok'];
        $gid = Yii::$app->request->post('gid');
        $subject = [
            'sk' => 'Vrátenie peňazí',
            'hu' => 'Pénz visszatérítése',
        ];

        $guest = Guest::findOne(['id' => $gid]);
        if (!$guest) {
            return [
                'status' => 'error',
                'message' => 'Zákazník nebol nájdený!',
            ];
        }
        if ($guest->balance <= 0) {
            return [
                'status' => 'error',
                'message' => 'Zákazník nemá žiadny otvorený kredit!',
            ];
        }

        Yii::$app->mailer->compose(
            [
                'html' => 'refund-money-' . $guest->lang . '-html'
            ],
            [
                'guest' => $guest,
                'customer' => $guest->getFullName(),
                'orders' => PromoOrder::find()->where(['guest_id' => $guest->id])->all(),
            ]
        )
            ->setFrom('info@aoreal.sk')
            ->setTo($guest->email)
            ->setBcc(['fbcharity@aoreal.sk','sksja1981@gmail.com'])
            ->setSubject($subject[$guest->lang])
            ->send();

        return $result;
    }

    public function actionDeletePic()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $id = Yii::$app->request->post('id');
        $pic = StockItemMedia::findOne(['id' => $id]);
        $result = ['status' => 'ok'];

        if ($pic) {
            $fileName = Yii::getAlias('@web') . '/../media/stock/' . $pic->file_name;
            if (file_exists($fileName)) {
                unlink($fileName);
            }
            $pic->delete();
            $result['image'] = Yii::getAlias('@web') . '/../media/no-image.webp';
        } else {
            $result = [
                'status' => 'error',
                'message' => 'File not found!',
            ];
        }

        return $result;
    }
    public function actionCheckPin()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $pin = Yii::$app->request->post('pin');
        $promoId = Yii::$app->request->post('promo_id');
        $result = ['status' => 'ok'];

        $exists = PromoPersonal::find()
            ->where(['pin' => $pin])
            ->andWhere(['promo_id' => $promoId])
            ->exists();

        if ($exists) {
            $result['status'] = 'error';
            $result['message'] = 'PIN už existuje!';
        }

        return $result;
    }

    public function actionSendMail()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $data = Yii::$app->request->post();
        $result = ['status' => 'ok'];

        $order = Order::findOne(['id' => $data['oid']]);
        if (!$order) {
            return [
                'status' => 'error',
                'message' => 'Order not found!',
            ];
        }

        if ($order->status !== OrderStatus::PAID) {
            return [
                'status' => 'error',
                'message' => 'Order is not paid!',
            ];
        }

        $tickets = new OrderTicket($order);
        $tickets->generateTickets();

        return $result;
    }

    public function actionOrderStatus()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $status = Yii::$app->request->post('status');
        $oid = Yii::$app->request->post('oid');
        $order = Order::findOne(['id' => $oid]);
        if (!$order) {
            return [
                'status' => 'error',
                'message' => 'Order not found!',
            ];
        }
        $order->status = $status;
        $order->save();

        return [
            'status' => 'ok'
        ];
    }

    public function actionReopenStockGroup()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $id = Yii::$app->request->post('gid');
        $group = StockItemGroup::findOne(['id' => $id]);
        $result = ['status' => 'ok'];

        if (!$group) {
            return [
                'result' => 'error',
                'message' => 'Group not found!',
            ];
        }
        $group->deleted_at = null;
        $group->save();

        $result['tbody'] = $this->renderPartial('stock_groups_tbody', [
                'groups' => StockItemGroup::find()->all(),
        ]);

        return $result;
    }

    public function actionDeleteStockGroup()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $id = Yii::$app->request->post('gid');
        $group = StockItemGroup::findOne(['id' => $id]);
        $result = ['status' => 'ok'];

        if (!$group) {
            return [
                'result' => 'error',
                'message' => 'Group not found!',
            ];
        }

        $group->deleted_at = (new DateTimeImmutable('now'))->format('Y-m-d H:i:s');
        $group->save();

        $result['tbody'] = $this->renderPartial('stock_groups_tbody', [
            'groups' => StockItemGroup::find()->all(),
        ]);


        return $result;
    }

    public function actionDeleteOrder()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $oid = Yii::$app->request->post('oid');
        $order = Order::findOne(['id' => $oid]);
        if (!$order) {
            return [
                'status' => 'error',
                'message' => 'Order not found!',
            ];
        }
        $order->status = OrderStatus::DELETED;
        $order->save();

        return [
            'status' => 'ok',
            'tbody' => $this->renderPartial('orders_tbody', [
                'orders' => Order::find()->all(),
            ]),
        ];
    }

    public function actionReopenOrder()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $oid = Yii::$app->request->post('oid');
        $order = Order::findOne(['id' => $oid]);
        if (!$order) {
            return [
                'status' => 'error',
                'message' => 'Order not found!',
            ];
        }
        $order->status = OrderStatus::PENDING;
        $order->save();

        return [
            'status' => 'ok',
            'tbody' => $this->renderPartial('orders_tbody', [
                'orders' => Order::find()->all(),
            ]),
        ];
    }


    public function actionDeletePlace()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $id = Yii::$app->request->post('pid');
        $place = PromoPlace::findOne(['id' => $id]);
        $result = ['status' => 'ok'];

        if (!$place) {
            $result = [
                'result' => 'error',
                'message' => 'Position/place not found!',
            ];
        } else {
            $place->deleted_at = (new DateTimeImmutable('now'))->format('Y-m-d H:i:s');
            $place->save();

            $result['tbody'] = $this->renderPartial('promo_places_tbody', [
                'places' => PromoPlace::find()->where(['is', 'deleted_at', null])->all(),
            ]);
        }

        return $result;
    }

    public function actionGenerateReferralCode()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $data = Yii::$app->request->post();
        $done = false;

        do {
            $code = GeneratorHelper::promoCodeGenerator([4,4,6], $data['sufix'], '-', $data['prefix'], $data['last']);
            $exists = PromoCode::find()
                ->where(['code' => $code])
                ->andWhere(['code_type' => PromoCode::REFERRAL])
                ->exists();
            if (!$exists) {
                $done = true;
            }
        } while (!$done);

        return [
            'status' => 'ok',
            'result' => [
                'code' => $code,
                'last' => (int)$data['last'] + 1,
            ]
        ];
    }
    public function actionGeneratePromoCode()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $data = Yii::$app->request->post();
        $done = false;

        do {
            $code = GeneratorHelper::promoCodeGenerator([4,4,6], $data['sufix'], '-', $data['prefix'], $data['last']);
            $exists = PromoCode::find()->where(['code' => $code])->exists();
            if (!$exists) {
                $done = true;
            }
        } while (!$done);

        return [
            'status' => 'ok',
            'result' => [
                'code' => $code,
                'last' => (int)$data['last'] + 1,
            ]
        ];
    }

    public function actionSaveReferral()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $result = ['status' => 'ok'];
        $data = Yii::$app->request->post();

        $tr = Yii::$app->db->beginTransaction();

        try {
            $code = new PromoCode();
            $code->code = $data['code'];
            $code->available_from = $data['available_from'];
            $code->available_to = $data['available_to'];
            $code->used_at = null;
            $code->created_at = (new DateTimeImmutable('now'))->format('Y-m-d H:i:s');
            $code->save();
            $tr->commit();
        } catch (Exception $e) {
            $tr->rollBack();
            $result = [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }

        return $result;
    }

    public function actionSaveCode()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $result = ['status' => 'ok'];
        $data = Yii::$app->request->post();

        $tr = Yii::$app->db->beginTransaction();

        try {
            $code = new PromoCode();
            $code->code = $data['code'];
            $code->available_from = $data['available_from'];
            $code->available_to = $data['available_to'];
            $code->used_at = null;
            $code->created_at = (new DateTimeImmutable('now'))->format('Y-m-d H:i:s');
            $code->save();
            $tr->commit();
        } catch (Exception $e) {
            $tr->rollBack();
            $result = [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }

        return $result;
    }

    public function actionUpdateGuestBalance(int $id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $result = ['status' => 'ok'];
        $data = Yii::$app->request->post();

        $guest = PromoGuest::findOne(['id' => $id]);
        if (!$guest) {
            return [
                'status' => 'error',
                'message' => 'Guest not found!',
            ];
        }
        $guest->balance = 0;
        $guest->save();
        $result['balance'] = 0;

        return $result;
    }

    public function actionUpdateGuest(int $id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $result = ['status' => 'ok'];
        $data = Yii::$app->request->post();

        $guest = PromoGuest::findOne(['id' => $id]);
        if (!$guest) {
            return [
                'status' => 'error',
                'message' => 'Guest not found!',
            ];
        }
        $guest->name_first = $data['name_first'];
        $guest->name_last = $data['name_last'];
        $guest->email = $data['email'];
        $guest->phone = $data['phone'];
        $guest->save();

        return $result;
    }
}
