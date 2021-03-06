<?php

use Pagekit\View\Event\ResponseListener;
use Pagekit\View\Helper\DateHelper;
use Pagekit\View\Helper\GravatarHelper;
use Pagekit\View\Helper\MarkdownHelper;
use Pagekit\View\Helper\TemplateHelper;
use Pagekit\View\Helper\TokenHelper;
use Pagekit\View\ViewListener;

return [

    'name' => 'system/view',

    'main' => function ($app) {

        $app->extend('view', function($view, $app) {

            $helpers = [
                'gravatar' => new GravatarHelper(),
                'tmpl'     => new TemplateHelper()
            ];

            if (isset($app['dates'])) {
                $helpers['date'] = new DateHelper($app['dates']);
            }

            if (isset($app['markdown'])) {
                $helpers['markdown'] = new MarkdownHelper($app['markdown']);
            }

            if (isset($app['csrf'])) {
                $helpers['token'] = new TokenHelper($app['csrf']);
            }

            return $view->addHelpers($helpers);
        });

        $app->subscribe(new ResponseListener);

        $app->on('system.init', function() use ($app) {

            $debug = $app['module']['framework']->config('debug');

            $app['styles']->register('codemirror', 'vendor/assets/codemirror/codemirror.css');
            $app['scripts']->register('angular', 'vendor/assets/angular/angular.min.js', 'jquery');
            $app['scripts']->register('angular-animate', 'vendor/assets/angular-animate/angular-animate.min.js', 'angular');
            $app['scripts']->register('angular-cookies', 'vendor/assets/angular-cookies/angular-cookies.min.js', 'angular');
            $app['scripts']->register('angular-loader', 'vendor/assets/angular-loader/angular-loader.min.js', 'angular');
            $app['scripts']->register('angular-messages', 'vendor/assets/angular-messages/angular-messages.min.js', 'angular');
            $app['scripts']->register('angular-resource', 'vendor/assets/angular-resource/angular-resource.min.js', 'angular');
            $app['scripts']->register('angular-route', 'vendor/assets/angular-route/angular-route.min.js', 'angular');
            $app['scripts']->register('angular-sanitize', 'vendor/assets/angular-sanitize/angular-sanitize.min.js', 'angular');
            $app['scripts']->register('angular-touch', 'vendor/assets/angular-touch/angular-touch.min.js', 'angular');
            $app['scripts']->register('application', 'extensions/system/app/application.js', 'angular');
            $app['scripts']->register('application-directives', 'extensions/system/app/directives.js', 'application');
            $app['scripts']->register('codemirror', 'vendor/assets/codemirror/codemirror.js');
            $app['scripts']->register('jquery', 'vendor/assets/jquery/dist/jquery.min.js');
            $app['scripts']->register('marked', 'vendor/assets/marked/marked.js');
            $app['scripts']->register('uikit', 'vendor/assets/uikit/js/uikit.min.js', 'jquery');
            $app['scripts']->register('uikit-autocomplete', 'vendor/assets/uikit/js/components/autocomplete.min.js', 'uikit');
            $app['scripts']->register('uikit-form-password', 'vendor/assets/uikit/js/components/form-password.min.js', 'uikit');
            $app['scripts']->register('uikit-htmleditor', 'vendor/assets/uikit/js/components/htmleditor.min.js', ['uikit', 'marked', 'codemirror']);
            $app['scripts']->register('uikit-pagination', 'vendor/assets/uikit/js/components/pagination.min.js', 'uikit');
            $app['scripts']->register('uikit-nestable', 'vendor/assets/uikit/js/components/nestable.min.js', 'uikit');
            $app['scripts']->register('uikit-notify', 'vendor/assets/uikit/js/components/notify.min.js', 'uikit');
            $app['scripts']->register('uikit-sortable', 'vendor/assets/uikit/js/components/sortable.min.js', 'uikit');
            $app['scripts']->register('uikit-sticky', 'vendor/assets/uikit/js/components/sticky.min.js', 'uikit');
            $app['scripts']->register('uikit-upload', 'vendor/assets/uikit/js/components/upload.min.js', 'uikit');
            $app['scripts']->register('uikit-datepicker', 'vendor/assets/uikit/js/components/datepicker.min.js', 'uikit');
            $app['scripts']->register('uikit-timepicker', 'vendor/assets/uikit/js/components/timepicker.js', 'uikit-autocomplete');
            $app['scripts']->register('gravatar', 'vendor/assets/gravatarjs/gravatar.js');
            $app['scripts']->register('system', 'extensions/system/app/system.js', ['jquery', 'tmpl', 'locale']);
            $app['scripts']->register('vue', 'vendor/assets/vue/dist/'.($debug ? 'vue.js' : 'vue.min.js'));
            $app['scripts']->register('vue-system', 'extensions/system/app/vue-system.js', ['vue-resource', 'locale', 'uikit-pagination']);
            $app['scripts']->register('vue-resource', 'extensions/system/app/vue-resource.js', ['vue']);
            $app['scripts']->register('vue-validator', 'extensions/system/app/vue-validator.js', ['vue']);

            $app['view']->data('$pagekit', ['version' => $app['version'], 'url' => $app['router']->getContext()->getBaseUrl(), 'csrf' => $app['csrf']->generate()]);

            $app['view']->section()->set('messages', function() use ($app) {
                return $app['tmpl']->render('extensions/system/views/messages/messages.php');
            });

            $app['view']->section()->prepend('head', function () use ($app) {
                if ($templates = $app['view']->tmpl()->queued()) {
                    $app['view']->script('tmpl', $app['url']->get('@system/system/tmpls', ['templates' => implode(',', $templates)]));
                }
            });
        });

        $app->on('system.loaded', function () use ($app) {
            foreach ($app['module'] as $module) {
                if (isset($module->templates)) {
                    foreach ($module->templates as $name => $tmpl) {
                        $app['view']->tmpl()->register($name, $tmpl);
                    }
                }
            }
        });

        $app->on('kernel.view', function ($event) use ($app) {
            if (is_array($result = $event->getControllerResult())) {
                foreach ($result as $key => $value) {
                    if ($key == '$meta') {
                        $app['view']->meta($value);
                    } elseif ($key[0] == '$') {
                        $app['view']->data($key, $value);
                    }
                }
            }
        });

    },

    'autoload' => [

        'Pagekit\\View\\' => 'src'

    ]

];
