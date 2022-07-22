<?php

namespace App\Hubspotremote\Controllers;

use Phalcon\Http\Client\Provider\Curl;
use Phalcon\Http\Response;


class ContactController extends \App\Core\Controllers\BaseController
{

    public function indexAction()
    {
    }

    public function listAction()
    {
        $helper = new HelperController();
        $contactData = $helper->curlGet('crm/v3/objects/contacts?limit=100&archived=false');
        // echo "<pre>";
        // die(print_r($contactData));

        $html = "";
        foreach ($contactData['results'] as $key => $value) {
            // print_r($value->properties);
            // die;
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
                <form method='post' action='http://remote.local.cedcommerce.com/hubspotremote/contact/edit?bearer=" . BEARER . "'>
                    <button class='ml-3 mt-2 btn btn-outline-info' name='Eid' value='" . $value['id'] . "'>Edit</button>
                </form></td>
            </tr>
            ";
        }
        $this->view->data = $html;


        // Delete A Spacific Contact 
        if ($this->request->isPost()) {

            $delID = $this->request->getPost("Did");
            $response = $helper->curlDelete("crm/v3/objects/contacts/" . $delID);
            $this->response->redirect("http://remote.local.cedcommerce.com/hubspotremote/contact/list?bearer=" . BEARER . "");
        }
    }


    // Function for added a contact into hubspot Contact Object.
    public function addAction()
    {
        $helper = new HelperController();

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
            //post request of Curl
            $responseArray = $helper->curlPost('crm/v3/objects/contacts', $formData);
            //for display the added Contact ID
            // print_r($responseArray->id);
            // die;
            if (isset($responseArray->id)) {
                $this->view->flag = $responseArray->id;
            }
        }
    }

    //Function for Edit the Conatct Details
    public function editAction()
    {
        $helper = new HelperController();

        if ($this->request->getPost('lastname')) {
            $Formdata = array(
                "properties" => array(
                    "firstname" => '' . $this->request->getPost('firstname') . '',
                    "lastname" => '' . $this->request->getPost('lastname') . '',
                    "email" => '' . $this->request->getPost('email') . '',

                )
            );
            $ContactID = $this->request->getPost('id');
            $responseArray = $helper->curlPatch("crm/v3/objects/contacts/" . $ContactID, $Formdata);
            // die(print_r($responseArray));
            if (isset($responseArray->id)) {
                $this->response->redirect("http://remote.local.cedcommerce.com/hubspotremote/contact/list?bearer=" . BEARER . "");
            }
            //  else {
            //     die("Something Went Wrong!");
            // }
        }

        //fatch the value of contact and send to the view which is display in Edit form field. 
        $arrayData = $helper->curlGet("crm/v3/objects/contacts/" . $this->request->getPost('Eid') . "");
        $this->view->data = $arrayData;
    }
}
