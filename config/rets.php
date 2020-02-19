<?php
$rets_config = new \PHRETS\Configuration;
$rets_config -> setLoginUrl(env('RETS_URL'))
    -> setUsername(env('RETS_USERNAME'))
    -> setPassword(env('RETS_PASSWORD'))
    -> setRetsVersion('RETS/1.8')
    -> setUserAgent('Bright RETS Application/1.0')
    -> setHttpAuthenticationMethod('digest')
    -> setOption('disable_follow_location', false); // or 'basic' if required
    // -> setOption('use_post_method', true)
    ;
return [
    'rets' => [
        'url' => env('RETS_URL'),
        'username' => env('RETS_USERNAME'),
        'password' => env('RETS_PASSWORD'),
        'rets_config' => $rets_config
    ]
];

?>

