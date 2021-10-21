<?php

namespace NotificationChannels\HismsWs;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\RequestException;
use NotificationChannels\HismsWs\Exceptions\CouldNotSendHismsWsNotification;

class HismsWsService
{
    
    
    /** @var HismsWsConfig */
    private $config;

    /** @var HttpClient */
    private $http;

    /**
     * Create a new HismsWs channel instance.
     *
     * @param HismsWsConfig $config
     * @param HttpClient     $http
     */
    public function __construct(HismsWsConfig $config, HttpClient $http)
    {
        //dd($http);
        $this->http = $http;
        $this->config = $config;
    }
    
    /**
     * Send request with string message
     *
     * @param $params
     *
     * @return array
     */
    public function sendString($params)
    {
        $payload = $this->preparePayload($params);
        //info($payload);
        return $this->send($payload);
    }
    
    /**
     * Send request with HismsWsSmsMessage instance
     *
     * @param HismsWsSmsMessage $message
     *
     * @param                 $number
     *
     * @return array
     * @internal param $params
     */
    public function sendMessage(HismsWsSmsMessage $message, $number)
    {
        
        $params = [
            'message' => $message->text,
            'numbers' => $number,
        ];
        
        $payload = $this->preparePayload($params);
        return $this->send($payload);
    }

    public function getEndpoint()
    {
       return  $this->config->guzzle['client']['base_uri'];
    }

    /**
     * Send request to hisms.ws
     *
     * @param array $payload
     *
     * @return array
     * @throws \NotificationChannels\HismsWs\Exceptions\CouldNotSendHismsWsNotification
     * @internal param array $params
     *
     */
    public function send(array $payload)
    {
         
        try {
          // dd ($this->endpoint, $payload);
          // $query=http_build_query($data);
            $query=$this->getEndpoint().http_build_query($payload);

            $response = $this->http->get($query);

            if ($response->getStatusCode() == 200) {
                return [
                    'code' => $code = (string) $response->getBody(),
                    'message' => $this->msgSendResponse($code),
                ];
            }
            throw CouldNotSendNotification::someErrorWhenSendingSms($response);
        } catch (RequestException $exception) {
            throw CouldNotSendNotification::couldNotSendRequestToHismsWs($exception);
        }
    }

    /**
     * Prepare payload for http request.
     *
     * @param $params
     *
     * @return array
     */
    protected function preparePayload($params)
    {
       // dd($this->config);
        $form = array_merge([
            'sender' => $this->config->sender,
        ], $params, $this->config->getCredentials());
        return $form;
     
    }

    /**
     * Parse the response body from hisms.ws.
     *
     * @param $code
     *
     * @return string
     */
    protected function msgSendResponse($code)
    {
        $arraySendMsg = [1-2,403,404,504];
        
        if (array_key_exists($code, $arraySendMsg)) {
            return trans('hismsws.'.$code);
        }
        $message = trans('hismsws.result_message');
        $message .= trans('hismsws.result_message_2');
        $message .= "{$code}";

        return $message;
    }
}
