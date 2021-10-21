<?php

namespace NotificationChannels\HismsWs;

use Illuminate\Events\Dispatcher;
use Illuminate\Notifications\Events\NotificationFailed;
use Illuminate\Notifications\Notification;
use NotificationChannels\Hisms\Exceptions\CouldNotSendNotification;

class HismsWsChannel
{
    /** @var HismsWsApi */
    private $api;

    /** @var Dispatcher */
    private $events;

    /**
     * HismsWsChannel constructor.
     *
     * @param HismsWsApi                   $HismsWs
     * @param \Illuminate\Events\Dispatcher $events
     */
    public function __construct(HismsWsService $hismsWs, Dispatcher $events)
    {
        $this->api = $hismsWs;
        $this->events = $events;
    }

    /**
     * Send the given notification.
     *
     * @param mixed        $notifiable
     * @param Notification $notification
     *
     * @return string
     *
     * @throws \NotificationChannels\HismsWs\Exceptions\CouldNotSendHismsWsNotification
     */
    public function send($notifiable, Notification $notification)
    {
        try {
            
            if (!method_exists($notification, 'toHismsWs')) {
                throw CouldNotSendNotification::withErrorMessage('HismsWs notifications must have toHismsWs method');
            }
            $message = $notification->toHismsWs($notifiable, new HismsWsSmsMessage());
            $number = $notifiable->routeNotificationFor('HismsWs') ?: $notifiable->phone_number;
            // TODO Validate Number
            $response = [];$this->api->sendMessage($message, $number);
            return $response;
           // $response = $this->dispatchRequest($message, $number);
            //dd($response);
            if ($response['code'] == 1) {
                return $response['message'];
            }
    

         } catch (Exception $exception) {
            $event = new NotificationFailed(
                $notifiable,
                $notification,
                'hismsWs',
                ['message' => $exception->getMessage(), 'exception' => $exception]
            );

            $this->events->dispatch($event);

            if ($this->api->config->isIgnoredErrorCode($exception->getCode())) {
                return;
            }

            throw $exception;
        }
    }

    /**
     * @param $message
     * @param $number
     *
     * @return array
     *
     * @throws CouldNotSendNotification
     */
    private function dispatchRequest($message, $number)
    {
        if (is_string($message)) {
        //    dd($message);
            $response = $this->api->sendString([
                'msg' => $message,
                'numbers' => $number,
            ]);

        } elseif ($message instanceof HismsWsSmsMessage) {
           // dd($message, $number);

            $response = $this->api->sendMessage($message, $number);
        } else {
            $errorMessage = sprintf('toHismsWs must return a string or instance of %s. Instance of %s returned',
                HismsWsSmsMessage::class,
                gettype($message)
            );
            dd($errorMessage);
            throw CouldNotSendNotification::withErrorMessage($errorMessage);
        }

        return $response;
    }
}
