<?php

return [

    'include' => [
        '.env.primary' => ['encrypt' => true],     // Base env file, will be encrypted to be included in repo
        '.env.branch' => ['encrypt' => true],   // optional, content override base values, will be encrypted to be included in repo
        '.env.custom' => ['encrypt' => false],  // optional, content override previous values, exclude this with gitignore
    ],
];
