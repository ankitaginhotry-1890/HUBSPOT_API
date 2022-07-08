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
            $flagg = count($helper->fatchCollectionData($CollectionData));
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

   
}
