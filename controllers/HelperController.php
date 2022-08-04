<?php

namespace App\Hubspotremote\Controllers;

use App\Hubspotremote\Models\HubSpot_Token;

class HelperController extends \App\Core\Controllers\BaseController
{

    public function test()
    {
        die("helper");
    }

    //Function for Curl Get Request
    public function curlGet($path, $hapikey = '')
    {
        $CollectionData = $this->fatchCollectionData();
        $returnData = $this->refreshAccess_token($CollectionData);
        if ($returnData === "Expire") {
            $CollectionData = $this->fatchCollectionData();
        }

        $curl = curl_init();

        if ($hapikey == '') {
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.hubapi.com/' . $path,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'authorization: Bearer ' . $CollectionData[0]['access_token'] . '',
                    'content-type: application/json',

                ),
            ));
        } else {
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.hubapi.com/' . $path . "/?hapikey=" . $hapikey,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'content-type: application/json',

                ),
            ));
        }
        $response = curl_exec($curl);
        return json_decode(
            $response,
            true
        );
    }

    // Fucntion for Curl Delete Request
    public function curlDelete($path, $hapikey = '')
    {
        $CollectionData = $this->fatchCollectionData();
        $returnData = $this->refreshAccess_token($CollectionData);
        if ($returnData === "Expire") {
            $CollectionData = $this->fatchCollectionData();
        }
        $curl = curl_init();

        if ($hapikey == '') {

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.hubapi.com/' . $path,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => 'DELETE',
                CURLOPT_HTTPHEADER => array(
                    'authorization: Bearer ' . $CollectionData[0]['access_token'] . ''
                ),
            ));
            $response = curl_exec($curl);
        } else {
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.hubapi.com/' . $path . '/?hapikey=' . $hapikey,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => 'DELETE',
                CURLOPT_HTTPHEADER => array(),
            ));
            $response = curl_exec($curl);
        }

        return json_decode($response, true);
    }

    //Function for Curl Post Request
    //path (String)
    //$postData (Array)
    public function curlPost($path, $postData, $hapikey = '')
    {

        // echo "<pre>";
        //     print_r($postData);
        //     die;
        $CollectionData = $this->fatchCollectionData();
        $returnData = $this->refreshAccess_token($CollectionData);
        $curl = curl_init();
        if ($returnData === "Expire") {
            $CollectionData = $this->fatchCollectionData();
        }


        if ($hapikey !== "") {
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.hubapi.com/' . $path . '/?hapikey=' . $hapikey,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($postData, true),
                CURLOPT_HTTPHEADER => array(
                    'content-type: application/json',
                ),
            ));

            $response = curl_exec($curl);
        } else {
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.hubapi.com/' . $path,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($postData, true),
                CURLOPT_HTTPHEADER => array(
                    'content-type: application/json',
                    'authorization: Bearer ' . $CollectionData[0]['access_token'] . ''
                ),
            ));

            $response = curl_exec($curl);
        }


        return json_decode($response);
    }

    //Function for Curl PATCH Request
    //path (String)
    //$postData (Array)
    public function curlPatch($path, $postData, $hapikey = '')
    {

        $CollectionData = $this->fatchCollectionData();
        $returnData = $this->refreshAccess_token($CollectionData);
        if ($returnData === "Expire") {
            $CollectionData = $this->fatchCollectionData();
        }

        $curl = curl_init();
        // $CURLOPT_URL = 'https://api.hubapi.com/' . $path;
        // // die($CollectionData[0]['access_token']);
        // // die(json_encode($postData));

        if ($hapikey !== "") {
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.hubapi.com/' . $path . "/?hapikey=" . $hapikey,

                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'PATCH',
                CURLOPT_POSTFIELDS => json_encode($postData),
                CURLOPT_HTTPHEADER => array(
                    'content-type: application/json',
                ),
            ));
        } else {
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.hubapi.com/' . $path,

                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'PATCH',
                CURLOPT_POSTFIELDS => json_encode($postData),
                CURLOPT_HTTPHEADER => array(
                    'content-type: application/json',
                    'authorization: Bearer ' . $CollectionData[0]['access_token'] . ''
                ),
            ));
        }


        $response = curl_exec($curl);
        return json_decode($response);
    }

    //For Put Request curl Fucntion/////////////////////////
    public function curlPut($path)
    {
        $CollectionData = $this->fatchCollectionData();
        $returnData = $this->refreshAccess_token($CollectionData);
        if ($returnData === "Expire") {
            $CollectionData = $this->fatchCollectionData();
        }
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.hubapi.com/' . $path,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'PUT',
            CURLOPT_HTTPHEADER => array(
                'authorization: Bearer ' . $CollectionData[0]['access_token'] . ''
            ),
        ));

        $response = curl_exec($curl);
        return json_decode($response, true);
    }

    //Curl Request for access token when code is recived
    public function CurlOauth($code)
    {
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
            CURLOPT_POSTFIELDS => 'grant_type=authorization_code&code=' . $code . '&redirect_uri=https%3A%2F%2Fremote.local.cedcommerce.com%2Fhubspotremote%2Fconnect%2Findex%3Fbearer%3DeyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJ1c2VyX2lkIjoiNjI2YmNjMzc2NzM0MTkzZmQ0NTA0MWI5Iiwicm9sZSI6ImFkbWluIiwiZXhwIjoxNjg4NjM2MTE1LCJpc3MiOiJodHRwczpcL1wvYXBwcy5jZWRjb21tZXJjZS5jb20iLCJ0b2tlbl9pZCI6IjYyYzU1NzUzODBjMDYwNTA5MjA2MTVjMiJ9.i45WyHgJ3b11ntGWMGuiNMUri6ezbnALFpFoZkhS2KHGbNA0xge2R6AR-Dsd1U-Gdcv5E9nrQKa3sEh_k7SGA_V4_FGAmFuJUQ5lrLoFpj9oaCc0qSb5A7hf3TY592SozFp-jKRxPlVSWqLhFghWTvcVLV-S_8VfhtSkbretnDY00MCJFaZmTboZkv-FYwHUQM2u1GNsYQAegXL8lHDtz3d9vw1d_t24eZYcvlBlAU1gRQyJQNqaqVThgGdHEvqmyYB2iEsk3LgI8rcxdBEBFYHFJMCfL05BlZ6Ht55d0d5gku-_tGK9cnPz2EVDfQ9OlaQmTrxl2zkTC6Z4G56zIQ&client_id=ba103f00-0167-468d-8385-da1b4f983e20&client_secret=bb4d0ef6-1721-4162-88f0-31d3d1ead715',
            CURLOPT_HTTPHEADER => array(
                'content-type: application/x-www-form-urlencoded',
            ),
        ));

        date_default_timezone_set('Asia/Kolkata');
        $response = curl_exec($curl);
        return json_decode($response, true);
    }


    public function isTokenExpire($CollectionData)
    {

        $object_id = json_decode(json_encode($CollectionData[0]['_id'], true), true)['$oid'];
        $table = new HubSpot_Token();
        $container = $table->getCollectionForTable(false);
        $dbData = $container->findOne(['_id' => new \MongoDB\BSON\ObjectID($object_id)]);
        // echo "<pre>";
        $expire_time = $dbData['expire_time'];
        date_default_timezone_set('Asia/Kolkata');
        $time = strtotime(date('h:i'));
        $current_time = date("H:i");
        if ($current_time < $expire_time) {
            return "valid"; //not expire
        } else {
            return "Expire"; //expire
        }
    }

    //functin refresh the token if the token is Expired
    public function refreshAccess_token($CollectionData)
    {
        if ($this->isTokenExpire($CollectionData) == "Expire") {


            $refreshToken = $CollectionData[0]['refresh_token'];

            //curl Request to get Access token from refresh token
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
                CURLOPT_POSTFIELDS => 'grant_type=refresh_token&refresh_token=' . $refreshToken . '&redirect_uri=https%3A%2F%2Fremote.local.cedcommerce.com%2Fhubspotremote%2Fconnect%2Findex%3Fbearer%3DeyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJ1c2VyX2lkIjoiNjI2YmNjMzc2NzM0MTkzZmQ0NTA0MWI5Iiwicm9sZSI6ImFkbWluIiwiZXhwIjoxNjg4NjM2MTE1LCJpc3MiOiJodHRwczpcL1wvYXBwcy5jZWRjb21tZXJjZS5jb20iLCJ0b2tlbl9pZCI6IjYyYzU1NzUzODBjMDYwNTA5MjA2MTVjMiJ9.i45WyHgJ3b11ntGWMGuiNMUri6ezbnALFpFoZkhS2KHGbNA0xge2R6AR-Dsd1U-Gdcv5E9nrQKa3sEh_k7SGA_V4_FGAmFuJUQ5lrLoFpj9oaCc0qSb5A7hf3TY592SozFp-jKRxPlVSWqLhFghWTvcVLV-S_8VfhtSkbretnDY00MCJFaZmTboZkv-FYwHUQM2u1GNsYQAegXL8lHDtz3d9vw1d_t24eZYcvlBlAU1gRQyJQNqaqVThgGdHEvqmyYB2iEsk3LgI8rcxdBEBFYHFJMCfL05BlZ6Ht55d0d5gku-_tGK9cnPz2EVDfQ9OlaQmTrxl2zkTC6Z4G56zIQ&client_id=ba103f00-0167-468d-8385-da1b4f983e20&client_secret=bb4d0ef6-1721-4162-88f0-31d3d1ead715',
                CURLOPT_HTTPHEADER => array(
                    'content-type: application/x-www-form-urlencoded',
                    'Authorization: Bearer ' . $refreshToken . ''
                ),
            ));

            //calculate the expire time
            date_default_timezone_set('Asia/Kolkata');
            $response = curl_exec($curl);
            $tokendata = json_decode($response, true);
            // echo "<pre>";
            date_default_timezone_set('Asia/Kolkata');
            $time = strtotime(date('h:i'));
            $object_id = json_decode(json_encode($CollectionData[0]['_id'], true), true)['$oid'];

            //Save the when refresh token refresh the access_token
            $table = new HubSpot_Token();
            $table->_id = new \MongoDB\BSON\ObjectID($object_id);
            $table->access_token = $tokendata['access_token'];
            $table->refresh_token = $tokendata['refresh_token'];
            $table->expire_in = $tokendata['expires_in'];
            $table->token_type = $tokendata['token_type'];
            $table->expire_time = date("H:i", strtotime('+30 minutes', $time));
            $table->save();

            return "Expire";
        } else {
            //code if token is valid

            // $CollectionData = $this->fatchCollectionData();
            // echo "<pre>";
            // die(print_r($CollectionData[0]['access_token']));
        }
    }

    //Fucntion to fatch the collection data
    public function fatchCollectionData()
    {

        $table = new HubSpot_Token();
        $container = $table->getCollectionForTable(false);
        $dbData = $container->find()->ToArray();
        // die(print_r($dbData));
        return $dbData;
        // if (count($dbData) == 0) {
        //     // echo "<script>alert('HubspotNotConnected Please Connect to the Hubspot')</script>";
        //     // die;
        // } else {
        // }
    }
}
