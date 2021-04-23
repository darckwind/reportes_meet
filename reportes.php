<?php
require __DIR__ . '/vendor/autoload.php';
include 'database.php';

if (php_sapi_name() != 'cli') {
    throw new Exception('This application must be run on the command line.');
}

/**
 * Returns an authorized API client.
 * @return Google_Client the authorized client object
 */
function getClient()
{
    $client = new Google_Client();
    $client->setApplicationName('Reports Meet ');
    $client->setScopes(Google_Service_Reports::ADMIN_REPORTS_AUDIT_READONLY);
    $client->setAuthConfig('credentials.json');
    $client->setDeveloperKey('AIzaSyBc4qa1YIp1RUNh4o4LBRm5LBWX0iuSpRk');
    $client->setAccessType('offline');
    $client->setPrompt('select_account consent');

    $tokenPath = 'token.json';
    if (file_exists($tokenPath)) {
        $accessToken = json_decode(file_get_contents($tokenPath), true);
        $client->setAccessToken($accessToken);
    }

    // If there is no previous token or it's expired.
    if ($client->isAccessTokenExpired()) {
        // Refresh the token if possible, else fetch a new one.
        if ($client->getRefreshToken()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
        } else {
            // Request authorization from the user.
            $authUrl = $client->createAuthUrl();
            printf("Open the following link in your browser:\n%s\n", $authUrl);
            print 'Enter verification code: ';
            $authCode = trim(fgets(STDIN));

            // Exchange authorization code for an access token.
            $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
            $client->setAccessToken($accessToken);

            // Check to see if there was an error.
            if (array_key_exists('error', $accessToken)) {
                throw new Exception(join(', ', $accessToken));
            }
        }
        // Save the token to a file.
        if (!file_exists(dirname($tokenPath))) {
            mkdir(dirname($tokenPath), 0700, true);
        }
        file_put_contents($tokenPath, json_encode($client->getAccessToken()));
    }
    return $client;
}


function data_sorter(){
    // Get the API client and construct the service object.
    $client = getClient();
    $service = new Google_Service_Reports($client);

    // Print the last 10 login events.
    $userKey = 'all';
    $applicationName = 'meet';
    $optParams =[
        'maxResults' => 10,
    ];
    $results = $service->activities->listActivities(
        $userKey, $applicationName, $optParams);


    $arrMeetData = [];
    $meeting_code ="";
    $duration_seconds= 0;
    $organizer_email = "";
    $display_name = "";
    $device_type = "";
    $identifier = "";
    $conference_id = "";

    foreach ($results->getItems() as $res){
        $duration_seconds_tmp =0;
        foreach ($res->getEvents()[0]->getParameters() as $rest) {
            switch ($rest->getName()) {
                case "meeting_code":
                    $meeting_code = $rest->value;
                    break;
                case "duration_seconds":
                    $duration_seconds = $rest->intValue;
                    if($duration_seconds > $duration_seconds_tmp){
                        $duration_seconds_tmp = $duration_seconds;
                    }
                    break;
                case "organizer_email":
                    $organizer_email = $rest->value;
                    break;
                case "display_name":
                    $display_name = $rest->value;
                    break;
                case "device_type":
                    $device_type = $rest->value;
                    break;
                case "identifier";
                    $identifier = $rest->value;
                    break;
                case "conference_id":
                    $conference_id = $rest->value;
                    break;
            }
        }

        $id = $meeting_code."-".$conference_id;
        if(!array_key_exists($id,$arrMeetData)){
            $arrMeetData[$id]= [
                'meeting_code'=>$meeting_code,
                'conference_id' => $conference_id,
                'duration_seconds'=>$duration_seconds_tmp,
                'organizer_email'=>$organizer_email
            ];
        }

        if(!array_key_exists('participante',$arrMeetData[$id])){
            $arrMeetData[$id]['participante'] = [];
        }
        $arrMeetData[$id]['participante'][] = [
            'display_name'=>$display_name,
            'device_type'=>$device_type,
            'identifier'=>$identifier,
            'conference_id' => $conference_id,
            'duration_seconds_in_call'=>$duration_seconds
        ];

    }


    $fp = fopen('results.json', 'w');
    fwrite($fp, json_encode($arrMeetData));
    fclose($fp);

    $database = new database();

    foreach ($arrMeetData as $meet){
        $database->meetData($meet['conference_id'],$meet['meeting_code'],$meet['duration_seconds'],$meet['organizer_email']);
        foreach ($meet['participante'] as $meet_p){
            $database->meetParticipant($meet_p['display_name'],$meet_p['device_type'],$meet_p['identifier'],$meet_p['conference_id'],$meet_p['duration_seconds_in_call']);
        }
    }

}

data_sorter();