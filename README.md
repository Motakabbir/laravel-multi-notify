# Laravel Multi Notify

A flexible Laravel package for sending notifications via multiple channels, including SMS, email, Pusher, Firebase, and various SMS gateways.

## Author

- **Motakabbir Morshed** - [GitHub Profile](https://github.com/Motakabbir) - [dolardx@gmail.com](mailto:dolardx@gmail.com)

## Features

- Multiple notification channels (SMS, email, Pusher, Firebase)
- Integration with 40+ SMS gateway providers
- Built-in support for all major Bangladesh SMS gateways
- Support for both transactional and promotional SMS
- Push notifications via Pusher and Firebase
- Easy-to-use API
- Built-in queueing support for all notifications
- Database logging of all notification attempts and responses
- Queue management and bulk notifications
- Customizable configuration
- Extensible architecture

## Installation

You can install the package via composer:

```bash
composer require laravel-multi-notify
```

## Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --provider="LaravelMultiNotify\MultiNotifyServiceProvider"
```

## Usage

### Sending SMS

```php
use LaravelMultiNotify\Facades\MultiNotify;

// Queue SMS using default gateway (recommended)
MultiNotify::sms('1234567890', 'Your message here');

// Send SMS immediately using specific gateway
MultiNotify::sms('1234567890', 'Your message here', 'twilio', false);

// Queue bulk SMS
MultiNotify::sms(['1234567890', '0987654321'], 'Your message here');

// All notifications are logged to the database automatically
```

### Sending Push Notifications

```php
use LaravelMultiNotify\Facades\MultiNotify;

// Queue push notification using default service (recommended)
MultiNotify::push('device_token', [
    'title' => 'Notification Title',
    'body' => 'Notification Body',
    'data' => [
        'key' => 'value'
    ]
]);

// Send push notification immediately using specific service
MultiNotify::push('device_token', [
    'title' => 'Notification Title',
    'body' => 'Notification Body'
], 'firebase', false);

// View notification logs
use LaravelMultiNotify\Models\NotificationLog;
$logs = NotificationLog::latest()->get();
```

### Using Different SMS Gateways

You can use any of the supported gateways by specifying the gateway name as the third parameter in the `sms()` method:

```php
use LaravelMultiNotify\Facades\MultiNotify;

// Using Bangladesh Gateways
MultiNotify::sms('1234567890', 'Your message', 'ajuratech');
MultiNotify::sms('1234567890', 'Your message', 'ssl');
MultiNotify::sms('1234567890', 'Your message', 'grameenphone');
MultiNotify::sms('1234567890', 'Your message', 'banglalink');

// Using International Gateways
MultiNotify::sms('1234567890', 'Your message', 'twilio');
MultiNotify::sms('1234567890', 'Your message', 'aws-sns');
MultiNotify::sms('1234567890', 'Your message', 'textlocal');

// Using Iranian Gateways
MultiNotify::sms('1234567890', 'Your message', 'kavenegar');
MultiNotify::sms('1234567890', 'Your message', 'mellipayamak');
```

### Gateway Configuration

Each gateway requires specific configuration in your `.env` file. Here are examples for commonly used gateways:

```env
# Twilio Configuration
TWILIO_SID=your_twilio_sid
TWILIO_AUTH_TOKEN=your_auth_token
TWILIO_FROM=your_twilio_number

# SSL Wireless Configuration
SSL_API_TOKEN=your_ssl_api_token
SSL_SID=your_ssl_sid
SSL_DOMAIN=https://smsplus.sslwireless.com

# Grameenphone Configuration
GP_USERNAME=your_gp_username
GP_PASSWORD=your_gp_password
GP_FROM=your_gp_sender_id

# AWS SNS Configuration
AWS_ACCESS_KEY_ID=your_aws_access_key
AWS_SECRET_ACCESS_KEY=your_aws_secret
AWS_DEFAULT_REGION=your_aws_region

# Banglalink Configuration
BANGLALINK_USERNAME=your_banglalink_username
BANGLALINK_PASSWORD=your_banglalink_password
BANGLALINK_FROM=your_sender_id
```

### Setting Default Gateway

You can set your default gateway in the `config/multi-notify.php` file:

```php
'default' => [
    'sms' => env('DEFAULT_SMS_GATEWAY', 'ssl'),
    'push' => env('DEFAULT_PUSH_SERVICE', 'firebase'),
],
```

### Bulk SMS with Specific Gateway

You can send bulk SMS using any gateway:

```php
// Send same message to multiple numbers
MultiNotify::sms(['1234567890', '0987654321'], 'Your message', 'ssl');

// Send different messages to different numbers
MultiNotify::sms([
    '1234567890' => 'Message for first user',
    '0987654321' => 'Message for second user'
], null, 'grameenphone');
```

### Gateway Response Handling

All gateway responses are automatically logged. You can access the logs:

```php
use LaravelMultiNotify\Models\NotificationLog;

// Get all notifications sent through a specific gateway
$logs = NotificationLog::where('gateway', 'ssl')->get();

// Get failed notifications
$failed = NotificationLog::where('status', 'failed')->get();

// Get successful notifications
$success = NotificationLog::where('status', 'success')->get();
```

## Configuration

The package can be configured via the `config/multi-notify.php` configuration file. Here you can set:

### Available SMS Gateways

#### International Gateways
- AWS SNS
- Twilio
- Textlocal
- Clockwork
- LINK Mobility
- SMS Gateway Me
- SmsGateWay24
- Sms77
- D7networks
- SMSApi

#### Iranian Gateways
- Kavenegar
- Melipayamak
- Melipayamak Pattern
- Smsir
- Tsms
- Farazsms
- Farazsms Pattern
- Ghasedak
- SabaPayamak
- LSim
- Rahyabcp
- Rahyabir
- Hamyarsms

#### Bangladesh SMS Gateways

- AjuraTech
- Adn
- Alpha
- Banglalink
- BDBulkSMS
- BoomCast
- BulksmsBD
- DhorolaSms
- DianaHost
- DianaSMS
- DurjoySoft
- ElitBuzz
- Esms
- Grameenphone
- Infobip
- Lpeek
- MDL
- Metronet
- MimSms
- Mobireach
- Muthofun
- NovocomBD
- OnnoRokomSMS
- QuickSms
- RedmoITSms
- SendMySms
- SmartLabSMS
- Sms4BD
- SmsBangladesh
- SmsinBD
- SMS.net.bd
- SmsQ
- SMSNet24
- SmsNoc
- SongBird
- Sslsms
- Tense
- Twenty4BulkSms
- TwentyFourBulkSmsBD
- Trubosms
- Viatech
- WinText
- ZamanIT

To use any of these gateways, configure their credentials in your .env file and set the gateway in your config:

- Default notification channel
- SMS gateway configurations
- Push notification service configurations
- Email settings
- Logging preferences

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This package is open-sourced software licensed under the MIT license.
