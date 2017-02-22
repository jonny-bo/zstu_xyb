<?php

return array(
    'GET'  => array(
        '/^\/api\/users\/\d+$/',
    ),
    'POST' => array(
        '/^\/api\/users$/',
        '/^\/api\/users\/login$/',
        '/^\/api\/users\/password$/',
        '/^\/api\/emails$/'
    )
);
