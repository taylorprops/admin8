<?php

$active_states = explode(',', env('ACTIVE_STATES'));
return [
    'vars' => [
        'bad_characters' => ['/\#/', '/\</', '/\$/', '/\+/', '/\%/', '/\>/', '/\!/', '/\&/', '/\*/', '/\'/', '/\|/', '/\{/', '/\?/', '/\"/', '/\=/', '/\}/', '/\//', '/\:/', '/\s/', '/\@/', '/\;/', '/\,/', '/\(/', '/\)/', '/\[/', '/\]/', '/\./'],
        'active_states' => $active_states,
        'google_api_key' => env('GOOGLE_API_KEY')
    ]
];

?>
