<?php

use LaravelMultiNotify\Gateways\SMS\International\{
    AwsSnsGateway,
    TwilioGateway,
    TextlocalGateway,
    ClockworkGateway,
    LinkMobilityGateway,
    SmsGatewayMeGateway,
    Sms77Gateway,
    D7networksGateway,
    SmsApiGateway,
    SemaphoreGateway,
    GlobeLabsGateway,
    ChikkaSMSGateway,
    AfricellGambiaGateway,
    QcellGambiaGateway
};
use LaravelMultiNotify\Gateways\SMS\Iran\{
    KavenegarGateway,
    MelipayamakGateway,
    SmsirGateway,
    FarazsmsGateway,
    GhasedakGateway,
    MelipayamakPatternGateway,
    TsmsGateway,
    FarazsmsPatternGateway,
    SabaPayamakGateway,
    LSimGateway,
    RahyabcpGateway,
    RahyabirGateway,
    HamyarsmsGateway
};
use LaravelMultiNotify\Gateways\SMS\BD\{
    AjuraTechGateway,
    AdnGateway,
    AlphaGateway,
    BanglalinkGateway,
    BDBulkSMSGateway,
    BoomCastGateway,
    BulksmsBDGateway,
    DhorolaSmsGateway,
    DianaHostGateway,
    DianaSMSGateway,
    DurjoySoftGateway,
    ElitBuzzGateway,
    EsmsGateway,
    GrameenphoneGateway,
    InfobipGateway,
    LpeekGateway,
    MDLGateway,
    MetronetGateway,
    MimSmsGateway,
    MobireachGateway,
    MuthofunGateway,
    NovocomBDGateway,
    OnnoRokomSMSGateway,
    QuickSmsGateway,
    RedmoITSmsGateway,
    SendMySmsGateway,
    SmartLabSMSGateway,
    Sms4BDGateway,
    SmsBangladeshGateway,
    SmsinBDGateway,
    SMSNet24Gateway,
    SMSNetBDGateway,
    SmsNocGateway,
    SmsQGateway,
    SongBirdGateway,
    SslsmsGateway,
    TenseGateway,
    TrubosmsGateway,
    Twenty4BulkSmsGateway,
    TwentyFourBulkSmsBDGateway,
    ViatechGateway,
    WinTextGateway,
    ZamanITGateway
};
use LaravelMultiNotify\Gateways\Push\{
    FirebaseGateway,
    PusherGateway
};
use LaravelMultiNotify\Gateways\Email\EmailGateway;

return [
    'default' => env('NOTIFY_DEFAULT_CHANNEL', 'email'),

    'sms' => [
        'default' => env('SMS_GATEWAY', 'twilio'),

        'gateways' => [
            'aws_sns' => [
                'key' => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
                'region' => env('AWS_DEFAULT_REGION'),
                'sender_id' => env('AWS_SNS_SENDER_ID'),
                'class' => AwsSnsGateway::class,
            ],
            'twilio' => [
                'sid' => env('TWILIO_SID'),
                'token' => env('TWILIO_TOKEN'),
                'from' => env('TWILIO_FROM'),
                'class' => TwilioGateway::class,
            ],
            'textlocal' => [
                'apikey' => env('TEXTLOCAL_API_KEY'),
                'sender' => env('TEXTLOCAL_SENDER'),
                'class' => TextlocalGateway::class,
            ],
            'clockwork' => [
                'api_key' => env('CLOCKWORK_API_KEY'),
                'from' => env('CLOCKWORK_FROM'),
                'class' => ClockworkGateway::class,
            ],
            'link_mobility' => [
                'api_key' => env('LINK_MOBILITY_API_KEY'),
                'sender' => env('LINK_MOBILITY_SENDER'),
                'class' => LinkMobilityGateway::class,
            ],
            'sms_gateway_me' => [
                'api_key' => env('SMS_GATEWAY_ME_API_KEY'),
                'device_id' => env('SMS_GATEWAY_ME_DEVICE_ID'),
                'class' => SmsGatewayMeGateway::class,
            ],
            // SmsGateway24 configuration is temporarily disabled as the gateway class is not implemented yet
            'sms77' => [
                'api_key' => env('SMS77_API_KEY'),
                'from' => env('SMS77_FROM'),
                'class' => Sms77Gateway::class,
            ],
            'd7networks' => [
                'api_token' => env('D7NETWORKS_API_TOKEN'),
                'class' => D7networksGateway::class,
            ],
            'smsapi' => [
                'access_token' => env('SMSAPI_ACCESS_TOKEN'),
                'sender' => env('SMSAPI_SENDER'),
                'class' => SmsApiGateway::class,
            ],
            'semaphore' => [
                'api_key' => env('SEMAPHORE_API_KEY'),
                'sender_name' => env('SEMAPHORE_SENDER_NAME'),
                'class' => SemaphoreGateway::class,
            ],
            'globe_labs' => [
                'access_token' => env('GLOBE_LABS_ACCESS_TOKEN'),
                'sender_address' => env('GLOBE_LABS_SENDER_ADDRESS'),
                'class' => GlobeLabsGateway::class,
            ],
            'chikka' => [
                'client_id' => env('CHIKKA_CLIENT_ID'),
                'secret_key' => env('CHIKKA_SECRET_KEY'),
                'shortcode' => env('CHIKKA_SHORTCODE'),
                'class' => ChikkaSMSGateway::class,
            ],
            'africell_gambia' => [
                'api_token' => env('AFRICELL_GAMBIA_API_TOKEN'),
                'sender_id' => env('AFRICELL_GAMBIA_SENDER_ID'),
                'class' => AfricellGambiaGateway::class,
            ],
            'qcell_gambia' => [
                'username' => env('QCELL_GAMBIA_USERNAME'),
                'password' => env('QCELL_GAMBIA_PASSWORD'),
                'sender_id' => env('QCELL_GAMBIA_SENDER_ID'),
                'class' => QcellGambiaGateway::class,
            ],
            'kavenegar' => [
                'api_key' => env('KAVENEGAR_API_KEY'),
                'sender' => env('KAVENEGAR_SENDER'),
                'class' => KavenegarGateway::class,
            ],
            'melipayamak' => [
                'username' => env('MELIPAYAMAK_USERNAME'),
                'password' => env('MELIPAYAMAK_PASSWORD'),
                'class' => MelipayamakGateway::class,
            ],
            'smsir' => [
                'api_key' => env('SMSIR_API_KEY'),
                'secret_key' => env('SMSIR_SECRET_KEY'),
                'line_number' => env('SMSIR_LINE_NUMBER'),
                'class' => SmsirGateway::class,
            ],
            'farazsms' => [
                'username' => env('FARAZSMS_USERNAME'),
                'password' => env('FARAZSMS_PASSWORD'),
                'from' => env('FARAZSMS_FROM'),
                'class' => FarazsmsGateway::class,
            ],
            'ghasedak' => [
                'api_key' => env('GHASEDAK_API_KEY'),
                'line_number' => env('GHASEDAK_LINE_NUMBER'),
                'class' => GhasedakGateway::class,
            ],
            'melipayamak_pattern' => [
                'username' => env('MELIPAYAMAK_USERNAME'),
                'password' => env('MELIPAYAMAK_PASSWORD'),
                'class' => MelipayamakPatternGateway::class,
            ],
            'tsms' => [
                'username' => env('TSMS_USERNAME'),
                'password' => env('TSMS_PASSWORD'),
                'class' => TsmsGateway::class,
            ],
            'farazsms_pattern' => [
                'username' => env('FARAZSMS_USERNAME'),
                'password' => env('FARAZSMS_PASSWORD'),
                'pattern_code' => env('FARAZSMS_PATTERN_CODE'),
                'class' => FarazsmsPatternGateway::class,
            ],
            'sabapayamak' => [
                'username' => env('SABAPAYAMAK_USERNAME'),
                'password' => env('SABAPAYAMAK_PASSWORD'),
                'class' => SabaPayamakGateway::class,
            ],
            'lsim' => [
                'api_key' => env('LSIM_API_KEY'),
                'class' => LSimGateway::class,
            ],
            'rahyabcp' => [
                'username' => env('RAHYABCP_USERNAME'),
                'password' => env('RAHYABCP_PASSWORD'),
                'class' => RahyabcpGateway::class,
            ],
            'rahyabir' => [
                'username' => env('RAHYABIR_USERNAME'),
                'password' => env('RAHYABIR_PASSWORD'),
                'class' => RahyabirGateway::class,
            ],
            'hamyarsms' => [
                'api_key' => env('HAMYARSMS_API_KEY'),
                'class' => HamyarsmsGateway::class,
            ],
            'ajuratech' => [
                'api_key' => env('AJURATECH_API_KEY'),
                'sender_id' => env('AJURATECH_SENDER_ID'),
                'class' => AjuraTechGateway::class,
            ],
            'adn' => [
                'api_key' => env('ADN_API_KEY'),
                'api_secret' => env('ADN_API_SECRET'),
                'class' => AdnGateway::class,
            ],
            'alpha' => [
                'api_key' => env('ALPHA_API_KEY'),
                'sender_id' => env('ALPHA_SENDER_ID'),
                'class' => AlphaGateway::class,
            ],
            'banglalink' => [
                'username' => env('BANGLALINK_USERNAME'),
                'password' => env('BANGLALINK_PASSWORD'),
                'class' => BanglalinkGateway::class,
            ],
            'bdbulksms' => [
                'api_key' => env('BDBULKSMS_API_KEY'),
                'sender_id' => env('BDBULKSMS_SENDER_ID'),
                'class' => BDBulkSMSGateway::class,
            ],
            'boomcast' => [
                'username' => env('BOOMCAST_USERNAME'),
                'password' => env('BOOMCAST_PASSWORD'),
                'masking' => env('BOOMCAST_MASKING'),
                'class' => BoomCastGateway::class,
            ],
            'bulksmsbd' => [
                'api_key' => env('BULKSMSBD_API_KEY'),
                'sender_id' => env('BULKSMSBD_SENDER_ID'),
                'class' => BulksmsBDGateway::class,
            ],
            'grameenphone' => [
                'username' => env('GP_USERNAME'),
                'password' => env('GP_PASSWORD'),
                'sender_id' => env('GP_SENDER_ID'),
                'class' => GrameenphoneGateway::class,
            ],
            'infobip' => [
                'username' => env('INFOBIP_USERNAME'),
                'password' => env('INFOBIP_PASSWORD'),
                'class' => InfobipGateway::class,
            ],
            'ssl_wireless' => [
                'api_token' => env('SSL_API_TOKEN'),
                'sid' => env('SSL_SID'),
                'class' => SslsmsGateway::class,
            ],
            'dhorolasms' => [
                'api_key' => env('DHOROLASMS_API_KEY'),
                'class' => DhorolaSmsGateway::class,
            ],
            'dianahost' => [
                'api_key' => env('DIANAHOST_API_KEY'),
                'class' => DianaHostGateway::class,
            ],
            'dianasms' => [
                'api_key' => env('DIANASMS_API_KEY'),
                'class' => DianaSMSGateway::class,
            ],
            'durjoysoft' => [
                'username' => env('DURJOYSOFT_USERNAME'),
                'password' => env('DURJOYSOFT_PASSWORD'),
                'class' => DurjoySoftGateway::class,
            ],
            'elitbuzz' => [
                'api_key' => env('ELITBUZZ_API_KEY'),
                'class' => ElitBuzzGateway::class,
            ],
            'esms' => [
                'api_key' => env('ESMS_API_KEY'),
                'class' => EsmsGateway::class,
            ],
            'lpeek' => [
                'api_key' => env('LPEEK_API_KEY'),
                'class' => LpeekGateway::class,
            ],
            'mdl' => [
                'api_key' => env('MDL_API_KEY'),
                'class' => MDLGateway::class,
            ],
            'metronet' => [
                'api_key' => env('METRONET_API_KEY'),
                'class' => MetronetGateway::class,
            ],
            'mimsms' => [
                'api_key' => env('MIMSMS_API_KEY'),
                'class' => MimSmsGateway::class,
            ],
            'mobireach' => [
                'api_key' => env('MOBIREACH_API_KEY'),
                'class' => MobireachGateway::class,
            ],
            'muthofun' => [
                'username' => env('MUTHOFUN_USERNAME'),
                'password' => env('MUTHOFUN_PASSWORD'),
                'class' => MuthofunGateway::class,
            ],
            'novocombd' => [
                'api_key' => env('NOVOCOMBD_API_KEY'),
                'class' => NovocomBDGateway::class,
            ],
            'onnorokomsms' => [
                'username' => env('ONNOROKOMSMS_USERNAME'),
                'password' => env('ONNOROKOMSMS_PASSWORD'),
                'class' => OnnoRokomSMSGateway::class,
            ],
            'quicksms' => [
                'api_key' => env('QUICKSMS_API_KEY'),
                'class' => QuickSmsGateway::class,
            ],
            'redmoitsms' => [
                'api_key' => env('REDMOITSMS_API_KEY'),
                'class' => RedmoITSmsGateway::class,
            ],
            'sendmysms' => [
                'api_key' => env('SENDMYSMS_API_KEY'),
                'class' => SendMySmsGateway::class,
            ],
            'smartlabsms' => [
                'api_key' => env('SMARTLABSMS_API_KEY'),
                'class' => SmartLabSMSGateway::class,
            ],
            'sms4bd' => [
                'api_key' => env('SMS4BD_API_KEY'),
                'class' => Sms4BDGateway::class,
            ],
            'smsbangladesh' => [
                'api_key' => env('SMSBANGLADESH_API_KEY'),
                'class' => SmsBangladeshGateway::class,
            ],
            'smsinbd' => [
                'api_key' => env('SMSINBD_API_KEY'),
                'class' => SmsinBDGateway::class,
            ],
            'smsnetbd' => [
                'api_key' => env('SMSNETBD_API_KEY'),
                'class' => SMSNetBDGateway::class,
            ],
            'smsq' => [
                'api_key' => env('SMSQ_API_KEY'),
                'class' => SmsQGateway::class,
            ],
            'smsnet24' => [
                'api_key' => env('SMSNET24_API_KEY'),
                'class' => SMSNet24Gateway::class,
            ],
            'smsnoc' => [
                'api_key' => env('SMSNOC_API_KEY'),
                'class' => SmsNocGateway::class,
            ],
            'songbird' => [
                'api_key' => env('SONGBIRD_API_KEY'),
                'class' => SongBirdGateway::class,
            ],
            'tense' => [
                'api_key' => env('TENSE_API_KEY'),
                'class' => TenseGateway::class,
            ],
            'twenty4bulksms' => [
                'api_key' => env('TWENTY4BULKSMS_API_KEY'),
                'class' => Twenty4BulkSmsGateway::class,
            ],
            'twentyfourbulksmsbd' => [
                'api_key' => env('TWENTYFOURBULKSMSBD_API_KEY'),
                'class' => TwentyFourBulkSmsBDGateway::class,
            ],
            'trubosms' => [
                'api_key' => env('TRUBOSMS_API_KEY'),
                'class' => TrubosmsGateway::class,
            ],
            'viatech' => [
                'api_key' => env('VIATECH_API_KEY'),
                'class' => ViatechGateway::class,
            ],
            'wintext' => [
                'api_key' => env('WINTEXT_API_KEY'),
                'class' => WinTextGateway::class,
            ],
            'zamanit' => [
                'api_key' => env('ZAMANIT_API_KEY'),
                'class' => ZamanITGateway::class,
            ],
        ],
    ],

    'push' => [
        'default' => env('PUSH_SERVICE', 'firebase'),

        'services' => [
            'firebase' => [
                'credentials' => env('FIREBASE_CREDENTIALS'),
                'class' => FirebaseGateway::class,
            ],
            'pusher' => [
                'app_id' => env('PUSHER_APP_ID'),
                'app_key' => env('PUSHER_APP_KEY'),
                'app_secret' => env('PUSHER_APP_SECRET'),
                'cluster' => env('PUSHER_APP_CLUSTER'),
                'class' => PusherGateway::class,
            ],
        ],
    ],

    'email' => [
        'default' => env('NOTIFY_EMAIL_GATEWAY', 'smtp'),
        'from' => [
            'address' => env('MAIL_FROM_ADDRESS'),
            'name' => env('MAIL_FROM_NAME'),
        ],
        'template' => [
            'default' => 'vendor.multi-notify.emails.default',
            'layout' => 'vendor.multi-notify.emails.layouts.email',
        ],
        'gateways' => [
            'smtp' => [
                'class' => EmailGateway::class,
            ],
            // Other email gateways can be added here
        ],
    ],

    'logging' => [
        'enabled' => true,
        'channel' => env('NOTIFY_LOG_CHANNEL', 'daily'),
    ],
];
