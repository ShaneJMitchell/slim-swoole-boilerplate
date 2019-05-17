<?php

// Sample config onlly setup currently to recognize a single DB connection
return [
    'connection' => getenv('DB_CONN'),
    'port' => getenv('DB_PORT'),
    'host' => getenv('DB_HOST'),
    'dbname' => getenv('DB_NAME'),
    'user' => getenv('DB_USER'),
    'pass' =>  getenv('DB_PASS'),
];
