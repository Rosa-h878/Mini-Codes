<?php

//customize a text for a SQL Statement

$text = "PERSON_ID
PERSON_NO
SALUTATION
TITLE
FIRSTNAME
LASTNAME
BIRTHNAME
FORMER_NAME
DATE_OF_BIRTH
PLACE_OF_BIRTH
STREET
ZIPCODE
CITY
NATIONALITY
PHONE
EMAIL
STATUS
JOB_TENURE
IS_PRIVACY_INSTRUCTION
IS_STATE_ACCEPTANCE
IS_CERTIFICATE_OF_CONDUCT
CERTIFICATE_OF_CONDUCT_DATE
IS_MEASLES_PROTECTION" ;

$wordsArray = explode("\n", $text);
$modifiedArray = array_map(function($word){
    return '"' . trim($word) . '"';
}, $wordsArray);
$goodText = implode(", ", $modifiedArray);

echo $goodText;
