<?php

namespace App\Hubspotremote\Controllers;

use Phalcon\Http\Response;

class CompanyController extends \App\Core\Controllers\BaseController
{
    //Function to hit the enpoint of Company (Read) and for Listing
    public function listAction()
    {
        $ArrayData = json_decode($this->hubspot->crm()->companies()->basicApi()->getPage($limit = 100), true);
        $html = "";
        foreach ($ArrayData['results'] as $key => $value) {
            $html .= "
            <tr'>
                <form method='post'>
                <th scope='row'>" . $value["id"] . "</th>
                <td text-center>" . $value['properties']['name'] . "</td>
                <td text-center>" . $value['properties']['createdate'] . "</td>
                <td text-center>&nbsp&nbsp<button class='btn btn-outline-danger' name='Did' value='" . $value["id"] . "'>Delete</button></form>
                <td><form method='post' target='_parent' action='https://remote.local.cedcommerce.com/hubspotremote/engagement/profile?bearer=eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJ1c2VyX2lkIjoiNjI2YmNjMzc2NzM0MTkzZmQ0NTA0MWI5Iiwicm9sZSI6ImFkbWluIiwiZXhwIjoxNjg4NjM2MTE1LCJpc3MiOiJodHRwczpcL1wvYXBwcy5jZWRjb21tZXJjZS5jb20iLCJ0b2tlbl9pZCI6IjYyYzU1NzUzODBjMDYwNTA5MjA2MTVjMiJ9.i45WyHgJ3b11ntGWMGuiNMUri6ezbnALFpFoZkhS2KHGbNA0xge2R6AR-Dsd1U-Gdcv5E9nrQKa3sEh_k7SGA_V4_FGAmFuJUQ5lrLoFpj9oaCc0qSb5A7hf3TY592SozFp-jKRxPlVSWqLhFghWTvcVLV-S_8VfhtSkbretnDY00MCJFaZmTboZkv-FYwHUQM2u1GNsYQAegXL8lHDtz3d9vw1d_t24eZYcvlBlAU1gRQyJQNqaqVThgGdHEvqmyYB2iEsk3LgI8rcxdBEBFYHFJMCfL05BlZ6Ht55d0d5gku-_tGK9cnPz2EVDfQ9OlaQmTrxl2zkTC6Z4G56zIQ&username=" . $value['properties']['name'] . "&user_id=" . $value['id'] . "&objectType=company'><button class='btn btn-outline-info'>Preview</button></form></td>
                
               </td>
            </tr>
            ";
        }
        $this->view->data = $html;

        //For Delete a Spacific Company Based on their Company ID
        if ($this->request->isPost('Did')) {
            $CompanyID = $this->request->getPost('Did');
            $response = $this->hubspot->crm()->companies()->basicApi()->archive($CompanyID);
            $this->response->redirect('http://remote.local.cedcommerce.com/hubspotremote/company/list?bearer=' . BEARER . '');
        }
    }


    //Fucntion Edit the Details of Company
    public function editAction()
    {
        $helper = new HelperController();
        $CompanyID = $this->request->getPost('Eid');
        // $response = $this->hubspot->crm()->companies()->basicApi()->update($CompanyID);
        $arrayData = $helper->curlGet('crm/v3/objects/companies/' . $this->request->getPost('Eid') . '?archived=false');
        print_r($arrayData);
        die;
    }

    //Function add a new company into Company Obejct of hubspot
    public function addAction()
    {
        if ($this->request->isPost('state')) {
            $postData = [

                "properties" =>
                [
                    "name" => '' . $this->request->getPost('companyName') . '',
                    "domain" => '' . $this->request->getPost('domain') . '',
                    "city" => '' . $this->request->getPost('city') . '',
                    "phone" => '' . $this->request->getPost('number') . '',
                    "state" => '' . $this->request->getPost('state') . '',
                ]
            ];
            $responseArray = json_decode($this->hubspot->crm()->companies()->basicApi()->create($postData), true);
            if (isset($responseArray['id'])) {
                $this->view->flag = $$responseArray['id'];
            } else {
                die("Something went wrong! :( Company is not registered");
            }
        }
    }
}
