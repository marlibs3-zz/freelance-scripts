<?php
$customerCardsFile     = './customer-cards.csv';

$wePayAccountID        = 123456789;
$wePayAccessToken      = "wePayAccessToken";
$wePayClientID         = 123456789;
$wePayClientSecret     = "wePayClientSecret";

$amountPayable         = 1;
$currency              = "GBP";
$description           = "Service Description";

echo "Processing customer cards from file: {$customerCardsFile} to charge amount: $amountPayable $currency \n";

// First we create the variable cardsString and we put the data from the csv
// file in it
$cardsString = file_get_contents($customerCardsFile);
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
  
  // We convert the JSON string into a PHP array
  $creditCardCreateResponseArray = json_decode($creditCardCreateResponseJSON, true);

  $transactionRequestData = array(
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
  );

  // We encode the transactionRequestData as a JSON string
  $transactionRequestDataJSON = json_encode($transactionRequestData);
  // We make a post request to WePay API using curl
  $curlSession = curl_init("https://wepayapi.com/v2/checkout/create");
  curl_setopt($curlSession, CURLOPT_CUSTOMREQUEST, "POST");
  curl_setopt($curlSession, CURLOPT_POSTFIELDS, $transactionRequestDataJSON);
  curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curlSession, CURLOPT_HTTPHEADER,
    array(
      'Content-Type: application/json',
      'Content-Length: ' . strlen($transactionRequestDataJSON)
    )
  );
  // We execute the curl request
  $checkoutCreateResponseJSON = curl_exec($curlSession);
  // We convert the JSON string into a PHP array
  $checkoutCreateResponseArray = json_decode($checkoutCreateResponseJSON, true);

  echo "WePay Checkout ID: {$checkoutCreateResponseArray['checkout_id']} / State: {$checkoutCreateResponseArray['state']} \n";
}
