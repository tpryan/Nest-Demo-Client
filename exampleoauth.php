<?php


///////////////////////////////////\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
// Google API OAuth Authorization using the OAuthSimple library
//
// Author: Guido Schlabitz
// Email: guido.schlabitz@gmail.com
//
// This example uses the OAuthSimple library for PHP
// found here:  http://unitedHeroes.net/OAuthSimple
//
// For more information about the OAuth process for web applications
// accessing Google APIs, read this guide:
// http://code.google.com/apis/accounts/docs/OAuth_ref.html
//
//////////////////////////////////////////////////////////////////////
require 'lib/OAuth/OAuthSimple.php';
$oauthObject = new OAuthSimple();

require 'config/settings.php';
require 'lib/Purl/Purl.php';





// As this is an example, I am not doing any error checking to keep 
// things simple.  Initialize the output in case we get stuck in
// the first step.
$output = 'Authorizing...';

// Fill in your API key/consumer key you received when you registered your 
// application with Google.
// get from settings.php

// In step 3, a verifier will be submitted.  If it's not there, we must be
// just starting out. Let's do step 1 then.
if (!isset($_GET['oauth_token'])) {
    ///////////////////////////////////\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
    // Step 1: Get a Request Token
    //
    // Get a temporary request token to facilitate the user authorization 
    // in step 2. We make a request to the OAuthGetRequestToken endpoint,
    // submitting the scope of the access we need (in this case, all the 
    // user's calendars) and also tell Google where to go once the token
    // authorization on their side is finished.
    //
    // get from settings.php

    $result = $oauthObject->sign(array(
        'path'      =>$access_token_url,
        'parameters'=> array(
            'oauth_callback'=> $oauth_callback),
        'signatures'=> $signatures));

    // The above object generates a simple URL that includes a signature, the 
    // needed parameters, and the web page that will handle our request.  I now
    // "load" that web page into a string variable.
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $result['signed_url']);
    $r = curl_exec($ch);
    curl_close($ch);
    syslog(LOG_DEBUG, "Getting a request token\n");

    // We parse the string for the request token and the matching token
    // secret. Again, I'm not handling any errors and just plough ahead 
    // assuming everything is hunky dory.
    parse_str($r, $returned_items);
    $request_token = $returned_items['oauth_token'];
    $request_token_secret = $returned_items['oauth_token_secret'];

    // We will need the request token and secret after the authorization.
    // Google will forward the request token, but not the secret.
    // Set a cookie, so the secret will be available once we return to this page.
    setcookie("oauth_token_secret", $request_token_secret, time()+3600);
    //
    //////////////////////////////////////////////////////////////////////
    
    ///////////////////////////////////\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
    // Step 2: Authorize the Request Token
    //
    // Generate a URL for an authorization request, then redirect to that URL
    // so the user can authorize our access request.  The user could also deny
    // the request, so don't forget to add something to handle that case.
    $result = $oauthObject->sign(array(
        'path'      =>$authorize_url,
        'parameters'=> array(
            'oauth_token' => $request_token),
        'signatures'=> $signatures));

    // See you in a sec in step 3.
    syslog(LOG_DEBUG, "Sending off for a authorized token\n");
    header("Location:$result[signed_url]");
    exit;
    //////////////////////////////////////////////////////////////////////
}
else {
    ///////////////////////////////////\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
    // Step 3: Exchange the Authorized Request Token for a Long-Term
    //         Access Token.
    //
    // We just returned from the user authorization process on Google's site.
    // The token returned is the same request token we got in step 1.  To 
    // sign this exchange request, we also need the request token secret that
    // we baked into a cookie earlier. 
    //

    // Fetch the cookie and amend our signature array with the request
    // token and secret.
    $signatures['oauth_secret'] = $_COOKIE['oauth_token_secret'];
    $signatures['oauth_token'] = $_GET['oauth_token'];
    
    // Build the request-URL...
    $result = $oauthObject->sign(array(
        'path'      => $authorize_url,
        'parameters'=> array(
            'oauth_verifier' => $_GET['oauth_verifier'],
            'oauth_token'    => $_GET['oauth_token']),
        'signatures'=> $signatures));

    // ... and grab the resulting string again. 
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $result['signed_url']);
    $r = curl_exec($ch);
    syslog(LOG_DEBUG, "Sending off for a access token\n");

    // Voila, we've got a long-term access token.
    parse_str($r, $returned_items);        
    $access_token = $returned_items['oauth_token'];
    $access_token_secret = $returned_items['oauth_token_secret'];
    
    
    
    
    
    
    // We can use this long-term access token to request Google API data,
    // for example, a list of calendars. 
    // All Google API data requests will have to be signed just as before,
    // but we can now bypass the authorization process and use the long-term
    // access token you hopefully stored somewhere permanently.
    $signatures['oauth_token'] = $access_token;
    $signatures['oauth_secret'] = $access_token_secret;
    
    
    $api_url = "https://developer-api.nest.com/devices.json";
    $oauth_cred = new OAuthConsumerCredential($signatures['consumer_key'], $signatures['shared_secret'], $signatures['oauth_token'], $signatures['oauth_secret']);
    
    
  
    
    
    
//    $futureraw = getFutureAirTravel($oauth_cred, $api_url);
//    $futureflights = extractFlightInfo($futureraw);
//    print_r($futureflights);
    
    
//    $jsonFile = $_SERVER['DOCUMENT_ROOT'] . "/tripit/future.json";
//    $jsonContent = json_encode($futureflights);
//    file_put_contents($jsonFile, $jsonContent);


    
// $pastContent = getPastAirTravel($oauth_cred, $api_url);



// $pastflights = extractFlightInfo($pastContent);


// $futureContent = getFutureAirTravel($oauth_cred, $api_url);
// $futureflights = extractFlightInfo($futureContent);
    
    
    
    
//    $jsonFile = $_SERVER['DOCUMENT_ROOT'] . "/tripit/past.json";
//    $jsonContent = json_encode($pastflights);
//    file_put_contents($jsonFile, $jsonContent);



    // $output = "<table>";
    // $output .= createDisplayTable($pastflights, "Completed");
    // $output .= createDisplayTable($futureflights, "Upcoming");
    // $output .= "<tr>";
    // $output .= '<td colspan="3">Total Miles</td>';
    // $output .= "<td>" . number_format(totalFlights($pastflights) +  totalFlights($futureflights) ) . "</td>";
    // $output .= "</tr>";
    // $output .= "</table>";


}    

    
function getFutureAirTravel($oauth_cred, $api_url){
    $t = new TripIt($oauth_cred, $api_url);
    $filter  = [];
    $filter["past"] = "false";
    $filter["include_objects"] = "true";
    $filter['format'] = 'json';
    $filter['page_size'] = 20;
    $filter['type'] = 'air';
    $r = $t->list_trip($filter);
    return json_decode(json_encode($r));
}  
            
function getPastAirTravel($oauth_cred, $api_url){
    $t = new TripIt($oauth_cred, $api_url);
    $filter  = [];
    $filter["past"] = "true";
    $filter["include_objects"] = "true";
    $filter['format'] = 'json';
    $filter['page_size'] = 20;
    $filter['type'] = 'air';
    $r = $t->list_trip($filter);
    return json_decode(json_encode($r));
}   
            
function totalFlights($flightInfo){
    $total = 0;
    date_default_timezone_set('America/New_York');
    foreach ($flightInfo as $flight) {
        if (date('Y') != substr($flight['date'], 0, 4)){
            continue;
        }
        $total += $flight['milage'];
    }
    return $total;
}            
            
                 
function extractFlightInfo($tripit){
   $milagefloor = 500;
   date_default_timezone_set('America/New_York');
   $flights = [];
   
   
   foreach ( $tripit->AirObject as $AirObject ){
        
        
        
        
        foreach ($AirObject->Segment as $segment){
            $flightprovider = $AirObject->supplier_name;
            
            $miles = strpos($AirObject->total_cost,"miles");
            
            if (strpos($AirObject->total_cost,"miles") === false){
                $miles = false;
            } else {
                $miles = true;
            }
            
            
            if (!isset($segment->start_airport_code)){
                continue;
            }

            $flight = [];
            $flight['date'] = date($segment->StartDateTime->date ." " . $segment->StartDateTime->time );
            
            $flight['origin'] = $segment->start_city_name;
            $flight['destination'] = $segment->end_city_name;
            $flight['distance'] = str_replace(",", "", trim(explode(" ", $segment->distance)[0]));
            
            if ($flight['distance'] < $milagefloor) {
                $flight['milage'] = $milagefloor;
            } else {
                $flight['milage'] = $flight['distance'] ;
            }
            
            if ($miles === true) {
                $flight['milage'] = 0 ;
            }
            
            $flight['provider'] = $flightprovider;
            $flight['miles'] = $miles;
            
            array_push($flights, $flight);

            
        }
    }
    asort($flights,0);
 
    return $flights; 
}

function createDisplayTable($flightInfo, $label){
    $total = 0;
    date_default_timezone_set('America/New_York');
    foreach ($flightInfo as $flight) {
        
        if (date('Y') != substr($flight['date'], 0, 4)){
            continue;
        }

        $total += $flight['milage'];

        $output .= "<tr>";
        $output .= "<td>" . $flight['date'] . "</td>";
        $output .= "<td>" . $flight['origin'] . "</td>";
        $output .= "<td>" . $flight['destination'] . "</td>";
        if ($flight['distance'] != $flight['milage']) {
            $output .= "<td>" . $flight['milage'] . " (" . $flight['distance'] . ")" . "</td>";
        } else {
            $output .= "<td>" . number_format($flight['milage']) . "</td>";
        }
        $output .= "</tr>";
    }

    $output .= "<tr>";
    $output .= '<td colspan="3">' . $label .'</td>';
    $output .= "<td>" . number_format($total) . "</td>";
    $output .= "</tr>";
    return $output;

}                      
                                
            
                    
?>
<HTML>
<BODY>
<?php echo $output;?>
</BODY>
</HTML>