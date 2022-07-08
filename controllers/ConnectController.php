<?php

namespace App\Hubspotremote\Controllers;

use App\Connector\Components\Helper;
use App\Hubspotremote\Models\HubSpot_Token as ModelsHubSpot_Token;
use App\Mymodule\Models\HubSpot_Token;
use Phalcon\Http\Client\Provider\Curl;
use Phalcon\Http\Response;


class ConnectController extends \App\Core\Controllers\BaseController
{

    public function indexAction()
    {
        // die("woking");

        if ($this->request->get('code')) {

            $helper = new HelperController();
            $CollectionData = $helper->fatchCollectionData();
            $flagg = count($helper->fatchCollectionData());
            $object_id = json_decode(json_encode($CollectionData[0]['_id'], true), true)['$oid'];


            if ($flagg > 0) {
                //if Data is already Present in DB then overide some value only
                $curl = curl_init();
                $tokendata = $helper->CurlOauth($this->request->get('code'));
                date_default_timezone_set('Asia/Kolkata');
                echo "<pre>";
                date_default_timezone_set('Asia/Kolkata');
                $time = strtotime(date('h:i'));
                $table = new ModelsHubSpot_Token();
                $table->_id = new \MongoDB\BSON\ObjectID($object_id);
                $table->access_token = $tokendata['access_token'];
                $table->refresh_token = $tokendata['refresh_token'];
                $table->expire_in = $tokendata['expires_in'];
                $table->token_type = $tokendata['token_type'];
                $table->expire_time = date("H:i", strtotime('+30 minutes', $time));
                $table->save();

                curl_close($curl);
            } else {
                //if Data is not present in DB then Create data in Collection
                $tokendata = $helper->CurlOauth($this->request->get('code'));
                date_default_timezone_set('Asia/Kolkata');
                echo "<pre>";
                date_default_timezone_set('Asia/Kolkata');
                $time = strtotime(date('h:i'));
                $table = new ModelsHubSpot_Token();
                $table->access_token = $tokendata['access_token'];
                $table->refresh_token = $tokendata['refresh_token'];
                $table->expire_in = $tokendata['expires_in'];
                $table->token_type = $tokendata['token_type'];
                $table->expire_time = date("H:i", strtotime('+30 minutes', $time));
                $table->save();
            }
        }
    }

    //This Function Just for Testing Purpose
    public function timeTestAction()
    {

        // return "funtion called";
        echo "<pre>";
        $helper = new HelperController();
        $data = $helper->isTokenExpire();
        print_r($data);
        die;
    }

    public function checkToken()
    {
        $helper = new HelperController();
        $CollectionData = $helper->fatchCollectionData();
        $flagg = count($helper->fatchCollectionData());

        if ($flagg > 0) {
            $object_id = json_decode(json_encode($CollectionData[0]['_id'], true), true)['$oid'];
            $table = new ModelsHubSpot_Token();
            $container = $table->getCollectionForTable(false);
            $dbData = $container->findOne(['_id' => new \MongoDB\BSON\ObjectID($object_id)]);
            echo "<pre>";
            $expire_time = $dbData['expire_time'];
            date_default_timezone_set('Asia/Kolkata');
            $time = strtotime(date('h:i'));
            $current_time = date("H:i", strtotime('+30 minutes', $time));
            if ($current_time <= $expire_time) {
                // die("Nonexpired");
            } else {
                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://api.hubapi.com/oauth/v1/token',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => 'grant_type=refresh_token&refresh_token=' . $dbData['refresh_token'] . '&redirect_uri=https%3A%2F%2Fremote.local.cedcommerce.com%2Fhubspotremote%2Fconnect%2Findex%3Fbearer%3DeyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJ1c2VyX2lkIjoiNjI2YmNjMzc2NzM0MTkzZmQ0NTA0MWI5Iiwicm9sZSI6ImFkbWluIiwiZXhwIjoxNjg4NjM2MTE1LCJpc3MiOiJodHRwczpcL1wvYXBwcy5jZWRjb21tZXJjZS5jb20iLCJ0b2tlbl9pZCI6IjYyYzU1NzUzODBjMDYwNTA5MjA2MTVjMiJ9.i45WyHgJ3b11ntGWMGuiNMUri6ezbnALFpFoZkhS2KHGbNA0xge2R6AR-Dsd1U-Gdcv5E9nrQKa3sEh_k7SGA_V4_FGAmFuJUQ5lrLoFpj9oaCc0qSb5A7hf3TY592SozFp-jKRxPlVSWqLhFghWTvcVLV-S_8VfhtSkbretnDY00MCJFaZmTboZkv-FYwHUQM2u1GNsYQAegXL8lHDtz3d9vw1d_t24eZYcvlBlAU1gRQyJQNqaqVThgGdHEvqmyYB2iEsk3LgI8rcxdBEBFYHFJMCfL05BlZ6Ht55d0d5gku-_tGK9cnPz2EVDfQ9OlaQmTrxl2zkTC6Z4G56zIQ&client_id=ba103f00-0167-468d-8385-da1b4f983e20&client_secret=bb4d0ef6-1721-4162-88f0-31d3d1ead715',
                    CURLOPT_HTTPHEADER => array(
                        'content-type: application/x-www-form-urlencoded',
                        'Authorization: Bearer CJapp7qdMBIHAAEAQAAAARjm8M8KILqy4xUo0I47MhR9VI4CeIbGdTUcWF3Q_ITz3l4VHDowAAAAQQAAAAAAAAAAAAAAAACAAAAAAAAAAAAAIAAAAAAA4AEAAAAAAAAAAAAAABACQhRJhjHG4C3HWK26E5vPb16nVpe5hkoDbmExUgBaAA'
                    ),
                ));

                date_default_timezone_set('Asia/Kolkata');
                $response = curl_exec($curl);
                $tokendata = json_decode($response, true);
                echo "<pre>";
                // die(print_r($tokendata['access_token']));
                date_default_timezone_set('Asia/Kolkata');
                $time = strtotime(date('h:i'));

                $table = new ModelsHubSpot_Token();
                $table->_id = new \MongoDB\BSON\ObjectID($object_id);
                $table->access_token = $tokendata['access_token'];
                $table->refresh_token = $tokendata['refresh_token'];
                $table->expire_in = $tokendata['expires_in'];
                $table->token_type = $tokendata['token_type'];
                $table->expire_time = date("H:i", strtotime('+30 minutes', $time));
                $table->save();
            }
        }
    }
}
