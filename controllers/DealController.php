<?php

namespace App\Hubspotremote\Controllers;

use Phalcon\Http\Client\Provider\Curl;
use Phalcon\Http\Response;


class DealController extends \App\Core\Controllers\BaseController
{

    public function indexAction()
    {
        die("deal");
    }

    public function listAction()
    {
        $helper = new HelperController();
        $contactData = $helper->curlGet('crm/v3/objects/deals?limit=10&archived=false');
        // print_r($contactData);
        $html = "";
        foreach ($contactData['results'] as $key => $value) {
            // print_r($value->properties);
            // die;
            $html .= "
            <tr'>
                <form method='post'>
                <th scope='row'>" . $value['id'] . "</th>
                <td text-center>" . $value['properties']['dealname'] . "</td>
                <td text-center>" . $value['properties']['amount'] . "</td>
                <td text-center>" . $value['properties']['createdate'] . "</td>
                <td text-center>" . $value['properties']['closedate'] . "</td>
                <td text-center>" . $value['properties']['dealstage'] . "</td>
                <td text-center>&nbsp&nbsp<button class='btn btn-outline-danger' name='Did' value='" . $value['id'] . "'>Delete</button>
                </form>
                <form method='post' action='http://remote.local.cedcommerce.com/hubspotremote/contact/edit?bearer=eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJ1c2VyX2lkIjoiNjI2YmNjMzc2NzM0MTkzZmQ0NTA0MWI5Iiwicm9sZSI6ImFkbWluIiwiZXhwIjoxNjg4NjM2MTE1LCJpc3MiOiJodHRwczpcL1wvYXBwcy5jZWRjb21tZXJjZS5jb20iLCJ0b2tlbl9pZCI6IjYyYzU1NzUzODBjMDYwNTA5MjA2MTVjMiJ9.i45WyHgJ3b11ntGWMGuiNMUri6ezbnALFpFoZkhS2KHGbNA0xge2R6AR-Dsd1U-Gdcv5E9nrQKa3sEh_k7SGA_V4_FGAmFuJUQ5lrLoFpj9oaCc0qSb5A7hf3TY592SozFp-jKRxPlVSWqLhFghWTvcVLV-S_8VfhtSkbretnDY00MCJFaZmTboZkv-FYwHUQM2u1GNsYQAegXL8lHDtz3d9vw1d_t24eZYcvlBlAU1gRQyJQNqaqVThgGdHEvqmyYB2iEsk3LgI8rcxdBEBFYHFJMCfL05BlZ6Ht55d0d5gku-_tGK9cnPz2EVDfQ9OlaQmTrxl2zkTC6Z4G56zIQ'>
                    <button class='ml-3 mt-2 btn btn-outline-info' name='Eid' value='" . $value['id'] . "'>Edit</button>
                </form></td>
            </tr>
            ";
        }
        $this->view->data = $html;
        // die;

        if ($this->request->getPost('Did')) {
            // die(print_r($this->request->getPost('Did')));
            $helper = new HelperController();
            $helper->curlDelete("crm/v3/objects/deal
            s/".$this->request->getPost('Did'));
        }
    }

    public function addAction()
    {
        if ($this->request->getPost()) {

            $helper = new HelperController();
            $response = $helper->curlPost("crm/v3/objects/deals", $this->request->getPost());
            print_r($response);
        }
        die;
    }
}
