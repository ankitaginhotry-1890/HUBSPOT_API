<?php

/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://cedcommerce.com/license-agreement.txt
 *
 * @category    Ced
 * @package     Ced_Spotifuwebapi
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
 * @license     http://cedcommerce.com/license-agreement.txt
 */

namespace App\Remotetest\Components\Route;

use \GuzzleHttp\Client;
use Phalcon\Http\Response;


class Handler extends \App\Apiconnect\Components\Authenticate\Common
{
    public function requestRedirectAuthUrl($postData)
    {
        $auth = $this->di->getObjectManager()->get('\App\Shopifyremote\Components\Authenticate\Sellerauth')->fetchAuthenticationUrl($postData);
        return $auth;
    }

    public function processAuth()
    {
        // die("asdasd");
        if ($_GET['code'] == null) {
            // die("data is not found ");
            $response = new Response();
            $response->redirect("https://mystore8707.myshopify.com/admin/oauth/authorize?client_id=66a11abc0d85cebd207ad9e287e01d04&scope=read_products&redirect_uri=http://remote.local.cedcommerce.com/apiconnect/request/auth?sAppId=12");
            $response->send();
        } else {
            // die("hard");

            $postData = $this->di->getRequest()->get();
            $tokenInfo = $this->requestAccessToken($postData['code']);
            // echo "<pre>";
            // print_r($tokenInfo);
            // die;
            $appConfig = $this->di->getRegistry()->getAppConfig();
            echo "<pre>";
            // die(print_r($appConfig));
            $subAppId = $appConfig['sub_app_id'] ?? false;
            //$tokenInfo['state'] = $postData['state'];
            if ($tokenInfo['success']) {
                // die(print_r($tokenInfo));
                $shop = $this->di->getobjectManager()->create('\App\Apiconnect\Models\Apps\Shop')->addShop(
                    false,
                    false,
                    [
                        'shop_url' => '',
                        'token' => $tokenInfo['token'],
                        'group_code' => $appConfig['group_code'],
                        'app_code' => $appConfig['app_code'],
                        'apps' => [
                            $appConfig['app_code'] => [
                                "app_id" => $appConfig['_id'],
                                "token" => $tokenInfo['token'],
                                "sub_app_id" => $subAppId
                            ]
                        ]
                    ],
                    ['shop_url', "group_code"],
                    false
                );
                if ($shop['success']) $tokenInfo['data'] = $shop['data'];
                else return ['success' => false, 'message' => $shop['message']];
            }
            // $tokenInfo['data']['app_code'] = $appConfig['app_code'];
            // die(print_r($tokenInfo));
            return $tokenInfo;
        }
    }

    public function requestAccessToken(string $code)
    {
        // die($code);
        // Do a JSON POST request to grab the access token
        $client = new \GuzzleHttp\Client();

        $request = $client->request(
            'POST',
            'https://mystore8707.myshopify.com/admin/oauth/access_token',
            [
                'form_params' => [
                    "client_id" => "66a11abc0d85cebd207ad9e287e01d04",
                    "client_secret" => "53da26f5ee5aa96f48238c5dae2532fd",
                    'code'              => $code,
                ],
                'http_errors' => false
            ]
        );

        $response = json_decode($request->getBody()->getContents(), true);
        // print_r($response);
        // die;
        // if(isset($response['error'])) return ['success' => false, 'message' => $response['error']];
        // die(print_r($response));
        // die(print_r($code));
        return ['success' => true, 'token' => $response];
        // Decode the response body as an array and return access token string
        /*return json_decode($request->getBody(), true)['access_token'];*/
    }

    public function prepareShopInfo(array $postData, array $sellerAuthToken)
    {
        $shopifyEndPoint =  "rest/v1/shop";
        $this->di->getObjectManager()->get('\App\Connector\Components\ApiClient')->call($shopifyEndPoint, [], ['AppId' => $this->di->getConfig()->get('shopify-app-id')]);
    }

    public function isSellerEligible($url, $data)
    {

        $shopExists = $this->di->getobjectManager()->create('\App\Apiconnect\Models\Apps\Shop')->getShop(false, false, ['shop_url' => $url], ['shop_url']);
        if (isset($data['sAppId']) && ($data['sAppId'] == 24)) {
            return ['success' => true];
        }

        if ($shopExists['success']) {
            if (isset($shopExists['data']['datos']['info'])) {

                if ($shopExists['data']['datos']['info']['status'] == 'ACCEPTED') {
                    return ['success' => true];
                } else if ($shopExists['data']['datos']['info']['status'] == 'UNDER_REVIEW') {
                    return [
                        'success' => false,
                        'force_redirect' => true,
                        'message' => 'Your Facebook Request Status : UNDER_REVIEW. We will notify you once your Application is accepted by Facebook.'
                    ];
                } else {
                    return [
                        'success' => false,
                        'force_redirect' => true,
                        'message' => 'Sorry , your Application is not eligible as per Facebook Guidelines. Kindly contact Facebook support for more details.'
                    ];
                }
            } else {
                return [
                    'success' => false,
                    'force_redirect' => true,
                    'install_form' => 1,
                    'shop_url' => $url
                ];
            }
        } else {
            if (isset($data['bypass_approval']) && $data['bypass_approval']) {
                $shop = $this->di->getobjectManager()->create('\App\Apiconnect\Models\Apps\Shop')->addShop(
                    false,
                    false,
                    [
                        'shop_url' => $url,
                        'info' => [
                            'status' => 'ACCEPTED',
                            'email' => isset($data['email']) ? $data['email'] : '',
                            'phone' => isset($data['phone']) ? $data['phone'] : '',
                            'name' => isset($data['name']) ? $data['name'] : '',
                            'category' => isset($data['category']) ? $data['category'] : '',
                            'facebook_id' => isset($data['facebook_id']) ? $data['facebook_id'] : '',
                        ]
                    ],
                    ['shop_url']
                );
                return ['success' => true];
            }

            /*$shop = $this->di->getobjectManager()->create('\App\Apiconnect\Models\Apps\Shop')->addShop(false, false, [
                'shop_url' => $url,
            ],
                ['shop_url']
            );*/

            return [
                'success' => false,
                'force_redirect' => true,
                'install_form' => 1,
                'shop_url' => $url
            ];
        }
    }
}
