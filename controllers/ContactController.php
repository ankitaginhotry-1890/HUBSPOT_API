<?php

namespace App\Hubspotremote\Controllers;

use Phalcon\Http\Client\Provider\Curl;
use Phalcon\Http\Response;

class ContactController extends \App\Core\Controllers\BaseController
{
    public function indexAction()
    {
        echo "Working..";
    }

    public function listAction()
    {
        $contactData = json_decode($this->hubspot->crm()->contacts()->basicApi()->getPage($limit = 100), true);
        $html = "";
        foreach ($contactData['results'] as $key => $value) {
            $html .= "
            <tr'>
                <form method='post'>
                <th scope='row'>" . $value['id'] . "</th>
                <td text-center>" . $value['properties']['firstname'] . "</td>
                <td text-center>" . $value['properties']['lastname'] . "</td>
                <td text-center>" . $value['properties']['email'] . "</td>
                <td text-center>" . $value['properties']['createdate'] . "</td>
                <td text-center>&nbsp&nbsp<button class='btn btn-outline-danger' name='Did' value='" . $value['id'] . "'>Delete</button>
                </form>
                <form method='post' action='https://remote.local.cedcommerce.com/hubspotremote/contact/edit?bearer=" . BEARER . "'>
                    <button class='ml-3 mt-2 btn btn-outline-info' name='Eid' value='" . $value['id'] . "'>Edit</button>
                </form></td>
                <td>
                <form method='post' target='_parent' action='https://remote.local.cedcommerce.com/hubspotremote/engagement/profile?bearer=eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJ1c2VyX2lkIjoiNjI2YmNjMzc2NzM0MTkzZmQ0NTA0MWI5Iiwicm9sZSI6ImFkbWluIiwiZXhwIjoxNjg4NjM2MTE1LCJpc3MiOiJodHRwczpcL1wvYXBwcy5jZWRjb21tZXJjZS5jb20iLCJ0b2tlbl9pZCI6IjYyYzU1NzUzODBjMDYwNTA5MjA2MTVjMiJ9.i45WyHgJ3b11ntGWMGuiNMUri6ezbnALFpFoZkhS2KHGbNA0xge2R6AR-Dsd1U-Gdcv5E9nrQKa3sEh_k7SGA_V4_FGAmFuJUQ5lrLoFpj9oaCc0qSb5A7hf3TY592SozFp-jKRxPlVSWqLhFghWTvcVLV-S_8VfhtSkbretnDY00MCJFaZmTboZkv-FYwHUQM2u1GNsYQAegXL8lHDtz3d9vw1d_t24eZYcvlBlAU1gRQyJQNqaqVThgGdHEvqmyYB2iEsk3LgI8rcxdBEBFYHFJMCfL05BlZ6Ht55d0d5gku-_tGK9cnPz2EVDfQ9OlaQmTrxl2zkTC6Z4G56zIQ&username=" . $value['properties']['firstname'] . " " . $value['properties']['lastname'] . "&email=" . $value['properties']['email'] . "&user_id=" . $value['id'] . "&objectType=contact'><button class='btn btn-primary'>Preview</button></form></td>
            </tr>
            ";
        }
        $this->view->data = $html;


        // Delete A Spacific Contact
        if ($this->request->isPost("Did")) {
            $helper = new HelperController();
            $delID = $this->request->getPost("Did");
            $this->hubspot->crm()->contacts()->basicApi()->archive($delID);
            $this->response->redirect("http://remote.local.cedcommerce.com/hubspotremote/contact/list?bearer=" . BEARER . "");
        }
    }

    /**
    Function for added a contact into hubspot Contact Object.
     * @return void
     */
    public function addAction()
    {
        if ($this->request->isPost()) {
            $formData = array(
                'properties' =>
                array(
                    'company' => '' . $this->request->getPost('company') . '',
                    'email' => '' . $this->request->getPost('email') . '',
                    'firstname' => '' . $this->request->getPost('firstname') . '',
                    'lastname' => '' . $this->request->getPost('lastname') . '',
                    'phone' => '' . $this->request->getPost('number') . '',
                ),
            );
            $response = json_decode($this->hubspot->crm()->contacts()->basicApi()->create($formData), true);
            if (isset($response['id'])) {
                $this->view->flag = $response['id'];
            }
        }
    }

    /**
     Function for Edit the Conatct Details
     *
     *
     * @return void
     */
    public function editAction()
    {

        //fatch the value of contact and send to the view which is display in Edit form field.
        if ($this->request->getPost('lastname')) {
            $Formdata = array(
                "properties" => array(
                    "firstname" => '' . $this->request->getPost('firstname') . '',
                    "lastname" => '' . $this->request->getPost('lastname') . '',
                    "email" => '' . $this->request->getPost('email') . '',

                )
            );
            $ContactID = $this->request->getPost('id');
            $response = $this->hubspot->crm()->contacts()->basicApi()->update($ContactID, $Formdata);
            if (isset($response['id'])) {
                die("<h1><b>Contact Successfully Updated</b></h1>\n<a href='http://remote.local.cedcommerce.com/hubspotremote/contact/list?bearer=" . BEARER . "'>Go to Listing Page</a>");
            } else {
                die("Something Went Wrong!");
            }
        }

        $EditID = $this->request->getPost("Eid");
        if (isset($EditID)) {
            $contactData = json_decode(json_encode($this->hubspot->crm()->contacts()->basicApi()->getByIdWithHttpInfo($EditID), true), true);
            $this->view->data = $contactData[0];
        } else {
            die("Error 101: Requried Post Data not Get");
        }
    }
}
