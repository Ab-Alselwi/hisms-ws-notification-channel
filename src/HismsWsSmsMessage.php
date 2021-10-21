<?php

namespace NotificationChannels\HismsWs;

use Carbon\Carbon;
use DateTime;
use DateTimeInterface;
use NotificationChannels\Hisms\Exceptions\CouldNotSendNotification;

class HismsWsSmsMessage
{
    /** @var string */
    public $text;

    /** @var Carbon */
    public $time;

    /**
     * Create new instance of HismsWsMessage.
     *
     * @param string $text
     *
     * @return static
     */
    public static function create($text = '')
    {
        return new static($text);
    }

    /**
     * HismsWsMessage constructor.
     *
     * @param string $text
     */
    public function __construct($text = '')
    {
        $this->text = $text;
    }

    /**
     * Set the Content of the SMS message.
     *
     * @param $text
     *
     * @return $this
     */
    public function text($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Set the message scheduled date and time.
     *
     * @param DateTime|Carbon|int $time
     *
     * @return $this
     *
     * @throws CouldNotSendHismsWsNotification
     */
    public function time($time)
    {
        if ($time instanceof DateTimeInterface) {
             //dd('instanceof',$time);
            return $this->time($time->getTimestamp());
        }
        
        if (is_numeric($time)) {
            $this->time = Carbon::createFromTimestamp($time);
          //  dd('numric',$time);
            return $this;
        }
       // dd($time);
        throw CouldNotSendNotification::withErrorMessage(
            sprintf('Time must be a timestamp or an object implementing DateTimeInterface. %s is given', gettype($time))
        );
    }

    /**
     * Get the message schedule date.
     *
     * @return string
     */
    public function dateSend()
    {
        if ($this->time) {
            return $this->time->format('m/d/Y');
        }
    }

    /**
     * Get the message schedule time.
     *
     * @return string
     */
    public function timeSend()
    {
        if ($this->time) {
            return $this->time->format('H:i:s');
        }
    }
}
