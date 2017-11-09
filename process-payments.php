<?php
  // First we create the variable cardsString and we put the data from the csv
  // file in it
  $cardsString = file_get_contents('./customer-cards.csv');
  // Then we create a variable in which the cardsString is split by new lines
  $cardsArray = explode("\n", $cardsString);
  //
  foreach ($cardsArray as $card) {
    echo "$card \n";
  }
