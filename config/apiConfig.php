<?php
return [
    'getBuyCreditLink' => [
        'baseUri' =>  'PRIVATE-CALL-ADDRESS',
        'subUri' =>  'v1/pbc/BuyCredit',
        'method' =>  'GET'
    ],

    'getBuyCreditByAmountLink' => [
        'baseUri' => 'PLATFORM-ADDRESS',
        'subUri' =>  'v1/pbc/BuyCredit',
        'method' => 'GET'
    ],

    'issueCreditInvoiceAndGetHash' => [
        'baseUri' => 'PLATFORM-ADDRESS',
        'subUri'  => 'nzh/doServiceCall',
        'method' => 'POST'
    ],

    'getIssueCreditInvoiceLink' => [
        'baseUri' => 'PLATFORM-ADDRESS',
        'subUri'  => 'nzh/payAnyCreditInvoice',
    ],

    'verifyCreditInvoice' => [
        'baseUri' => 'PLATFORM-ADDRESS',
        'subUri'  => 'nzh/doServiceCall',
        'method'  => 'POST'
    ],

    'transferToContact' => [
        'baseUri' => 'PLATFORM-ADDRESS',
        'subUri'  => 'nzh/doServiceCall',
        'method' => 'POST'
    ],

    'transferToContactList' => [
        'baseUri' => 'PLATFORM-ADDRESS',
        'subUri'  => 'nzh/doServiceCall',
        'method' => 'POST'
    ],

    'transferFromOwnAccounts' => [
        'baseUri' => 'PLATFORM-ADDRESS',
        'subUri'  => 'nzh/doServiceCall',
        'method' => 'GET'
    ],

    'transferFromOwnAccountsList' => [
        'baseUri' => 'PLATFORM-ADDRESS',
        'subUri'  => 'nzh/doServiceCall',
        'method' => 'GET'
    ],

    'follow' => [
        'baseUri' => 'PLATFORM-ADDRESS',
        'subUri'  => 'nzh/doServiceCall',
        'method' => 'POST'
    ],

    'getFollowers' => [
        'baseUri' => 'PLATFORM-ADDRESS',
        'subUri'  => 'nzh/doServiceCall',
        'method' => 'POST'
    ],

    'getBusiness' => [
        'baseUri' => 'PLATFORM-ADDRESS',
        'subUri'  => 'nzh/doServiceCall',
        'method' => 'POST'
    ],

    'transferToFollower' => [
        'baseUri' => 'PLATFORM-ADDRESS',
        'subUri'  => 'nzh/doServiceCall',
        'method' => 'POST'
    ],

    'transferToFollowerAndCashout' => [
        'baseUri' => 'PLATFORM-ADDRESS',
        'subUri'  => 'nzh/doServiceCall',
        'method' => 'POST'
    ],

    'transferToFollowerList' => [
        'baseUri' => 'PLATFORM-ADDRESS',
        'subUri'  => 'nzh/doServiceCall',
        'method' => 'POST'
    ],

    'transferByInvoice' => [
        'baseUri' => 'PLATFORM-ADDRESS',
        'subUri'  => 'nzh/doServiceCall',
        'method' => 'POST'
    ],

    'listTransferByInvoice' => [
        'baseUri' => 'PLATFORM-ADDRESS',
        'subUri'  => 'nzh/doServiceCall',
        'method' => 'GET'
    ],

    'getWalletAccountBill' => [
        'baseUri' => 'PLATFORM-ADDRESS',
        'subUri'  => 'nzh/doServiceCall',
        'method' => 'GET'
    ],

    'getGuildAccountBill' => [
        'baseUri' => 'PLATFORM-ADDRESS',
        'subUri'  => 'nzh/doServiceCall',
        'method' => 'GET'
    ],

    'getAccountBillAsFile' => [
        'baseUri' => 'PLATFORM-ADDRESS',
        'subUri'  => 'nzh/doServiceCall',
        'method' => 'GET'
    ],

    'cardToCardList' => [
        'baseUri' => 'PLATFORM-ADDRESS',
        'subUri'  => 'nzh/doServiceCall',
        'method' => 'GET'
    ],

    'updateCardToCard' => [
        'baseUri' => 'PLATFORM-ADDRESS',
        'subUri'  => 'nzh/doServiceCall',
        'method' => 'POST'
    ],

];