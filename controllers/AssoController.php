<?php

namespace App\Hubspotremote\Controllers;

use App\Connector\Components\Helper;
use AWS\CRT\HTTP\Response;
use Aws\Pricing\PricingClient;
use Type;

class AssoController extends \App\Core\Controllers\BaseController
{
    //General Function of Common Purpose
    ///--------------------------------------------------------Code Optimization--------------------------------///

    public function indexAction()
    {
        echo "hello";
    }
    //Create Association with Object
    public function createAssociationWithObjectAction()
    {
        if ($this->request->getPost('primaryObject')) {
            $data = $this->request->getPost();
            $primaryObject = $data['primaryObject'];
            $primaryObjectID = $data['primaryObjectID'];
            $AssoObjectID = $data['associatedObjectID'];
            $AsssObject_type = $data['associatedObjectType'];
            $associationType = $data['associationType'];
            $response = $this->hubspot->crm()->objects()->associationsApi()->create($primaryObject, $primaryObjectID, $AsssObject_type, $AssoObjectID, $associationType);
            return json_encode($response, true);
        } else {
            return "Association unsuccessfully";
        }
    }

    //Remove Association with Object
    public function removeAssociationWithObjectAction()
    {
        if ($this->request->getPost('primaryObject')) {
            $primaryObject = $this->request->getPost('primaryObject');
            $primaryObjectID = $this->request->getPost('primaryObjectID');
            $AssoObjectID = $this->request->getPost('associatedObjectID');
            $AsssObject_type = $this->request->getPost('associatedObjectType');
            $associationType = $this->request->getPost('associationType');
            $response = $this->hubspot->crm()->objects()->associationsApi()->archive($primaryObject, $primaryObjectID, $AsssObject_type, $AssoObjectID, $associationType);
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
            $response = $this->hubspot->crm()->objects()->associationsApi()->getAll($primaryObject, $primaryObjectID, $toObjectType, null, 500);
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
        $dealData = json_decode($this->hubspot->crm()->deals()->basicApi()->getPage($limit = 100), true);
        $HtmlContainstr = "";
        foreach ($dealData['results'] as $key => $value) {
            $HtmlContainstr .= '<option value=' . $value['id'] . '>' . $value['properties']['dealname'] . '</option>';
        }
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
            // $ArrayData = $helper->curlGet("crm/v3/objects/contacts/" . $Contact_id);
            $ArrayData = json_decode($this->hubspot->crm()->contacts()->basicApi()->getById($Contact_id), true);
            return json_encode($ArrayData, true);
        } else {
            return "data Invalid Please Provide a contact_id";
        }
    }

    //contact Details
    public function contactDeailsAction()
    {
        $contactData = json_decode($this->hubspot->crm()->contacts()->basicApi()->getPage($limit = 100), true);
        return json_encode($contactData, true);
    }

    //Deal Details
    public function DealDetailsAction()
    {
        $contactData = json_decode($this->hubspot->crm()->deals()->basicApi()->getPage($limit = 100), true);
        return $contactData;
    }

    //Companty Details
    public function companyDeatilsAction()
    {
        $ArrayData = json_decode($this->hubspot->crm()->companies()->basicApi()->getPage($limit = 100), true);
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
        // $response = $helper->curlGet("crm/v3/objects/line_items?properties=name%2Chs_product_id%2Cquantity%2Cprice");
        $response = json_decode($this->hubspot->crm()->lineItems()->basicApi()->getPage($limit = 100, null, "name,product_id,quantity,price"), true);
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
    //View Action
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
