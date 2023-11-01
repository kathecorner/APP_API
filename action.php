<?php
$url = "https://terminal-api-test.adyen.com/sync";

$payments_data = $_POST;


$fpt = fopen('count.txt','r+');
$count = fgets($fpt, 11 );
$count++;
rewind( $fpt );
fputs( $fpt, $count );
fclose( $fpt );

//echo $payments_data;

$additional_data = [    
    "SaleToPOIRequest" => [
        "MessageHeader" => [
           "ProtocolVersion" => "3.0", 
           "MessageClass" => "Service", 
           "MessageCategory" => "Payment", 
           "MessageType" => "Request", 
           "SaleID" => "KenjiWTerminals", 
           "ServiceID" => strval($count), //countup,10digit

           //"ServiceID" => "0425202323", //countup,10digit
           "POIID" => "V400m-346777636" 
        ], 
        "PaymentRequest" => [
              "SaleData" => [
                 "SaleTransactionID" => [
                    "TransactionID" => 'IPP_'.date("Y/m/d H:i:s"),
                    //"TimeStamp" => $_POST['SRN']
                    "TimeStamp" => "2023-11-01T13:48:17.455Z"
                 ], 
                 "SaleToAcquirerData" => "recurringProcessingModel=UnscheduledCardOnFile&shopperReference=".$_POST['REF']."&shopperEmail=S.Hopper@example.com", 
                 "TokenRequestedType" => "Customer" 
              ], 
              "PaymentTransaction" => [
                       "AmountsReq" => [
                          "Currency" => "JPY", 
                          "RequestedAmount" => 3000 
                       ] 
                    ] 
           ] 
     ]
];

unset($payments_data['REF']);
unset($payments_data['SRN']);
unset($payments_data['send']);

$final_payment_data = array_merge($payments_data, $additional_data);

$curl_http_header = array(
    "X-API-Key: AQEyhmfxL4PJahZCw0m/n3Q5qf3VaY9UCJ1+XWZe9W27jmlZiv4PD4jhfNMofnLr2K5i8/0QwV1bDb7kfNy1WIxIIkxgBw==-lUKXT9IQ5GZ6d6RH4nnuOG4Bu//eJZxvoAOknIIddv4=-<anpTLkW{]ZgGy,7",
    "Content-Type: application/json"
);

$curl = curl_init();

curl_setopt_array(
    $curl,
    [
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST  => 'POST',
        CURLOPT_POSTFIELDS     => json_encode($final_payment_data),
        CURLOPT_HTTPHEADER     => $curl_http_header,
        CURLOPT_VERBOSE        => true
    ]
);

$payments_response = curl_exec($curl);
$file = 'PBL_CallResponse.txt';
$current = $payments_response;
file_put_contents($file, $current);

header('Content-Type: application/json');
//echo $payments_response;

$arr = json_decode($payments_response, true);

echo $payments_response;

$addRes = $arr['SaleToPOIResponse']['PaymentResponse']['Response']['AdditionalResponse'];



//preg_match('/recurring.recurringDetailReference=\w+/', $addRes, $match);
//echo($match);


curl_close($curl);
?>
