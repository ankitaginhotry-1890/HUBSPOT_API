<?php

namespace App\Hubspotremote\Controllers;

use App\Connector\Components\Helper;
use App\Hubspotremote\Models\HubSpot_Token;
use FuncInfo;
use PDO;
use ValueError;

class EngagementController extends \App\Core\Controllers\BaseController
{

    public function indexAction()
    {
        die("Engagement Controller");
    }

    public function profileAction()
    {
        $objectType = "";
        if ($this->request->get('objectType') == 'contact') {
            $objectType = "contacts";
        } else if ($this->request->get('objectType') == 'company') {
            $objectType = "companies";
        } else if ($this->request->get('objectType') == 'deal') {
            $objectType = "deals";
        }

        if ($this->request->get('username')) {
            $this->view->username = $this->request->get('username');
            $this->view->email = $this->request->get('email');
            $this->view->user_id = $this->request->get('user_id');
            $user_id = $this->request->get('user_id');


            //*****************************NOTES LISTING**************************//
            $Notes = [];
            $helper = new HelperController();
            $notesData = $helper->curlGet("crm/v3/objects/notes?limit=100&properties=hs_note_body&associations=" . $this->request->get('objectType') . "&archived=false");
            echo "<pre>";
            // print_r($notesData);
            // die;
            $htm = '';
            foreach ($notesData['results'] as $key => $value) {
                //                 print_r($value);
                // die;
                // print_r($value['associations']['contacts']['results'][$b]);
                // print_r($value);
                // die;
                if (isset($value['associations'])) {

                    if ($value['associations'][$objectType]['results'][0]['id'] == $user_id) {
                        $htm .= '
                            <div class="card mt-2 d-flex justify-content-center cardTASK ml-4" style="width: 15rem;">
                                <div class="card-body">
                                <div class="text-danger h6 font-weight-bold">ID: </div><h5 class="card-title">' . $value['id'] . '</h5>
                                <div class="text-danger h6 font-weight-bold">Note Data: </div><p class="card-text">' . $value['properties']['hs_note_body'] . '</p>
                                </div>
                                <button value=' . $value['id'] . ' class="btn btn-outline-danger delBtn mr-4" style="border-color: transparent; color:white;">X</button> 
                        </div>';
                        // echo $value['id'];
                    }
                }
            }

            // echo "Final";
            // echo $htm;
            // die;
            $owner_id = $helper->curlGet('owners/v2/owners')[0]['ownerId'];
            $this->view->owner_id = $owner_id;
            $this->view->notesHtml = $htm;
            // die;
        }

        if ($this->request->getPost('id')) {
            $id = ($this->request->getPost('id'));
            $name = ($this->request->getPost('username'));
            $email = ($this->request->getPost('email'));
            $helper = new HelperController();
            $owner_id = $helper->curlGet('owners/v2/owners')[0]['ownerId'];
            $this->response->redirect('https://remote.local.cedcommerce.com/hubspotremote/engagement/createNotes?bearer=eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJ1c2VyX2lkIjoiNjI2YmNjMzc2NzM0MTkzZmQ0NTA0MWI5Iiwicm9sZSI6ImFkbWluIiwiZXhwIjoxNjg4NjM2MTE1LCJpc3MiOiJodHRwczpcL1wvYXBwcy5jZWRjb21tZXJjZS5jb20iLCJ0b2tlbl9pZCI6IjYyYzU1NzUzODBjMDYwNTA5MjA2MTVjMiJ9.i45WyHgJ3b11ntGWMGuiNMUri6ezbnALFpFoZkhS2KHGbNA0xge2R6AR-Dsd1U-Gdcv5E9nrQKa3sEh_k7SGA_V4_FGAmFuJUQ5lrLoFpj9oaCc0qSb5A7hf3TY592SozFp-jKRxPlVSWqLhFghWTvcVLV-S_8VfhtSkbretnDY00MCJFaZmTboZkv-FYwHUQM2u1GNsYQAegXL8lHDtz3d9vw1d_t24eZYcvlBlAU1gRQyJQNqaqVThgGdHEvqmyYB2iEsk3LgI8rcxdBEBFYHFJMCfL05BlZ6Ht55d0d5gku-_tGK9cnPz2EVDfQ9OlaQmTrxl2zkTC6Z4G56zIQ&username=' . $name . '&email=' . $email . '&id=' . $id . '&owner_id=' . $owner_id . '&objectType=' . $this->request->get('objectType'));
        }

        //*****************************EMAIL LISTING**************************//
        $helper = new HelperController();
        $emailsData = $helper->curlGet("crm/v3/objects/emails?limit=100&properties=hs_email_text%2Chs_email_subject%2Chs_email_to_email%2Chs_email_to_firstname&associations=" . $objectType . "&archived=false");


        // print_r($emailsData);
        $htm2 = '';
        foreach ($emailsData['results'] as $key => $value) {
            //                 print_r($value);
            // die;
            // print_r($value['associations']['contacts']['results'][$b]);
            if (isset($value['associations'])) {
                // print_r($value['associations']);
                if ($value['associations'][$objectType]['results'][0]['id'] == $user_id) {
                    $htm2 .= '
                        <div class="row">
                            <div class="card mt-2 d-flex justify-content-center cardTASK ml-5" style="width: 15rem;">
                                <div class="card-body">
                                <div class="text-danger h6 font-weight-bold">ID: </div><h5 class="card-title">' . $value['id'] . '</h5>
                                <div class="text-danger h6 font-weight-bold">Subject: </div><p class="card-text">' . $value['properties']['hs_email_subject'] . '</p>
                                <div class="text-danger h6 font-weight-bold">Text: </div><p href="#" class="card-link">' . $value['properties']['hs_email_text'] . '</p>
                                </div>
                                <button value=' . $value['id'] . ' class="btn btn-outline-danger emaildelBTN" style="border-color: transparent; color:white;">X</button> 
                            </div>
                        </div>';
                }
            }
        }
        $this->view->emailsHtml = $htm2;


        //*****************************TASK LISTING**************************//
        $helper = new HelperController();
        $tasksData = $helper->curlGet("crm/v3/objects/tasks?limit=100&properties=hs_task_body%2Chs_task_subject%2Chs_task_status%2Chs_task_priority%2Chs_timestamp&associations=" . $objectType . "&archived=false");

        echo "crm/v3/objects/tasks?limit=100&properties=hs_task_body%2Chs_task_subject%2Chs_task_status%2Chs_task_priority%2Chs_timestamp&associations=" . $objectType . "&archived=false";

        // print_r($tasksData);
        // die;
        $taskHTML = '';
        foreach ($tasksData['results'] as $key => $value) {
            //                 print_r($value);
            // die;
            // print_r($value['associations']['contacts']['results'][$b]);
            if (isset($value['associations'])) {
                print_r($value);
                if ($value['associations'][$objectType]['results'][0]['id'] == $user_id) {
                    $taskHTML .= '
                     <div class="row">
                        <div class="card mt-2  d-flex justify-content-center cardTASK" style="width: 15rem;">
                            <div class="card-body">
                            <div class="text-danger h6 font-weight-bold">Subject: </div><h5 class="card-title">' . $value['properties']['hs_task_subject'] . '</h5>
                            <div class="text-danger h6 font-weight-bold">Task: </div><p class="card-text">' . $value['properties']['hs_task_body'] . '</p>
                            <div class="text-danger h6 font-weight-bold">Status: </div><p href="#" class="card-link">' . $value['properties']['hs_task_status'] . '</p>
                            <div class="text-danger h6 font-weight-bold">Priority: </div><p class="card-title">' . $value['properties']['hs_task_priority'] . '</p>
                            </div>
                            <button class="btn btn-outline-danger delTASKBTN" value=' . $value['id'] . ' style="border-color: transparent; color:white;">X</button>
                        </div>
                     </div>';
                }
            }
        }
        // echo $taskHTML;
        // die;
        $this->view->taskhtml = $taskHTML;

        //Meeting Listing 
        //*****************************MEETING LISTING**************************//
        $helper = new HelperController();
        $meetingData = $helper->curlGet("crm/v3/objects/meetings?limit=100&properties=hs_meeting_title%2Chs_meeting_body%2Chs_internal_meeting_notes%2Chs_meeting_start_time%2Chs_meeting_end_time%2Chs_meeting_outcome&associations=" . $objectType . "&archived=false");

        // echo "crm/v3/objects/tasks?limit=100&properties=hs_task_body%2Chs_task_subject%2Chs_task_status%2Chs_task_priority%2Chs_timestamp&associations=" . $objectType . "&archived=false";

        // print_r($meetingData);
        // die;
        $meetingHTML = '';
        foreach ($meetingData['results'] as $key => $value) {
            //                 print_r($value);
            // die;
            // print_r($value['associations']['contacts']['results'][$b]);
            if (isset($value['associations'])) {
                print_r($value);
                if ($value['associations'][$objectType]['results'][0]['id'] == $user_id) {
                    $meetingHTML .= '
                     <div class="row">
                        <div class="card mt-2  d-flex justify-content-center cardTASK" style="width: 15rem;">
                            <div class="card-body">
                            <div class="text-danger h6 font-weight-bold">Title: </div><h5 class="card-title">' . $value['properties']['hs_meeting_title'] . '</h5>
                            <div class="text-danger h6 font-weight-bold">Subject: </div><p class="card-text">' . $value['properties']['hs_meeting_body'] . '</p>
                            <div class="text-danger h6 font-weight-bold">Notes: </div><p href="#" class="card-link">' . $value['properties']['hs_internal_meeting_notes'] . '</p>
                            <div class="text-danger h6 font-weight-bold">Meeting Start Time: </div><p class="card-title">' . $value['properties']['hs_meeting_start_time'] . '</p>
                            <div class="text-danger h6 font-weight-bold">Meeting End Time: </div><p class="card-title">' . $value['properties']['hs_meeting_end_time'] . '</p>
                            <div class="text-danger h6 font-weight-bold">Outcome: </div><p class="card-title">' . $value['properties']['hs_meeting_outcome'] . '</p>
                            </div>
                            <button class="btn btn-outline-danger delMeetingBTN" value=' . $value['id'] . ' style="border-color: transparent; color:white;">X</button>
                        </div>
                     </div>';
                    // echo $value['id'];
                }
            }
        }
        // echo $taskHTML;
        // die;
        $this->view->meetingHTML = $meetingHTML;

        //Meeting Listing 
        //*****************************Call LOG LISTING**************************//
        $helper = new HelperController();
        $callData = $helper->curlGet("crm/v3/objects/calls?limit=100&properties=hs_call_title%2Chs_call_body%2Chs_call_duration%2Chs_call_from_number%2Chs_call_to_number%2Chs_call_recording_url%2hs_call_status&associations=" . $objectType . "&archived=false");

        // echo "crm/v3/objects/tasks?limit=100&properties=hs_task_body%2Chs_task_subject%2Chs_task_status%2Chs_task_priority%2Chs_timestamp&associations=" . $objectType . "&archived=false";
        echo "<h1>*********Call Logs Data*******</h1>";
        print_r($callData);
        // die;
        $callHTML = '';
        foreach ($callData['results'] as $key => $value) {
            //                 print_r($value);
            // die;
            // print_r($value['associations']['contacts']['results'][$b]);
            if (isset($value['associations'])) {
                print_r($value);
                if ($value['associations'][$objectType]['results'][0]['id'] == $user_id) {
                    $callHTML .= '
                     <div class="row">
                        <div class="col-12 card mt-3  d-flex justify-content-center cardTASK" style="width: 15rem;">
                            <div class="card-body">
                            <div class="text-danger h6 font-weight-bold">Title: </div><h5 class="card-title">' . $value['properties']['hs_call_title'] . '</h5>
                            <div class="text-danger h6 font-weight-bold">Subject: </div><p class="card-text">' . $value['properties']['hs_call_body'] . '</p>
                            <div class="text-danger h6 font-weight-bold">call to Number: </div><p class="card-title">' . $value['properties']['hs_call_to_number'] . '</p>
                            <div class="text-danger h6 font-weight-bold">call From Number: </div><p class="card-title">' . $value['properties']['hs_call_from_number'] . '</p>
                            <div class="text-danger h6 font-weight-bold">Call Duration: </div><p class="card-title">' . $value['properties']['hs_call_duration'] . '</p>
                            </div>
                            <button class="btn btn-outline-danger delCallBTN" value=' . $value['id'] . ' style="border-color: transparent; color:white;">X</button>
                        </div>
                     </div>';
                    // echo $value['id'];
                }
            }
        }
        // echo $taskHTML;
        // die;
        $this->view->callHTML = $callHTML;
    }

    //-------------------------------NOTE ACTION--------------------------------//
    public function createNotesAction()
    {
        $objectType = "";
        if ($this->request->get('objectType') == 'contact') {
            $objectType = "contacts";
        } else if ($this->request->get('objectType') == 'company') {
            $objectType = "companies";
        } else if ($this->request->get('objectType') == 'deal') {
            $objectType = "deals";
        } else if ($this->request->get('objectType') == 'ticket') {
            $objectType = "tickets";
        } else {
            $objectType = $this->request->get('objectType');
        }

        if ($this->request->getPost()) {
            echo "<pre>";
            print_r($this->request->getPost());
            $id = $this->request->getPost('id');
            $note = $this->request->getPost('note');
            $owner_id = $this->request->getPost('owner_id');
            $t = time();
            $date_time = date("Y-m-d h:m:s", $t);
            $timestamp = (str_replace('+00:00', '.000Z', gmdate('c', strtotime($date_time))));

            $helper = new HelperController();

            $arr = ['properties' => [
                'hs_timestamp' => $timestamp,
                'hs_note_body' => $note,
                'hubspot_owner_id' => $owner_id
            ],];
            $response = $helper->curlPost('crm/v3/objects/notes', $arr);
            $noteID = $response->id;


            echo "crm/v3/objects/notes/' . $noteID . '/associations/' . $objectType . '/' . $id . '/note_to_'" . $this->request->get('objectType');

            echo "<br><br>";
            // echo "asdsd";

            $finalResponse = $helper->curlPut('crm/v3/objects/notes/' . $noteID . '/associations/' . $objectType . '/' . $id . '/note_to_' . $this->request->get('objectType'));
            $this->view->result = "success";
            print_r($finalResponse);
            // print_r("crm/v3/objects/notes/' . $noteID . '/associations/' . $objectType . '/' . $id . '/note_to_' . $this->request->get('objectType'");
            // die;
        }
    }

    //-------------------------------Task Action--------------------------------//0
    public function createtaskAction()
    {
        // die("Sda");
        if ($this->request->getPost()) {
            echo "<pre>";
            print_r($this->request->getPost());
            $t = time();
            $date_time = date("Y-m-d h:m:s", $t);
            $timestamp = (str_replace('+00:00', '.000Z', gmdate('c', strtotime($date_time))));


            $postData = [
                'properties' => [
                    'hs_timestamp' => $timestamp,
                    'hs_task_body' => $this->request->getPost('body'),
                    'hubspot_owner_id' => $this->request->getPost('owner_id'),
                    'hs_task_subject' => $this->request->getPost('subject'),
                    'hs_task_status' => $this->request->getPost('status'),
                    'hs_task_priority' => $this->request->getPost('priority')
                ],
            ];

            print_r($postData);
            $helper = new HelperController();
            $response = $helper->curlPost("crm/v3/objects/tasks", $postData);
            echo "<h3>Response</h3>";
            print_r($response);

            //Associate With Object
            $assoObjecttoTask = $helper->curlPut("crm/v3/objects/tasks/" . $response->id . "/associations/" . $this->request->getPost('objectType') . "/" . $this->request->getPost('id') . "/task_to_" . $this->request->getPost('objectType'));
            echo "<h3>Association</h3>";
            print_r($assoObjecttoTask);
        }
        // die;
    }


    //-------------------------------Meeting EndPoint--------------------------------//0
    public function createMeetingAction()
    {

        if ($this->request->getPost()) {
            echo "<pre>";
            print_r($this->request->getPost());

            $t = time();
            $date_time = date("Y-m-d h:m:s", $t);
            $timestamp = (str_replace('+00:00', '.000Z', gmdate('c', strtotime($date_time))));


            $arr = [
                'properties' => [
                    'hs_timestamp' => $timestamp,
                    'hubspot_owner_id' => '11349275740',
                    'hs_meeting_title' => $this->request->getPost('title'),
                    'hs_meeting_body' => $this->request->getPost('title'),
                    'hs_internal_meeting_notes' => $this->request->getPost('title'),
                    'hs_meeting_external_url' => $this->request->getPost('title'),
                    'hs_meeting_location' => 'Remote',
                    'hs_meeting_start_time' => str_replace('+00:00', '.000Z', gmdate('c', strtotime($this->request->getPost('startTime')))),
                    'hs_meeting_end_time' => str_replace('+00:00', '.000Z', gmdate('c', strtotime($this->request->getPost('endTime')))),
                    'hs_meeting_outcome' => 'SCHEDULED',
                ],
            ];

            $helper = new HelperController();
            $response = $helper->curlPost("crm/v3/objects/meetings", $arr);
            print_r($response);

            $assoObjecttoTask = $helper->curlPut("crm/v3/objects/meetings/" . $response->id . "/associations/" . $this->request->getPost('objectType') . "/" . $this->request->getPost('id') . "/meeting_event_to_" . $this->request->getPost('objectType'));
            echo "<h3>Association</h3>";
            print_r($assoObjecttoTask);

            echo "crm/v3/objects/meetings/" . $response->id . "/associations/" . $this->request->getPost('objectType') . "/" . $this->request->getPost('id') . "/meeting_event_to_" . $this->request->getPost('objectType');
            echo "<h3>Association</h3>";
            // print_r($response);
            if (isset($response->id)) {
                $this->view->msg = "*Meeting Created";
            }
            // die;
        }
    }


    //-------------------------------SEND EMAIL ACTION--------------------------------//0

    public function sendEmailAction()
    {
        if ($this->request->getPost('objectType')) {
            echo "<pre>";
            // print_r($this->request->getPost('id'));
            // die;
            $t = time();
            $date_time = date("Y-m-d h:m:s", $t);
            $timestamp = (str_replace('+00:00', '.000Z', gmdate('c', strtotime($date_time))));

            $hs_email_headers = array(
                "from" => array(
                    "email" => $this->request->getPost('fromEmail'),
                    "firstName" => $this->request->getPost('username'),
                ),
                "to" => array(
                    array(
                        "email" => $this->request->getPost('email'),
                        "firstName" => $this->request->getPost('name'),
                    )
                ),
                "cc" => array(),
                "bcc" => array()
            );

            $data = ["properties" => [
                'hs_timestamp' => $timestamp,
                'hubspot_owner_id' => $this->request->getPost('owner_id'),
                'hs_email_direction' => 'EMAIL',
                'hs_email_status' => 'SENT',
                'hs_email_subject' => $this->request->getPost('subject'),
                'hs_email_text' => $this->request->getPost('message'),
                'hs_email_headers' => serialize($hs_email_headers)
            ],];

            $helper = new HelperController();
            $response = $helper->curlPost("crm/v3/objects/emails", $data);
            $assoResponse = $helper->curlPut('crm/v3/objects/emails/' . $response->id . '/associations/' . $this->request->getPost('objectType') . '/' . $this->request->getPost('id') . '/email_to_' . $this->request->getPost('objectType'));
            print_r($assoResponse);
            die;
            if (isset($assoResponse['id'])) {
                $this->view->msg = "success";
            }
            // die;
        }
    }

    //***********function to delete a note
    public function deleteNoteAction()
    {
        if ($this->request->isPost()) {
            $id = $this->request->getPost('Did');
            $helper = new HelperController();
            $response = $helper->curlDelete("crm/v3/objects/notes/" . $id);
            // $this->request->respon
            // return json_encode($response, true);
        }
    }


    //**********Delete Emails 

    public function delEmailsAction()
    {
        if ($this->request->getPost()) {
            $id = $this->request->getPost("id");
            $helper = new HelperController();
            $response = $helper->curlDelete('crm/v3/objects/emails/' . $id);
            return $response;
        }
    }

    //*********Delete Engagement
    public function delEngagementAction()
    {
        if ($this->request->getPost()) {
            $id = $this->request->getPost("id");
            $EngagementType = $this->request->getPost("EngagementType");
            $helper = new HelperController();
            $response = $helper->curlDelete('crm/v3/objects/' . $EngagementType . '/' . $id);
            return $response;
        }
    }

    //************************Create Call Log ********************************/
    public function createCallAction()
    {
        $objectType = "";
        if ($this->request->get('objectType') == 'contact') {
            $objectType = "contacts";
        } else if ($this->request->get('objectType') == 'company') {
            $objectType = "companies";
        } else if ($this->request->get('objectType') == 'deal') {
            $objectType = "deals";
        }

        if ($this->request->getPost()) {
            echo "<pre>";
            print_r($this->request->getPost());

            $t = time();
            $date_time = date("Y-m-d h:m:s", $t);
            $timestamp = (str_replace('+00:00', '.000Z', gmdate('c', strtotime($date_time))));


            $postData = [
                'properties' => [
                    'hs_timestamp' => $timestamp,
                    'hs_call_title' => $this->request->getPost('title'),
                    'hubspot_owner_id' => $this->request->getPost('owner_id'),
                    'hs_call_body' => $this->request->getPost('description'),
                    'hs_call_duration' => $this->request->getPost('callDuration'),
                    'hs_call_from_number' => $this->request->getPost('fromNumber'),
                    'hs_call_to_number' => $this->request->getPost('toNumber'),
                    'hs_call_recording_url' => $this->request->getPost('recordingURL'),
                    'hs_call_status' => $this->request->getPost('status'),
                ],
            ];

            print_r($postData);
            $helper = new HelperController();
            $response = $helper->curlPost('crm/v3/objects/calls', $postData);

            echo "<h1>Response</h1>";
            print_r($response);


            $assoResponse = $helper->curlPut("crm/v3/objects/calls/$response->id/associations/" . $objectType . "/" . $this->request->getPost('id') . "/call_to_" . $this->request->getPost('objectType') . "");
            echo "<h1>Association Response</h1>";
            echo "crm/v3/objects/calls/$response->id/associations/" . $objectType . "/" . $this->request->getPost('id') . "/call_to_" . $this->request->getPost('objectType') . "";
            print_r($assoResponse['id']);

            if (isset($assoResponse['id'])) {
                $this->view->msg = "Call Log Successully";
            } else {
                $this->view->msg = "Something Went Wrong";
            }


            // die;
        }
    }
}
