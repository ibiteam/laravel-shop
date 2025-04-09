<?php

namespace Database\Seeders;

use App\Enums\PaymentMethodEnum;
use App\Models\Payment;
use Illuminate\Database\Seeder;

class PaymentTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->addPaymentMethod(
            PaymentMethodEnum::WECHAT,
            '微信支付',
            '此方式仅支持付款金额≤900元的订单',
            url('/images/icons/wechat_pay_logo.png'),
            [
                'service_wechat_pay_app_id' => '',
                'mini_wechat_pay_app_id' => '',
                'app_wechat_pay_app_id' => '',
                'mch_id' => '',
                'secret_key' => '',
                'v2_secret_key' => '',
                'private_key' => '',
                'certificate' => '',
            ],
            false,
            99999
        );
    }

    /**
     * @param PaymentMethodEnum $payment_method_enum 别名
     * @param string            $name                名称
     * @param string            $description         描述
     * @param string            $icon                图标
     * @param array             $config              配置信息
     * @param bool              $is_enabled          是否开启
     * @param bool              $is_recommend        是否推荐
     * @param int               $limit               是否限额 -1表示不限额
     * @param int               $sort                排序
     */
    public function addPaymentMethod(
        PaymentMethodEnum $payment_method_enum,
        string $name,
        string $description,
        string $icon,
        array $config,
        bool $is_enabled = true,
        int $limit = -1,
        bool $is_recommend = false,
        int $sort = 0
    ): void {
        $payment = Payment::query()->firstOrNew(['alias' => $payment_method_enum->value]);

        if (! $payment->exists) {
            $payment->name = $name;
            $payment->description = $description;
            $payment->icon = $icon;
            $payment->config = $config;
            $payment->is_enabled = $is_enabled;
            $payment->is_recommend = $is_recommend;
            $payment->limit = $limit;
            $payment->sort = $sort;
            $payment->save();
        }
    }
}
