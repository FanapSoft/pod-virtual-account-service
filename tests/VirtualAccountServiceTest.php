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

            $this->assertArrayHasKey('scApiKey', $validation);
            $this->assertEquals('The property scApiKey is required', $validation['scApiKey'][0]);

            $this->assertArrayHasKey('id', $validation);
            $this->assertEquals('The property id is required', $validation['id'][0]);

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
            $this->assertEquals('Integer value found, but a string is required', $validation['scApiKey'][1]);

            $this->assertArrayHasKey('id', $validation);
            $this->assertEquals('Integer value found, but a string is required', $validation['id'][1]);

            $this->assertEquals(887, $result['code']);
        } catch (PodException $e) {
            $error = $e->getResult();
            $this->fail('PodException: ' . $error['message']);
        }
    }

    public function testTransferToContactAllParameters()
    {
        $params =
            [
                ## ================== Required Parameters  ======================
                'contactId' => 21111,            //شناسه مخاطب مقصد برای انتقال اعتبار
                'amount'    => 100,
                ## ================= Optional Parameters  ======================
                'wallet'    => 'PODLAND_WALLET',           //کد کیف پول
                'currencyCode' => 'IRR',
                'description' => 'test desc' ,           // توضیحات
                'uniqueId' =>  uniqid(),              //ارسال شناسه دلخواه و یکتا به منظور جستجو در لیست
                'scApiKey' => $this->scApiKey,
                'token'                => $this->apiToken,      # Api_Token | AccessToken
                'scVoucherHash'          => ["1234", "546"],
        ];

        try {
            $result = self::$virtualAccountService->transferToContact($params);
            $this->assertFalse($result['hasError']);
        } catch (ValidationException $e) {
            $this->fail('ValidationException: ' . $e->getErrorsAsString());
        } catch (PodException $e) {
            $error = $e->getResult();
            $this->fail('PodException: ' . $error['message']);
        }
    }

    public function testTransferToContactRequiredParameters()
    {
        $params =
            [
                ## ================== Required Parameters  ======================
                'contactId' => 21111,            //شناسه مخاطب مقصد برای انتقال اعتبار
                'amount'    => 100,
        ];
        try {
            $result = self::$virtualAccountService->transferToContact($params);
            $this->assertFalse($result['hasError']);
        } catch (ValidationException $e) {
            $this->fail('ValidationException: ' . $e->getErrorsAsString());
        } catch (PodException $e) {
            $error = $e->getResult();
            $this->fail('PodException: ' . $error['message']);
        }
    }

    public function testTransferToContactValidationError()
    {
        $paramsWithoutRequired = [];
        $paramsWrongValue = [
            'contactId' => '21111',            //شناسه مخاطب مقصد برای انتقال اعتبار
            'amount'    => '100',
        ];
        try {
            self::$virtualAccountService->transferToContact($paramsWithoutRequired);
        } catch (ValidationException $e) {

            $validation = $e->getErrorsAsArray();
            $this->assertNotEmpty($validation);

            $result = $e->getResult();

            $this->assertArrayHasKey('scApiKey', $validation);
            $this->assertEquals('The property scApiKey is required', $validation['scApiKey'][0]);

            $this->assertArrayHasKey('id', $validation);
            $this->assertEquals('The property id is required', $validation['id'][0]);

            $this->assertEquals(887, $result['code']);
        } catch (PodException $e) {
            $error = $e->getResult();
            $this->fail('PodException: ' . $error['message']);
        }
        try {
            self::$virtualAccountService->transferToContact($paramsWrongValue);
        } catch (ValidationException $e) {

            $validation = $e->getErrorsAsArray();
            $this->assertNotEmpty($validation);

            $result = $e->getResult();


            $this->assertArrayHasKey('scApiKey', $validation);
            $this->assertEquals('Integer value found, but a string is required', $validation['scApiKey'][1]);

            $this->assertArrayHasKey('id', $validation);
            $this->assertEquals('Integer value found, but a string is required', $validation['id'][1]);

            $this->assertEquals(887, $result['code']);
        } catch (PodException $e) {
            $error = $e->getResult();
            $this->fail('PodException: ' . $error['message']);
        }
    }

    public function testFollowAllParameters()
    {
        $params =
            [
                ## ============================ Required Parameters  ==================================
                'scApiKey'           => '006b40658c284e11a35dd51613749046',
                ## ============================ Optional Parameters  ==================================
                'offset' => 0,
                'size' => 10,
                'orderBy' => 'id',
                'order' => 'desc',
                'filter' => 'message',
                'filterValue' => 'dude',
                'token'                => $this->apiToken,      # Api_Token | AccessToken
                'scVoucherHash'          => ["1234", "546"],
        ];

        try {
            $result = self::$virtualAccountService->follow($params);
            $this->assertFalse($result['hasError']);
            
        } catch (ValidationException $e) {
            $this->fail('ValidationException: ' . $e->getErrorsAsString());
        } catch (PodException $e) {
            $error = $e->getResult();
            $this->fail('PodException: ' . $error['message']);
        }
    }

    public function testFollowRequiredParameters()
    {
        $params =
            [
                ## ============================ Required Parameters  ==================================
                'scApiKey'           => '006b40658c284e11a35dd51613749046',
        ];
        try {
            $result = self::$virtualAccountService->follow($params);
            $this->assertFalse($result['hasError']);
            
        } catch (ValidationException $e) {
            $this->fail('ValidationException: ' . $e->getErrorsAsString());
        } catch (PodException $e) {
            $error = $e->getResult();
            $this->fail('PodException: ' . $error['message']);
        }
    }

    public function testFollowValidationError()
    {
        $paramsWithoutRequired = [];
        $paramsWrongValue = [
            'scApiKey'             => 1234,
        ];
        try {
            self::$virtualAccountService->follow($paramsWithoutRequired);
        } catch (ValidationException $e) {

            $validation = $e->getErrorsAsArray();
            $this->assertNotEmpty($validation);

            $result = $e->getResult();

            $this->assertArrayHasKey('scApiKey', $validation);
            $this->assertEquals('The property scApiKey is required', $validation['scApiKey'][0]);

            $this->assertEquals(887, $result['code']);
        } catch (PodException $e) {
            $error = $e->getResult();
            $this->fail('PodException: ' . $error['message']);
        }
        try {
            self::$virtualAccountService->follow($paramsWrongValue);
        } catch (ValidationException $e) {

            $validation = $e->getErrorsAsArray();
            $this->assertNotEmpty($validation);

            $result = $e->getResult();

            $this->assertArrayHasKey('scApiKey', $validation);
            $this->assertEquals('Integer value found, but a string is required', $validation['scApiKey'][1]);

            $this->assertEquals(887, $result['code']);
        } catch (PodException $e) {
            $error = $e->getResult();
            $this->fail('PodException: ' . $error['message']);
        }
    }

    public function testGetFollowersAllParameters()
    {
        $params =
            [
                ## ============================ Required Parameters  ==================================
                'scApiKey'  => '9238ff044afd4bfcb6df78fb1b47d4be',
                'id' => '84919cc0-ceb9-4907-87e7-2b877c038d0c',
                'statusType' => 'received',  # all or received or seen
                ## ======================== Optional Parameters  ==========================
                'token'                => $this->apiToken,      # Api_Token | AccessToken
//            'scVoucherHash'          => ["1234", "546"],
        ];

        try {
            $result = self::$virtualAccountService->GetFollowers($params);
            $this->assertFalse($result['hasError']);
            
        } catch (ValidationException $e) {
            $this->fail('ValidationException: ' . $e->getErrorsAsString());
        } catch (PodException $e) {
            $error = $e->getResult();
            $this->fail('PodException: ' . $error['message']);
        }
    }

    public function testGetFollowersRequiredParameters()
    {
        $params =
            [
                ## ======================= *Required Parameters  ==========================
                'scApiKey'  => '9238ff044afd4bfcb6df78fb1b47d4be',
                'id' => '84919cc0-ceb9-4907-87e7-2b877c038d0c',
                'statusType' => 'received',  # all or received or seen
            ];
        try {
            $result = self::$virtualAccountService->GetFollowers($params);
            $this->assertFalse($result['hasError']);
            
        } catch (ValidationException $e) {
            $this->fail('ValidationException: ' . $e->getErrorsAsString());
        } catch (PodException $e) {
            $error = $e->getResult();
            $this->fail('PodException: ' . $error['message']);
        }
    }

    public function testGetFollowersValidationError()
    {
        $paramsWithoutRequired = [];
        $paramsWrongValue = [
            'scApiKey' => 1234,
            'id' => 1234,
            'statusType' => 1,
        ];
        try {
            self::$virtualAccountService->GetFollowers($paramsWithoutRequired);
        } catch (ValidationException $e) {

            $validation = $e->getErrorsAsArray();
            $this->assertNotEmpty($validation);

            $result = $e->getResult();

            $this->assertArrayHasKey('scApiKey', $validation);
            $this->assertEquals('The property scApiKey is required', $validation['scApiKey'][0]);

            $this->assertArrayHasKey('id', $validation);
            $this->assertEquals('The property id is required', $validation['id'][0]);

            $this->assertArrayHasKey('statusType', $validation);
            $this->assertEquals('The property statusType is required', $validation['statusType'][0]);

            $this->assertEquals(887, $result['code']);
        } catch (PodException $e) {
            $error = $e->getResult();
            $this->fail('PodException: ' . $error['message']);
        }
        try {
            self::$virtualAccountService->GetFollowers($paramsWrongValue);
        } catch (ValidationException $e) {

            $validation = $e->getErrorsAsArray();
            $this->assertNotEmpty($validation);

            $result = $e->getResult();


            $this->assertArrayHasKey('scApiKey', $validation);
            $this->assertEquals('Integer value found, but a string is required', $validation['scApiKey'][1]);

            $this->assertArrayHasKey('id', $validation);
            $this->assertEquals('Integer value found, but a string is required', $validation['id'][1]);

            $this->assertArrayHasKey('statusType', $validation);
            $this->assertEquals('Integer value found, but a string is required', $validation['statusType'][1]);
            $this->assertEquals(887, $result['code']);
        } catch (PodException $e) {
            $error = $e->getResult();
            $this->fail('PodException: ' . $error['message']);
        }
    }

    public function testGetBusinessAllParameters()
    {
        $params =
            [
                ## ======================= *Required Parameters  ==========================
                'scApiKey'             => $this->getBusinessScApiKey,
                'origins'                =>
                    [[
                        'lat' => 36.3141962,
                        'lng' => 59.5412054
                    ],
                        [
                            'lat' => 36.32203767,
                            'lng' => 59.4759665
                        ]
                    ],
                'destinations'           =>[
                    [
                        'lat' => 36.32203769,
                        'lng' => 59.4759667
                    ],
                    [
                        'lat' => 36.12111,
                        'lng' => 59.324454
                    ]
                ],
                ## ======================== Optional Parameters  ==========================
                'token'                => $this->apiToken,      # Api_Token | AccessToken
                'tokenIssuer'           => $this->tokenIssuer, # default is 1
//            'scVoucherHash'          => ["1234", "546"],
        ];

        try {
            $result = self::$virtualAccountService->getBusiness($params);
            $this->assertFalse($result['hasError']);
            
        } catch (ValidationException $e) {
            $this->fail('ValidationException: ' . $e->getErrorsAsString());
        } catch (PodException $e) {
            $error = $e->getResult();
            $this->fail('PodException: ' . $error['message']);
        }
    }

    public function testGetBusinessRequiredParameters()
    {
        $params =
            [
                ## ======================= *Required Parameters  ==========================
                'scApiKey'             => $this->getBusinessScApiKey,
                'origins'                =>
                    [[
                        'lat' => 36.3141962,
                        'lng' => 59.5412054
                    ],
                        [
                            'lat' => 36.32203767,
                            'lng' => 59.4759665
                        ]
                    ],
                'destinations'           =>[
                    [
                        'lat' => 36.32203769,
                        'lng' => 59.4759667
                    ],
                    [
                        'lat' => 36.12111,
                        'lng' => 59.324454
                    ]
                ],
            ];
        try {
            $result = self::$virtualAccountService->getBusiness($params);
            $this->assertFalse($result['hasError']);
            
        } catch (ValidationException $e) {
            $this->fail('ValidationException: ' . $e->getErrorsAsString());
        } catch (PodException $e) {
            $error = $e->getResult();
            $this->fail('PodException: ' . $error['message']);
        }
    }

    public function testGetBusinessValidationError()
    {
        $paramsWithoutRequired = [];
        $paramsWrongValue = [
            'scApiKey' => 1234,
            'origins'  => [],
            'destinations' => [],
        ];
        try {
            self::$virtualAccountService->getBusiness($paramsWithoutRequired);
        } catch (ValidationException $e) {

            $validation = $e->getErrorsAsArray();
            $this->assertNotEmpty($validation);

            $result = $e->getResult();

            $this->assertArrayHasKey('scApiKey', $validation);
            $this->assertEquals('The property scApiKey is required', $validation['scApiKey'][0]);

            $this->assertArrayHasKey('origins', $validation);
            $this->assertEquals('The property origins is required', $validation['origins'][0]);

            $this->assertArrayHasKey('destinations', $validation);
            $this->assertEquals('The property destinations is required', $validation['destinations'][0]);

            $this->assertEquals(887, $result['code']);
        } catch (PodException $e) {
            $error = $e->getResult();
            $this->fail('PodException: ' . $error['message']);
        }
        try {
            self::$virtualAccountService->getBusiness($paramsWrongValue);
        } catch (ValidationException $e) {

            $validation = $e->getErrorsAsArray();
            $this->assertNotEmpty($validation);

            $result = $e->getResult();

            $this->assertArrayHasKey('scApiKey', $validation);
            $this->assertEquals('Integer value found, but a string is required', $validation['scApiKey'][1]);

            $this->assertArrayHasKey('origins', $validation);
            $this->assertEquals('There must be a minimum of 1 items in the array', $validation['origins'][1]);

            $this->assertArrayHasKey('destinations', $validation);
            $this->assertEquals('There must be a minimum of 1 items in the array', $validation['destinations'][1]);

            $this->assertEquals(887, $result['code']);
        } catch (PodException $e) {
            $error = $e->getResult();
            $this->fail('PodException: ' . $error['message']);
        }
    }

    public function testTransferToFollowerAllParameters()
    {
        $params =
            [
                ## ======================= *Required Parameters  ==========================
                'scApiKey'             => $this->transferToFollowerScApiKey,
                'path'  => [
                    [
                        'lat'=> 36.297886,
                        'lng'=> 59.604335
                    ],
                    [
                        'lat'=> 36.297218,
                        'lng' =>  59.603496
                    ]
                ],
                ## ======================== Optional Parameters  ==========================
                'token'                => $this->apiToken,      # Api_Token | AccessToken
                'tokenIssuer'           => $this->tokenIssuer, # default is 1
//            'scVoucherHash'          => ["1234", "546"],
        ];

        try {
            $result = self::$virtualAccountService->transferToFollower($params);
            $this->assertFalse($result['hasError']);
            
        } catch (ValidationException $e) {
            $this->fail('ValidationException: ' . $e->getErrorsAsString());
        } catch (PodException $e) {
            $error = $e->getResult();
            $this->fail('PodException: ' . $error['message']);
        }
    }

    public function testTransferToFollowerRequiredParameters()
    {
        $params =
            [
                ## ======================= *Required Parameters  ==========================
                'scApiKey'             => $this->transferToFollowerScApiKey,
                'path'  => [
                    [
                        'lat'=> 36.297886,
                        'lng'=> 59.604335
                    ],
                    [
                        'lat'=> 36.297218,
                        'lng' =>  59.603496
                    ]
                ],
            ];
        try {
            $result = self::$virtualAccountService->transferToFollower($params);
            $this->assertFalse($result['hasError']);
            
        } catch (ValidationException $e) {
            $this->fail('ValidationException: ' . $e->getErrorsAsString());
        } catch (PodException $e) {
            $error = $e->getResult();
            $this->fail('PodException: ' . $error['message']);
        }
    }

    public function testTransferToFollowerValidationError()
    {
        $paramsWithoutRequired = [];
        $paramsWrongValue = [
            'scApiKey'             => 1234,
            'path'  => [
            ],
        ];
        try {
            self::$virtualAccountService->transferToFollower($paramsWithoutRequired);
        } catch (ValidationException $e) {

            $validation = $e->getErrorsAsArray();
            $this->assertNotEmpty($validation);

            $result = $e->getResult();

            $this->assertArrayHasKey('scApiKey', $validation);
            $this->assertEquals('The property scApiKey is required', $validation['scApiKey'][0]);

            $this->assertArrayHasKey('path', $validation);
            $this->assertEquals('The property path is required', $validation['path'][0]);

            $this->assertEquals(887, $result['code']);
        } catch (PodException $e) {
            $error = $e->getResult();
            $this->fail('PodException: ' . $error['message']);
        }
        try {
            self::$virtualAccountService->transferToFollower($paramsWrongValue);
        } catch (ValidationException $e) {

            $validation = $e->getErrorsAsArray();
            $this->assertNotEmpty($validation);

            $result = $e->getResult();


            $this->assertArrayHasKey('scApiKey', $validation);
            $this->assertEquals('Integer value found, but a string is required', $validation['scApiKey'][1]);

            $this->assertArrayHasKey('path', $validation);
            $this->assertEquals('There must be a minimum of 2 items in the array', $validation['path'][1]);

            $this->assertEquals(887, $result['code']);
        } catch (PodException $e) {
            $error = $e->getResult();
            $this->fail('PodException: ' . $error['message']);
        }
    }

    public function testTransferToFollowerListAllParameters()
    {
        $params =
            [
                ## ======================= *Required Parameters  ==========================
                'scApiKey'             => $this->transferToFollowerListScApiKey,
                'content' => [
                    ## ======== Required Parameters ==============
                    'receptor' => '09158107405',
                    'token1' => 'Elham',
                    'template' => 'testTemplate',
                    ## ======== Optional Parameters  =============
                    'token2' => '1213',
                    'token3' => 'test',
                    'type' => 'sms',
                ],
                ## ======================== Optional Parameters  ==========================
                'token'                => $this->apiToken,      # Api_Token | AccessToken
                'tokenIssuer'           => $this->tokenIssuer, # default is 1
//            'scVoucherHash'          => ["1234", "546"],
        ];

        try {
            $result = self::$virtualAccountService->transferToFollowerList($params);
            $this->assertFalse($result['hasError']);
            
        } catch (ValidationException $e) {
            $this->fail('ValidationException: ' . $e->getErrorsAsString());
        } catch (PodException $e) {
            $error = $e->getResult();
            $this->fail('PodException: ' . $error['message']);
        }
    }

    public function testTransferToFollowerListRequiredParameters()
    {
        $params =
            [
                ## ======================= *Required Parameters  ==========================
                'scApiKey'             => $this->scApiKey,
                'content' => [
                    ## ======== Required Parameters ==============
                    'receptor' => '09158107405',
                    'token1' => 'Elham',
                    'template' => 'testTemplate',
                ],
            ];
        try {
            $result = self::$virtualAccountService->transferToFollowerList($params);
            $this->assertFalse($result['hasError']);
            
        } catch (ValidationException $e) {
            $this->fail('ValidationException: ' . $e->getErrorsAsString());
        } catch (PodException $e) {
            $error = $e->getResult();
            $this->fail('PodException: ' . $error['message']);
        }
    }

    public function testTransferToFollowerListValidationError()
    {
        $paramsWithoutRequired = [];
        $paramsWrongValue = [
            ## ======================= *Required Parameters  ==========================
            'scApiKey'             => 1234,
            'content' => [],
        ];
        try {
            self::$virtualAccountService->transferToFollowerList($paramsWithoutRequired);
        } catch (ValidationException $e) {

            $validation = $e->getErrorsAsArray();
            $this->assertNotEmpty($validation);

            $result = $e->getResult();

            $this->assertArrayHasKey('scApiKey', $validation);
            $this->assertEquals('The property scApiKey is required', $validation['scApiKey'][0]);

            $this->assertArrayHasKey('content', $validation);
            $this->assertEquals('The property content is required', $validation['content'][0]);

            $this->assertEquals(887, $result['code']);
        } catch (PodException $e) {
            $error = $e->getResult();
            $this->fail('PodException: ' . $error['message']);
        }
        try {
            self::$virtualAccountService->transferToFollowerList($paramsWrongValue);
        } catch (ValidationException $e) {

            $validation = $e->getErrorsAsArray();
            $this->assertNotEmpty($validation);

            $result = $e->getResult();


            $this->assertArrayHasKey('scApiKey', $validation);
            $this->assertEquals('Integer value found, but a string is required', $validation['scApiKey'][1]);

            $this->assertArrayHasKey('content', $validation);
            $this->assertEquals('There must be a minimum of 3 items in the array', $validation['content'][1]);

            $this->assertEquals(887, $result['code']);
        } catch (PodException $e) {
            $error = $e->getResult();
            $this->fail('PodException: ' . $error['message']);
        }
    }

    public function testTransferByInvoiceAllParameters()
    {
        $params =
            [
                ## ======================= *Required Parameters  ==========================
                'scApiKey'             => $this->scApiKey,
                'content' => [
                    ## ======== Required Parameters ==============
                    'appId' => 'POD-Chat',
                    ## ======== Optional Parameters  =============
                    'title' => 'test push virtualAccount',
                    'text' => 'test',
                    'scheduler' => '1500/06/03 12:59',
                ],
                ## ======================== Optional Parameters  ==========================
                'token'                => $this->apiToken,      # Api_Token | AccessToken
               'scVoucherHash'          => ["1234", "546"],
        ];

        try {
            $result = self::$virtualAccountService->listTransferByInvoice($params);
            $this->assertFalse($result['hasError']);
            
        } catch (ValidationException $e) {
            $this->fail('ValidationException: ' . $e->getErrorsAsString());
        } catch (PodException $e) {
            $error = $e->getResult();
            $this->fail('PodException: ' . $error['message']);
        }
    }

    public function testTransferByInvoiceRequiredParameters()
    {
        $params =
            [
                ## ======================= *Required Parameters  ==========================
                'scApiKey'             => $this->scApiKey,
                'content' => [
                    ## ======== Required Parameters ==============
                    'appId' => 'POD-Chat',
                ],
            ];
        try {
            $result = self::$virtualAccountService->transferByInvoice($params);
            $this->assertFalse($result['hasError']);
            
        } catch (ValidationException $e) {
            $this->fail('ValidationException: ' . $e->getErrorsAsString());
        } catch (PodException $e) {
            $error = $e->getResult();
            $this->fail('PodException: ' . $error['message']);
        }
    }

    public function testTransferByInvoiceValidationError()
    {
        $paramsWithoutRequired = [];
        $paramsWrongValue = [
            'scApiKey'             => 1234,
            'content'  => [],
        ];
        try {
            self::$virtualAccountService->transferByInvoice($paramsWithoutRequired);
        } catch (ValidationException $e) {

            $validation = $e->getErrorsAsArray();
            $this->assertNotEmpty($validation);

            $result = $e->getResult();

            $this->assertArrayHasKey('scApiKey', $validation);
            $this->assertEquals('The property scApiKey is required', $validation['scApiKey'][0]);

            $this->assertArrayHasKey('content', $validation);
            $this->assertEquals('The property content is required', $validation['content'][0]);

            $this->assertEquals(887, $result['code']);
        } catch (PodException $e) {
            $error = $e->getResult();
            $this->fail('PodException: ' . $error['message']);
        }
        try {
            self::$virtualAccountService->transferByInvoice($paramsWrongValue);
        } catch (ValidationException $e) {

            $validation = $e->getErrorsAsArray();
            $this->assertNotEmpty($validation);

            $result = $e->getResult();

            $this->assertArrayHasKey('scApiKey', $validation);
            $this->assertEquals('Integer value found, but a string is required', $validation['scApiKey'][1]);

            $this->assertArrayHasKey('content', $validation);
            $this->assertEquals('There must be a minimum of 1 items in the array', $validation['content'][1]);

            $this->assertEquals(887, $result['code']);
        } catch (PodException $e) {
            $error = $e->getResult();
            $this->fail('PodException: ' . $error['message']);
        }
    }

    public function testListTransferByInvoiceAllParameters()
    {
        $params =
            [
                ## ======================= *Required Parameters  ==========================
                'scApiKey'             => $this->scApiKey,
                'content' => [
                    ## ======== Required Parameters ==============
                    'appId' => 'POD-Chat',
                    ## ======== Optional Parameters  =============
                    'title' => 'test push virtualAccount',
                    'text' => 'test',
                    'scheduler' => '1398/06/03 12:59',
                ],
                ## ======================== Optional Parameters  ==========================
                'token'                => $this->apiToken,      # Api_Token | AccessToken
                'scVoucherHash'          => ["1234", "546"],
        ];

        try {
            $result = self::$virtualAccountService->listTransferByInvoice($params);
            $this->assertFalse($result['hasError']);
            
        } catch (ValidationException $e) {
            $this->fail('ValidationException: ' . $e->getErrorsAsString());
        } catch (PodException $e) {
            $error = $e->getResult();
            $this->fail('PodException: ' . $error['message']);
        }
    }

    public function testListTransferByInvoiceRequiredParameters()
    {
        $params =
            [
                ## ======================= *Required Parameters  ==========================
                'scApiKey'             => $this->scApiKey,
                'content' => [
                    ## ======== Required Parameters ==============
                    'appId' => 'POD-Chat',
                ],
            ];
        try {
            $result = self::$virtualAccountService->listTransferByInvoice($params);
            $this->assertFalse($result['hasError']);
            
        } catch (ValidationException $e) {
            $this->fail('ValidationException: ' . $e->getErrorsAsString());
        } catch (PodException $e) {
            $error = $e->getResult();
            $this->fail('PodException: ' . $error['message']);
        }
    }

    public function testListTransferByInvoiceValidationError()
    {
        $paramsWithoutRequired = [];
        $paramsWrongValue = [
            'scApiKey'             => 1234,
            'content'  => [],
        ];
        try {
            self::$virtualAccountService->listTransferByInvoice($paramsWithoutRequired);
        } catch (ValidationException $e) {

            $validation = $e->getErrorsAsArray();
            $this->assertNotEmpty($validation);

            $result = $e->getResult();

            $this->assertArrayHasKey('scApiKey', $validation);
            $this->assertEquals('The property scApiKey is required', $validation['scApiKey'][0]);

            $this->assertArrayHasKey('content', $validation);
            $this->assertEquals('The property content is required', $validation['content'][0]);

            $this->assertEquals(887, $result['code']);
        } catch (PodException $e) {
            $error = $e->getResult();
            $this->fail('PodException: ' . $error['message']);
        }
        try {
            self::$virtualAccountService->listTransferByInvoice($paramsWrongValue);
        } catch (ValidationException $e) {

            $validation = $e->getErrorsAsArray();
            $this->assertNotEmpty($validation);

            $result = $e->getResult();

            $this->assertArrayHasKey('scApiKey', $validation);
            $this->assertEquals('Integer value found, but a string is required', $validation['scApiKey'][1]);

            $this->assertArrayHasKey('content', $validation);
            $this->assertEquals('There must be a minimum of 1 items in the array', $validation['content'][1]);

            $this->assertEquals(887, $result['code']);
        } catch (PodException $e) {
            $error = $e->getResult();
            $this->fail('PodException: ' . $error['message']);
        }
    }
}