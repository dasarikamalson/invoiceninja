<?php

/**
 * Invoice Ninja (https://invoiceninja.com)
 *
 * @link https://github.com/invoiceninja/invoiceninja source repository
 *
 * @copyright Copyright (c) 2020. Invoice Ninja LLC (https://invoiceninja.com)
 *
 * @license https://opensource.org/licenses/AAL
 */

namespace App\PaymentDrivers\Authorize;

use App\Models\Payment;
use App\PaymentDrivers\AuthorizePaymentDriver;
use App\PaymentDrivers\Authorize\AuthorizeTransactions;
use net\authorize\api\contract\v1\CreateTransactionRequest;
use net\authorize\api\contract\v1\CustomerProfilePaymentType;
use net\authorize\api\contract\v1\PaymentProfileType;
use net\authorize\api\contract\v1\TransactionRequestType;
use net\authorize\api\controller\CreateTransactionController;

/**
 * Class RefundTransaction
 * @package App\PaymentDrivers\Authorize
 *
 */
class RefundTransaction
{
	public $authorize;

	public $authorize_transaction;

    public function __construct(AuthorizePaymentDriver $authorize, AuthorizeTransactions $authorize_transaction)
    {
        $this->authorize = $authorize;
        $this->authorize_transaction = new AuthorizeTransactions($this->authorize);
    }

    function refundTransaction(Payment $payment, $amount)
	{

		$transaction_details = $this->authorize_transaction->getTransactionDetails($payment->transaction_reference);

		info(print_r($transaction_details,1));

	   	$this->authorize->init();
	    
	    // Set the transaction's refId
	    $refId = 'ref' . time();

	    $paymentProfile = new PaymentProfileType();
	    $paymentProfile->setPaymentProfileId( $payment_profile_id );

	    // set customer profile
	    $customerProfile = new CustomerProfilePaymentType();
	    $customerProfile->setCustomerProfileId( $profile_id );
	    $customerProfile->setPaymentProfile( $paymentProfile );

	    //create a transaction
	    $transactionRequest = new TransactionRequestType();
	    $transactionRequest->setTransactionType("refundTransaction"); 
	    $transactionRequest->setAmount($amount);
    	$transactionRequest->setProfile($customerProfile);
	    $transactionRequest->setRefTransId($payment->transaction_reference);

	    $request = new CreateTransactionRequest();
	    $request->setMerchantAuthentication($this->authorize->merchant_authentication);
	    $request->setRefId($refId);
	    $request->setTransactionRequest($transactionRequest);
	    $controller = new CreateTransactionController($request);
	    $response = $controller->executeWithApiResponse($this->authorize->mode());

	    if ($response != null)
	    {
	      if($response->getMessages()->getResultCode() == "Ok")
	      {
	        $tresponse = $response->getTransactionResponse();
	        
		    if ($tresponse != null && $tresponse->getMessages() != null)   
	        {

	        	return [
	        		'transaction_reference' => $tresponse->getTransId(),
	        		'success' => true,
	        		'description' => $tresponse->getMessages()[0]->getDescription(),
	        		'code' => $tresponse->getMessages()[0]->getCode(),
	        		'transaction_response' => $tresponse->getResponseCode()
	        	];

	        }
	        else
	        {

	          if($tresponse->getErrors() != null)
	          {

	        	return [
	        		'transaction_reference' => '',
	        		'transaction_response' => '',
	        		'success' => false,
	        		'description' => $tresponse->getErrors()[0]->getErrorText(),
	        		'code' => $tresponse->getErrors()[0]->getErrorCode(),
	        	];

	          }
	        }
	      }
	      else
	      {
	        echo "Transaction Failed \n";
	        $tresponse = $response->getTransactionResponse();
	        if($tresponse != null && $tresponse->getErrors() != null)
	        {

	        	return [
	        		'transaction_reference' => '',
	        		'transaction_response' => '',
	        		'success' => false,
	        		'description' => $tresponse->getErrors()[0]->getErrorText(),
	        		'code' => $tresponse->getErrors()[0]->getErrorCode(),
	        	];

	        }
	        else
	        {

	        	return [
	        		'transaction_reference' => '',
	        		'transaction_response' => '',
	        		'success' => false,
	        		'description' => $response->getMessages()->getMessage()[0]->getText(),
	        		'code' => $response->getMessages()->getMessage()[0]->getCode(),
	        	];

	        }
	      }      
	    }
	    else
	    {

	    	return [
	        		'transaction_reference' => '',
	        		'transaction_response' => '',
	        		'success' => false,
	        		'description' => 'No response returned',
	        		'code' => 'No response returned',
	        	];

	    }

	    	return [
	        		'transaction_reference' => '',
	        		'transaction_response' => '',
	        		'success' => false,
	        		'description' => 'No response returned',
	        		'code' => 'No response returned',
	        	];
	  }


}