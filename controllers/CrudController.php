<?php

namespace App\Hubspotremote\Controllers;

use MongoDB\Operation\Update;
use Phalcon\Http\Client\Provider\Curl;

class CrudController extends \App\Core\Controllers\BaseController
{
    public function indexAction()
    {
        // die("working");
    }

    //Action for searching the custom object
    public function objectListingAction()
    {
    }

    //Functions
    public function getCustomObjectDataAction()
    {
        $helper =  new HelperController();
        $response = $helper->curlGet("crm/v3/schemas?archived=false");
        return json_encode($response, true);
    }

    //Listing the custom object Items
    public function listingobjectitemAction()
    {
        echo "<pre>";
        $helper = new HelperController();
        $ObjectTypeID = $this->request->get('objectTypeID');
        $tHead = '';
        $tBody = '';
        $property = [];
        $propertyType = [];
        $property_url = '';
        $propertyFeildTypeStr = '';
        $count = 0;
        $propertyFeildTypeAsso = [];
        // $tableData='';
        //Tbody

        $response = $helper->curlGet('crm/v3/schemas/' . $ObjectTypeID);
        // die(print_r($response));
        foreach ($response['properties'] as $key => $value) {
            $hs = strstr($value['name'], "hs");
            $hubspot = strstr($value['name'], "hubspot");
            if (strlen($hs) == 0 && strlen($hubspot) == 0) {
                // print_r($value['name']);
                array_push($property, $value['name']);
                // array_push($propertyType, $value['fieldType']);
                $propertyType[$value['name']] = $value['fieldType'];

                $tHead .= '<th class="text-info">' . $value['name'] . '</th>';
            }
        }
        $tHead .= '<th class="text-info">Action</th><th class="text-info">Preview</th>';

        $propertyFeildTypeStr = serialize($propertyType);


        //Table Head Data
        $this->view->TableHead = $tHead;
        // print_r($property);

        //For creating A property Url
        foreach ($property as $key => $value) {
            $property_url .= $value . ",";
            // $propertyFeildTypeStr .= '{"' . $value . '":"' . $propertyType[$key] . '"}';
        }

        // die($propertyFeildTypeStr);
        $property_url = urlencode($property_url);
        // echo $property_url;

        //TBody
        $objectData = $helper->curlGet('crm/v3/objects/' . $ObjectTypeID . '?limit=10&properties=' . $property_url . 'j&archived=false');


        // print_r($objectData);
        // echo "new";
        $objectType=$this->request->get('ObjectName');
        echo $objectType;
        // die;
        foreach ($objectData['results'] as $key => $value) {
            $temp = '';
            foreach ($value['properties'] as $k => $v) {
                // die;
                if ($property[$count] == $k) {
                    $temp .= "<td scope='row'>" . $v . "</td>";
                    $count = $count + 1;
                }
            }
            // print_r($this->request->get(''));
            // die;
            echo "<br><br>";
            $count = 0;
          
            $tBody .= "<tr>" . $temp . "<td><form method='post' action='http://remote.local.cedcommerce.com/hubspotremote/crud/updateCustomCrud?bearer=" . BEARER . "'>
                <input type='hidden' name='objectTypeID' value=" . $ObjectTypeID . ">
                <input type='hidden' name='propertyURL' value=" . $property_url . ">
                <input type='hidden' name='propertyType' value=" . $propertyFeildTypeStr . ">
                <button class='ml-3 mt-2 btn btn-outline-info' name='Eid' value='" . $value['id'] . "'>Edit</button>
                &nbsp&nbsp</form><form method='post'><input type='hidden' name='ObjectTypeID' value=" . $ObjectTypeID . "><button class='btn btn-outline-danger mt-2 ml-2' name='Did' value='" . $value['id'] . "'>Delete</button></td></form>

                <td>
                <form method='post' target='_parent' action='https://remote.local.cedcommerce.com/hubspotremote/engagement/profile?bearer=eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJ1c2VyX2lkIjoiNjI2YmNjMzc2NzM0MTkzZmQ0NTA0MWI5Iiwicm9sZSI6ImFkbWluIiwiZXhwIjoxNjg4NjM2MTE1LCJpc3MiOiJodHRwczpcL1wvYXBwcy5jZWRjb21tZXJjZS5jb20iLCJ0b2tlbl9pZCI6IjYyYzU1NzUzODBjMDYwNTA5MjA2MTVjMiJ9.i45WyHgJ3b11ntGWMGuiNMUri6ezbnALFpFoZkhS2KHGbNA0xge2R6AR-Dsd1U-Gdcv5E9nrQKa3sEh_k7SGA_V4_FGAmFuJUQ5lrLoFpj9oaCc0qSb5A7hf3TY592SozFp-jKRxPlVSWqLhFghWTvcVLV-S_8VfhtSkbretnDY00MCJFaZmTboZkv-FYwHUQM2u1GNsYQAegXL8lHDtz3d9vw1d_t24eZYcvlBlAU1gRQyJQNqaqVThgGdHEvqmyYB2iEsk3LgI8rcxdBEBFYHFJMCfL05BlZ6Ht55d0d5gku-_tGK9cnPz2EVDfQ9OlaQmTrxl2zkTC6Z4G56zIQ&username=" .  $this->request->get('objectType') . " " .  $this->request->get('objectType'). "&email=" .  $this->request->get('objectType') . "&user_id=" . $value['id'] . "&objectType=" . $objectType . "'><button class='btn btn-primary'>Preview</button></form>
                </td>
                <tr>";
        }

        $createItemBTNData = "<form method='post' action='http://remote.local.cedcommerce.com/hubspotremote/crud/createobjectitem?bearer=" . BEARER . "'><input type='hidden' name='objectTypeID' value=" . $ObjectTypeID . ">
        <input type='hidden' name='propertyURL' value=" . $property_url . ">
        <input type='hidden' name='propertyType' value=" . $propertyFeildTypeStr . ">
        <button class='btn btn-outline-success' id='createBtn'>Create Item</button>
        </form>";


        // print_r($tBody);
        // die;
        $this->view->CreateBTN = $createItemBTNData;
        $this->view->TableBody = $tBody;

        // print_r($tBody);
        // die;

        if ($this->request->getPost('Did')) {
            $objectID = ($this->request->getPost('Did'));
            $objectType = ($this->request->getPost('ObjectTypeID'));
            $helper->curlDelete('crm/v3/objects/' . $objectType . '/' . $objectID . '');
            header('Location: ' . $_SERVER['REQUEST_URI']);
            // die()
        }
    }

    //Create custom object items
    public function createobjectitemAction()
    {

        if ($this->request->getPost('ObjectTypeID')) {
            echo "<pre>";
            $PostData = $this->request->getPost();
            $objectTypeID = $this->request->getPost('ObjectTypeID');
            unset($PostData['ObjectID']);
            unset($PostData['ObjectTypeID']);
            $arr = [
                'properties' =>
                [],
            ];

            $arr['properties'] = $PostData;

            $helper = new HelperController();
            $response = $helper->curlPost('crm/v3/objects/' . $objectTypeID, $arr);


            if (isset($response->id)) {
                die("<h1 class='text-center text-success'>Item Added Successfully</h1>");
            } else {
                die("<h1 class='text-center text-warning'>Something Went Wrong</h1>");
            }
        }

        echo "<pre>";
        $objectTypeID = $this->request->get("objectTypeID");
        $propertyURL = $this->request->get('propertyURL');
        $propertyType = unserialize($this->request->get('propertyType'));
        $str = '';
        foreach ($propertyType as $key => $value) {

            $str .= '<div class="row">
            <div class="col-sm-12">
            <div class="form-group">
            <input class="form-control" type="' . $value . '" placeholder=' . $key . ' name="' . $key . '" value="">
            </div>
            </div>
            </div>';
        }

        echo $str;
        $this->view->data = $str;
        $this->view->objectType = $objectTypeID;
        // die(print_r($propertyType));


    }

    public function updateCustomCrudAction()
    {
        $helper = new HelperController();
        if ($this->request->get('Eid')) {

            $objectTypeID = $this->request->get("objectTypeID");
            $objectID = $this->request->get('Eid');
            $propertyURL = $this->request->get('propertyURL');
            $propertyType = unserialize($this->request->get('propertyType')); //

            // for()
            // print_r($propertyType);
            $response = $helper->curlGet('crm/v3/objects/' . $objectTypeID . '/' . $objectID . '?properties=' . $propertyURL . '&archived=false');

            echo "<pre>";

            $str = '';
            foreach ($propertyType as $key => $value) {

                $str .= '<div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <input class="form-control" type="' . $value . '" placeholder=' . $key . ' name="' . $key . '" value=' . $response['properties'][$key] . '>
                    </div>
                </div>
            </div>';
            }

            echo $str;

            $this->view->data = $str;
            $this->view->objectType = $objectTypeID;
            $this->view->objectID = $objectID;
            // die;
            // $response = $helper->curlGet();
        }

        if ($this->request->getPost('ObjectTypeID')) {

            echo "<pre>";
            // die(print_r($));
            // $response= $helper->curlPatch();
            $PostData = $this->request->getPost();
            $objectID = $this->request->getPost('ObjectID');
            $objectTypeID = $this->request->getPost('ObjectTypeID');
            unset($PostData['ObjectID']);
            unset($PostData['ObjectTypeID']);
            print_r($PostData);
            echo $objectTypeID;
            echo $objectID;

            $arr = [
                'properties' =>
                [],
            ];

            $arr['properties'] = $PostData;
            $response = $helper->curlPatch('crm/v3/objects/' . $objectTypeID . '/' . $objectID . '', $arr);
            print_r($response);
            die;
        }
    }
}
