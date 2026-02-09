<?php

return [

    'class' => 'w-5 h-5',

    'attributes' => [
        // atributos padrão dos ícones
    ],

    'sets' => [

        'lucide' => [
            'prefix' => 'lucide',
            'path' => base_path('vendor/blade-ui-kit/blade-lucide-icons/resources/svg'),
        ],

    ],

    'components' => [

        'disabled' => false,

        'default' => [
            'class' => 'w-5 h-5',
        ],

    ],

    'caching' => [
        'enabled' => true,
        'key' => 'blade-icons',
        'duration' => 86400,
    ],

];
