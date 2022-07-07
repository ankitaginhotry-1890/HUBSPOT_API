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
 * @package     Ced_Magentowebapi
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
 * @license     http://cedcommerce.com/license-agreement.txt
 */

namespace App\Shopifyremote\Components\Authenticate;

// use App\Magentowebapi\Components\Core\Common As CoreCommon;
use Exception;

/**
 * Handle authentication process.
 */
class Sellerauth extends \App\Apiconnect\Components\Authenticate\Common
{

    /**
     * The redirect url.
     *
     * @var string
     */
    protected $redirectUrl;

    /**
     * Get the auth url.
     *
     * @param array $postData posted data for authoriation.
     * @return array
     */
    public function fetchAuthenticationUrl($postData)
    {
        try {
            $response = $this->getRedirectUrl($postData);

            if (!$response['success']) {
                return [
                    'success' => true,
                    'authUrl' => $this->redirectUrl
                ];
            } else {
                return [
                    'success' => true,
                    'authUrl' => $response['authUrl']
                ];
            }
        } catch (\Exception $exception) {
            return [
                'success' => false,
                'message' => $exception->getMessage()
            ];
        }
    }

    /**
     * Get app redirect url.
     *
     * @param array $postData
     * @return array
     */
    public function getRedirectUrl($postData)
    {
        try {
            // die(print_r($postData));
            $appcredentials = $this->getAppCredentials();
            $this->redirectUrl = $appcredentials['redirect_uri'];
            if (!$this->redirectUrl) {
                return [
                    'success' => false,
                    'msg' => 'Oops! Redirect is missing'
                ];
            }
            $params = $this->getAllowedDataForShop($postData);
            // print_r($params); die;

            $authUrl = $this->redirectUrl . '&' . http_build_query($params);


            $result = array(
                'success' => true,
                'authUrl' =>  $authUrl
            );
            return $result;
        } catch (\Exception $exception) {
            return [
                'success' => false,
                'message' => $exception->getMessage()
            ];
        }
    }

    public function getAppCredentials()
    {
        $requestData = $this->di->getRequest()->get();
        if (!empty($requestData['sandbox'])) {
            $mode = 'sandbox';
        } else {
            $mode = 'live';
        }
        $configData = $this->di->getRegistry()->getAppConfig();
        // die(print_r($configData));
        return [
            'redirect_uri' => !empty($configData[$mode]['redirect_uri']) ? $configData[$mode]['redirect_uri'] : false
        ];
    }

    /**
     * filter allowed data for shop.
     * @param array $data data for filter.
     * @since 1.0.0
     * @return array
     */
    public function getAllowedDataForShop($data = [])
    {

        $allowed_data = [
            'username',
            'email',
            'storeID',
            'storeurl',
            'AccessToken',
            'expireTime',
            'token_type',
            'storeCode',
            'code'
        ];
        if (!empty($data) && is_array($data)) {
            foreach ($data as $key => $value) {

                if (!in_array($key, $allowed_data)) {
                    unset($data[$key]);
                }
            }
        }

        return $data;
    }
}
