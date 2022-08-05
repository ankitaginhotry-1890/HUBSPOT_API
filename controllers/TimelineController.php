<?php

namespace App\Hubspotremote\Controllers;

use App\Apiconnect\Components\Webhook;
use App\Hubspotremote\Models\HubSpot_Token;

use Phalcon\Http\Response;
use ValueError;

class TimelineController extends \App\Core\Controllers\BaseController
{
    public function listingAction()
    {
        echo "<pre>";
        $helper = new HelperController();
        $response = $helper->curlGet("crm/v3/timeline/" . APPID . "/event-templates", DEVAPIKEY);
        // print_r($response);
        $html = "";
        foreach ($response['results'] as $key => $value) {
            print_r($value);
            $html .= '<tr>
                        <td>
                            <span class="custom-checkbox">
                                <input type="checkbox" id="checkbox1" name="options[]" value="1">
                                <label for="checkbox1"></label>
                            </span>
                        </td>
                        <td>' . $value['id'] . '</td>
                        <td><a href="https://remote.local.cedcommerce.com/hubspotremote/timeline/createevent?bearer=eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJ1c2VyX2lkIjoiNjI2YmNjMzc2NzM0MTkzZmQ0NTA0MWI5Iiwicm9sZSI6ImFkbWluIiwiZXhwIjoxNjg4NjM2MTE1LCJpc3MiOiJodHRwczpcL1wvYXBwcy5jZWRjb21tZXJjZS5jb20iLCJ0b2tlbl9pZCI6IjYyYzU1NzUzODBjMDYwNTA5MjA2MTVjMiJ9.i45WyHgJ3b11ntGWMGuiNMUri6ezbnALFpFoZkhS2KHGbNA0xge2R6AR-Dsd1U-Gdcv5E9nrQKa3sEh_k7SGA_V4_FGAmFuJUQ5lrLoFpj9oaCc0qSb5A7hf3TY592SozFp-jKRxPlVSWqLhFghWTvcVLV-S_8VfhtSkbretnDY00MCJFaZmTboZkv-FYwHUQM2u1GNsYQAegXL8lHDtz3d9vw1d_t24eZYcvlBlAU1gRQyJQNqaqVThgGdHEvqmyYB2iEsk3LgI8rcxdBEBFYHFJMCfL05BlZ6Ht55d0d5gku-_tGK9cnPz2EVDfQ9OlaQmTrxl2zkTC6Z4G56zIQ&id=' . $value['id'] . '&objectType=' . $value['objectType'] . '">' . $value['name'] . '</a></td>
                        <td>' . $value['objectType'] . '</td>
                        <td>' . $value['headerTemplate'] . '</td>
                        <td>
                            <button class="editData" name="Edit" href="#editEmployeeModal" value=' . $value['id'] . ' data-toggle="modal" style="font-size:20px; transform:rotate(90deg); margin-left:5px">✎</button></form>
                            <form method="post"><button class="delete" name="Did" value=' . $value['id'] . ' data-toggle="modal"><i class="material-icons" data-toggle="tooltip" title="Delete">&#xE872;</i></button></form>
                        </td>
                    </tr>
                    <tr>';
        }
        $this->view->tableData = $html;

        if ($this->request->getPost("Did")) {
            $response = $helper->curlDelete("/crm/v3/timeline/" . APPID . "/event-templates/" . $this->request->getPost("Did"), DEVAPIKEY);
            // die($response);
            echo "alert('Deleted')";
        }
        // die;
    }

    //Event Crud left update and delete only..
    public function createTemplateAction()
    {
        if ($this->request->getPost()) {


            $helper = new HelperController();
            $response = $helper->curlPost("crm/v3/timeline/" . APPID . "/event-templates", $this->request->getPost("PostData"), DEVAPIKEY);
            // return json_encode($this->request->getPost("PostData"), true);
            return json_encode($response, true);
        }
    }

    public function getCustomTemplateDataAction()
    {
        if ($this->request->getPost("id")) {
            $id = $this->request->getPost("id");
            $helper = new HelperController();
            $response = $helper->curlGet("crm/v3/timeline/971551/event-templates/" . $id, DEVAPIKEY);
            return json_encode($response, true);
        }
    }

    public function updateTemplateAction()
    {
        if ($this->request->getPost()) {

            // $helper = new HelperController();
            // $response = $helper->curlPut("crm/v3/timeline/" . APPID . "/event-templates", $this->request->getPost("PostData"), DEVAPIKEY);
            // return json_encode($this->request->getPost("PostData"), true);
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.hubapi.com/crm/v3/timeline/971551/event-templates/' . $this->request->getPost("id") . '?hapikey=d58bff24-1b67-49cf-9547-d6ab7233015f',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'PUT',
                CURLOPT_POSTFIELDS => json_encode($this->request->getPost("PostData"), true),
                CURLOPT_HTTPHEADER => array(
                    'content-type: application/json'
                ),
            ));

            $response = curl_exec($curl);
            return $response;
        }
    }



    public function createEventAction()
    {
        if ($this->request->get("id")) {
            $this->view->eventTemplateID = $this->request->get("id");
            $objectType = $this->request->get("objectType");

            $helper = new HelperController();

            $response = $helper->curlGet("crm/v3/objects/" . $objectType . "?limit=100");
            echo "<pre>";
            $htm = "";
            foreach ($response['results'] as $key => $value) {
                // print_r($value['properties']['email']."<br>");
                if ($objectType == "companies") {
                    $htm .= "<option value=" . $value['id'] . ">" . $value['properties']['name'] . "</option>";
                }
                if ($objectType == "deals") {
                    $htm .= "<option value=" . $value['id'] . ">" . $value['properties']['dealname'] . "</option>";
                } else {
                    $htm .= "<option value=" . $value['id'] . ">" . $value['properties']['email'] . "</option>";
                }
            }
            $this->view->optiondata = $htm;


            $html = "";
            $templateData = $helper->curlGet("crm/v3/timeline/" . APPID . "/event-templates/" . $this->request->get("id"), DEVAPIKEY);
            foreach ($templateData['tokens'] as $key => $value) {
                // print_r($value);
                if ($value['type'] == "string") {

                    $html .= '<div class="form-group"><label>' . $value['label'] . '</label><input type="text" class="form-control" name=' . $value['name'] . ' placeholder=' . $value['name'] . ' value="" /></div><br>';
                } else {
                    $html .= ' <div class="form-group"><label>' . $value['label'] . '</label><input type=' . $value['type'] . ' name=""class="form-control" name=' . $value['name'] . ' placeholder=' . $value['name'] . '></div><br>';
                }
            }

            $this->view->dynamicFeild = $html;
        }


        if ($this->request->getPost("createEvent")) {
            echo "<pre>";
            $Postdata = $this->request->getPost();
            $Newdata = $this->request->getPost();
            print_r($Postdata);
            unset($Newdata["eventTemplateId"]);
            unset($Newdata["objectID"]);
            unset($Newdata["createEvent"]);
            print_r($Newdata);
            $data = [
                'eventTemplateId' => $Postdata["eventTemplateId"],
                'objectId' => $Postdata["objectID"],
                'tokens' => $Newdata,
            ];

            $helper = new HelperController();
            $response = $helper->curlPost("crm/v3/timeline/events", $data);
            print_r($response);


            if (isset($response->id)) {
                $this->view->msg = "success";
            }
        }
    }
}
