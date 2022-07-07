<?php

return array(

    'restapi' => [
        "v1" => [
            "GET" => [
                "routes" => [
                    "test" => [
                        "url" => "test",
                        "method" => "test",
                        "resource" => "Dam/test",
                        "component" => "Dam"
                    ],
                    "retrive" => [
                        "url" => "retrive",
                        "method" => "retrive",
                        "resource" => "Dam/retrive",
                        "component" => "Dam"
                    ],
                    "userdetails" => [
                        "url" => "userdetails",
                        "method" => "userdetails",
                        "resource" => "Dam/userdetails",
                        "component" => "Dam"
                    ]
                ]
            ]
        ]
    ]

);
