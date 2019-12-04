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
        $optionHasArray = false;
        $header = $this->header;

        if(isset($params['token'])) {
            $header["_token_"] = $params['token'];
            unset($params['token']);
        }

        $method = self::$virtualAccountApi[$apiName]['method'];
        $paramKey = $method == 'GET' ? 'query' : 'form_params';

        array_walk_recursive($params, 'self::prepareData');
        $option = [
            'headers' => $header,
            $paramKey => $params,
        ];
        self::validateOption($option, self::$jsonSchema[$apiName], $paramKey);

        # set service call product Id
        $option[$paramKey]['scProductId'] = self::$serviceCallProductId[$apiName];

        # handle list and array parameters dont send list parameters with bracket and array parameters with indexed bracket
        if (isset($params['scVoucherHash'])) {
            $option['withoutBracketParams'] =  $option[$paramKey];
            unset($option[$paramKey]);
            $optionHasArray = true;
            $method = 'GET';
        }

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
        $optionHasArray = false;
        $header = $this->header;

        if(isset($params['token'])) {
            $header["_token_"] = $params['token'];
            unset($params['token']);
        }

        $method = self::$virtualAccountApi[$apiName]['method'];
        $paramKey = $method == 'GET' ? 'query' : 'form_params';

        array_walk_recursive($params, 'self::prepareData');

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
        $optionHasArray = false;
        $header = $this->header;

        if(isset($params['token'])) {
            $header["_token_"] = $params['token'];
            unset($params['token']);
        }

        $method = self::$virtualAccountApi[$apiName]['method'];
        $paramKey = $method == 'GET' ? 'query' : 'form_params';

        array_walk_recursive($params, 'self::prepareData');
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
        $optionHasArray = false;
        $header = $this->header;

        if(isset($params['token'])) {
            $header["_token_"] = $params['token'];
            unset($params['token']);
        }

        $method = self::$virtualAccountApi[$apiName]['method'];
        $paramKey = $method == 'GET' ? 'query' : 'form_params';

        array_walk_recursive($params, 'self::prepareData');

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
        $optionHasArray = false;
        $header = $this->header;

        if(isset($params['token'])) {
            $header["_token_"] = $params['token'];
            unset($params['token']);
        }

        $method = self::$virtualAccountApi[$apiName]['method'];
        $paramKey = $method == 'GET' ? 'query' : 'form_params';

        array_walk_recursive($params, 'self::prepareData');
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
        $optionHasArray = false;
        $header = $this->header;

        if(isset($params['token'])) {
            $header["_token_"] = $params['token'];
            unset($params['token']);
        }

        $method = self::$virtualAccountApi[$apiName]['method'];
        $paramKey = $method == 'GET' ? 'query' : 'form_params';

        array_walk_recursive($params, 'self::prepareData');
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
        $optionHasArray = false;
        $header = $this->header;

        if(isset($params['token'])) {
            $header["_token_"] = $params['token'];
            unset($params['token']);
        }

        $method = self::$virtualAccountApi[$apiName]['method'];
        $paramKey = $method == 'GET' ? 'query' : 'form_params';

        array_walk_recursive($params, 'self::prepareData');

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
        $optionHasArray = false;
        $header = $this->header;

        if(isset($params['token'])) {
            $header["_token_"] = $params['token'];
            unset($params['token']);
        }

        $method = self::$virtualAccountApi[$apiName]['method'];
        $paramKey = $method == 'GET' ? 'query' : 'form_params';

        array_walk_recursive($params, 'self::prepareData');

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
        $optionHasArray = false;
        $header = $this->header;

        if(isset($params['token'])) {
            $header["_token_"] = $params['token'];
            unset($params['token']);
        }

        $method = self::$virtualAccountApi[$apiName]['method'];
        $paramKey = $method == 'GET' ? 'query' : 'form_params';

        array_walk_recursive($params, 'self::prepareData');
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
        $optionHasArray = false;
        $header = $this->header;

        if(isset($params['token'])) {
            $header["_token_"] = $params['token'];
            unset($params['token']);
        }

        $method = self::$virtualAccountApi[$apiName]['method'];
        $paramKey = $method == 'GET' ? 'query' : 'form_params';

        array_walk_recursive($params, 'self::prepareData');
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
        $optionHasArray = false;
        $header = $this->header;

        if(isset($params['token'])) {
            $header["_token_"] = $params['token'];
            unset($params['token']);
        }

        $method = self::$virtualAccountApi[$apiName]['method'];
        $paramKey = $method == 'GET' ? 'query' : 'form_params';

        array_walk_recursive($params, 'self::prepareData');
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
        $optionHasArray = false;
        $header = $this->header;

        if(isset($params['token'])) {
            $header["_token_"] = $params['token'];
            unset($params['token']);
        }

        $method = self::$virtualAccountApi[$apiName]['method'];
        $paramKey = $method == 'GET' ? 'query' : 'form_params';

        array_walk_recursive($params, 'self::prepareData');
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
        $optionHasArray = false;
        $header = $this->header;

        if(isset($params['token'])) {
            $header["_token_"] = $params['token'];
            unset($params['token']);
        }

        $method = self::$virtualAccountApi[$apiName]['method'];
        $paramKey = $method == 'GET' ? 'query' : 'form_params';

        array_walk_recursive($params, 'self::prepareData');
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
        $optionHasArray = false;
        $header = $this->header;

        if(isset($params['token'])) {
            $header["_token_"] = $params['token'];
            unset($params['token']);
        }

        $method = self::$virtualAccountApi[$apiName]['method'];
        $paramKey = $method == 'GET' ? 'query' : 'form_params';

        array_walk_recursive($params, 'self::prepareData');
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
        $optionHasArray = false;
        $header = $this->header;

        if(isset($params['token'])) {
            $header["_token_"] = $params['token'];
            unset($params['token']);
        }

        $method = self::$virtualAccountApi[$apiName]['method'];
        $paramKey = $method == 'GET' ? 'query' : 'form_params';

        array_walk_recursive($params, 'self::prepareData');
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
        $optionHasArray = false;
        $header = $this->header;

        if(isset($params['token'])) {
            $header["_token_"] = $params['token'];
            unset($params['token']);
        }

        $method = self::$virtualAccountApi[$apiName]['method'];
        $paramKey = $method == 'GET' ? 'query' : 'form_params';

        array_walk_recursive($params, 'self::prepareData');
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
        $optionHasArray = false;
        $header = $this->header;

        if(isset($params['token'])) {
            $header["_token_"] = $params['token'];
            unset($params['token']);
        }

        $method = self::$virtualAccountApi[$apiName]['method'];
        $paramKey = $method == 'GET' ? 'query' : 'form_params';

        array_walk_recursive($params, 'self::prepareData');
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
        $optionHasArray = false;
        $header = $this->header;

        if(isset($params['token'])) {
            $header["_token_"] = $params['token'];
            unset($params['token']);
        }

        $method = self::$virtualAccountApi[$apiName]['method'];
        $paramKey = $method == 'GET' ? 'query' : 'form_params';

        array_walk_recursive($params, 'self::prepareData');
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

        return ApiRequestHandler::Request(
            self::$baseUri[self::$virtualAccountApi[$apiName]['baseUri']],
            $method,
            self::$virtualAccountApi[$apiName]['subUri'],
            $option,
            false,
            $optionHasArray
        );
    }
}