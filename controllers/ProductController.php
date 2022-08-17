<?php

namespace App\Hubspotremote\Controllers;

use App\Hubspotremote\Models\HubSpot_Token;

use Phalcon\Http\Response;

class ProductController extends \App\Core\Controllers\BaseController
{
    public function indexAction()
    {
    }

    //Function for listing the products
    public function listAction()
    {
        if ($this->request->get('msg')) {
            echo $this->request->get('msg');
        }
        $response = json_decode($this->hubspot->crm()->products()->basicApi()->getPage($limit = 100), true);
        $html = "";
        foreach ($response['results'] as $key => $value) {
            $html .= "
            <tr'>
                <form method='post'>
                <th scope='row'>" . $value['id'] . "</th>
                <td text-center>" . $value['properties']['name'] . "</td>
                <td text-center>" . $value['properties']['price'] . "</td>
                <td text-center>" . $value['properties']['createdate'] . "</td>
                <td text-center>&nbsp&nbsp<button class='btn btn-outline-danger' name='Did' value='" . $value['id'] . "'>Delete</button>
                </form>
                <form method='post' action='http://remote.local.cedcommerce.com/hubspotremote/product/edit?bearer=eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJ1c2VyX2lkIjoiNjI2YmNjMzc2NzM0MTkzZmQ0NTA0MWI5Iiwicm9sZSI6ImFkbWluIiwiZXhwIjoxNjg4NjM2MTE1LCJpc3MiOiJodHRwczpcL1wvYXBwcy5jZWRjb21tZXJjZS5jb20iLCJ0b2tlbl9pZCI6IjYyYzU1NzUzODBjMDYwNTA5MjA2MTVjMiJ9.i45WyHgJ3b11ntGWMGuiNMUri6ezbnALFpFoZkhS2KHGbNA0xge2R6AR-Dsd1U-Gdcv5E9nrQKa3sEh_k7SGA_V4_FGAmFuJUQ5lrLoFpj9oaCc0qSb5A7hf3TY592SozFp-jKRxPlVSWqLhFghWTvcVLV-S_8VfhtSkbretnDY00MCJFaZmTboZkv-FYwHUQM2u1GNsYQAegXL8lHDtz3d9vw1d_t24eZYcvlBlAU1gRQyJQNqaqVThgGdHEvqmyYB2iEsk3LgI8rcxdBEBFYHFJMCfL05BlZ6Ht55d0d5gku-_tGK9cnPz2EVDfQ9OlaQmTrxl2zkTC6Z4G56zIQ'>
                    <button class='ml-3 mt-2 btn btn-outline-info' name='Eid' value='" . $value['id'] . "'>Edit</button>
                </form></td>
            </tr>
            ";
        }
        $this->view->data = $html;

        if ($this->request->getPost('Did')) {
            $this->hubspot->crm()->products()->basicApi()->archive($this->request->getPost('Did'));
            $this->response->redirect("http://remote.local.cedcommerce.com/hubspotremote/product/list?bearer=eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJ1c2VyX2lkIjoiNjI2YmNjMzc2NzM0MTkzZmQ0NTA0MWI5Iiwicm9sZSI6ImFkbWluIiwiZXhwIjoxNjg4NjM2MTE1LCJpc3MiOiJodHRwczpcL1wvYXBwcy5jZWRjb21tZXJjZS5jb20iLCJ0b2tlbl9pZCI6IjYyYzU1NzUzODBjMDYwNTA5MjA2MTVjMiJ9.i45WyHgJ3b11ntGWMGuiNMUri6ezbnALFpFoZkhS2KHGbNA0xge2R6AR-Dsd1U-Gdcv5E9nrQKa3sEh_k7SGA_V4_FGAmFuJUQ5lrLoFpj9oaCc0qSb5A7hf3TY592SozFp-jKRxPlVSWqLhFghWTvcVLV-S_8VfhtSkbretnDY00MCJFaZmTboZkv-FYwHUQM2u1GNsYQAegXL8lHDtz3d9vw1d_t24eZYcvlBlAU1gRQyJQNqaqVThgGdHEvqmyYB2iEsk3LgI8rcxdBEBFYHFJMCfL05BlZ6Ht55d0d5gku-_tGK9cnPz2EVDfQ9OlaQmTrxl2zkTC6Z4G56zIQ");
        }
    }

    public function editAction()
    {
        $response2 = json_decode(json_encode($this->hubspot->crm()->products()->basicApi()->getByIdWithHttpInfo($this->request->getPost('Eid')), true), true);
        $this->view->data = $response2[0];
        if ($this->request->getPost('Name')) {
            $formData = array(
                'properties' =>
                array(
                    'name' => '' . $this->request->getPost('Name') . '',
                    'price' => '' . $this->request->getPost('price') . '',
                ),
            );
            $responseArray = $this->hubspot->crm()->products()->basicApi()->update($this->request->getPost('id'), $formData);
            if (isset($responseArray['id'])) {
                $this->response->redirect("http://remote.local.cedcommerce.com/hubspotremote/product/list?bearer=eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJ1c2VyX2lkIjoiNjI2YmNjMzc2NzM0MTkzZmQ0NTA0MWI5Iiwicm9sZSI6ImFkbWluIiwiZXhwIjoxNjg4NjM2MTE1LCJpc3MiOiJodHRwczpcL1wvYXBwcy5jZWRjb21tZXJjZS5jb20iLCJ0b2tlbl9pZCI6IjYyYzU1NzUzODBjMDYwNTA5MjA2MTVjMiJ9.i45WyHgJ3b11ntGWMGuiNMUri6ezbnALFpFoZkhS2KHGbNA0xge2R6AR-Dsd1U-Gdcv5E9nrQKa3sEh_k7SGA_V4_FGAmFuJUQ5lrLoFpj9oaCc0qSb5A7hf3TY592SozFp-jKRxPlVSWqLhFghWTvcVLV-S_8VfhtSkbretnDY00MCJFaZmTboZkv-FYwHUQM2u1GNsYQAegXL8lHDtz3d9vw1d_t24eZYcvlBlAU1gRQyJQNqaqVThgGdHEvqmyYB2iEsk3LgI8rcxdBEBFYHFJMCfL05BlZ6Ht55d0d5gku-_tGK9cnPz2EVDfQ9OlaQmTrxl2zkTC6Z4G56zIQ&msg=inforamtion%updated%sucessfully");
            } else {
                die("Something Went Wrong");
            }
        }
    }

    public function addAction()
    {
        if ($this->request->isPost()) {
            echo "<pre>";
            $helper = new HelperController();
            $name = $this->request->getPost('name');
            $hs_sku = $this->request->getPost('hs_sku');
            $price = $this->request->getPost('price');
            $description = $this->request->getPost('description');


            $postData =  [

                'description' => $description,
                'hs_sku' => $hs_sku,
                'name' => $name,
                'price' => $price
            ];

            $response = json_decode($this->hubspot->crm()->deals()->basicApi()->create($postData), true);

            if (isset($response['id'])) {
                // die(print_r($response));
                $this->response->redirect("http://remote.local.cedcommerce.com/hubspotremote/product/list?bearer=eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJ1c2VyX2lkIjoiNjI2YmNjMzc2NzM0MTkzZmQ0NTA0MWI5Iiwicm9sZSI6ImFkbWluIiwiZXhwIjoxNjg4NjM2MTE1LCJpc3MiOiJodHRwczpcL1wvYXBwcy5jZWRjb21tZXJjZS5jb20iLCJ0b2tlbl9pZCI6IjYyYzU1NzUzODBjMDYwNTA5MjA2MTVjMiJ9.i45WyHgJ3b11ntGWMGuiNMUri6ezbnALFpFoZkhS2KHGbNA0xge2R6AR-Dsd1U-Gdcv5E9nrQKa3sEh_k7SGA_V4_FGAmFuJUQ5lrLoFpj9oaCc0qSb5A7hf3TY592SozFp-jKRxPlVSWqLhFghWTvcVLV-S_8VfhtSkbretnDY00MCJFaZmTboZkv-FYwHUQM2u1GNsYQAegXL8lHDtz3d9vw1d_t24eZYcvlBlAU1gRQyJQNqaqVThgGdHEvqmyYB2iEsk3LgI8rcxdBEBFYHFJMCfL05BlZ6Ht55d0d5gku-_tGK9cnPz2EVDfQ9OlaQmTrxl2zkTC6Z4G56zIQ&msg=inforamtion%updated%sucessfully");
            } else {
                echo("*Product is Not Added Due to Some Technical Error\n*Please Try After Some time\n\nResons of error\n1.Some Technical Error\n2.Product Is already saved");
                echo "\n\n<a href='http://remote.local.cedcommerce.com/hubspotremote/product/add?bearer=eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJ1c2VyX2lkIjoiNjI2YmNjMzc2NzM0MTkzZmQ0NTA0MWI5Iiwicm9sZSI6ImFkbWluIiwiZXhwIjoxNjg4NjM2MTE1LCJpc3MiOiJodHRwczpcL1wvYXBwcy5jZWRjb21tZXJjZS5jb20iLCJ0b2tlbl9pZCI6IjYyYzU1NzUzODBjMDYwNTA5MjA2MTVjMiJ9.i45WyHgJ3b11ntGWMGuiNMUri6ezbnALFpFoZkhS2KHGbNA0xge2R6AR-Dsd1U-Gdcv5E9nrQKa3sEh_k7SGA_V4_FGAmFuJUQ5lrLoFpj9oaCc0qSb5A7hf3TY592SozFp-jKRxPlVSWqLhFghWTvcVLV-S_8VfhtSkbretnDY00MCJFaZmTboZkv-FYwHUQM2u1GNsYQAegXL8lHDtz3d9vw1d_t24eZYcvlBlAU1gRQyJQNqaqVThgGdHEvqmyYB2iEsk3LgI8rcxdBEBFYHFJMCfL05BlZ6Ht55d0d5gku-_tGK9cnPz2EVDfQ9OlaQmTrxl2zkTC6Z4G56zIQ&msg=inforamtion%updated%sucessfully'>Go to Product Add Page</a>";
                // print_r($response);
                die;
            }
        }
    }
}
