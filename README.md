# Laravel Hisms.ws Notification Channel


This package makes it easy to send notifications using [HismsWs](https://www.hisms.ws) with Laravel 8.x.

This package was inspired from :
- [twilio](https://github.com/laravel-notification-channels/twilio)
- [laravel-mobily-ws-notification](https://github.com/alhoqbani/laravel-mobily-ws-notification)


## Contents

- [Installation](#installation)
	- [Package Installation](#package-installation)
	- [Set up hisms.ws account](#set-up-hisms.ws-account)
- [Usage](#usage)
	- [Credentials](#credentials)
	- [Create Notification](#create-notification)
	- [Routing SMS Notifications](#routing-sms-notifications)
	- [Sending SMS](#sending-sms)




## Installation

### Package Installation

Install the package using composer:
```bash
composer require ab-alselwi/hisms-ws-notification-channel
```
Add service provider to your array of providers in `config/app.php` 
> You don't need to do this step for laravel 8.0+
```php
        NotificationChannels\HismsWs\HismsWsServiceProvider::class,
```
Publish the configuration file:
```bash
php artisan vendor:publish --provider="NotificationChannels\HismsWs\HismsWsServiceProvider"
```

### Set up hisms.ws account
You must have an account with [HismsWs](https://www.hisms.ws)  to be able to use this package.



#### Credentials.
There are two methods of authentication when using hisms.ws api. 

You could send requests using your login credentials (mobile/password),
 or by using the apiKey which you can generate from your hisms.ws account.
 
You must add hisms.ws credentials to your `.env` file.
```
// Mobile number and password used for log in.
HISMS_WS_MOBILE= 
HISMS_WS_PASSWORD=

// name/number of the sender which must be approved by hisms.ws for GCC
HISMS_WS_SENDER=
```
##### Which method to use:
You can define the authentication method you would like to use 
by editing your `config/hismsWs` file.


```php

// config/hismsws


    
     // Set yor login credentials to communicate with hisms.ws Api
    'mobile' => env('HISMS_WS_MOBILE'),
    'password' =>  env('HISMS_WS_PASSWORD'),
    
    // Name of Sender must be approved by hisms.ws
    'sender' => env('HISMS_WS_SENDER'),

```

## Usage

### Create new notification:
Make a new notification class using laravel artisan
```bash
php artisan make:notification UserRegistered
``` 
and configure the notification class to use HismsWsChannel.


The `toHismsWs` method should return a string of the text message to be sent or an instance of `HismsWsMessage`.

```php
<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use NotificationChannels\HismsWs\HismsWsChannel;
use NotificationChannels\HismsWs\HismsWsSmsMessage;

class UserRegistered extends Notification
{
    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [HismsWsChannel::class];
    }
    
    /**
     * Get the text message representation of the notification
     *
     * @param  mixed      $notifiable
     * @param \NotificationChannels\HismsWs\HismsWsSmsMessage $msg
     *
     * @return \NotificationChannels\HismsWs\HismsWsSmsMessage|string
     */
    public function toHismsWs($notifiable, HismsWsSmsMessage $msg)
    {
        return "Dear $notifiable->name, welcome to our website";
    }
}
```

### Routing SMS Notifications:

When sending notifications via the `HismsWs` channel, the notification system will automatically look for a `phone_number` attribute on the notifiable entity.
If you would like to customize the phone number the notification is delivered to, define a `routeNotificationForHismsWs` method on the entity:

```php
<?php

    namespace App;

    use Illuminate\Notifications\Notifiable;
    use Illuminate\Foundation\Auth\User as Authenticatable;

    class User extends Authenticatable
    {
        use Notifiable;

        /**
         * Route notifications for the HismsWs channel.
         *
         * @return string
         */
        public function routeNotificationForHismsWs()
        {
            return $this->mobile;
        }
    }
```
`routeNotificationForHismsWs` should return a mobile number to which the SMS message will be sent.

Please note that the mobile number must start with the country code without leading zeros.

For example, `9665xxxxxxxx`

### Sending SMS:
```php
use App\Notifications\UserRegistered;

$user = App\User::first();

$user->notify(new UserRegistered());
```

### Scheduled SMS
[HismsWs](https://www.hisms.ws) Api allows for sending scheduled message which will be sent on the defined date/time.

> Please note that if you define time in the past, the message will be sent immediately by hismsws. 
This library will not check if the defined time is in the future.

You can define the time on which the message should be sent by hisms.ws by calling `time` method on the HismsWsMessage instance.
```php
    public function toHismsWs($notifiable)
    {
        return (new HismsWsSmsMessage)
            ->text("Message text")
            ->time(Carbon::parse("+1 week);
    }
```
The `time` method accepts either a DateTime object or a timestamp.


## Testing

``` bash
$ composer test
```


## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.