<?php

// Hanya pesan yang diterjemahkan; sisanya otomatis memakai fallback bahasa Inggris.
return [
    'min' => ['string' => ':Attribute minimal :min karakter.'],
    'password' => [
        'letters'       => ':Attribute harus mengandung minimal satu huruf.',
        'mixed'         => ':Attribute harus mengandung huruf besar dan huruf kecil.',
        'numbers'       => ':Attribute harus mengandung minimal satu angka.',
        'symbols'       => ':Attribute harus mengandung minimal satu simbol (mis. !@#$).',
        'uncompromised' => ':Attribute ini pernah bocor di internet. Gunakan password lain.',
    ],
    'attributes' => ['password' => 'Password'],
];
