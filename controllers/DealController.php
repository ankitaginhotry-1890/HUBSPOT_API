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

    //Data
    public function listAction()
    {
        $helper = new HelperController();
        $contactData = json_decode($this->hubspot->crm()->deals()->basicApi()->getPage($limit = 100), true);

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
                <td><form method='post' target='_parent' action='https://remote.local.cedcommerce.com/hubspotremote/engagement/profile?bearer=eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJ1c2VyX2lkIjoiNjI2YmNjMzc2NzM0MTkzZmQ0NTA0MWI5Iiwicm9sZSI6ImFkbWluIiwiZXhwIjoxNjg4NjM2MTE1LCJpc3MiOiJodHRwczpcL1wvYXBwcy5jZWRjb21tZXJjZS5jb20iLCJ0b2tlbl9pZCI6IjYyYzU1NzUzODBjMDYwNTA5MjA2MTVjMiJ9.i45WyHgJ3b11ntGWMGuiNMUri6ezbnALFpFoZkhS2KHGbNA0xge2R6AR-Dsd1U-Gdcv5E9nrQKa3sEh_k7SGA_V4_FGAmFuJUQ5lrLoFpj9oaCc0qSb5A7hf3TY592SozFp-jKRxPlVSWqLhFghWTvcVLV-S_8VfhtSkbretnDY00MCJFaZmTboZkv-FYwHUQM2u1GNsYQAegXL8lHDtz3d9vw1d_t24eZYcvlBlAU1gRQyJQNqaqVThgGdHEvqmyYB2iEsk3LgI8rcxdBEBFYHFJMCfL05BlZ6Ht55d0d5gku-_tGK9cnPz2EVDfQ9OlaQmTrxl2zkTC6Z4G56zIQ&username=" . $value['properties']['firstname'] . " " . $value['properties']['lastname'] . "&email=" . $value['properties']['email'] . "&user_id=" . $value['id'] . "&objectType=deal'><button class='btn btn-primary'>Preview</button></form></td>
            </tr>
            ";
        }
        $this->view->data = $html;
        // die;

        if ($this->request->getPost('Did')) {
            $this->hubspot->crm()->deals()->basicApi()->archive($this->request->getPost('Did'));
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
            echo "<pre>";
            $postData = [
                'properties' => [
                    'amount'            => $this->request->getPost('amount'),
                    'closedate'         => (int)($this->request->getPost('closedate')),
                    'dealname'          => $this->request->getPost('dealname'),
                    'dealstage'         => $this->request->getPost('dealstage'),
                    'hubspot_owner_id'  => 201118343,
                    'pipeline'          => "default"
                ]
            ];
            print_r($postData);
            $response = json_decode($this->hubspot->crm()->deals()->basicApi()->create($postData), true);

            if (isset($response->id)) {
                $this->view->flag = $response['id'];
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
            $response = json_decode($this->hubspot->crm()->deals()->basicApi()->getById($dealID), true);
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
            $response = json_decode($this->hubspot->crm()->deals()->basicApi()->update($id, $postData), true);
            if (isset($response['id'])) {
                $this->response->redirect('https://remote.local.cedcommerce.com/hubspotremote/deal/list?bearer=eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJ1c2VyX2lkIjoiNjI2YmNjMzc2NzM0MTkzZmQ0NTA0MWI5Iiwicm9sZSI6ImFkbWluIiwiZXhwIjoxNjg4NjM2MTE1LCJpc3MiOiJodHRwczpcL1wvYXBwcy5jZWRjb21tZXJjZS5jb20iLCJ0b2tlbl9pZCI6IjYyYzU1NzUzODBjMDYwNTA5MjA2MTVjMiJ9.i45WyHgJ3b11ntGWMGuiNMUri6ezbnALFpFoZkhS2KHGbNA0xge2R6AR-Dsd1U-Gdcv5E9nrQKa3sEh_k7SGA_V4_FGAmFuJUQ5lrLoFpj9oaCc0qSb5A7hf3TY592SozFp-jKRxPlVSWqLhFghWTvcVLV-S_8VfhtSkbretnDY00MCJFaZmTboZkv-FYwHUQM2u1GNsYQAegXL8lHDtz3d9vw1d_t24eZYcvlBlAU1gRQyJQNqaqVThgGdHEvqmyYB2iEsk3LgI8rcxdBEBFYHFJMCfL05BlZ6Ht55d0d5gku-_tGK9cnPz2EVDfQ9OlaQmTrxl2zkTC6Z4G56zIQ&dealID=9458346917');
            } else {
                die("Something Went Wrong");
            }
        }
    }
}
