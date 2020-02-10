<?php
/**
 * Created by PhpStorm.
 * User: keshtgar
 * Date: 11/11/19
 * Time: 9:49 AM
 */
use PHPUnit\Framework\TestCase;
use Pod\Virtual\Account\Service\VirtualAccountService;
use Pod\Base\Service\BaseInfo;
use Pod\Base\Service\Exception\ValidationException;
use Pod\Base\Service\Exception\PodException;

final class VirtualAccountServiceTest extends TestCase
{
//    public static $apiToken;
    public static $virtualAccountService;
    private $tokenIssuer;
    private $apiToken;
    private $searchScApiKey;
    private $scApiKey;
    public function setUp(): void
    {
        parent::setUp();
        # set serverType to SandBox or Production
        BaseInfo::initServerType(BaseInfo::SANDBOX_SERVER);
        $virtualAccountTestData =  require __DIR__ . '/virtualAccountTestData.php';
        $this->apiToken = $virtualAccountTestData['token'];
        $this->tokenIssuer =  $virtualAccountTestData['tokenIssuer'];
        $this->scApiKey = $virtualAccountTestData['scApiKey'];

        $baseInfo = new BaseInfo();
        $baseInfo->setTokenIssuer($this->apiToken);
        $baseInfo->setToken($this->apiToken);

        self::$virtualAccountService = new virtualAccountService($baseInfo);
    }

    public function testIssueCreditInvoiceAndGetHashAllParameters()
    {
        $params =
            [
                ## ================ Required Parameters  =====================
                'amount'        => 1000,
                'userId'        => 16128,
                'billNumber'    => uniqid(),
                'wallet'        => 'PODLAND_WALLET',
                'redirectUrl'   => 'http://www.google.com',
                ## ================ Optional Parameters  =====================
                'token'         => $this->apiToken,
                'scApiKey'          => $this->scApiKey,
//        'scVoucherHash'     => ['{Put Service Call Voucher Hashes}', '234'],
        ];

        try {
            $result = self::$virtualAccountService->issueCreditInvoiceAndGetHash($params);
            $this->assertFalse($result['hasError']);
            $this->assertIsString($result['result']);
        } catch (ValidationException $e) {
            $this->fail('ValidationException: ' . $e->getErrorsAsString());
        } catch (PodException $e) {
            $error = $e->getResult();
            $this->fail('PodException: ' . $error['message']);
        }
    }

    public function testIssueCreditInvoiceAndGetHashRequiredParameters()
    {
        $params =
            [
                ## ================ Required Parameters  =====================
                'amount'        => 1000,
                'userId'        => 16128,
                'billNumber'    => uniqid(),
                'wallet'        => 'PODLAND_WALLET',
                'redirectUrl'   => 'http://www.google.com',
            ];
        try {
            $result = self::$virtualAccountService->issueCreditInvoiceAndGetHash($params);
            $this->assertFalse($result['hasError']);
            $this->assertIsString($result['result']);
        } catch (ValidationException $e) {
            $this->fail('ValidationException: ' . $e->getErrorsAsString());
        } catch (PodException $e) {
            $error = $e->getResult();
            $this->fail('PodException: ' . $error['message']);
        }
    }

    public function testIssueCreditInvoiceAndGetHashValidationError()
    {
        $paramsWithoutRequired = [];
        $paramsWrongValue = [
            'amount'        => '1000',
            'userId'        => '16128',
            'billNumber'    => 1141,
            'wallet'        => 1234,
            'redirectUrl'   => 'www.google.com',
        ];
        try {
            self::$virtualAccountService->issueCreditInvoiceAndGetHash($paramsWithoutRequired);
        } catch (ValidationException $e) {

            $validation = $e->getErrorsAsArray();
            $this->assertNotEmpty($validation);

            $result = $e->getResult();

            $this->assertArrayHasKey('amount', $validation);
            $this->assertEquals('The property amount is required', $validation['amount'][0]);

            $this->assertArrayHasKey('userId', $validation);
            $this->assertEquals('The property userId is required', $validation['userId'][0]);

            $this->assertArrayHasKey('billNumber', $validation);
            $this->assertEquals('The property billNumber is required', $validation['billNumber'][0]);

            $this->assertArrayHasKey('wallet', $validation);
            $this->assertEquals('The property wallet is required', $validation['wallet'][0]);

            $this->assertArrayHasKey('redirectUrl', $validation);
            $this->assertEquals('The property redirectUrl is required', $validation['redirectUrl'][0]);

            $this->assertEquals(887, $result['code']);
        } catch (PodException $e) {
            $error = $e->getResult();
            $this->fail('PodException: ' . $error['message']);
        }
        try {
            self::$virtualAccountService->issueCreditInvoiceAndGetHash($paramsWrongValue);
        } catch (ValidationException $e) {

            $validation = $e->getErrorsAsArray();
            $this->assertNotEmpty($validation);

            $result = $e->getResult();


            $this->assertArrayHasKey('amount', $validation);
            $this->assertEquals('String value found, but a number is required', $validation['amount'][1]);

            $this->assertArrayHasKey('userId', $validation);
            $this->assertEquals('String value found, but an integer is required', $validation['userId'][1]);

            $this->assertArrayHasKey('billNumber', $validation);
            $this->assertEquals('Integer value found, but a string is required', $validation['billNumber'][1]);

            $this->assertArrayHasKey('wallet', $validation);
            $this->assertEquals('Integer value found, but a string is required', $validation['wallet'][1]);

            $this->assertArrayHasKey('redirectUrl', $validation);
            $this->assertEquals('Invalid URL format', $validation['redirectUrl'][1]);

            $this->assertEquals(887, $result['code']);
        } catch (PodException $e) {
            $error = $e->getResult();
            $this->fail('PodException: ' . $error['message']);
        }
    }

    public function testVerifyCreditInvoiceAllParameters()
    {
        $params =
            [
                ## ================= Required Parameters  =======================
                'billNumber'    => '1234',
                ## ================= Optional Parameters  =======================
//                 'id'            => 12,
                'scVoucherHash' => ['{Put Service Call Voucher Hashes}', '234'],
                'scApiKey'             => $this->scApiKey,
                'token'                => $this->apiToken,      # Api_Token | AccessToken
        ];

        try {
            $result = self::$virtualAccountService->verifyCreditInvoice($params);
            $this->assertFalse($result['hasError']);
        } catch (ValidationException $e) {
            $this->fail('ValidationException: ' . $e->getErrorsAsString());
        } catch (PodException $e) {
            $error = $e->getResult();
            $this->fail('PodException: ' . $error['message']);
        }
    }

    public function testVerifyCreditInvoiceRequiredParameters()
    {
        $params =
            [
                ## ================= Required Parameters  =======================
                'billNumber'    => '1234',
            ];
        try {
            $result = self::$virtualAccountService->verifyCreditInvoice($params);
            $this->assertFalse($result['hasError']);
        } catch (ValidationException $e) {
            $this->fail('ValidationException: ' . $e->getErrorsAsString());
        } catch (PodException $e) {
            $error = $e->getResult();
            $this->assertEquals('فاکتور قبلا پرداخت شده است', $error['message']);
        }
    }

    public function testVerifyCreditInvoiceValidationError()
    {
        $paramsWithoutRequired = [];
        $paramsWrongValue = [
            'scApiKey'      => 1234,
            'billNumber'    => 1234,
            'id'           => '12',
        ];
        try {
            self::$virtualAccountService->verifyCreditInvoice($paramsWithoutRequired);
        } catch (ValidationException $e) {

            $validation = $e->getErrorsAsArray();
            $this->assertNotEmpty($validation);

            $result = $e->getResult();

            $this->assertArrayHasKey('billNumber', $validation);
            $this->assertEquals('The property billNumber is required', $validation['billNumber'][0]);


            $this->assertEquals(887, $result['code']);
        } catch (PodException $e) {
            $error = $e->getResult();
            $this->fail('PodException: ' . $error['message']);
        }
        try {
            self::$virtualAccountService->verifyCreditInvoice($paramsWrongValue);
        } catch (ValidationException $e) {

            $validation = $e->getErrorsAsArray();
            $this->assertNotEmpty($validation);

            $result = $e->getResult();


            $this->assertArrayHasKey('scApiKey', $validation);
            $this->assertEquals('Integer value found, but a string is required', $validation['scApiKey'][0]);

            $this->assertArrayHasKey('billNumber', $validation);
            $this->assertEquals('Integer value found, but a string is required', $validation['billNumber'][1]);

            $this->assertArrayHasKey('id', $validation);
            $this->assertEquals('String value found, but an integer is required', $validation['id'][0]);

            $this->assertEquals(887, $result['code']);
        } catch (PodException $e) {
            $error = $e->getResult();
            $this->fail('PodException: ' . $error['message']);
        }
    }

    public function testTransferFromOwnAccountsAllParameters()
    {
        $params =
            [
                ## ================= *Required Parameters  ===================
                'guildAmount' => [
                    [
                        'guildCode' => 'INFORMATION_TECHNOLOGY_GUILD',
                        'amount' => +100,
                    ],
                    [
                        'guildCode' => 'TOILETRIES_GUILD',
                        'amount' => +200,
                    ]

                ],
                'customerAmount' => -300,
                ## ================ Optional Parameters  =====================
                'token'                => $this->apiToken,      # Api_Token | AccessToken
                'scApiKey'             => $this->scApiKey,
                'currencyCode' => 'IRR',
                'description' => 'IR',
                'wallet' => 'PODLAND_WALLET',
                'uniqueId' => uniqid(),
                'scVoucherHash'          => ["1234", "546"],
        ];

        try {
            $result = self::$virtualAccountService->transferFromOwnAccounts($params);
            $this->assertFalse($result['hasError']);
        } catch (ValidationException $e) {
            $this->fail('ValidationException: ' . $e->getErrorsAsString());
        } catch (PodException $e) {
            $error = $e->getResult();
            $this->fail('PodException: ' . $error['message']);
        }
    }

    public function testTransferFromOwnAccountsRequiredParameters()
    {
        $params =
            [
                ## ================= *Required Parameters  ===================
                'guildAmount' => [
                    [
                        'guildCode' => 'INFORMATION_TECHNOLOGY_GUILD',
                        'amount' => +100,
                    ],
                    [
                        'guildCode' => 'TOILETRIES_GUILD',
                        'amount' => +200,
                    ]

                ],
                'customerAmount' => -300,
            ];
        try {
            $result = self::$virtualAccountService->transferFromOwnAccounts($params);
            $this->assertFalse($result['hasError']);
        } catch (ValidationException $e) {
            $this->fail('ValidationException: ' . $e->getErrorsAsString());
        } catch (PodException $e) {
            $error = $e->getResult();
            $this->fail('PodException: ' . $error['message']);
        }
    }

    public function testTransferFromOwnAccountsValidationError()
    {
        $paramsWithoutRequired = [];
        $paramsWrongValue = [
            'guildAmount'       => [],
            'customerAmount'    => '10',
        ];
        try {
            self::$virtualAccountService->transferFromOwnAccounts($paramsWithoutRequired);
        } catch (ValidationException $e) {

            $validation = $e->getErrorsAsArray();
            $this->assertNotEmpty($validation);

            $result = $e->getResult();

            $this->assertArrayHasKey('customerAmount', $validation);
            $this->assertEquals('The property customerAmount is required', $validation['customerAmount'][0]);

            $this->assertArrayHasKey('guildAmount', $validation);
            $this->assertEquals('The property guildAmount is required', $validation['guildAmount'][0]);

            $this->assertEquals(887, $result['code']);
        } catch (PodException $e) {
            $error = $e->getResult();
            $this->fail('PodException: ' . $error['message']);
        }
        try {
            self::$virtualAccountService->transferFromOwnAccounts($paramsWrongValue);
        } catch (ValidationException $e) {

            $validation = $e->getErrorsAsArray();
            $this->assertNotEmpty($validation);

            $result = $e->getResult();


            $this->assertArrayHasKey('guildAmount', $validation);
            $this->assertEquals('There must be a minimum of 1 items in the array', $validation['guildAmount'][1]);

            $this->assertArrayHasKey('customerAmount', $validation);
            $this->assertEquals('String value found, but a number is required', $validation['customerAmount'][1]);

            $this->assertEquals(887, $result['code']);
        } catch (PodException $e) {
            $error = $e->getResult();
            $this->fail('PodException: ' . $error['message']);
        }
    }

    public function testTransferFromOwnAccountsListAllParameters()
    {
        $params =
            [

                ## ================= Required Parameters  ===================
                'offset'        => 0,
                'size'          => 10,
                ## ================= Optional Parameters  ===================
                'scApiKey'      => $this->scApiKey,
                'token'         => $this->apiToken,      # Api_Token | AccessToken
                'scVoucherHash' => ["1234", "546"],
        ];

        try {
            $result = self::$virtualAccountService->transferFromOwnAccountsList($params);
            $this->assertFalse($result['hasError']);
        } catch (ValidationException $e) {
            $this->fail('ValidationException: ' . $e->getErrorsAsString());
        } catch (PodException $e) {
            $error = $e->getResult();
            $this->fail('PodException: ' . $error['message']);
        }
    }

    public function testTransferFromOwnAccountsListRequiredParameters()
    {
        $params =
            [
                ## ================= Required Parameters  ===================
                'offset'        => 0,
                'size'          => 10,
            ];
        try {
            $result = self::$virtualAccountService->transferFromOwnAccountsList($params);
            $this->assertFalse($result['hasError']);
        } catch (ValidationException $e) {
            $this->fail('ValidationException: ' . $e->getErrorsAsString());
        } catch (PodException $e) {
            $error = $e->getResult();
            $this->fail('PodException: ' . 'code: '.$error['code'] . ';;' . $error['message']);
        }
    }

    public function testTransferFromOwnAccountsListValidationError()
    {
        $paramsWithoutRequired = [];
        $paramsWrongValue = [
            'scApiKey' => 1234,
            'offset'  => '1234',
            'size'  => '1234',
        ];
        try {
            self::$virtualAccountService->transferFromOwnAccountsList($paramsWithoutRequired);
        } catch (ValidationException $e) {

            $validation = $e->getErrorsAsArray();
            $this->assertNotEmpty($validation);
            $result = $e->getResult();

            $this->assertArrayHasKey('offset', $validation);
            $this->assertEquals('The property offset is required', $validation['offset'][0]);

            $this->assertArrayHasKey('size', $validation);
            $this->assertEquals('The property size is required', $validation['size'][0]);

            $this->assertEquals(887, $result['code']);
        } catch (PodException $e) {
            $error = $e->getResult();
            $this->fail('PodException: ' . $error['message']);
        }
        try {
            self::$virtualAccountService->transferFromOwnAccountsList($paramsWrongValue);
        } catch (ValidationException $e) {

            $validation = $e->getErrorsAsArray();
            $this->assertNotEmpty($validation);

            $result = $e->getResult();


            $this->assertArrayHasKey('scApiKey', $validation);
            $this->assertEquals('Integer value found, but a string is required', $validation['scApiKey'][0]);

            $this->assertArrayHasKey('offset', $validation);
            $this->assertEquals('String value found, but an integer is required', $validation['offset'][1]);

            $this->assertArrayHasKey('size', $validation);
            $this->assertEquals('String value found, but an integer is required', $validation['size'][1]);

            $this->assertEquals(887, $result['code']);
        } catch (PodException $e) {
            $error = $e->getResult();
            $this->fail('PodException: ' . $error['message']);
        }
    }
}