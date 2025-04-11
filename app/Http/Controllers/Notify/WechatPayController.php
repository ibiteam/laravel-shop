<?php

namespace App\Http\Controllers\Notify;

use App\Enums\PayFormEnum;
use App\Enums\PaymentEnum;
use App\Http\Controllers\Controller;
use App\Http\Dao\PaymentDao;
use App\Models\Payment;
use App\Utils\Wechat\WechatPayUtil;
use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Kernel\Exceptions\RuntimeException;
use EasyWeChat\Kernel\ServerResponse;
use EasyWeChat\Pay\Message;
use Illuminate\Http\Request;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use ReflectionException;
use Throwable;

class WechatPayController extends Controller
{
    /**
     * @return Response|ResponseInterface
     *
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws ReflectionException
     * @throws Throwable
     */
    public function notifyPay(Request $request, PaymentDao $payment_dao)
    {
        // 支付回调
        $payment = $payment_dao->getInfoByAlias(PaymentEnum::WECHAT);

        if (! $payment instanceof Payment) {
            return ServerResponse::make(new Response(
                200,
                [],
                strval(json_encode(['code' => 'FAIL', 'message' => 'Not Fount Payment'], JSON_UNESCAPED_UNICODE))
            ));
        }
        $wechat_server = (new WechatPayUtil($payment->config, PayFormEnum::PAY_FORM_H5))->server();

        $wechat_server->handlePaid(function (Message $message, \Closure $next) {
            // todo 支付成功回调处理

            return $next($message);
        });

        return $wechat_server->serve();
    }
}
