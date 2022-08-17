<?php

namespace App\Hubspotremote;

use Exception;
use Phalcon\Mvc\View;
use Phalcon\Di\DiInterface;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\ModuleDefinitionInterface;
use Phalcon\Http\Response;
use Phalcon\Http\Request;


define("BEARER", "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJ1c2VyX2lkIjoiNjI2YmNjMzc2NzM0MTkzZmQ0NTA0MWI5Iiwicm9sZSI6ImFkbWluIiwiZXhwIjoxNjg4NjM2MTE1LCJpc3MiOiJodHRwczpcL1wvYXBwcy5jZWRjb21tZXJjZS5jb20iLCJ0b2tlbl9pZCI6IjYyYzU1NzUzODBjMDYwNTA5MjA2MTVjMiJ9.i45WyHgJ3b11ntGWMGuiNMUri6ezbnALFpFoZkhS2KHGbNA0xge2R6AR-Dsd1U-Gdcv5E9nrQKa3sEh_k7SGA_V4_FGAmFuJUQ5lrLoFpj9oaCc0qSb5A7hf3TY592SozFp-jKRxPlVSWqLhFghWTvcVLV-S_8VfhtSkbretnDY00MCJFaZmTboZkv-FYwHUQM2u1GNsYQAegXL8lHDtz3d9vw1d_t24eZYcvlBlAU1gRQyJQNqaqVThgGdHEvqmyYB2iEsk3LgI8rcxdBEBFYHFJMCfL05BlZ6Ht55d0d5gku-_tGK9cnPz2EVDfQ9OlaQmTrxl2zkTC6Z4G56zIQ");
define("APPID", "971551");
define("DEVAPIKEY", "d58bff24-1b67-49cf-9547-d6ab7233015f");
define("NGROK", "https://351a-103-97-184-106.in.ngrok.io");
// define("TESTAPIKEY", "971551");
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
        $di->set(
            'hubspot',
            function () {
                $CollectionData = (new Controllers\HelperController())->fatchCollectionData();
                if (!isset($CollectionData[0]->access_token) || $CollectionData[0]->access_token == null) {
                    die("<h1 style='text-align:center;color:darkred'>WARNING\n\nSomething Went Wrong\n\nPlease Connect to the App</h1>");
                } else {
                    $returnData = (new Controllers\HelperController())->refreshAccess_token($CollectionData);
                    if ($returnData === "Expire") {
                        $CollectionData = (new Controllers\HelperController())->fatchCollectionData();
                    }
                    $access_token = $CollectionData[0]['access_token'];
                    return \HubSpot\Factory::createWithApiKey("1b1ebd50-22c2-4174-bb58-ba6ff2f7c95a");
                    // return \HubSpot\Factory::createWithApiKey("d58bff24-1b67-49cf-9547-d6ab7233015f");
                    // return  \HubSpot\Factory::createWithAccessToken("pat-na1-de259ba7-b2c7-433e-8b20-50e63796e3fc");
                    // return  \HubSpot\Factory::createWithAccessToken($access_token);
                }
            }
        );

        $di->set(
            'hubspotDevAPI',
            function () {
                $CollectionData = (new Controllers\HelperController())->fatchCollectionData();
                if (!isset($CollectionData[0]->access_token) || $CollectionData[0]->access_token == null) {
                    die("<h1 style='text-align:center;color:darkred'>WARNING\n\nSomething Went Wrong\n\nPlease Connect to the App</h1>");
                } else {
                    $returnData = (new Controllers\HelperController())->refreshAccess_token($CollectionData);
                    if ($returnData === "Expire") {
                        $CollectionData = (new Controllers\HelperController())->fatchCollectionData();
                    }
                    $access_token = $CollectionData[0]['access_token'];
                    return \HubSpot\Factory::createWithApiKey("d58bff24-1b67-49cf-9547-d6ab7233015f");
                }
            }
        );

        $di->set(
            'hubspotOauth',
            function () {
                $CollectionData = (new Controllers\HelperController())->fatchCollectionData();
                if (!isset($CollectionData[0]->access_token) || $CollectionData[0]->access_token == null) {
                    die("<h1 style='text-align:center;color:darkred'>WARNING\n\nSomething Went Wrong\n\nPlease Connect to the App</h1>");
                } else {
                    $returnData = (new Controllers\HelperController())->refreshAccess_token($CollectionData);
                    if ($returnData === "Expire") {
                        $CollectionData = (new Controllers\HelperController())->fatchCollectionData();
                    }
                    $access_token = $CollectionData[0]['access_token'];
                    return  \HubSpot\Factory::createWithAccessToken($access_token);
                }
            }
        );

        $di->set(
            'print',
            function ($data = "") {
                echo "<pre>";
                print_r($data);
                echo "</pre>";
            }
        );

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
