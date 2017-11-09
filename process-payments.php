<?php
  // WePay PHP SDK - http://git.io/mY7iQQ
  // require 'wepay.php';

  $wePayID    = 123456789;
  $wePayToken  = "STAGE_8a19aff55b85a436dad5cd1386db1999437facb5914b494f4da5f206a56a5d20";

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
      "client_id" => $wePayID,
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
    $cardDataArrayJSON = json_encode($cardDataArray, JSON_PRETTY_PRINT);
    // We make a post request to WePay API using curl
    $curlSession = curl_init("https://wepayapi.com/v2/credit_card/create");
    curl_setopt($curlSession, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($curlSession, CURLOPT_POSTFIELDS, $cardDataArrayJSON);
    curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curlSession, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($cardDataArrayJSON)
      )
    );
    $result = curl_exec($curlSession);
    echo "$result \n";
  }
  
