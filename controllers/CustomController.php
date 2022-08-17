<?php

namespace App\Hubspotremote\Controllers;

use Phalcon\Http\Client\Provider\Curl;

//Class of Custom Object
class CustomController extends \App\Core\Controllers\BaseController
{
    public function indexAction()
    {
        die("working");
    }

    public function createCustomAction()
    {
        if ($this->request->getPost()) {
            $data = $this->request->getPost();
            $CustomObjectdata = json_decode($this->hubspot->crm()->schemas()->coreApi()->create($data), true);
            die(json_encode($CustomObjectdata, true));
        }
    }

    public function listCustomAction()
    {
        $helper = new HelperController();
        $CustomObjectdata = json_decode($this->hubspot->crm()->schemas()->coreApi()->getAll(false), true);
        
        $html = '';
        foreach ($CustomObjectdata['results'] as $key => $value) {
            $html .= "
            <tr'>
                <form method='post'>
                <th scope='row'>" . $value['objectTypeId'] . "</th>
                <th scope='row'>" . $value['id'] . "</th>
                <td text-center>" . $value['name'] . "</td>
                <td text-center>" . $value['primaryDisplayProperty'] . "</td>
                <td text-center>" . implode(", ", $value['requiredProperties']) . "</td>
                <td text-center>&nbsp&nbsp<button class='btn btn-outline-danger' name='Did' value='" . $value['objectTypeId'] . "'>Delete</button>
                </form>
                <form method='post' action='https://remote.local.cedcommerce.com/hubspotremote/custom/update?bearer=eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJ1c2VyX2lkIjoiNjI2YmNjMzc2NzM0MTkzZmQ0NTA0MWI5Iiwicm9sZSI6ImFkbWluIiwiZXhwIjoxNjg4NjM2MTE1LCJpc3MiOiJodHRwczpcL1wvYXBwcy5jZWRjb21tZXJjZS5jb20iLCJ0b2tlbl9pZCI6IjYyYzU1NzUzODBjMDYwNTA5MjA2MTVjMiJ9.i45WyHgJ3b11ntGWMGuiNMUri6ezbnALFpFoZkhS2KHGbNA0xge2R6AR-Dsd1U-Gdcv5E9nrQKa3sEh_k7SGA_V4_FGAmFuJUQ5lrLoFpj9oaCc0qSb5A7hf3TY592SozFp-jKRxPlVSWqLhFghWTvcVLV-S_8VfhtSkbretnDY00MCJFaZmTboZkv-FYwHUQM2u1GNsYQAegXL8lHDtz3d9vw1d_t24eZYcvlBlAU1gRQyJQNqaqVThgGdHEvqmyYB2iEsk3LgI8rcxdBEBFYHFJMCfL05BlZ6Ht55d0d5gku-_tGK9cnPz2EVDfQ9OlaQmTrxl2zkTC6Z4G56zIQ'>
                    <button class='ml-3 mt-2 btn btn-outline-info' name='Eid' value='" . $value['objectTypeId'] . "'>Edit</button>
                </form></td>
            </tr>
            ";
        }

        $this->view->data = $html;

        //For delete
        if ($this->request->getPost('Did')) {
            $delete_id = $this->request->getPost('Did');
            $response = json_decode($this->hubspot->crm()->schemas()->coreApi()->archive($delete_id, false), true);
            die(print_r($response));
        }
    }

    public function updateAction()
    {
        $edit_id = '';

        if ($this->request->get('Eid')) {
            $edit_id = $this->request->get('Eid');
            $helper = new HelperController();
            $response = $helper->curlGet('crm/v3/schemas/' . $edit_id);
            // echo "<pre>";
            // print_r($response);
            // die;
            $requriedPPT = "";
            $searchablePPT = "";
            $primaryDisplayPPT = "";
            $b = 0;
            foreach ($response['properties'] as $key => $value) {
                //For getting the only user created property
                if (isset($value['updatedAt'])) {
                    if (isset($response['requiredProperties'][$b]) && $response['requiredProperties'][$b] == $value['name']) {
                        $requriedPPT .= '
                    <div class="form-check">
                        <input class="form-check-input requiredProperties" type="checkbox" value="' . $value['name'] . '" id="flexCheckDefault" checked>
                        <label class="form-check-label" for="flexCheckDefault">
                        ' . $value['name'] . '
                        </label>
                        </div><br>';
                        $b = $b + 1;
                    } else {
                        $requriedPPT .= '
                        <div class="form-check">
                        <input class="form-check-input requiredProperties" type="checkbox" value="' . $value['name'] . '" id="flexCheckDefault">
                        <label class="form-check-label" for="flexCheckDefault">
                        ' . $value['name'] . '
                        </label>
                        </div><br>';
                    }
                    if ($response['searchableProperties'][$b] && $response['searchableProperties'][$b] == $value['name']) {
                        $searchablePPT .= '
                    <div class="form-check">
                        <input class="form-check-input searchableProperties" type="checkbox" value="' . $value['name'] . '" id="flexCheckDefault" checked>
                        <label class="form-check-label" for="flexCheckDefault">
                        ' . $value['name'] . '
                        </label>
                        </div><br>';
                        $b = $b + 1;
                    } else {
                        $searchablePPT .= '
                        <div class="form-check">
                        <input class="form-check-input searchableProperties" type="checkbox" value="' . $value['name'] . '" id="flexCheckDefault">
                        <label class="form-check-label" for="flexCheckDefault">
                        ' . $value['name'] . '
                        </label>
                        </div><br>';
                    }
                    if ($response['primaryDisplayProperty'] && $response['primaryDisplayProperty'] == $value['name']) {
                        $primaryDisplayPPT .= '<div class="form-check">
                        <input class="form-check-input primaryDisplayProperty" type="radio"  name="same" value=' . $value['name'] . ' id="flexRadioDefault1" checked>
                        <label class="form-check-label" for="flexRadioDefault1">
                        ' . $value['name'] . '
                        </label>
                      </div><br>';
                        $b = $b + 1;
                    } else {
                        $primaryDisplayPPT .= '<div class="form-check">
                        <input type="hidden" class="EditID" value=' . $edit_id . '>
                        <input class="form-check-input primaryDisplayProperty" type="radio" name="same" value=' . $value['name'] . ' id="flexRadioDefault1" >
                        <label class="form-check-label" for="flexRadioDefault1">
                        ' . $value['name'] . '
                        </label>
                      </div><br>';
                    }
                }
            }
            $this->view->requriedPPT = $requriedPPT;
            $this->view->searchablePPT = $searchablePPT;
            $this->view->primaryDisplayPPT = $primaryDisplayPPT;
        }

        if ($this->request->getPost('EditID')) {
            // $jsonData = json_decode(json_encode($this->request->getPost(), true), true);
            $jsonData = (json_encode($this->request->getPost(), true));
            $arrayData = json_decode($jsonData, true);
            $jsondata2 = json_encode($arrayData['data'], true);
            $objectTypeID = $this->request->getPost('EditID');
            echo "<pre>";
            $CustomObjectUpdatedata = json_decode($this->hubspot->crm()->schemas()->coreApi()->update($objectTypeID), true);
            print_r($CustomObjectUpdatedata);
            die;
            return  $response;
        }
    }
}
