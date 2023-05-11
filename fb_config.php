<?php
    require_once "../facebook_api/autoload.php";

    if (!session_id()) {
        session_start();
    }

    $FBObject = new \Facebook\Facebook([
        'app_id' => "927509841720079",
        'app_secret' => "65781eeacad2c37aad69e7cc0b7c24e8",
        'default_graph_version' => "v2.10"
    ]);

    $handler = $FBObject->getRedirectLoginHelper();
    
    $redirectTo = "http://localhost/xampp/includes/fb_controller.php";
    $data = ["email"];
    $fb_url = $handler->getLoginUrl($redirectTo, $data);
?>