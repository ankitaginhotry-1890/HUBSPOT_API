<?php

namespace App\Remotetest\Api;

use GuzzleHttp\Client;
use GuzzleHttp\Exception;


class Dam extends \App\Apiconnect\Api\Base
{

    public function test()
    {
        return [
            "success" => "message",
            "msg" => "remote and home Connected"
        ];
    }

    public function retrive()
    {
        $shop = $this->di->getRegistry()->getCurrentShop();
        $token = $shop['apps']['default']['token']['access_token'];

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://mystore8707.myshopify.com/admin/api/2022-04/products.json',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'X-Shopify-Access-Token: ' . $token . '',
                'Cookie: _master_udr=eyJfcmFpbHMiOnsibWVzc2FnZSI6IkJBaEpJaWsyWlRWaU4yVmxaaTAwTURSaUxUUXhNR1F0WWpJNU1TMWpZV1ZoWkdaa05tWmtZamNHT2daRlJnPT0iLCJleHAiOiIyMDI0LTA1LTEwVDEwOjUyOjQzLjk1OVoiLCJwdXIiOiJjb29raWUuX21hc3Rlcl91ZHIifX0%3D--8404ab28cd7277abd05e35872442956c52a8c529; _secure_admin_session_id=872b843046c88f2445fd34a26f793345; _secure_admin_session_id_csrf=872b843046c88f2445fd34a26f793345'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return [
            "success" => "message",
            "msg" => "Retrive Working",
            "Data" => $response,
            "Access_token" => $token
        ];
    }
    public function userdetails()
    {
        $shop = $this->di->getRegistry()->getCurrentShop();
        $token = $shop['apps']['default']['token']['access_token'];

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://mystore8707.myshopify.com/admin/api/2022-04/shop.json',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'X-Shopify-Access-Token: ' . $token . '',
                'Cookie: _master_udr=eyJfcmFpbHMiOnsibWVzc2FnZSI6IkJBaEpJaWsyWlRWaU4yVmxaaTAwTURSaUxUUXhNR1F0WWpJNU1TMWpZV1ZoWkdaa05tWmtZamNHT2daRlJnPT0iLCJleHAiOiIyMDI0LTA1LTEwVDEwOjUyOjQzLjk1OVoiLCJwdXIiOiJjb29raWUuX21hc3Rlcl91ZHIifX0%3D--8404ab28cd7277abd05e35872442956c52a8c529; _secure_admin_session_id=872b843046c88f2445fd34a26f793345; _secure_admin_session_id_csrf=872b843046c88f2445fd34a26f793345'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return [
            "success" => "message",
            "msg" => "Retrive Working",
            "Data" => $response,
            "Access_token" => $token
        ];
    }
}
