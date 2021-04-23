<?php

class database{
    function connectionDB(){
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "meet";
        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        return $conn;
    }
    function meetData($conference_id,$meeting_code,$duration_seconds,$organizer_email){

        $conn = $this->connectionDB();
        $sql = "INSERT INTO `meet_info` (`conference_id`, `meeting_code`, `duration_seconds`, `organizer_email`) VALUES ('".$conference_id."','".$meeting_code."',$duration_seconds,'".$organizer_email."')";

        if ($conn->query($sql) === TRUE) {
            echo "New record created successfully \n";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }

        $conn->close();
    }
    function meetParticipant($display_name, $device_type, $identifier, $conference_id, $duration_seconds){
        $conn = $this->connectionDB();
        $sql = "INSERT INTO `meet_participant` (`display_name`, `device_type`, `identifier`,`conference_id`, `duration_seconds_in_call`) VALUES ('".$display_name."','".$device_type."','".$identifier."','".$conference_id."',$duration_seconds)";

        if ($conn->query($sql) === TRUE) {
            echo "New record created successfully \n";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }

        $conn->close();
    }
}
