<?php

namespace App\Utils;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class KuaiDi100Util
{
    public static function getLabelByStatus(int $status_code): string
    {
        if (in_array($status_code, [1, 101, 102, 103])) {
            return '已揽件';
        }

        if (in_array($status_code, [0, 1001, 1002, 1003])) {
            return '运输中';
        }

        if (in_array($status_code, [5, 501])) {
            return '派送中';
        }

        if (in_array($status_code, [3, 301, 302, 303, 304])) {
            return '已签收';
        }

        if (in_array($status_code, [6])) {
            return '退回';
        }

        if (in_array($status_code, [4, 401, 14])) {
            return '退签';
        }

        if (in_array($status_code, [2, 201, 202, 203, 204, 205, 206, 207, 208, 209, 210, 7, 8, 10, 11, 12, 13])) {
            return '疑难';
        }

        return '';
    }

    /**
     * 快递查询.
     *
     * @param string $ship_no           快递单号
     * @param string $ship_company_code 快递公司编码
     * @param string $phone             手机号 选填；（必填：顺丰速运、顺丰快运）
     *
     * @throws \Exception
     */
    public static function queryExpress(string $ship_no, string $ship_company_code, string $phone = ''): array
    {
        $customer = config('custom.kuaidi100.customer');
        $key = config('custom.kuaidi100.key');

        if (! $customer || ! $key) {
            return [];
        }
        $param = json_encode([
            'com' => $ship_company_code, // 快递公司code
            'num' => $ship_no, // 快递单号
            'phone' => $phone, // 手机号
            'from' => '', // 出发地城市
            'to' => '', // 目的地城市
            'resultv2' => 4, // 开启行政区域解析
        ]);

        $data = [
            'customer' => $customer,
            'param' => $param,
            'sign' => strtoupper(md5($param.$key.$customer)),
        ];

        $response = self::doPost('/poll/query.do', $data);
        // todo  先返回模拟数据 给前端调试
        $response = json_decode('{"message":"ok","nu":"YT7199652228745","ischeck":"1","com":"yuantongkuaiyun","status":"200","data":[{"time":"2023-05-04 19:50:07","context":"\u5305\u88f9\u5df2\u5b8c\u6210\u7b7e\u6536\uff0c\u7b7e\u6536\u65b9\u5f0f\uff1a\u5bb6\u95e8\u53e3\uff0c\u5982\u6709\u7591\u95ee\u8bf7\u7535\u805413341029942\uff0c\u64cd\u4f5c\u65b9\u5317\u4eac\u4e30\u53f0\u5bcc\u9526\u4e8c\u533a9\u53f7\u697c\u5e97\u83dc\u9e1f\u9a7f\u7ad9\u3002\u3002\u5982\u6709\u7591\u95ee\u8bf7\u8054\u7cfb: 13341029942\uff0c\u6295\u8bc9\u7535\u8bdd: 010-86489160\u3002\u611f\u8c22\u4f7f\u7528\u5706\u901a\u901f\u9012\uff0c\u671f\u5f85\u518d\u6b21\u4e3a\u60a8\u670d\u52a1\uff01\u4e3e\u624b\u4e4b\u52b3\u52ff\u5fd8\u9001\u4ef6\u4eba\uff0c\u8bf7\u5728[\u8bc4\u4ef7\u5feb\u9012\u5458]\u5904\u8d50\u4e88\u6211\u4eec\u4e94\u661f\u597d\u8bc4~","ftime":"2023-05-04 19:50:07","areaCode":"CN110106000000","areaName":"\u5317\u4eac,\u5317\u4eac,\u4e30\u53f0\u533a","status":"\u6295\u67dc\u6216\u7ad9\u7b7e\u6536","location":"","areaCenter":"116.287149,39.858427","areaPinYin":"feng tai qu","statusCode":"304"},{"time":"2023-05-03 20:16:21","context":"\u60a8\u597d\uff0c\u5305\u88f9\u5df2\u7ecf\u5230\u8fbe\u9644\u8fd1\u8857\u533a\uff0c\u5b89\u6392\u6d3e\u9001\u4e2d\uff0c\u5982\u6709\u7591\u95ee\u8bf7\u8054\u7cfb18309599720\u3002\u611f\u8c22\u4f7f\u7528\u5706\u901a\u901f\u9012\uff0c\u671f\u5f85\u518d\u6b21\u4e3a\u60a8\u670d\u52a1\uff01","ftime":"2023-05-03 20:16:21","areaCode":null,"areaName":null,"status":"\u6d3e\u4ef6","location":"","areaCenter":null,"areaPinYin":null,"statusCode":"5"},{"time":"2023-05-03 19:40:56","context":"\u60a8\u597d\uff0c\u5feb\u4ef6\u5df2\u88ab\u5feb\u9012\u5458\u4ece[\u9012\u7ba1\u5bb6\u81ea\u63d0\u67dc]\u5bcc\u9526\u5609\u56ed\u56db\u533a3\u53f7\u697c\u897f1\u4eac\u4e1c\u5feb\u9012\u67dc\u53d6\u56de\uff0c\u8bf7\u7b49\u5f85\u5feb\u9012\u5458\u4e0e\u60a8\u8054\u7cfb\u3002\u82e5\u6709\u7591\u95ee\uff0c\u8bf7\u7535\u8054\u6d3e\u4ef6\u5458\uff0813341029942\uff09\u3002\u611f\u8c22\u4f7f\u7528\u5706\u901a\u901f\u9012\uff0c\u671f\u5f85\u518d\u6b21\u4e3a\u60a8\u670d\u52a1\uff01","ftime":"2023-05-03 19:40:56","areaCode":null,"areaName":null,"status":"\u6295\u67dc\u6216\u9a7f\u7ad9","location":"","areaCenter":null,"areaPinYin":null,"statusCode":"501"},{"time":"2023-04-30 19:26:09","context":"\u60a8\u7684\u5feb\u4ef6\u5df2\u5230\u8fbe[\u9012\u7ba1\u5bb6\u81ea\u63d0\u67dc](\u5bcc\u9526\u5609\u56ed\u56db\u533a3\u53f7\u697c\u897f1\u4eac\u4e1c\u5feb\u9012\u67dc)\u4e30\u8446\u8def\u4e16\u754c\u516c\u56ed\u5317300\u7c73\uff0c\u8bf7\u53ca\u65f6\u53d6\u4ef6\uff0c\u8054\u7cfb\u7535\u8bdd\uff1a13341029942\u3002\u611f\u8c22\u4f7f\u7528\u5706\u901a\u901f\u9012\uff0c\u671f\u5f85\u518d\u6b21\u4e3a\u60a8\u670d\u52a1\uff01","ftime":"2023-04-30 19:26:09","areaCode":null,"areaName":null,"status":"\u5728\u9014","location":"","areaCenter":null,"areaPinYin":null,"statusCode":"0"},{"time":"2023-04-30 07:10:43","context":"\u3010\u5317\u4eac\u5e02\u4e30\u53f0\u533a\u79d1\u5b66\u57ce\u3011\u7684\u738b\u6b22(13341029942)\u6b63\u5728\u6d3e\u4ef6\uff0c\u5706\u901a\u5df2\u5f00\u542f\u201c\u5b89\u5168\u547c\u53eb\u201d\u4fdd\u62a4\u60a8\u7684\u7535\u8bdd\u9690\u79c1\uff0c\u8bf7\u653e\u5fc3\u63a5\u542c\uff01[95161\u548c185211\u53f7\u6bb5\u7684\u4e0a\u6d77\u53f7\u7801\u4e3a\u5706\u901a\u4e1a\u52a1\u5458\u4e13\u5c5e\u53f7\u7801]","ftime":"2023-04-30 07:10:43","areaCode":"CN110106000000","areaName":"\u5317\u4eac,\u5317\u4eac,\u4e30\u53f0\u533a","status":"\u6d3e\u4ef6","location":"","areaCenter":"116.287149,39.858427","areaPinYin":"feng tai qu","statusCode":"5"},{"time":"2023-04-30 05:20:13","context":"\u60a8\u7684\u5feb\u4ef6\u5df2\u7ecf\u5230\u8fbe\u3010\u5317\u4eac\u5e02\u4e30\u53f0\u533a\u79d1\u5b66\u57ce\u516c\u53f8\u3011","ftime":"2023-04-30 05:20:13","areaCode":null,"areaName":null,"status":"\u5728\u9014","location":"","areaCenter":null,"areaPinYin":null,"statusCode":"0"},{"time":"2023-04-29 14:20:39","context":"\u60a8\u7684\u5feb\u4ef6\u79bb\u5f00\u3010\u534e\u5317\u8f6c\u8fd0\u4e2d\u5fc3\u3011","ftime":"2023-04-29 14:20:39","areaCode":null,"areaName":null,"status":"\u5728\u9014","location":"","areaCenter":null,"areaPinYin":null,"statusCode":"0"},{"time":"2023-04-29 13:30:39","context":"\u60a8\u7684\u5feb\u4ef6\u5df2\u7ecf\u5230\u8fbe\u3010\u534e\u5317\u8f6c\u8fd0\u4e2d\u5fc3\u516c\u53f8\u3011","ftime":"2023-04-29 13:30:39","areaCode":null,"areaName":null,"status":"\u5728\u9014","location":"","areaCenter":null,"areaPinYin":null,"statusCode":"0"},{"time":"2023-04-28 03:10:25","context":"\u60a8\u7684\u5feb\u4ef6\u79bb\u5f00\u3010\u4f5b\u5c71\u8f6c\u8fd0\u4e2d\u5fc3\u3011\uff0c\u51c6\u5907\u53d1\u5f80\u3010\u534e\u5317\u8f6c\u8fd0\u4e2d\u5fc3\u516c\u53f8\u3011","ftime":"2023-04-28 03:10:25","areaCode":"CN440600000000","areaName":"\u5e7f\u4e1c,\u4f5b\u5c71\u5e02","status":"\u5728\u9014","location":"","areaCenter":"113.121416,23.021548","areaPinYin":"fo shan shi","statusCode":"0"},{"time":"2023-04-28 03:09:40","context":"\u60a8\u7684\u5feb\u4ef6\u5df2\u7ecf\u5230\u8fbe\u3010\u4f5b\u5c71\u8f6c\u8fd0\u4e2d\u5fc3\u516c\u53f8\u3011","ftime":"2023-04-28 03:09:40","areaCode":null,"areaName":null,"status":"\u5728\u9014","location":"","areaCenter":null,"areaPinYin":null,"statusCode":"0"},{"time":"2023-04-28 00:53:41","context":"\u60a8\u7684\u5feb\u4ef6\u79bb\u5f00\u3010\u5e7f\u4e1c\u7701\u5e7f\u5dde\u5e02\u756a\u79ba\u533a\u5357\u6751\u3011\uff0c\u51c6\u5907\u53d1\u5f80\u3010\u4f5b\u5c71\u8f6c\u8fd0\u4e2d\u5fc3\u516c\u53f8\u3011","ftime":"2023-04-28 00:53:41","areaCode":"CN440113102000","areaName":"\u5e7f\u4e1c,\u5e7f\u5dde\u5e02,\u756a\u79ba\u533a,\u5357\u6751\u9547","status":"\u5728\u9014","location":"","areaCenter":"113.380238,23.00326","areaPinYin":"nan cun zhen","statusCode":"0"},{"time":"2023-04-28 00:38:31","context":"\u60a8\u7684\u5feb\u4ef6\u5df2\u7ecf\u5230\u8fbe\u3010\u5e7f\u4e1c\u7701\u5e7f\u5dde\u5e02\u756a\u79ba\u533a\u5357\u6751\u516c\u53f8\u3011","ftime":"2023-04-28 00:38:31","areaCode":null,"areaName":null,"status":"\u5728\u9014","location":"","areaCenter":null,"areaPinYin":null,"statusCode":"0"},{"time":"2023-04-27 15:08:18","context":"\u60a8\u7684\u5feb\u4ef6\u88ab\u3010\u5e7f\u4e1c\u7701\u5e7f\u5dde\u5e02\u756a\u79ba\u533a\u5357\u6751\u3011\u63fd\u6536\uff0c\u63fd\u6536\u4eba: \u5b59\u6021\u9601-T (020-34698777)","ftime":"2023-04-27 15:08:18","areaCode":"CN440113102000","areaName":"\u5e7f\u4e1c,\u5e7f\u5dde\u5e02,\u756a\u79ba\u533a,\u5357\u6751\u9547","status":"\u63fd\u6536","location":"","areaCenter":"113.380238,23.00326","areaPinYin":"nan cun zhen","statusCode":"1"}],"state":"304","condition":"F00","routeInfo":{"from":{"number":"CN440113102000","name":"\u5e7f\u4e1c,\u5e7f\u5dde\u5e02,\u756a\u79ba\u533a,\u5357\u6751\u9547"},"cur":{"number":"CN110106000000","name":"\u5317\u4eac,\u5317\u4eac,\u4e30\u53f0\u533a"},"to":{"number":"CN110106000000","name":"\u5317\u4eac,\u5317\u4eac,\u4e30\u53f0\u533a"}},"isLoop":false}', true);

        if (! isset($response['status']) || $response['status'] != Response::HTTP_OK) {
            throw new \Exception('物流信息未查询到！');
        }

        return $response['data'] ?? [];
    }

    private static function doPost(string $url, array $params): array
    {
        $host = config('custom.kuaidi100.host');

        if (! $host) {
            return [];
        }

        try {
            $client = new Client([
                'base_uri' => $host,
                'timeout' => 10,
            ]);
            $response = $client->post($url, [
                'form_params' => $params,
            ]);

            if ($response->getStatusCode() !== Response::HTTP_OK) {
                throw new \Exception('请求失败');
            }
            $content = $response->getBody()->getContents();

            if (! $content) {
                throw new \Exception('请求失败:获取 Content 为空');
            }

            return json_decode(html_entity_decode($content), true);
        } catch (\Throwable $throwable) {
            Log::error($throwable->getMessage());
            return [];
        }
    }
}
