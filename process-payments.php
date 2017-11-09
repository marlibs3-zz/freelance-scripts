<?php
// WePay PHP SDK - http://git.io/mY7iQQ
require 'PHP-SDK/wepay.php';

$wePayAccountID        = 1871946093;
$wePayAccessToken      = "PRODUCTION_12d9167f273a60d9c2a4210acf467116f604474f8654fc1006d8cbc29402a6a7";
$wePayClientID         = 123456789;
$wePayClientSecret     = "Shhhhhh";
$amountPayable         = 1;
$currency              = "GBP";
$description           = "A lovely service";

// First we create the variable cardsString and we put the data from the csv
// file in it
$cardsString = file_get_contents('./customer-cards.csv');
// Then we create a variable in which the cardsString is split by new lines
$cardsArray = explode("\n", $cardsString);
// We loop through the array
foreach ($cardsArray as $card) {
  // We split each card into its pieces of information
  $item = explode("|", $card);
  // We create am array from the items above
  $cardDataArray = [
    "client_id" => $wePayClientID,
    "user_name" => $item[6]." ".$item[7],
    "email" => $item[8],
    "cc_number" => $item[0],
    "cvv" => $item[3],
    "expiration_month" => $item[1],
    "expiration_year" => $item[2],
    "address" => [
      "country" => $item[12],
      "postal_code" => $item[11]
    ]
  ];
  // We encode the cardDataArray as a JSON string
  $cardDataArrayJSON = json_encode($cardDataArray);
  // We make a post request to WePay API using curl
  $curlSession = curl_init("https://wepayapi.com/v2/credit_card/create");
  curl_setopt($curlSession, CURLOPT_CUSTOMREQUEST, "POST");
  curl_setopt($curlSession, CURLOPT_POSTFIELDS, $cardDataArrayJSON);
  curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curlSession, CURLOPT_HTTPHEADER,
    array(
      'Content-Type: application/json',
      'Content-Length: ' . strlen($cardDataArrayJSON)
    )
  );
  // We execute the curl request
  $creditCardCreateResponseJSON = curl_exec($curlSession);
  // For testing purposes we hardcode a success response from WePay
  $creditCardCreateResponseJSON = '{"credit_card_id": 235810395803, "state": "new"}';
  // We convert the JSON string into a PHP array
  $creditCardCreateResponseArray = json_decode($creditCardCreateResponseJSON, true);
  // This configures WePay to make a real (not test) API call
  Wepay::useProduction($wePayClientID, $wePayClientSecret);
  // We obtain a WePay object instance from the WePay class, passing the
  // access token to the constructor
  $wepay = new WePay($wePayAccessToken);
  // We make an API request to charge the card
  $response = $wepay->request('checkout/create', array(
    'account_id'          => $wePayAccountID,
    'amount'              => $amountPayable,
    'currency'            => $currency,
    'short_description'   => $description,
    'type'                => 'service',
    'payment_method'      => array(
      'type'            => 'credit_card',
      'credit_card'     => array(
        'id'          => $creditCardCreateResponseArray['credit_card_id']
      )
    )
  ));

  print_r($response);
}
