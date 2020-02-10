<?php
/**
 * Created by PhpStorm.
 * User: keshtgar
 * Date: 8/11/19
 * Time: 12:33 PM
 */
namespace Pod\Virtual\Account\Service;

use Pod\Base\Service\BaseInfo;
use Pod\Base\Service\BaseService;
use Pod\Base\Service\ApiRequestHandler;


class VirtualAccountService extends BaseService
{
    private $header;
    private static $jsonSchema;
    private static $virtualAccountApi;
    private static $baseUri;
    private static $serviceCallProductId;

    public function __construct($baseInfo)
    {
        parent::__construct();
        self::$jsonSchema = json_decode(file_get_contents(__DIR__ . '/../config/validationSchema.json'), true);
        $this->header = [
            '_token_issuer_'    =>  $baseInfo->getTokenIssuer(),
            '_token_'           => $baseInfo->getToken(),
        ];
        self::$virtualAccountApi = require __DIR__ . '/../config/apiConfig.php';
        self::$serviceCallProductId = require __DIR__ . '/../config/serviceCallProductId.php';
        self::$serviceCallProductId = self::$serviceCallProductId[self::$serverType];
        self::$baseUri = self::$config[self::$serverType];
    }

    public function getBuyCreditLink($params) {
        $apiName = 'getBuyCreditLink';

        $option = [
            'headers' => [],
            'query' => $params,
        ];

        $httpQuery = self::buildHttpQuery($params);

        self::validateOption($option, self::$jsonSchema[$apiName]);

        return self::$baseUri['PRIVATE-CALL-ADDRESS'] . self::$virtualAccountApi[$apiName]['subUri'] . '?' . $httpQuery . PHP_EOL;
    }

    public function getBuyCreditByAmountLink($params) {
        $apiName = 'getBuyCreditByAmountLink';

        $option = [
            'headers' => [],
            'query' => $params,
        ];

        $httpQuery = self::buildHttpQuery($params);

        self::validateOption($option, self::$jsonSchema[$apiName]);

        return self::$baseUri['PRIVATE-CALL-ADDRESS'] . self::$virtualAccountApi[$apiName]['subUri'] . '?' . $httpQuery . PHP_EOL;
    }

    public function issueCreditInvoiceAndGetHash($params) {
        $apiName = 'issueCreditInvoiceAndGetHash';
        list($method, $option, $optionHasArray) = $this->prepareDataBeforeSend($params, $apiName);

        return ApiRequestHandler::Request(
            self::$baseUri[self::$virtualAccountApi[$apiName]['baseUri']],
            $method,
            self::$virtualAccountApi[$apiName]['subUri'],
            $option,
            false,
            $optionHasArray
        );
    }

    public function getIssueCreditInvoiceLink($hash){
        $apiName = 'getIssueCreditInvoiceLink';

        $gateway = self::$serverType == BaseInfo::PRODUCTION_SERVER ? 'PEP' : 'LOC';
        return self::$baseUri['FILE-SERVER-ADDRESS'] . self::$virtualAccountApi[$apiName]['subUri'] . '?hash='. $hash . '&gateway=' . $gateway. PHP_EOL;
    }

    public function verifyCreditInvoice($params) {
        $apiName = 'verifyCreditInvoice';
        list($method, $option, $optionHasArray) = $this->prepareDataBeforeSend($params, $apiName);

        return ApiRequestHandler::Request(
            self::$baseUri[self::$virtualAccountApi[$apiName]['baseUri']],
            $method,
            self::$virtualAccountApi[$apiName]['subUri'],
            $option,
            false,
            $optionHasArray
        );
    }

    public function transferFromOwnAccounts($params) {
        $apiName = 'transferFromOwnAccounts';
        $header = $this->header;

        if(isset($params['token'])) {
            $header["_token_"] = $params['token'];
            unset($params['token']);
        }

//        $method = self::$virtualAccountApi[$apiName]['method'];
        $method = 'GET';
//        $paramKey = $method == 'GET' ? 'query' : 'form_params';

        array_walk_recursive($params, 'self::prepareData');
        $option = [
            'headers' => $header,
            'query' => $params,
        ];

        self::validateOption($option, self::$jsonSchema[$apiName]);

        foreach ($params['guildAmount'] as $guildAmount){
            $params['guildCode'][] = $guildAmount['guildCode'];
            $params['amount'][] = $guildAmount['amount'];
        }

        unset($params['guildAmount']);

        # set service call product Id
        $params['scProductId'] = self::$serviceCallProductId[$apiName];

        $option['withoutBracketParams'] = $params;
        unset($option['query']);

//        if (isset($params['scVoucherHash'])) {
//            $option['withoutBracketParams'] =  $option[$paramKey];
//            unset($option[$paramKey]);
//            $optionHasArray = true;
//            $method = 'GET';
//        }

        return ApiRequestHandler::Request(
            self::$baseUri[self::$virtualAccountApi[$apiName]['baseUri']],
            $method,
            self::$virtualAccountApi[$apiName]['subUri'],
            $option,
            false,
            true
        );
    }

    public function transferFromOwnAccountsList($params) {
        $apiName = 'transferFromOwnAccountsList';
        list($method, $option, $optionHasArray) = $this->prepareDataBeforeSend($params, $apiName);

        return ApiRequestHandler::Request(
            self::$baseUri[self::$virtualAccountApi[$apiName]['baseUri']],
            $method,
            self::$virtualAccountApi[$apiName]['subUri'],
            $option,
            false,
            $optionHasArray
        );
    }

    public function transferToContact($params) {
        $apiName = 'transferToContact';
        list($method, $option, $optionHasArray) = $this->prepareDataBeforeSend($params, $apiName);

        return ApiRequestHandler::Request(
            self::$baseUri[self::$virtualAccountApi[$apiName]['baseUri']],
            $method,
            self::$virtualAccountApi[$apiName]['subUri'],
            $option,
            false,
            $optionHasArray
        );
    }

    public function transferToContactList($params) {
        $apiName = 'transferToContactList';
        list($method, $option, $optionHasArray) = $this->prepareDataBeforeSend($params, $apiName);

        return ApiRequestHandler::Request(
            self::$baseUri[self::$virtualAccountApi[$apiName]['baseUri']],
            $method,
            self::$virtualAccountApi[$apiName]['subUri'],
            $option,
            false,
            $optionHasArray
        );
    }

    public function follow($params) {
        $apiName = 'follow';
        list($method, $option, $optionHasArray) = $this->prepareDataBeforeSend($params, $apiName);

        return ApiRequestHandler::Request(
            self::$baseUri[self::$virtualAccountApi[$apiName]['baseUri']],
            $method,
            self::$virtualAccountApi[$apiName]['subUri'],
            $option,
            false,
            $optionHasArray
        );
    }

    public function getFollowers($params) {
        $apiName = 'getFollowers';
        list($method, $option, $optionHasArray) = $this->prepareDataBeforeSend($params, $apiName);

        return ApiRequestHandler::Request(
            self::$baseUri[self::$virtualAccountApi[$apiName]['baseUri']],
            $method,
            self::$virtualAccountApi[$apiName]['subUri'],
            $option,
            false,
            $optionHasArray
        );
    }

    public function getBusiness($params) {
        $apiName = 'getBusiness';
        list($method, $option, $optionHasArray) = $this->prepareDataBeforeSend($params, $apiName);

        return ApiRequestHandler::Request(
            self::$baseUri[self::$virtualAccountApi[$apiName]['baseUri']],
            $method,
            self::$virtualAccountApi[$apiName]['subUri'],
            $option,
            false,
            $optionHasArray
        );
    }

    public function transferToFollower($params) {
        $apiName = 'transferToFollower';
        list($method, $option, $optionHasArray) = $this->prepareDataBeforeSend($params, $apiName);

        return ApiRequestHandler::Request(
            self::$baseUri[self::$virtualAccountApi[$apiName]['baseUri']],
            $method,
            self::$virtualAccountApi[$apiName]['subUri'],
            $option,
            false,
            $optionHasArray
        );
    }

    public function transferToFollowerAndCashout($params) {
        $apiName = 'transferToFollowerAndCashout';
        list($method, $option, $optionHasArray) = $this->prepareDataBeforeSend($params, $apiName);

        return ApiRequestHandler::Request(
            self::$baseUri[self::$virtualAccountApi[$apiName]['baseUri']],
            $method,
            self::$virtualAccountApi[$apiName]['subUri'],
            $option,
            false,
            $optionHasArray
        );
    }

    public function transferToFollowerList($params) {
        $apiName = 'transferToFollowerList';
        list($method, $option, $optionHasArray) = $this->prepareDataBeforeSend($params, $apiName);

        return ApiRequestHandler::Request(
            self::$baseUri[self::$virtualAccountApi[$apiName]['baseUri']],
            $method,
            self::$virtualAccountApi[$apiName]['subUri'],
            $option,
            false,
            $optionHasArray
        );
    }

    public function transferByInvoice($params) {
        $apiName = 'transferByInvoice';
        list($method, $option, $optionHasArray) = $this->prepareDataBeforeSend($params, $apiName);

        return ApiRequestHandler::Request(
            self::$baseUri[self::$virtualAccountApi[$apiName]['baseUri']],
            $method,
            self::$virtualAccountApi[$apiName]['subUri'],
            $option,
            false,
            $optionHasArray
        );
    }

    public function listTransferByInvoice($params) {
        $apiName = 'listTransferByInvoice';
        list($method, $option, $optionHasArray) = $this->prepareDataBeforeSend($params, $apiName);

        return ApiRequestHandler::Request(
            self::$baseUri[self::$virtualAccountApi[$apiName]['baseUri']],
            $method,
            self::$virtualAccountApi[$apiName]['subUri'],
            $option,
            false,
            $optionHasArray
        );
    }

    public function getWalletAccountBill($params) {
        $apiName = 'getWalletAccountBill';
        list($method, $option, $optionHasArray) = $this->prepareDataBeforeSend($params, $apiName);

        return ApiRequestHandler::Request(
            self::$baseUri[self::$virtualAccountApi[$apiName]['baseUri']],
            $method,
            self::$virtualAccountApi[$apiName]['subUri'],
            $option,
            false,
            $optionHasArray
        );
    }

    public function getGuildAccountBill($params) {
        $apiName = 'getGuildAccountBill';
        list($method, $option, $optionHasArray) = $this->prepareDataBeforeSend($params, $apiName);

        return ApiRequestHandler::Request(
            self::$baseUri[self::$virtualAccountApi[$apiName]['baseUri']],
            $method,
            self::$virtualAccountApi[$apiName]['subUri'],
            $option,
            false,
            $optionHasArray
        );
    }

    public function getAccountBillAsFile($params) {
        $apiName = 'getAccountBillAsFile';
        list($method, $option, $optionHasArray) = $this->prepareDataBeforeSend($params, $apiName);

        return ApiRequestHandler::Request(
            self::$baseUri[self::$virtualAccountApi[$apiName]['baseUri']],
            $method,
            self::$virtualAccountApi[$apiName]['subUri'],
            $option,
            false,
            $optionHasArray
        );
    }

    public function cardToCardList($params) {
        $apiName = 'cardToCardList';
        list($method, $option, $optionHasArray) = $this->prepareDataBeforeSend($params, $apiName);

        return ApiRequestHandler::Request(
            self::$baseUri[self::$virtualAccountApi[$apiName]['baseUri']],
            $method,
            self::$virtualAccountApi[$apiName]['subUri'],
            $option,
            false,
            $optionHasArray
        );
    }

    public function updateCardToCard($params) {
        $apiName = 'updateCardToCard';
        list($method, $option, $optionHasArray) = $this->prepareDataBeforeSend($params, $apiName);

        return ApiRequestHandler::Request(
            self::$baseUri[self::$virtualAccountApi[$apiName]['baseUri']],
            $method,
            self::$virtualAccountApi[$apiName]['subUri'],
            $option,
            false,
            $optionHasArray
        );
    }

    public function addWithdrawRulePlan($params) {
        $apiName = 'addWithdrawRulePlan';
        list($method, $option, $optionHasArray) = $this->prepareDataBeforeSend($params, $apiName);

        return ApiRequestHandler::Request(
            self::$baseUri[self::$virtualAccountApi[$apiName]['baseUri']],
            $method,
            self::$virtualAccountApi[$apiName]['subUri'],
            $option,
            false,
            $optionHasArray
        );
    }

    public function withdrawRulePlanList($params) {
        $apiName = 'withdrawRulePlanList';
        list($method, $option, $optionHasArray) = $this->prepareDataBeforeSend($params, $apiName);

        return ApiRequestHandler::Request(
            self::$baseUri[self::$virtualAccountApi[$apiName]['baseUri']],
            $method,
            self::$virtualAccountApi[$apiName]['subUri'],
            $option,
            false,
            $optionHasArray
        );
    }

    public function getLinkIssueWithdrawRuleByPlan($params) {
        $apiName = 'getLinkIssueWithdrawRuleByPlan';
        $header = $this->header;
        array_walk_recursive($params, 'self::prepareData');
        $paramKey = (self::$virtualAccountApi[$apiName]['method'] == 'GET') ? 'query' : 'form_params';

        // if token is set replace it
        if(isset($params['token'])) {
            $header["_token_"] = $params['token'];
            unset($params['token']);
        }

        $option = [
            'headers' => $header,
            $paramKey => $params,
        ];

        self::validateOption($option, self::$jsonSchema[$apiName], $paramKey);
        $httpQuery = self::buildHttpQuery($params);

        return self::$baseUri['PRIVATE-CALL-ADDRESS'] . self::$virtualAccountApi[$apiName]['subUri'] . '?' . $httpQuery. PHP_EOL;
    }

    public function getLinkIssueWithdrawRule($params) {
        $apiName = 'getLinkIssueWithdrawRule';
        $header = $this->header;
        array_walk_recursive($params, 'self::prepareData');
        $paramKey = (self::$virtualAccountApi[$apiName]['method'] == 'GET') ? 'query' : 'form_params';

        // if token is set replace it
        if(isset($params['token'])) {
            $header["_token_"] = $params['token'];
            unset($params['token']);
        }

        $option = [
            'headers' => $header,
            $paramKey => $params,
        ];

        self::validateOption($option, self::$jsonSchema[$apiName], $paramKey);
        $httpQuery = self::buildHttpQuery($params);

        return self::$baseUri['PRIVATE-CALL-ADDRESS'] . self::$virtualAccountApi[$apiName]['subUri'] . '?' . $httpQuery. PHP_EOL;
    }

    public function grantedWithdrawRuleList($params) {
        $apiName = 'grantedWithdrawRuleList';
        list($method, $option, $optionHasArray) = $this->prepareDataBeforeSend($params, $apiName);

        return ApiRequestHandler::Request(
            self::$baseUri[self::$virtualAccountApi[$apiName]['baseUri']],
            $method,
            self::$virtualAccountApi[$apiName]['subUri'],
            $option,
            false,
            $optionHasArray
        );
    }

    public function revokeWithdrawRule($params) {
        $apiName = 'revokeWithdrawRule';
        list($method, $option, $optionHasArray) = $this->prepareDataBeforeSend($params, $apiName);

        return ApiRequestHandler::Request(
            self::$baseUri[self::$virtualAccountApi[$apiName]['baseUri']],
            $method,
            self::$virtualAccountApi[$apiName]['subUri'],
            $option,
            false,
            $optionHasArray
        );
    }

    public function withdrawRuleUsageReport($params) {
        $apiName = 'withdrawRuleUsageReport';
        list($method, $option, $optionHasArray) = $this->prepareDataBeforeSend($params, $apiName);

        return ApiRequestHandler::Request(
            self::$baseUri[self::$virtualAccountApi[$apiName]['baseUri']],
            $method,
            self::$virtualAccountApi[$apiName]['subUri'],
            $option,
            false,
            $optionHasArray
        );
    }

    public function PayInvoiceByCredit($params) {
        $apiName = 'PayInvoiceByCredit';
        list($method, $option, $optionHasArray) = $this->prepareDataBeforeSend($params, $apiName);

        return ApiRequestHandler::Request(
            self::$baseUri[self::$virtualAccountApi[$apiName]['baseUri']],
            $method,
            self::$virtualAccountApi[$apiName]['subUri'],
            $option,
            false,
            $optionHasArray
        );
    }

    private function prepareDataBeforeSend($params, $apiName){
        $header = $this->header;
        $optionHasArray = false;
        array_walk_recursive($params, 'self::prepareData');
        $method = self::$virtualAccountApi[$apiName]['method'];
        $paramKey = $method == 'GET' ? 'query' : 'form_params';

        // if token is set replace it
        if(isset($params['token'])) {
            $header["_token_"] = $params['token'];
            unset($params['token']);
        }

        $option = [
            'headers' => $header,
            $paramKey => $params,
        ];

        self::validateOption($option, self::$jsonSchema[$apiName], $paramKey);

        # set service call product Id
        $option[$paramKey]['scProductId'] = self::$serviceCallProductId[$apiName];

        if (isset($params['scVoucherHash'])) {
            $option['withoutBracketParams'] =  $option[$paramKey];
            unset($option[$paramKey]);
            $optionHasArray = true;
            $method = 'GET';
        }

        return [$method, $option, $optionHasArray];
    }
}