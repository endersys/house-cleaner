<?php

use App\Enums\MaterialStatusEnum;

return [

    'title' => 'Editar :label',

    'breadcrumb' => 'Editar',

    'form' => [

        'actions' => [

            'cancel' => [
                'label' => 'Cancelar',
            ],

            'save' => [
                'label' => 'Salvar',
            ],

        ],

        'tab' => [
            'label' => 'Editar',
        ],

    ],

    'messages' => [
        'saved' => 'Salvo!',
    ],

    'statuses' => [
        'active' => 'Ativo',
        MaterialStatusEnum::Inactive->name => 'Inativo',
    ]
];
