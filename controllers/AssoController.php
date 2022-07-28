<?php

namespace App\Hubspotremote\Controllers;

use App\Connector\Components\Helper;
use AWS\CRT\HTTP\Response;
use Type;

class AssoController extends \App\Core\Controllers\BaseController
{

    //Genral Function of Common Purpose
    ///--------------------------------------------------------Code Optiomisation--------------------------------///

    //Create Association with Object
    public function createAssociationWithObjectAction()
    {
        if ($this->request->getPost('primaryObject')) {
            // return "Sucessfull";
            $primaryObject = $this->request->getPost('primaryObject');
            $primaryObjectID = $this->request->getPost('primaryObjectID');
            $AssoObjectID = $this->request->getPost('associatedObjectID');
            $AsssObject_type = $this->request->getPost('associatedObjectType');
            $associationType = $this->request->getPost('associationType');
            $helper = new HelperController();
            $response = $helper->curlPut("crm/v3/objects/" . $primaryObject . "/" . $primaryObjectID . "/associations/" . $AsssObject_type . "/" . $AssoObjectID . "/" . $associationType . "");
            return json_encode($response, true);
        } else {
            return "Association unsucessfull";
        }
    }

    //Remove Association with Object
    public function removeAssociationWithObjectAction()
    {
        if ($this->request->getPost('primaryObject')) {
            // return "Sucessfull";
            $primaryObject = $this->request->getPost('primaryObject');
            $primaryObjectID = $this->request->getPost('primaryObjectID');
            $AssoObjectID = $this->request->getPost('associatedObjectID');
            $AsssObject_type = $this->request->getPost('associatedObjectType');
            $associationType = $this->request->getPost('associationType');
            $helper = new HelperController();
            $response = $helper->curlDelete("crm/v3/objects/" . $primaryObject . "/" . $primaryObjectID . "/associations/" . $AsssObject_type . "/" . $AssoObjectID . "/" . $associationType . "");
            return json_encode($response, true);
        } else {
            return "Association unsucessfull";
        }
    }

    //Function for Listing the Assocaiton between two Objects
    public function getAssociatedIbjectsDetailsAction()
    {
        if ($this->request->isPost()) {
            $helper = new HelperController();
            $primaryObject = $this->request->getPost('primaryObject');
            $primaryObjectID = $this->request->getPost('primaryObjectID');
            $toObjectType = $this->request->getPost('toObjectType');
            $response = $helper->curlGet("crm/v3/objects/" . $primaryObject . "/" . $primaryObjectID . "/associations/" . $toObjectType . "?limit=500");
            return json_encode($response, true);
        }
    }
    //----------------------------------------------------------;)----------------------------------------------------------------------------


    public function contactTocompanyAssoAction()
    {
        //Here you can create and remove Association between Company and Contacts
    }


    //Deal Association (Deal Main Page where you can create or remove association with another object)
    public function dealAction()
    {
        $helper = new HelperController();
        $dealData = $helper->curlGet('crm/v3/objects/deals?limit=10&archived=false');
        echo "<pre>";
        $HtmlContainstr = "";
        foreach ($dealData['results'] as $key => $value) {
            $HtmlContainstr .= '<option value=' . $value['id'] . '>' . $value['properties']['dealname'] . '</option>';
        }
        // die(print_r($dealData));
        $this->view->data = $HtmlContainstr;
    }

    //Just Only For Testing Purpose
    public function testingAction()
    {
        $helper = new HelperController();
        $contactData = $helper->curlGet('crm/v3/objects/contacts?limit=100&archived=false');

        if ($this->request->isPost('SearchData')) {
            return json_encode($contactData, true);
        }
    }

    public function testing2Action()
    {
        if ($this->request->isPost('data')) {
            return "Yaa Data recived";
        }
    }



    //-------------------------------Fetch Details-->Function()----------------------------------------//


    //One Contact Detail Fetch Fucntion
    public function contactDeatilsAction()
    {
        if ($this->request->getPost("contact_id")) {

            $Contact_id = $this->request->getPost("contact_id");
            $helper = new HelperController();
            $ArrayData = $helper->curlGet("crm/v3/objects/contacts/" . $Contact_id);
            return json_encode($ArrayData, true);
        } else {
            return "data Invalid Please Provide a contact_id";
        }
    }

    //contact Details
    public function contactDeailsAction()
    {
        $helper = new HelperController();
        $contactData = $helper->curlGet('crm/v3/objects/contacts?limit=100&archived=false');
        return json_encode($contactData, true);
    }

    //Deal Details
    public function DealDetailsAction()
    {
        $helper = new HelperController();
        $contactData = $helper->curlGet('crm/v3/objects/deals?limit=10&archived=false');
        return $contactData;
    }

    //Companty Details
    public function companyDeatilsAction()
    {
        $helper = new HelperController();
        $ArrayData = $helper->curlGet("crm/v3/objects/companies?limit=100&archived=false");
        return json_encode($ArrayData);
    }

    //Comman Function to fatch Details of An Object

    public function fatchObjectDetailsAction()
    {
        if ($this->request->getPost()) {
            $objectName = $this->request->getPost('ObjectName');
            $helper = new HelperController();
            $ArrayData = $helper->curlGet("crm/v3/objects/" . $objectName . "?limit=100&archived=false");
            return json_encode($ArrayData);
        }
    }

    //Line Item Deatils
    public function listItemDataAction()
    {
        $helper = new HelperController();
        $response = $helper->curlGet("crm/v3/objects/line_items?properties=name%2Chs_product_id%2Cquantity%2Cprice");
        $html = '';
        foreach ($response['results'] as $key => $value) {
            $html .= "
                <option value=" . $value['id'] . ">" . $value['properties']['name'] . "</option>
            ";
        }
        return $html;
    }


    //Filter Search and fetch details
    public function getSpacificContactDataAction($contact_id)
    {
        $helper = new HelperController();
        $jayParsedAry = [
            "filterGroups" => [
                [
                    "filters" => [
                        [
                            "propertyName" => "hs_object_id",
                            "operator" => "EQ",
                            "value" => $contact_id
                        ]
                    ]
                ]
            ]
        ];
        $ArrayData = $helper->curlPost("crm/v3/objects/contacts/search", $jayParsedAry);
        return $ArrayData;
    }

    //-----------------------------------------Custom Object Association---------------------------
    public function customAssoAction()
    {
        if ($this->request->getPost()) {
            $helper = new HelperController();
            $response = $helper->curlPost('crm/v3/schemas/' . $this->request->getPost('fromObjectTypeId') . '/associations', $this->request->getPost(), '1b1ebd50-22c2-4174-bb58-ba6ff2f7c95a');
            return json_encode($response, true);
        }
    }

    public function getExistingSchemaDataAction()
    {
        if ($this->request->getPost('schemaID')) {
            $schemaID = $this->request->getPost('schemaID');
            $helper = new HelperController();
            $response = $helper->curlGet("crm/v3/schemas/" . $schemaID);
            return json_encode($response, true);
        }
    }

    public function removeAssoCustomObjAction()
    {
        if ($this->request->getPost()) {

            $objectTypeID = $this->request->getPost('objectType');
            $uniqueID = $this->request->getPost('id');

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.hubapi.com/crm/v3/schemas/' . $objectTypeID . '/associations/' . $uniqueID . '?hapikey=1b1ebd50-22c2-4174-bb58-ba6ff2f7c95a',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'DELETE',
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            die(print_r($response));
        }
    }
}
