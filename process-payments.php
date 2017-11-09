<?php
/*
CSV Format:
|00000000000000000|02|19|CVV2: 402|City: NY | State: NY| First Name: eyal|
Last Name: azulay|PayPal email: mail@yahoo.com| phone: 00112312312323|
address:BL bl |zip: 4643402|USA

Spec:
Customers' credit card details are recorded (offline), by entering card
details into a spreadsheet.
These customers should be charged a monthly fee, but this is currently a
manual process and is quite laborious.
The Client would like a PHP script which will take in a specified CSV file
(containing one line per customer / credit card detail), and charge each
customer the monthly fee using the WePay API.
*/

  // First we create the variable cardsString and we put the data from the csv
  // file in it
  $cardsString = file_get_contents('./customer-cards.csv');
  // Then we create a variable in which the cardsString is split by new lines
  $cardsArray = explode("\n", $cardsString);
  // We loop through the array
  foreach ($cardsArray as $card) {
    // We output each item in a new line
    // We split each card into its pieces of information
    $item = explode("|", $card);
    echo "$item[6] \n";
  }
