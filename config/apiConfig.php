<?php
return [
    #1
    'getBuyCreditLink' => [
        'baseUri' =>  'PRIVATE-CALL-ADDRESS',
        'subUri' =>  'v1/pbc/BuyCredit',
        'method' =>  'GET'
    ],
    #2
    'getBuyCreditByAmountLink' => [
        'baseUri' => 'PLATFORM-ADDRESS',
        'subUri' =>  'v1/pbc/BuyCredit',
        'method' => 'GET'
    ],
    #3
    'issueCreditInvoiceAndGetHash' => [
        'baseUri' => 'PLATFORM-ADDRESS',
        'subUri'  => 'nzh/doServiceCall',
        'method' => 'POST'
    ],
    #4
    'getIssueCreditInvoiceLink' => [
        'baseUri' => 'PLATFORM-ADDRESS',
        'subUri'  => 'nzh/payAnyCreditInvoice',
    ],
    #5
    'verifyCreditInvoice' => [
        'baseUri' => 'PLATFORM-ADDRESS',
        'subUri'  => 'nzh/doServiceCall',
        'method'  => 'POST'
    ],
    #6
    'transferToContact' => [
        'baseUri' => 'PLATFORM-ADDRESS',
        'subUri'  => 'nzh/doServiceCall',
        'method' => 'POST'
    ],
    #7
    'transferToContactList' => [
        'baseUri' => 'PLATFORM-ADDRESS',
        'subUri'  => 'nzh/doServiceCall',
        'method' => 'POST'
    ],
    #8
    'transferFromOwnAccounts' => [
        'baseUri' => 'PLATFORM-ADDRESS',
        'subUri'  => 'nzh/doServiceCall',
        'method' => 'GET'
    ],
    #9
    'transferFromOwnAccountsList' => [
        'baseUri' => 'PLATFORM-ADDRESS',
        'subUri'  => 'nzh/doServiceCall',
        'method' => 'GET'
    ],
    #10
    'follow' => [
        'baseUri' => 'PLATFORM-ADDRESS',
        'subUri'  => 'nzh/doServiceCall',
        'method' => 'POST'
    ],
    #11
    'getFollowers' => [
        'baseUri' => 'PLATFORM-ADDRESS',
        'subUri'  => 'nzh/doServiceCall',
        'method' => 'POST'
    ],
    #12
    'getBusiness' => [
        'baseUri' => 'PLATFORM-ADDRESS',
        'subUri'  => 'nzh/doServiceCall',
        'method' => 'POST'
    ],
    #13
    'transferToFollower' => [
        'baseUri' => 'PLATFORM-ADDRESS',
        'subUri'  => 'nzh/doServiceCall',
        'method' => 'POST'
    ],
    #14
    'transferToFollowerAndCashout' => [
        'baseUri' => 'PLATFORM-ADDRESS',
        'subUri'  => 'nzh/doServiceCall',
        'method' => 'POST'
    ],
    #15
    'transferToFollowerList' => [
        'baseUri' => 'PLATFORM-ADDRESS',
        'subUri'  => 'nzh/doServiceCall',
        'method' => 'POST'
    ],
    #16
    'transferByInvoice' => [
        'baseUri' => 'PLATFORM-ADDRESS',
        'subUri'  => 'nzh/doServiceCall',
        'method' => 'POST'
    ],
    #17
    'listTransferByInvoice' => [
        'baseUri' => 'PLATFORM-ADDRESS',
        'subUri'  => 'nzh/doServiceCall',
        'method' => 'GET'
    ],
    #18
    'getWalletAccountBill' => [
        'baseUri' => 'PLATFORM-ADDRESS',
        'subUri'  => 'nzh/doServiceCall',
        'method' => 'GET'
    ],
    #19
    'getGuildAccountBill' => [
        'baseUri' => 'PLATFORM-ADDRESS',
        'subUri'  => 'nzh/doServiceCall',
        'method' => 'GET'
    ],
    #20
    'getAccountBillAsFile' => [
        'baseUri' => 'PLATFORM-ADDRESS',
        'subUri'  => 'nzh/doServiceCall',
        'method' => 'GET'
    ],
    #21
    'cardToCardList' => [
        'baseUri' => 'PLATFORM-ADDRESS',
        'subUri'  => 'nzh/doServiceCall',
        'method' => 'GET'
    ],
    #22
    'updateCardToCard' => [
        'baseUri' => 'PLATFORM-ADDRESS',
        'subUri'  => 'nzh/doServiceCall',
        'method' => 'POST'
    ],
    #23
    'addWithdrawRulePlan' => [
        'baseUri' => 'PLATFORM-ADDRESS',
        'subUri'  => 'nzh/doServiceCall',
        'method' => 'POST'
    ],
    #24
    'withdrawRulePlanList' => [
        'baseUri' => 'PLATFORM-ADDRESS',
        'subUri'  => 'nzh/doServiceCall',
        'method' => 'POST'
    ],
    #25
    'getLinkIssueWithdrawRuleByPlan' => [
        'baseUri' => 'PRIVATE-CALL-ADDRESS',
        'subUri'  => 'v1/pbc/issueWithdrawRuleByPlan',
        'method' => 'POST'
    ],
    #26
    'getLinkIssueWithdrawRule' => [
        'baseUri' => 'PRIVATE-CALL-ADDRESS',
        'subUri'  => 'v1/pbc/issueWithdrawRule',
        'method' => 'POST'
    ],
    #27
    'grantedWithdrawRuleList' => [
        'baseUri' => 'PLATFORM-ADDRESS',
        'subUri'  => 'nzh/doServiceCall',
        'method' => 'POST'
    ],
    #28
    'revokeWithdrawRule' => [
        'baseUri' => 'PLATFORM-ADDRESS',
        'subUri'  => 'nzh/doServiceCall',
        'method' => 'POST'
    ],
    #29
    'withdrawRuleUsageReport' => [
        'baseUri' => 'PLATFORM-ADDRESS',
        'subUri'  => 'nzh/doServiceCall',
        'method' => 'POST'
    ],
    #30
    'PayInvoiceByCredit' => [
        'baseUri' => 'PLATFORM-ADDRESS',
        'subUri'  => 'nzh/doServiceCall',
        'method' => 'POST'
    ],
];