<?php

namespace App\Hubspotremote\Controllers;

use Phalcon\Http\Client\Provider\Curl;
use Phalcon\Http\Response;


class ContactController extends \App\Core\Controllers\BaseController
{

    public function indexAction()
    {
        
    }

    public function listAction()
    {
        $url = "https://api.hubapi.com/crm/v3/objects/contacts?limit=100&archived=false&hapikey=1b1ebd50-22c2-4174-bb58-ba6ff2f7c95a";

        $ch = curl_init("https://api.hubapi.com/crm/v3/objects/contacts?limit=100&archived=false&hapikey=1b1ebd50-22c2-4174-bb58-ba6ff2f7c95a");

        // curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);

        $contactData = json_decode($response);
        echo "<pre>";
        // die(print_r($contactData));

        $html = "";
        foreach ($contactData->results as $key => $value) {
            // print_r($value->properties);
            // die;
            $html .= "
            <tr'>
                <form method='post'>
                <th scope='row'>" . $value->id . "</th>
                <td text-center>" . $value->properties->firstname . "</td>
                <td text-center>" . $value->properties->lastname . "</td>
                <td text-center>" . $value->properties->email . "</td>
                <td text-center>" . $value->properties->createdate . "</td>
                <td text-center>&nbsp&nbsp<button class='btn btn-outline-danger' name='Did' value='" . $value->id . "'>Delete</button>
                </form>
                <form method='post' action='http://remote.local.cedcommerce.com/hubspotremote/contact/edit?bearer=eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJ1c2VyX2lkIjoiNjI2YmNjMzc2NzM0MTkzZmQ0NTA0MWI5Iiwicm9sZSI6ImFkbWluIiwiZXhwIjoxNjg4NjM2MTE1LCJpc3MiOiJodHRwczpcL1wvYXBwcy5jZWRjb21tZXJjZS5jb20iLCJ0b2tlbl9pZCI6IjYyYzU1NzUzODBjMDYwNTA5MjA2MTVjMiJ9.i45WyHgJ3b11ntGWMGuiNMUri6ezbnALFpFoZkhS2KHGbNA0xge2R6AR-Dsd1U-Gdcv5E9nrQKa3sEh_k7SGA_V4_FGAmFuJUQ5lrLoFpj9oaCc0qSb5A7hf3TY592SozFp-jKRxPlVSWqLhFghWTvcVLV-S_8VfhtSkbretnDY00MCJFaZmTboZkv-FYwHUQM2u1GNsYQAegXL8lHDtz3d9vw1d_t24eZYcvlBlAU1gRQyJQNqaqVThgGdHEvqmyYB2iEsk3LgI8rcxdBEBFYHFJMCfL05BlZ6Ht55d0d5gku-_tGK9cnPz2EVDfQ9OlaQmTrxl2zkTC6Z4G56zIQ'>
                    <button class='ml-3 mt-2 btn btn-outline-info' name='Eid' value='" . $value->id . "'>Edit</button>
                </form></td>
            </tr>
            ";
        }
        $this->view->data = $html;


        if ($this->request->isPost()) {

            $delID = $this->request->getPost("Did");
            // die($delID);
            $ch = curl_init();
            curl_setopt_array($ch, array(
                CURLOPT_URL => 'https://api.hubapi.com/crm/v3/objects/contacts/' . $delID . '?hapikey=1b1ebd50-22c2-4174-bb58-ba6ff2f7c95a',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => 'DELETE',
            ));

            $response = curl_exec($ch);
            // die($response);
            $this->response->redirect("http://remote.local.cedcommerce.com/hubspotremote/contact/list?bearer=eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJ1c2VyX2lkIjoiNjI2YmNjMzc2NzM0MTkzZmQ0NTA0MWI5Iiwicm9sZSI6ImFkbWluIiwiZXhwIjoxNjg4NjM2MTE1LCJpc3MiOiJodHRwczpcL1wvYXBwcy5jZWRjb21tZXJjZS5jb20iLCJ0b2tlbl9pZCI6IjYyYzU1NzUzODBjMDYwNTA5MjA2MTVjMiJ9.i45WyHgJ3b11ntGWMGuiNMUri6ezbnALFpFoZkhS2KHGbNA0xge2R6AR-Dsd1U-Gdcv5E9nrQKa3sEh_k7SGA_V4_FGAmFuJUQ5lrLoFpj9oaCc0qSb5A7hf3TY592SozFp-jKRxPlVSWqLhFghWTvcVLV-S_8VfhtSkbretnDY00MCJFaZmTboZkv-FYwHUQM2u1GNsYQAegXL8lHDtz3d9vw1d_t24eZYcvlBlAU1gRQyJQNqaqVThgGdHEvqmyYB2iEsk3LgI8rcxdBEBFYHFJMCfL05BlZ6Ht55d0d5gku-_tGK9cnPz2EVDfQ9OlaQmTrxl2zkTC6Z4G56zIQ");
        }
    }

    public function addAction()
    {
        if ($this->request->isPost()) {
            echo "<pre>";
            print_r($this->request->getPost());

            // die;

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.hubapi.com/crm/v3/objects/contacts?hapikey=1b1ebd50-22c2-4174-bb58-ba6ff2f7c95a',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode(array(
                    'properties' =>
                    array(
                        'company' => '' . $this->request->getPost('company') . '',
                        'email' => '' . $this->request->getPost('email') . '',
                        'firstname' => '' . $this->request->getPost('firstname') . '',
                        'lastname' => '' . $this->request->getPost('lastname') . '',
                        'phone' => '' . $this->request->getPost('number') . '',
                    ),
                )),
                CURLOPT_HTTPHEADER => array(
                    'content-type: application/json'
                ),

            ));

            $response = curl_exec($curl);
            $responseArray = json_decode($response, true);
            if (isset($responseArray['id'])) {
                $this->view->flag = $responseArray['id'];
            }
        }
    }

    public function editAction()
    {

        if ($this->request->getPost('lastname')) {
            // die($this->request->getPost('email'));
            // die($this->request->getPost('id'));
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.hubapi.com/crm/v3/objects/contacts/' . $this->request->getPost('id') . '?hapikey=1b1ebd50-22c2-4174-bb58-ba6ff2f7c95a',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'PATCH',
                CURLOPT_POSTFIELDS => json_encode(array(
                    "properties" => array(
                        "firstname" => '' . $this->request->getPost('firstname') . '',
                        "lastname" => '' . $this->request->getPost('lastname') . '',
                        "email" => '' . $this->request->getPost('email') . '',

                    )
                )),
                CURLOPT_HTTPHEADER => array(
                    'content-type: application/json'
                ),
            ));

            $response = curl_exec($curl);
            
            echo "<pre>";
            $responseArray = json_decode($response, true);
            // die(print_r($responseArray['id']));
            if (isset($responseArray['id'])) {
                echo "<h1 class='text-center text-success'>Information Updated Successfully</h1>";
                // sleep(100);
                $this->response->redirect("http://remote.local.cedcommerce.com/hubspotremote/contact/list?bearer=eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJ1c2VyX2lkIjoiNjI2YmNjMzc2NzM0MTkzZmQ0NTA0MWI5Iiwicm9sZSI6ImFkbWluIiwiZXhwIjoxNjg4NjM2MTE1LCJpc3MiOiJodHRwczpcL1wvYXBwcy5jZWRjb21tZXJjZS5jb20iLCJ0b2tlbl9pZCI6IjYyYzU1NzUzODBjMDYwNTA5MjA2MTVjMiJ9.i45WyHgJ3b11ntGWMGuiNMUri6ezbnALFpFoZkhS2KHGbNA0xge2R6AR-Dsd1U-Gdcv5E9nrQKa3sEh_k7SGA_V4_FGAmFuJUQ5lrLoFpj9oaCc0qSb5A7hf3TY592SozFp-jKRxPlVSWqLhFghWTvcVLV-S_8VfhtSkbretnDY00MCJFaZmTboZkv-FYwHUQM2u1GNsYQAegXL8lHDtz3d9vw1d_t24eZYcvlBlAU1gRQyJQNqaqVThgGdHEvqmyYB2iEsk3LgI8rcxdBEBFYHFJMCfL05BlZ6Ht55d0d5gku-_tGK9cnPz2EVDfQ9OlaQmTrxl2zkTC6Z4G56zIQ");
            }
            curl_close($curl);
        }
        $ch = curl_init("https://api.hubapi.com/crm/v3/objects/contacts/" . $this->request->getPost('Eid') . "?archived=false&hapikey=1b1ebd50-22c2-4174-bb58-ba6ff2f7c95a");
        curl_setopt_array(
            $ch,
            array(
                CURLOPT_RETURNTRANSFER => true,
            )
        );

        echo "<pre>";
        $response = curl_exec($ch);
        $arrayData = json_decode($response, true);
        // die(print_r($arrayData['id']));
        $this->view->data = json_decode($response, true);
    }
}
