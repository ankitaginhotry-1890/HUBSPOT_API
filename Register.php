<?php

namespace App\Hubspotremote;

use Phalcon\Mvc\View;
use Phalcon\Di\DiInterface;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\ModuleDefinitionInterface;
use Phalcon\Http\Response;
use Phalcon\Http\Request;

class Register implements ModuleDefinitionInterface
{

    /**
     * Register a specific autoloader for the module
     */
    public function registerAutoloaders(DiInterface $di = null)
    {
    }

    /**
     * Register specific services for the module
     */
    public function registerServices(DiInterface $di)
    {

        // Registering a dispatcher
        $di->set(
            'dispatcher',
            function () {
                $dispatcher = new Dispatcher();
                $dispatcher->setDefaultNamespace('App\Hubspotremote\Controllers');
                return $dispatcher;
            }
        );
        // $di->set(
        //     'response',
        //     function () {
        //         $response = new Response();
        //         $response->send();
        //         return $response;
        //     }
        // );

        // Registering the view component
        $di->set(
            'view',
            function () {
                $view = new View();
                $view->setViewsDir(CODE . '/hubspotremote/views/');
                return $view;
            }
        );
    }
}
