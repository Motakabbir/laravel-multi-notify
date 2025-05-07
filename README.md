# 📬 Laravel Multi Notify

![Laravel Multi Notify](https://img.shields.io/badge/Laravel-Multi--Notify-blueviolet?style=flat-square) ![Packagist Version](https://img.shields.io/packagist/v/laravel-multi-notify?style=flat-square) ![License](https://img.shields.io/github/license/Motakabbir/laravel-multi-notify?style=flat-square) ![Build Status](https://img.shields.io/github/actions/workflow/status/Motakabbir/laravel-multi-notify/tests.yml?branch=main&style=flat-square)

🚀 **Laravel Multi Notify** is an advanced and versatile notification package for the Laravel framework. Designed to streamline messaging, it supports **multiple channels** like SMS, email, Pusher, Firebase, and more than **40 SMS gateways**, making it the ultimate package for robust notification systems.

Whether you're sending transactional alerts, promotional campaigns, or push notifications, Laravel Multi Notify provides an **extensive suite of tools** to meet your needs.

---

## 🌟 Key Features

- **Multi-Channel Support**: Send notifications via SMS, email, Pusher, or Firebase.
- **Over 40 SMS Gateways**: Seamless integration with international and regional gateways, including popular Bangladeshi providers.
- **Transactional & Promotional SMS**: Flexibility to send both types of SMS efficiently.
- **Push Notifications**: Integration with Pusher and Firebase for reliable push services.
- **Built-In Queue Management**: Handle bulk notifications and queue processes effortlessly.
- **Database Logging**: Automatically log notification attempts and responses.
- **Customizable & Extensible**: Modify and extend the package to fit your specific requirements.
- **Developer-Friendly APIs**: Clean and intuitive APIs for quick implementation.

---

## 🛠️ Installation

Install the package via Composer:

```bash
composer require laravel-multi-notify
```

---

## ⚙️ Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --provider="LaravelMultiNotify\MultiNotifyServiceProvider"
```

Set up your `.env` file with the required gateway configurations. For example, for Twilio:

```env
TWILIO_SID=your_twilio_sid
TWILIO_AUTH_TOKEN=your_auth_token
TWILIO_FROM=your_twilio_number
```

Configure the default gateway in the `config/multi-notify.php` file:

```php
'default' => [
    'sms' => env('DEFAULT_SMS_GATEWAY', 'ssl'),
    'push' => env('DEFAULT_PUSH_SERVICE', 'firebase'),
],
```

---

## 🚀 Quick Start Guide

### Sending SMS Notifications

```php
use LaravelMultiNotify\Facades\MultiNotify;

// Send an SMS via the default gateway
MultiNotify::sms('1234567890', 'Your message here');

// Use a specific SMS gateway
MultiNotify::sms('1234567890', 'Your message here', 'twilio', false);

// Send bulk SMS
MultiNotify::sms(['1234567890', '0987654321'], 'Your message here');
```

### Sending Push Notifications

```php
use LaravelMultiNotify\Facades\MultiNotify;

MultiNotify::push('device_token', [
    'title' => 'Notification Title',
    'body' => 'Notification Body',
    'data' => ['key' => 'value']
]);
```

---

## 🌍 Supported Gateways

Laravel Multi Notify integrates with a wide range of SMS gateways, enabling global and regional messaging. Below is a comprehensive list of supported gateways:

### **International Gateways**
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

### **Bangladeshi SMS Gateways**
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

### **Iranian Gateways**
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

---

## 📚 Comprehensive Documentation

Discover how to leverage Laravel Multi Notify for your projects. Our documentation includes:

- **Installation Guides**: Step-by-step instructions for installing and configuring the package.
- **Usage Examples**: Practical examples for sending SMS, email, and push notifications.
- **Advanced Features**: Learn about queue management, bulk notifications, and database logging.
- **Custom Gateway Configuration**: Tailor the package to integrate with your preferred SMS or push services.

Check the [Laravel Multi Notify documentation](#) for detailed information.

---

## 💡 Why Use Laravel Multi Notify?

1. **Global Reach**: Integrates with SMS gateways worldwide.
2. **Scalability**: Handles millions of notifications seamlessly.
3. **Flexibility**: Easily switch between gateways and notification channels.
4. **Developer-Friendly**: Clean API design with extensive logging for debugging.
5. **Open Source**: Fully customizable under the MIT license.

---

## 🤝 Contributing

We welcome contributions to make Laravel Multi Notify even better! Feel free to:

- Fork the repository
- Submit issues
- Create pull requests

---

## 📄 License

Laravel Multi Notify is open-source software licensed under the [MIT license](LICENSE).

---

## 🔗 Get in Touch

- **Author**: [Motakabbir Morshed](https://github.com/Motakabbir)
- **Email**: [dolardx@gmail.com](mailto:dolardx@gmail.com)

---

## 📈 Keywords

Laravel Multi Notify, Laravel SMS package, Laravel push notifications, Laravel email notifications, Laravel SMS gateways, AWS SNS Laravel, Twilio Laravel, Pusher Laravel, Firebase Laravel, Laravel notification system, transactional SMS Laravel, promotional SMS Laravel, Laravel queue notifications.

---