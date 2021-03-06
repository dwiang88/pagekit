<?php

use Pagekit\Migration\Migrator;

return [

    'name' => 'system/migration',

    'main' => function ($app) {

        $app['migrator'] = function ($app) {

            $migrator = new Migrator;
            $migrator->addGlobal('app', $app);

            return $migrator;
        };

    },

    'autoload' => [

        'Pagekit\\Migration\\' => 'src'

    ]

];
