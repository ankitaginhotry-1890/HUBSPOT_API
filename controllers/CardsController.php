<?php

namespace App\Hubspotremote\Controllers;

use App\Connector\Components\Helper;
use App\Hubspotremote\Models\HubSpot_Token as ModelsHubSpot_Token;
use App\Hubspotremote\Models\ProductPrice;
use App\Hubspotremote\Models\TestData;
use App\Mymodule\Models\HubSpot_Token;
use Phalcon\Http\Client\Provider\Curl;
use Phalcon\Http\Response;


class CardsController extends \App\Core\Controllers\BaseController
{

    public function indexAction()
    {
    }

    public function createCardAction()
    {
        if ($this->request->getPost()) {

            // die(print_r(json_encode($this->request->getPost('postdata'))));
            $helper = new HelperController();
            $response = $helper->curlPost("crm/v3/extensions/cards/" . APPID, $this->request->getPost('postdata'), DEVAPIKEY);
            return json_encode($response, true);
        }
    }

    public function getObjectPPtAction()
    {
        if ($this->request->isPost()) {
            $obejctName = $this->request->getPost('obejctName');
            $helper = new HelperController();
            $response = $helper->curlGet("crm/v3/properties/" . $obejctName . "?archived=false");
            return json_encode($response, true);
        }
    }

    public function demoDataAction()
    {
        $table = new ProductPrice();
        $price = $table->find(
            [
                "_id" => new \MongoDB\BSON\ObjectId("62e8ee68ff4690ee1f29b9b2")
            ]
        )->toArray();
        $priceNew = ($price[0]['price']);
        $productName = ($price[0]['Name']);


        $jayParsedAry = [
            "results" => [
                [
                    "objectId" => 1,
                    "title" => "Product Inforamtion",
                    //propeties define the data of the card which is print by the cards
                    "properties" => [
                        [
                            "label" => "Product Name",
                            "dataType" => "STRING",
                            "value" => $productName,
                        ],
                        [
                            "label" => "Product Price",
                            "dataType" => "STRING",
                            "value" => $priceNew,
                        ]
                    ],

                ],
                [
                    "objectId" => 2,
                    "title" => "Product Operation",
                    //propeties define the data of the card which is print by the cards
                    "properties" => [
                        [
                            "label" => "",
                            "dataType" => "STRING",
                            "value" => "You can Perfrom Crud Operation on that Product",
                        ],
                    ],
                    //action define the settings of the custom cards in iframe
                    "actions" => [
                        [
                            "type" => "IFRAME",
                            "width" => 890,
                            "height" => 748,
                            "uri" => "https://7ead-103-97-184-106.in.ngrok.io//hubspotremote/product/add?bearer=eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJ1c2VyX2lkIjoiNjI2YmNjMzc2NzM0MTkzZmQ0NTA0MWI5Iiwicm9sZSI6ImFkbWluIiwiZXhwIjoxNjg4NjM2MTE1LCJpc3MiOiJodHRwczpcL1wvYXBwcy5jZWRjb21tZXJjZS5jb20iLCJ0b2tlbl9pZCI6IjYyYzU1NzUzODBjMDYwNTA5MjA2MTVjMiJ9.i45WyHgJ3b11ntGWMGuiNMUri6ezbnALFpFoZkhS2KHGbNA0xge2R6AR-Dsd1U-Gdcv5E9nrQKa3sEh_k7SGA_V4_FGAmFuJUQ5lrLoFpj9oaCc0qSb5A7hf3TY592SozFp-jKRxPlVSWqLhFghWTvcVLV-S_8VfhtSkbretnDY00MCJFaZmTboZkv-FYwHUQM2u1GNsYQAegXL8lHDtz3d9vw1d_t24eZYcvlBlAU1gRQyJQNqaqVThgGdHEvqmyYB2iEsk3LgI8rcxdBEBFYHFJMCfL05BlZ6Ht55d0d5gku-_tGK9cnPz2EVDfQ9OlaQmTrxl2zkTC6Z4G56zIQ",
                            "label" => "ADD Product"
                        ],
                        [
                            "type" => "IFRAME",
                            "width" => 890,
                            "height" => 748,
                            "uri" => "https://7ead-103-97-184-106.in.ngrok.io//hubspotremote/cards/update?bearer=eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJ1c2VyX2lkIjoiNjI2YmNjMzc2NzM0MTkzZmQ0NTA0MWI5Iiwicm9sZSI6ImFkbWluIiwiZXhwIjoxNjg4NjM2MTE1LCJpc3MiOiJodHRwczpcL1wvYXBwcy5jZWRjb21tZXJjZS5jb20iLCJ0b2tlbl9pZCI6IjYyYzU1NzUzODBjMDYwNTA5MjA2MTVjMiJ9.i45WyHgJ3b11ntGWMGuiNMUri6ezbnALFpFoZkhS2KHGbNA0xge2R6AR-Dsd1U-Gdcv5E9nrQKa3sEh_k7SGA_V4_FGAmFuJUQ5lrLoFpj9oaCc0qSb5A7hf3TY592SozFp-jKRxPlVSWqLhFghWTvcVLV-S_8VfhtSkbretnDY00MCJFaZmTboZkv-FYwHUQM2u1GNsYQAegXL8lHDtz3d9vw1d_t24eZYcvlBlAU1gRQyJQNqaqVThgGdHEvqmyYB2iEsk3LgI8rcxdBEBFYHFJMCfL05BlZ6Ht55d0d5gku-_tGK9cnPz2EVDfQ9OlaQmTrxl2zkTC6Z4G56zIQ",
                            "label" => "Update Product"
                        ],
                    ]
                ]

            ],
        ];

        return json_encode($jayParsedAry, true);
    }

    public function updateAction()
    {
        if ($this->request->getPost()) {
            echo "<pre>";
            print_r($this->request->getPost());
            $table = new ProductPrice();
            $table->_id = new \MongoDB\BSON\ObjectID("62e8ee68ff4690ee1f29b9b2");
            $table->price = $this->request->getPost("price");
            $table->Name = $this->request->getPost("name");
            $table->save();

            $this->view->msg = "<div class='h3 text-center text-success'>Product Updated Successfully</div>";
        }
    }

    public function listingAction()
    {

        $helper = new HelperController();
        $response = $helper->curlGet("crm/v3/extensions/cards/971551", "d58bff24-1b67-49cf-9547-d6ab7233015f");
        echo "<pre>";
        // print_r(json_encode($response));
        $html = "";
        foreach ($response['results'] as $key => $value) {
            // print_r($value['fetch']['objectTypes'][$key]['name']);
            $ht = "";
            foreach ($value['fetch']['objectTypes'] as $k => $v) {
                $ht .= "<h6 class='ppt'>" . $v['name'] . "\t</h6>";
            }
            $html .= ' <tr>
            <td>' . $value['title'] . '</td>
            <td>' . $ht . '</td>
            <td>
            <form method="post" action="https://remote.local.cedcommerce.com/hubspotremote/cards/edit?bearer=eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJ1c2VyX2lkIjoiNjI2YmNjMzc2NzM0MTkzZmQ0NTA0MWI5Iiwicm9sZSI6ImFkbWluIiwiZXhwIjoxNjg4NjM2MTE1LCJpc3MiOiJodHRwczpcL1wvYXBwcy5jZWRjb21tZXJjZS5jb20iLCJ0b2tlbl9pZCI6IjYyYzU1NzUzODBjMDYwNTA5MjA2MTVjMiJ9.i45WyHgJ3b11ntGWMGuiNMUri6ezbnALFpFoZkhS2KHGbNA0xge2R6AR-Dsd1U-Gdcv5E9nrQKa3sEh_k7SGA_V4_FGAmFuJUQ5lrLoFpj9oaCc0qSb5A7hf3TY592SozFp-jKRxPlVSWqLhFghWTvcVLV-S_8VfhtSkbretnDY00MCJFaZmTboZkv-FYwHUQM2u1GNsYQAegXL8lHDtz3d9vw1d_t24eZYcvlBlAU1gRQyJQNqaqVThgGdHEvqmyYB2iEsk3LgI8rcxdBEBFYHFJMCfL05BlZ6Ht55d0d5gku-_tGK9cnPz2EVDfQ9OlaQmTrxl2zkTC6Z4G56zIQ">
            <button name="Eid" value=' . $value['id'] . '><i class="material-icons">&#xE254;</i></button></form>
            <form method="post"><button class="delete" title="Delete" name="delID" value=' . $value['id'] . ' data-toggle="tooltip"><i class="material-icons">&#xE872;</i></button>
            </td></form>
            </tr>';
            $ht = "";
        }
        // die;
        // print_r(($response['results']));
        echo "</pre>";
        $this->view->data = $html;
        // die;

        if ($this->request->getPost()) {
            $cardID = $this->request->getPost('delID');
            $response = $helper->curlDelete("crm/v3/extensions/cards/" . APPID . "/" . $cardID, DEVAPIKEY);
            print_r($response);
            die;
        }
    }


    public function editAction()
    {
        $CardID = $this->request->getPost("Eid");
        $helper = new HelperController();
        $response = $helper->curlGet("crm/v3/extensions/cards/" . APPID . "/" . $CardID, DEVAPIKEY);
        echo "<pre>";
        // print_r($response);

        $actionURls = "";
        foreach ($response['actions']['baseUrls'] as $key => $value) {
            // print_r($value);
            $actionURls .= '<div class="form-outline mb-4 row">
                            <div class="col-9">
                            <input type="text" id="form3Example1cg" value=' . $value . ' class="form-control form-control-lg actionURLS" />
                            </div>
                            <div class="col-3 mt-1">
                            <button class="btn btn-outline-danger delAction">X</button>
                            </div>
                        </div>';
        }
        $this->view->actionUrls = $actionURls;

        $displayPPT = "";
        foreach ($response['display']['properties'] as $key => $value) {
            // print_r($value);
            $displayPPT .= "<div class='row mt-3 s-row d-flex justify-content-center pl-2 pr-2'>
            <div class='col' class='propertyName'>
                <input type='text' id='form12' class='form-control pptName p-3' value=" . $value['name'] . ">
            </div>
            <div class='col' class='labelName'>
                <input type='text' id='form12' class='form-control pptlabel' value=" . $value['label'] . ">
            </div>
            <div class='col mt-2' class='propertyType'>
                <select class='form-select pptType' aria-label='Default select example'>
                    <option value='STRING'>Currency</option>
                    <option value='STRING'>Date</option>
                    <option value='STRING'>Datetime</option>
                    <option value='STRING'>Numberic</option>
                    <option value='STRING'>Email</option>
                    <option value='STRING'>Numberic</option>
                    <option value='STRING' selected>String</option>
                    <option value='STRING'>Status</option>
                </select>
            </div>
            <div class='col-1'>
               <button class='btn btn-outline-danger delPPT mb-4' style='margin-left:-30px' value='XX'>X Delete</button>
            </div>
        </div>";
        }
        $this->view->displayPPT = $displayPPT;

        print_r($response['fetch']['objectTypes']);
        $this->view->targetURl = $response['fetch']['targetUrl'];
        $this->view->title = $response['title'];
        // $$this->requesy
        $this->view->cardID = $CardID;
        // die;
    }


    public function updateCardAction()
    {
        $cardID = $this->request->getPost('cardID');
        $PatchData = $this->request->getPost('postdata');

        $helper = new HelperController();
        $response = $helper->curlPatch("crm/v3/extensions/cards/" . APPID . "/" . $cardID, $PatchData, DEVAPIKEY);
        return json_encode($response, true);
    }
}
