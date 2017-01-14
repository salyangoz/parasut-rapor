<?php

return [
    'parasut'   => [
        'client_id'                 =>  env('PARASUT_CLIENT_ID'),
        'client_secret'             =>  env('PARASUT_CLIENT_SECRET'),
        'username'                  =>  env('PARASUT_USERNAME'),
        'password'                  =>  env('PARASUT_PASSWORD'),
        'company_id'                =>  env('PARASUT_COMPANY_ID'),
        'category_id'               =>  env('PARASUT_CATEGORY_ID'),
        'account_id'                =>  env('PARASUT_ACCOUNT_ID'),
    ],
    "mail"      => [
        'from_email'                =>  env('EMAIL_FROM_EMAIL'),
        'from_name'                 =>  env('EMAIL_FROM_NAME'),
        'to_email'                  =>  env('EMAIL_TO_EMAIL'),
        'cc_email'                  =>  env('EMAIL_CC_EMAIL')
    ],
    'report'    => [
        'period'                    =>  env('PARASUT_REPORT_PERIOD'),
        'invoice_prefix'            =>  env('PARASUT_REPORT_INVOICE_PREFIX')
    ]
];