<?php

namespace App\Hubspotremote\Controllers;

use OAuth\OAuth1\Service\Tumblr;
use Phalcon\Http\Client\Provider\Curl;
use Phalcon\Http\Response;


class DealController extends \App\Core\Controllers\BaseController
{

    public function indexAction()
    {
        die("deal");
    }

    public function listAction()
    {
        $helper = new HelperController();
        $contactData = $helper->curlGet('crm/v3/objects/deals?limit=10&archived=false');

        // print_r($contactData);
        $html = "";
        foreach ($contactData['results'] as $key => $value) {
            // print_r($value->properties);
            // die;
            $html .= "
            <tr'>
                <form method='post'>
                <th scope='row'><div class='form-check'>
                    <input class='form-check-input checkBoxes' name='checkBoxvalues' type='checkbox' value='" . $value['id'] . "' id='flexCheckDefault'>
                </div></th>
                <th scope='row'>" . $value['id'] . "</th>
                <td text-center>" . $value['properties']['dealname'] . "</td>
                <td text-center>" . $value['properties']['amount'] . "</td>
                <td text-center>" . $value['properties']['createdate'] . "</td>
                <td text-center>" . $value['properties']['closedate'] . "</td>
                <td text-center>" . $value['properties']['dealstage'] . "</td>
                <td text-center>&nbsp&nbsp<button class='btn btn-outline-danger' name='Did' value='" . $value['id'] . "'>Delete</button>
                </form>
                <form method='post' action='https://remote.local.cedcommerce.com/hubspotremote/deal/edit?bearer=eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJ1c2VyX2lkIjoiNjI2YmNjMzc2NzM0MTkzZmQ0NTA0MWI5Iiwicm9sZSI6ImFkbWluIiwiZXhwIjoxNjg4NjM2MTE1LCJpc3MiOiJodHRwczpcL1wvYXBwcy5jZWRjb21tZXJjZS5jb20iLCJ0b2tlbl9pZCI6IjYyYzU1NzUzODBjMDYwNTA5MjA2MTVjMiJ9.i45WyHgJ3b11ntGWMGuiNMUri6ezbnALFpFoZkhS2KHGbNA0xge2R6AR-Dsd1U-Gdcv5E9nrQKa3sEh_k7SGA_V4_FGAmFuJUQ5lrLoFpj9oaCc0qSb5A7hf3TY592SozFp-jKRxPlVSWqLhFghWTvcVLV-S_8VfhtSkbretnDY00MCJFaZmTboZkv-FYwHUQM2u1GNsYQAegXL8lHDtz3d9vw1d_t24eZYcvlBlAU1gRQyJQNqaqVThgGdHEvqmyYB2iEsk3LgI8rcxdBEBFYHFJMCfL05BlZ6Ht55d0d5gku-_tGK9cnPz2EVDfQ9OlaQmTrxl2zkTC6Z4G56zIQ&dealID=" . $value['id'] . "'>
                    <button class='ml-3 mt-2 btn btn-outline-info' name='Eid' value='" . $value['id'] . "'>Edit</button>
                </form></td>
            </tr>
            ";
        }
        $this->view->data = $html;
        // die;

        if ($this->request->getPost('Did')) {
            // die(print_r($this->request->getPost('Did')));
            $helper = new HelperController();
            $helper->curlDelete("crm/v3/objects/deals/" . $this->request->getPost('Did'));
            $this->response->redirect('hubspotremote/deal/list?bearer=eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJ1c2VyX2lkIjoiNjI2YmNjMzc2NzM0MTkzZmQ0NTA0MWI5Iiwicm9sZSI6ImFkbWluIiwiZXhwIjoxNjg4NjM2MTE1LCJpc3MiOiJodHRwczpcL1wvYXBwcy5jZWRjb21tZXJjZS5jb20iLCJ0b2tlbl9pZCI6IjYyYzU1NzUzODBjMDYwNTA5MjA2MTVjMiJ9.i45WyHgJ3b11ntGWMGuiNMUri6ezbnALFpFoZkhS2KHGbNA0xge2R6AR-Dsd1U-Gdcv5E9nrQKa3sEh_k7SGA_V4_FGAmFuJUQ5lrLoFpj9oaCc0qSb5A7hf3TY592SozFp-jKRxPlVSWqLhFghWTvcVLV-S_8VfhtSkbretnDY00MCJFaZmTboZkv-FYwHUQM2u1GNsYQAegXL8lHDtz3d9vw1d_t24eZYcvlBlAU1gRQyJQNqaqVThgGdHEvqmyYB2iEsk3LgI8rcxdBEBFYHFJMCfL05BlZ6Ht55d0d5gku-_tGK9cnPz2EVDfQ9OlaQmTrxl2zkTC6Z4G56zIQ');
        }
    }

    public function removeDataInBulkAction()
    {
        if ($this->request->getPost('data')) {
            $helper = new HelperController();
            $data = $this->request->getPost('data');
            // return $data[0];

            for ($i = 0; $i < count($data); $i++) {
                $helper->curlDelete("crm/v3/objects/deals/" . $data[$i]);
            }

            return "Deleted";
        }
    }
    public function addAction()
    {
        if ($this->request->getPost()) {

            print_r($this->request->getPost());
            // die;
            $postData = [
                'properties' => [
                    'amount'            => $this->request->getPost('amount'),
                    'closedate'         => (int)($this->request->getPost('closedate')),
                    'dealname'          => $this->request->getPost('dealname'),
                    'dealstage'         => $this->request->getPost('dealstage'),
                    'hubspot_owner_id'  => 201118343,
                    'pipeline'          => $this->request->getPost('pipeline')
                ],
            ];

            $helper = new HelperController();
            // $response=$helper->curlGet("owners/v2/owners");
            $response = $helper->curlPost("crm/v3/objects/deals", $postData);
            echo "<pre>";
            if (isset($response->id)) {
                $this->view->flag = $response->properties->properties;
                // print_r($response);
            } else {
            }
        }
    }

    public function editAction()
    {
        $helper = new HelperController();
        if ($this->request->get('dealID')) {
            $dealID = $this->request->get('dealID');
            $response = $helper->curlGet("crm/v3/objects/deals/" . $dealID . "?archived=false");
            // echo "<pre>";
            // print_r($helper->curlGet('owners/v2/owners'));
            // die;
            $this->view->data = $response;
        } else {
            die("Something went wrong :(");
        }

        if ($this->request->getPost('id')) {

            $postData = [
                "properties" => [
                    'amount' => $this->request->getPost('amount'),
                    'dealname' => $this->request->getPost('dealname'),
                    'dealstage' => $this->request->getPost('dealstage'),
                    'hubspot_owner_id' => "201118343",
                    'pipeline' => 'default',
                ]
            ];
            $id = $this->request->getPost('id');

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.hubapi.com/crm/v3/objects/deals/' . $id . '?hapikey=1b1ebd50-22c2-4174-bb58-ba6ff2f7c95a',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'PATCH',
                CURLOPT_POSTFIELDS => json_encode($postData),
                CURLOPT_HTTPHEADER => array(
                    'content-type: application/json'
                ),
            ));

            $response = curl_exec($curl);
            $response2 = json_decode($response, true);
            $flag = '';
            if (isset($response2['id'])) {
                // $this->view->flag = true;
                $this->response->redirect('https://remote.local.cedcommerce.com/hubspotremote/deal/list?bearer=eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJ1c2VyX2lkIjoiNjI2YmNjMzc2NzM0MTkzZmQ0NTA0MWI5Iiwicm9sZSI6ImFkbWluIiwiZXhwIjoxNjg4NjM2MTE1LCJpc3MiOiJodHRwczpcL1wvYXBwcy5jZWRjb21tZXJjZS5jb20iLCJ0b2tlbl9pZCI6IjYyYzU1NzUzODBjMDYwNTA5MjA2MTVjMiJ9.i45WyHgJ3b11ntGWMGuiNMUri6ezbnALFpFoZkhS2KHGbNA0xge2R6AR-Dsd1U-Gdcv5E9nrQKa3sEh_k7SGA_V4_FGAmFuJUQ5lrLoFpj9oaCc0qSb5A7hf3TY592SozFp-jKRxPlVSWqLhFghWTvcVLV-S_8VfhtSkbretnDY00MCJFaZmTboZkv-FYwHUQM2u1GNsYQAegXL8lHDtz3d9vw1d_t24eZYcvlBlAU1gRQyJQNqaqVThgGdHEvqmyYB2iEsk3LgI8rcxdBEBFYHFJMCfL05BlZ6Ht55d0d5gku-_tGK9cnPz2EVDfQ9OlaQmTrxl2zkTC6Z4G56zIQ&dealID=9458346917');
            } else {
                $this->view->flag = false;
                die("Something Went Wrong");
            }
            // echo "<pre>";
            // die(print_r($response2['id']));
            // print_r($response);
            // die;
        }
    }
}
