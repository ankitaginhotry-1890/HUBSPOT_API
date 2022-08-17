<?php

namespace App\Hubspotremote\Controllers;

use App\Apiconnect\Components\Webhook;
use App\Core\ConsoleApplication;
use App\Hubspotremote\Models\HubSpot_Token;
use App\Hubspotremote\Models\ProductPrice;
use Phalcon\Http\Response;
use ValueError;

class WebhookController extends \App\Core\Controllers\BaseController
{
    public function webhookResponseAction()
    {
        $table = new ProductPrice();
        $table->msg = "One Contact is Created";
        $table->save();
        return "Saved";
    }


    public function createWebHookAction()
    {
        $helper = new HelperController();

        if ($this->request->getPost("dID")) {
            $response = $this->hubspotDevAPI->webhooks()->settings()->subscriptionsApi()->archive($this->request->getPost("dID"), APPID);
        }

        $response = json_decode($this->hubspotDevAPI->webhooks()->subscriptionsApi()->getAll(APPID), true);
        $htm = "";
        foreach ($response['results'] as $key => $value) {
            $newResponse = $helper->curlGet("webhooks/v3/" . APPID . "/subscriptions/" . $value['id'], DEVAPIKEY);
            // print_r($newResponse);
            $data = explode(".", $newResponse['eventType']);
            // print_r($data);
            // die;
            if ($newResponse['active'] == 1) {
                $htm .= '<tr>
                    <th class="text-dark">' . $data[0] . '</th>
                    <th class="text-dark">' . $data[1] . '</th>
                    <th class="text-dark">🟢 True <label class="switch">
                    <input type="checkbox" class="checkState" value=' . $newResponse['id'] . ' checked>
                    <span class="slider round"></span>
                  </label></th>
                  <td><form method="post"><button class="btn btn-outline-danger" name="dID" value=' . $newResponse['id'] . '>Delete</button></form></td>
                  </tr>';
            } else {
                $htm .= '<tr>
                    <th class="text-dark">' . $data[0] . '</th>
                    <th class="text-dark">' . $data[1] . '</th>
                    <th class="text-dark">🔴 False <label class="switch">
                    <input type="checkbox" class="checkState"  value=' . $newResponse['id'] . ' >
                    <span class="slider round"></span>
                  </label></th>
                  <td><form method="post"><button class="btn btn-outline-danger" name="dID" value=' . $newResponse['id'] . '>Delete</button></form></td>
                  </tr>';
            }
            // break;
        }

        $res = $helper->curlGet("webhooks/v3/971551/settings", DEVAPIKEY);
        // $res = json_decode($this->hubspotOauth->webhooks()->settings()->settingsApi()->getAll(APPID), true);
        // echo '<pre>';
        // print_r($res);
        // echo '<br><br><b>';
        // die(__FILE__.'/line '.__LINE__);
        $this->view->targetUrl = $res['targetUrl'];
        $this->view->tableData = $htm;
        // die;


        if ($this->request->isPost("targetUrl")) {
            $data = $this->request->getPost();
            $helper = new HelperController();
            $PatchData = [
                'targetUrl' => $data['targetUrl'],
                'throttling' => [
                    'maxConcurrentRequests' => $data['maxConcurrentRequests'],
                    'period' => $data['period'],
                ],
            ];

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.hubapi.com/webhooks/v3/971551/settings?hapikey=d58bff24-1b67-49cf-9547-d6ab7233015f',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'PUT',
                CURLOPT_POSTFIELDS => json_encode($PatchData, true),
                CURLOPT_HTTPHEADER => array(
                    'content-type: application/json'
                ),
            ));
            $response = curl_exec($curl);
            print_r($response);
            die;
            $this->response->redirect("https://remote.local.cedcommerce.com/hubspotremote/webhook/createwebhook?bearer=eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJ1c2VyX2lkIjoiNjI2YmNjMzc2NzM0MTkzZmQ0NTA0MWI5Iiwicm9sZSI6ImFkbWluIiwiZXhwIjoxNjg4NjM2MTE1LCJpc3MiOiJodHRwczpcL1wvYXBwcy5jZWRjb21tZXJjZS5jb20iLCJ0b2tlbl9pZCI6IjYyYzU1NzUzODBjMDYwNTA5MjA2MTVjMiJ9.i45WyHgJ3b11ntGWMGuiNMUri6ezbnALFpFoZkhS2KHGbNA0xge2R6AR-Dsd1U-Gdcv5E9nrQKa3sEh_k7SGA_V4_FGAmFuJUQ5lrLoFpj9oaCc0qSb5A7hf3TY592SozFp-jKRxPlVSWqLhFghWTvcVLV-S_8VfhtSkbretnDY00MCJFaZmTboZkv-FYwHUQM2u1GNsYQAegXL8lHDtz3d9vw1d_t24eZYcvlBlAU1gRQyJQNqaqVThgGdHEvqmyYB2iEsk3LgI8rcxdBEBFYHFJMCfL05BlZ6Ht55d0d5gku-_tGK9cnPz2EVDfQ9OlaQmTrxl2zkTC6Z4G56zIQ");
            // die;
        }
    }

    public function updateStatusAction()
    {
        if ($this->request->isPost()) {
            $helper = new HelperController();
            $data = $this->request->getPost();
            $status = [
                "active" => $data['status']
            ];
            // die(print_r($data));

            $response = $helper->curlPatch("webhooks/v3/" . APPID . "/subscriptions/" . $data['id'], $status, DEVAPIKEY);
            return json_encode($response, true);
        }
    }

    public function createSubscriptionAction()
    {
        // die("sd");

        if ($this->request->getPost()) {
            $data = $this->request->getPost();

            $helper = new HelperController();
            $response = $helper->curlPost("webhooks/v3/" . APPID . "/subscriptions", $data, DEVAPIKEY);
            return json_encode($response, true);
        }
    }
}
