<?php

namespace App\Hubspotremote\Controllers;

use App\Hubspotremote\Models\HubSpot_Token;

class LineController extends \App\Core\Controllers\BaseController
{
    public function indexAction()
    {
        die("Line item");
    }

    public function listAction()
    {
        $response = json_decode($this->hubspot->crm()->lineItems()->basicApi()->getPage($limit = 100, null, "name,product_id,quantity,price"), true);
        // echo "<pre>";
        // print_r($response);
        // die;
        $html = '';
        foreach ($response['results'] as $key => $value) {
            $html .= "
            <tr'>
                <form method='post'>
                <th scope='row'><div class='form-check'>
                    <input class='form-check-input checkBoxes' name='checkBoxvalues' type='checkbox' value='" . $value['id'] . "' id='flexCheckDefault'>
                </div></th>
                <th scope='row'>" . $value['id'] . "</th>
                <th scope='row'>" . $value['properties']['name'] . "</th>
                <th scope='row'>" . $value['properties']['price'] . "</th>
                <td text-center>" . $value['properties']['createdate'] . "</td>
                <td text-center>" . $value['properties']['hs_product_id'] . "</td>
                <td text-center>" . $value['properties']['quantity'] . "</td>
                <td text-center>&nbsp&nbsp<button class='btn btn-outline-danger' name='Did' value='" . $value['id'] . "'>Delete</button>
                </form>
                <form method='post' action='https://remote.local.cedcommerce.com/hubspotremote/line/edit?bearer=eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJ1c2VyX2lkIjoiNjI2YmNjMzc2NzM0MTkzZmQ0NTA0MWI5Iiwicm9sZSI6ImFkbWluIiwiZXhwIjoxNjg4NjM2MTE1LCJpc3MiOiJodHRwczpcL1wvYXBwcy5jZWRjb21tZXJjZS5jb20iLCJ0b2tlbl9pZCI6IjYyYzU1NzUzODBjMDYwNTA5MjA2MTVjMiJ9.i45WyHgJ3b11ntGWMGuiNMUri6ezbnALFpFoZkhS2KHGbNA0xge2R6AR-Dsd1U-Gdcv5E9nrQKa3sEh_k7SGA_V4_FGAmFuJUQ5lrLoFpj9oaCc0qSb5A7hf3TY592SozFp-jKRxPlVSWqLhFghWTvcVLV-S_8VfhtSkbretnDY00MCJFaZmTboZkv-FYwHUQM2u1GNsYQAegXL8lHDtz3d9vw1d_t24eZYcvlBlAU1gRQyJQNqaqVThgGdHEvqmyYB2iEsk3LgI8rcxdBEBFYHFJMCfL05BlZ6Ht55d0d5gku-_tGK9cnPz2EVDfQ9OlaQmTrxl2zkTC6Z4G56zIQ&lineID=" . $value['id'] . "'>
                    <button class='ml-3 mt-2 btn btn-outline-info' name='Eid' value='" . $value['id'] . "'>Edit</button>
                </form></td>
            </tr>
            ";
        }

        //Delete the Spacific Items
        $this->view->data = $html;
        if ($this->request->getPost('Did')) {
            $response = json_decode($this->hubspot->crm()->lineItems()->basicApi()->archive($this->request->getPost("Did")), true);
            $this->response->redirect("https://remote.local.cedcommerce.com/hubspotremote/line/list?bearer=eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJ1c2VyX2lkIjoiNjI2YmNjMzc2NzM0MTkzZmQ0NTA0MWI5Iiwicm9sZSI6ImFkbWluIiwiZXhwIjoxNjg4NjM2MTE1LCJpc3MiOiJodHRwczpcL1wvYXBwcy5jZWRjb21tZXJjZS5jb20iLCJ0b2tlbl9pZCI6IjYyYzU1NzUzODBjMDYwNTA5MjA2MTVjMiJ9.i45WyHgJ3b11ntGWMGuiNMUri6ezbnALFpFoZkhS2KHGbNA0xge2R6AR-Dsd1U-Gdcv5E9nrQKa3sEh_k7SGA_V4_FGAmFuJUQ5lrLoFpj9oaCc0qSb5A7hf3TY592SozFp-jKRxPlVSWqLhFghWTvcVLV-S_8VfhtSkbretnDY00MCJFaZmTboZkv-FYwHUQM2u1GNsYQAegXL8lHDtz3d9vw1d_t24eZYcvlBlAU1gRQyJQNqaqVThgGdHEvqmyYB2iEsk3LgI8rcxdBEBFYHFJMCfL05BlZ6Ht55d0d5gku-_tGK9cnPz2EVDfQ9OlaQmTrxl2zkTC6Z4G56zIQ");
        }
    }

    //remove Data in Bulk
    public function removeDataInBulkAction()
    {
        if ($this->request->getPost('data')) {
            $data = $this->request->getPost('data');
            // return $data[0];

            for ($i = 0; $i < count($data); $i++) {
                json_decode($this->hubspot->crm()->lineItems()->basicApi()->archive($data[$i]), true);
            }
            return "Deleted";
        }
    }

    public function addAction()
    {
        $helper = new HelperController();
        $response = json_decode($this->hubspot->crm()->products()->basicApi()->getPage($limit = 100), true);
        $html = "";
        foreach ($response['results'] as $key => $value) {
            $html .= "<option value=" . $value['id'] . ',' . $value['properties']['name'] . ">" . $value['properties']['name'] . "</option>";
        }
        $this->view->data = $html;

        if ($this->request->getPost('productID')) {
            $data = [
                "properties" => [
                    "name" => $this->request->getPost('productName'),
                    "hs_product_id" => $this->request->getPost('productID'),
                    "quantity" => $this->request->getPost('quantity'),
                    "price" => $this->request->getPost('price')
                ]
            ];
            $response = json_decode($this->hubspot->crm()->lineItems()->basicApi()->create($data), true);
            // echo "<pre>";
            // print_r($response);
            // die;
            return  json_encode($response, true);
        }
    }

    public function editAction()
    {
        if ($this->request->get('lineID')) {
            $response = json_decode($this->hubspot->crm()->products()->basicApi()->getPage($limit = 100), true);
            $html = "";
            foreach ($response['results'] as $key => $value) {
                $html .= "<option value=" . $value['id'] . ',' . $value['properties']['name'] . ">" . $value['properties']['name'] . "</option>";
            }
            $this->view->data = $html;
        }

        if ($this->request->getPost('productID')) {
            $productID = $this->request->getPost('productID');
            $data = [
                "properties" => [
                    "name" => $this->request->getPost('productName'),
                    "hs_product_id" => $productID,
                    "quantity" => $this->request->getPost('quantity'),
                    "price" => $this->request->getPost('amount')
                ]
            ];

            // return  json_encode($data, true);
            $response = json_decode($this->hubspot->crm()->lineItems()->basicApi()->update($productID, $data), true);
            return  json_encode($response, true);
        }
    }
}
