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

use net\authorize\api\contract\v1\GetTransactionDetailsRequest;
use net\authorize\api\controller\GetTransactionDetailsController;


/**
 * Class AuthorizeTransactions
 * @package App\PaymentDrivers\Authorize
 *
 */
class AuthorizeTransactions
{

    public $authorize;

    public function __construct(AuthorizePaymentDriver $authorize)
    {
        $this->authorize = $authorize;
    }

	function getTransactionDetails($transactionId)
	{
	    /* Create a merchantAuthenticationType object with authentication details
	       retrieved from the constants file */
	    $this->authorize->init();
	    
	    // Set the transaction's refId
	    $refId = 'ref' . time();

	    $request = new GetTransactionDetailsRequest();
	    $request->setMerchantAuthentication($this->authorize->merchant_authentication);
	    $request->setTransId($transactionId);

	    $controller = new GetTransactionDetailsController($request);

	    $response = $controller->executeWithApiResponse($this->authorize->mode());

	    if (($response != null) && ($response->getMessages()->getResultCode() == "Ok"))
	    {
	        info( "SUCCESS: Transaction Status:" . $response->getTransaction()->getTransactionStatus() );
	        info( "                Auth Amount:" . $response->getTransaction()->getAuthAmount() );
	        info( "                   Trans ID:" . $response->getTransaction()->getTransId() );
	     }
	    else
	    {
	        info( "ERROR :  Invalid response\n");
	        $errorMessages = $response->getMessages()->getMessage();
	        info( "Response : " . $errorMessages[0]->getCode() . "  " .$errorMessages[0]->getText() );
	    }

	    return $response;
  	}
}