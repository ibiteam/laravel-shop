<?php

namespace Database\Seeders;

use App\Enums\PaymentMethodEnum;
use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodTableSeeder extends Seeder
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
            ['mch_id' => '', 'secret_key' => '', 'v2_secret_key' => '', 'private_key' => '', 'certificate' => ''],
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
        $payment_method = PaymentMethod::query()->firstOrNew(['alias' => $payment_method_enum->value]);

        if (! $payment_method->exists) {
            $payment_method->name = $name;
            $payment_method->description = $description;
            $payment_method->icon = $icon;
            $payment_method->config = $config;
            $payment_method->is_enabled = $is_enabled;
            $payment_method->is_recommend = $is_recommend;
            $payment_method->limit = $limit;
            $payment_method->sort = $sort;
            $payment_method->save();
        }
    }
}
