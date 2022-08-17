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
        } elseif ($this->request->get('objectType') == 'company') {
            $objectType = "companies";
        } elseif ($this->request->get('objectType') == 'deal') {
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
            $notesData = json_decode($this->hubspot->crm()->objects()->notes()->basicApi()->getPage(100, null, "hs_note_body", null, $this->request->get('objectType')), true);
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
                    }
                }
            }

            $owner_id = $helper->curlGet('owners/v2/owners')[0]['ownerId'];
            $this->view->owner_id = $owner_id;
            $this->view->notesHtml = $htm;
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
        $emailsData = json_decode($this->hubspot->crm()->objects()->emails()->basicApi()->getPage(100, null, "hs_email_text,hs_email_subject,email_to_email,email_to_firstname", null, $this->request->get('objectType')), true);
        $htm2 = '';
        foreach ($emailsData['results'] as $key => $value) {
            if (isset($value['associations'])) {
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
        $tasksData = json_decode($this->hubspot->crm()->objects()->tasks()->basicApi()->getPage(100, null, "hs_task_body,hs_task_subject,hs_task_status,hs_task_priority,hs_timestamp", null, $this->request->get('objectType')), true);
        $taskHTML = '';
        foreach ($tasksData['results'] as $key => $value) {
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
        $this->view->taskhtml = $taskHTML;

        //Meeting Listing
        //*****************************MEETING LISTING**************************//
        $helper = new HelperController();
        $meetingData = $helper->curlGet("crm/v3/objects/meetings?limit=100&properties=hs_meeting_title%2Chs_meeting_body%2Chs_internal_meeting_notes%2Chs_meeting_start_time%2Chs_meeting_end_time%2Chs_meeting_outcome&associations=" . $objectType . "&archived=false");
        $meetingHTML = '';
        foreach ($meetingData['results'] as $key => $value) {
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
                }
            }
        }
        $this->view->meetingHTML = $meetingHTML;

        //Meeting Listing
        //*****************************Call LOG LISTING**************************//
        $helper = new HelperController();
        $callData = json_decode($this->hubspot->crm()->objects()->calls()->basicApi()->getPage(100, null, "hs_call_title,hs_call_body,hs_call_duration,hs_call_from_number,hs_call_to_number,hs_call_recording_url,hs_call_status", null, $this->request->get('objectType')), true);
        $callHTML = '';
        foreach ($callData['results'] as $key => $value) {
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
                }
            }
        }
        $this->view->callHTML = $callHTML;
    }

    //-------------------------------NOTE ACTION--------------------------------//
    public function createNotesAction()
    {
        $objectType = "";
        if ($this->request->get('objectType') == 'contact') {
            $objectType = "contacts";
        } elseif ($this->request->get('objectType') == 'company') {
            $objectType = "companies";
        } elseif ($this->request->get('objectType') == 'deal') {
            $objectType = "deals";
        } elseif ($this->request->get('objectType') == 'ticket') {
            $objectType = "tickets";
        } else {
            $objectType = $this->request->get('objectType');
        }

        if ($this->request->getPost()) {
            echo "<pre>";
            // print_r($this->request->getPost());
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
            $response = json_decode($this->hubspot->crm()->objects()->notes()->basicApi()->create($arr), true);
            $noteID = $response['id'];
            $finalResponse = json_decode($this->hubspot->crm()->objects()->notes()->associationsApi()->create($noteID, $objectType, $id, "note_to_" . $this->request->get('objectType')), true);
            $this->view->result = "success";
        }
    }

    //-------------------------------Task Action--------------------------------//0
    public function createtaskAction()
    {
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
            // $response = $helper->curlPost("crm/v3/objects/tasks", $postData);
            $response = json_decode($this->hubspot->crm()->objects()->tasks()->basicApi()->create($postData), true);

            echo "<h3>Response</h3>";
            print_r($response);

            echo "\n<h3>Association</h3>\n";
            $assoObjecttoTask = json_decode($this->hubspot->crm()->objects()->tasks()->associationsApi()->create($response['id'], $this->request->getPost('objectType'), $this->request->getPost('id'), "task_to_" . $this->request->get('objectType')), true);
            print_r($assoObjecttoTask);
        }
    }


    //-------------------------------Meeting EndPoint--------------------------------//0
    public function createMeetingAction()
    {
        if ($this->request->getPost()) {
            echo "<pre>";

            $t = time();
            $date_time = date("Y-m-d h:m:s", $t);
            $timestamp = (str_replace('+00:00', '.000Z', gmdate('c', strtotime($date_time))));
            $arr = [
                'properties' => [
                    'hs_timestamp' => $timestamp,
                    'hubspot_owner_id' => $this->request->getPost('owner_id'),
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
            $response = json_decode($this->hubspot->crm()->objects()->meetings()->basicApi()->create($arr), true);

            print_r($response);
            // $assoObjecttoMeeting = $helper->curlPut("crm/v3/objects/meetings/" . $response->id . "/associations/" . $this->request->getPost('objectType') . "/" . $this->request->getPost('id') . "/meeting_event_to_" . $this->request->getPost('objectType'));

            $assoObjecttoMeeting = json_decode($this->hubspot->crm()->objects()->meetings()->associationsApi()->create($response['id'], $this->request->getPost('objectType'), $this->request->getPost('id'), "meeting_event_to_" . $this->request->get('objectType')), true);

            echo "<h3>Association</h3>";
            print_r($assoObjecttoMeeting);

            echo "<h3>Association</h3>";
            // print_r($response);
            if (isset($response['id'])) {
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

            //Create Email
            $response = json_decode($this->hubspot->crm()->objects()->emails()->basicApi()->create($data), true);
            //Associate with Object
            $assoResponse = json_decode($this->hubspot->crm()->objects()->emails()->associationsApi()->create($response['id'], $this->request->getPost('objectType'), $this->request->getPost('id'), "email_to_" . $this->request->get('objectType')), true);

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
            $response = json_decode($this->hubspot->crm()->objects()->notes()->basicApi()->archive($id), true);
        }
    }


    //**********Delete Emails

    public function delEmailsAction()
    {
        if ($this->request->getPost()) {
            $id = $this->request->getPost("id");
            $response = json_decode($this->hubspot->crm()->objects()->emails()->basicApi()->archive($id), true);
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
            // $response = json_decode($this->hubspot->crm()->objects()->emails()->basicApi()->archive($id), true);
            return $response;
        }
    }

    //************************Create Call Log ********************************/
    public function createCallAction()
    {
        $objectType = "";
        if ($this->request->get('objectType') == 'contact') {
            $objectType = "contacts";
        } elseif ($this->request->get('objectType') == 'company') {
            $objectType = "companies";
        } elseif ($this->request->get('objectType') == 'deal') {
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

            $response = json_decode($this->hubspot->crm()->objects()->calls()->basicApi()->create($postData), true);

            $assoResponse = json_decode($this->hubspot->crm()->objects()->calls()->associationsApi()->create($response['id'], $this->request->getPost('objectType'), $this->request->getPost('id'), "call_to_" . $this->request->get('objectType')), true);

            if (isset($assoResponse['id'])) {
                $this->view->msg = "Call Log Successully";
            } else {
                $this->view->msg = "Something Went Wrong";
            }
            // die;
        }
    }
}
