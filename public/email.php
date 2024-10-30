<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

require_once( '../../../../wp-load.php');
    //ajout à la DB
    //send email to admin  
    $data = array(
            json_decode(file_get_contents("php://input['orderDate']"), true),
            json_decode(file_get_contents("php://input['orderTime']"), true),
            json_decode(file_get_contents("php://input['origin-input']"), true),
            json_decode(file_get_contents("php://input['destination-input']"), true),
            json_decode(file_get_contents("php://input['nom']"), true),
            json_decode(file_get_contents("php://input['prenom']"), true),
            json_decode(file_get_contents("php://input['mail']"), true),
            json_decode(file_get_contents("php://input['luggage']"), true),
            json_decode(file_get_contents("php://input['comment']"), true),
            json_decode(file_get_contents("php://input['total-price']"), true),
        );
    //var_dump($data);

        $to = $data["mail"];
        $subject = __('There is a new booking from your cab booking form', 'cpress');
        
        $body = 'Hello <br>';
        foreach ($data as $k => $v) {
            if ($k !== '' OR $k !== 'order-distance' 
            OR $k !== 'submit' 
            OR $k !== 'adminbar-search' 
            OR $k !== 'adminbar-search' 
            OR $k !== 'radio-price') {
                $body .= '<b>'.$k.'</b> : '.$v."<br>";
            }
        }
        $body .= '<br>Regards.';
        $headers = array('Content-Type: text/html; charset=UTF-8', get_option('admin_email'));

        try {
            wp_mail( $to, $subject, $body, $headers );

        } catch (\Throwable $th) {
            echo $th;
        }
   ?>
