<?php
// config/images.php

return [
    'disk' => 'public',

    // Diretório base onde salvamos uploads por tipo (posts, events, etc)
    'base_dirs' => [
        'posts'  => 'posts',
        'events' => 'events',
        'faqs'   => 'faqs',
    ],

    // Variantes geradas (largura alvo; altura proporcional)
    'variants' => [
        'sm' => 360,
        'md' => 720,
        'lg' => 1200,
        'xl' => 1600,
    ],

    // Qualidade (0-100)
    'quality' => [
        'jpeg' => 82,
        'webp' => 82,
        'png'  => 9,   // apenas se precisar PNG
    ],
];
