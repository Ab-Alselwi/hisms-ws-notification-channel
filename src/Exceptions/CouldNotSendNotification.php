<?php

declare(strict_types=1);

namespace NotificationChannels\Hisms\Exceptions;


use NotificationChannels\Hisms\HismsSmsMessage;

class CouldNotSendNotification extends \Exception
{
    public static function invalidMessageObject($message): self
    {
        $className = is_object($message) ? get_class($message) : 'Unknown';

        return new static(
            "Notification was not sent. Message object class `{$className}` is invalid. It should
            be either `".HismsSmsMessage::class.'`');
    }

    public static function missingFrom(): self
    {
        return new static('Notification was not sent. Missing `from` number.');
    }

    public static function invalidReceiver(): self
    {
        return new static(
            'The notifiable did not have a receiving phone number. Add a routeNotificationForTwilio
            method or a phone_number attribute to your notifiable.'
        );
    }

 

     /**
     * Thrown when hisms.ws return a response body other than '1'.
     *
     * @param $code
     * @param $message
     *
     * @return static
     */
    public static function HismsWsRespondedWithAnError($code, $message)
    {
        return new static(
            sprintf("hisms.ws responded with error number %s and message:\n%s",
                $code,
                $message
            ));
    }

    /**
     * Thrown when GuzzleHttp throw a request exception.
     *
     * @param RequestException $exception
     *
     * @return static
     */
    public static function couldNotSendRequestToHismsWs(RequestException $exception)
    {
        return new static(
            'Request to hisms.ws failed',
            $exception->getCode(),
            $exception
        );
    }

    /**
     * Thrown when any other errors received.
     *
     * @param Response $response
     *
     * @return static
     */
    public static function someErrorWhenSendingSms(Response $response)
    {
        $code = $response->getStatusCode();
        $message = $response->getBody()->getContents();

        return new static(
            sprintf('Could not send sms notification to hisms.ws. Status code %s and message: %s', $code, $message)
        );
    }

    /**
     * Thrown when any other errors occur.
     *
     * @param $message
     *
     * @return static
     */
    public static function withErrorMessage($message)
    {
        return new static($message);
    }
}
