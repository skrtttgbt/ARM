<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('send_semaphore_sms')) {
    function send_semaphore_sms($api_key, $number, $message, $sender = null) {
        if (!$api_key || !$number || !$message) {
            return false;
        }

        $payload = [
            'apikey' => $api_key,
            'number' => $number,
            'message' => $message
        ];

        if ($sender) {
            $payload['sendername'] = $sender;
        }

        $ch = curl_init('https://semaphore.co/api/v4/messages');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $http_code >= 200 && $http_code < 300;
    }
}
