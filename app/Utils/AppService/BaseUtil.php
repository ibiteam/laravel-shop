<?php

namespace App\Utils\AppService;

use App\Exceptions\BusinessException;
use App\Models\AppServiceConfig;
use App\Models\AppServiceLog;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseUtil
{
    protected AppServiceConfig $config;

    protected int $user_id;

    public function __construct(int $user_id)
    {
        $this->user_id = $user_id;
        $this->config = AppServiceConfig::where('alias', static::getAlias())->firstOrFail();
    }

    /**
     * get config model.
     */
    final public function getConfig(): ?AppServiceConfig
    {
        return $this->config;
    }

    /**
     * get app service settings.
     */
    final public function getSettings(): array
    {
        return $this->config->config;
    }

    /**
     * get the request host.
     */
    public function host(): string
    {
        return $this->getSettings()['host'] ?? '';
    }

    /**
     * get alias config.
     */
    abstract protected static function getAlias(): string;

    /**
     * add request log.
     */
    protected function addLog($user_id, $request_data, $result_data): bool
    {
        if ($this->config->is_record) {
            AppServiceLog::create([
                'user_id' => $user_id,
                'request_param' => json_encode($request_data, JSON_UNESCAPED_UNICODE),
                'result_data' => json_encode($result_data, JSON_UNESCAPED_UNICODE),
                'service_id' => $this->config->id,
            ]);
        }

        return true;
    }

    /**
     * get request log count.
     *
     * @param int $user_id request user id
     */
    protected function canRequest(int $user_id): bool
    {
        // check config is enabled
        if (! $this->config->is_enable) {
            return false;
        }

        // only record logs can get log count
        if ($this->config->is_record) {
            // every day can send requests
            $count = AppServiceLog::query()
                ->whereUserId($user_id)
                ->whereServiceId($this->config->id)
                ->whereDate('created_at', date('Y-m-d'))
                ->count();

            $error_number = (int) $this->config->error_number;
            $stop_number = (int) $this->config->stop_number;

            if ($error_number > 0 && $count >= $error_number) {
                $message = $this->config->name.'请求异常,user_id:'.$user_id.',已请求'.$count.'次，请及时处理！';
                Log::error($message);
            }

            if ($stop_number > 0 && $count >= $stop_number) {
                $message = $this->config->name.'请求超出阀值,user_id:'.$user_id.',已请求'.$count.'次，请求已关闭！';
                Log::error($message);

                return false;
            }
        }

        return true;
    }

    /**
     * send post request.
     *
     * @throws BusinessException
     * @throws GuzzleException
     */
    protected function doPost(string $url, array $data, bool $can_record_log = true, array $config = []): mixed
    {
        return $this->doRequest($url, $data, 'POST', $can_record_log, $config);
    }

    /**
     * send get request.
     *
     * @throws BusinessException
     * @throws GuzzleException
     */
    protected function doGet(string $url, array $data, bool $can_record_log = true): mixed
    {
        return $this->doRequest($url, $data, 'GET', $can_record_log);
    }

    /**
     * do request.
     *
     * @param string $url            request url
     * @param array  $data           request params
     * @param string $method         request method
     * @param bool   $can_record_log can record request log
     * @param array  $config         request config
     *
     * @throws BusinessException
     * @throws GuzzleException
     */
    private function doRequest(string $url, array $data, string $method = 'post', bool $can_record_log = true, array $config = []): mixed
    {
        $host = $this->host();

        if (! $host) {
            return false;
        }

        if ($this->canRequest($this->user_id)) {
            try {
                $client_default = [
                    'timeout' => 30,
                    'base_uri' => $host,
                ];
                $config = array_merge($client_default, $config);
                $client = new Client($config);
                $res = $client->request($method, $url, $data);

                if ($res->getStatusCode() === Response::HTTP_OK) {
                    $result = json_decode($res->getBody()->getContents(), true);

                    if ($can_record_log) {
                        $this->addLog($this->user_id, $data, $result);
                    }

                    return $result;
                }

                throw new BusinessException('服务器请求出错了~');
            } catch (BusinessException $api_exception) {
                throw new BusinessException($api_exception->getMessage());
            } catch (RequestException $request_exception) {
                throw $request_exception;
            } catch (\Exception $exception) {
                $message = '请求'.$this->config->name.'接口失败错误信息：'.$exception->getMessage();
                Log::info($message, $exception->getTrace());

                throw new BusinessException('服务器请求出错了');
            }
        }

        return false;
    }
}
