<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('send_unisms_sms')) {
    function send_unisms_sms($number, $message)
    {
        if (!$number || !$message) {
            return false;
        }

        $ci =& get_instance();
        $ci->load->config('unisms');

        $api_key = (string) $ci->config->item('unisms_api_key');
        $api_url = (string) $ci->config->item('unisms_api_url');

        if ($api_key === '' || $api_url === '') {
            log_message('error', 'UniSMS config missing.');
            return false;
        }

        $recipient = normalize_ph_mobile($number);
        if ($recipient === '') {
            log_message('error', 'UniSMS skipped: invalid mobile number ' . $number);
            return false;
        }

        $payload = json_encode([
            'recipient' => $recipient,
            'content' => $message
        ]);

        if ($payload === false) {
            log_message('error', 'UniSMS payload encoding failed for ' . $recipient);
            return false;
        }

        $ch = curl_init($api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, $api_key . ':');
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);

        $response = curl_exec($ch);
        $http_code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            log_message('error', 'UniSMS request failed for ' . $recipient . '. CURL error: ' . $curl_error);
            return false;
        }

        if ($http_code < 200 || $http_code >= 300) {
            log_message('error', 'UniSMS request failed for ' . $recipient . '. HTTP ' . $http_code . '. Response: ' . $response);
            return false;
        }

        $decoded = json_decode($response, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            $message_text = isset($decoded['message']) && is_string($decoded['message'])
                ? $decoded['message']
                : '';

            if (
                (isset($decoded['success']) && $decoded['success'] === false) ||
                isset($decoded['error']) ||
                isset($decoded['errors']) ||
                (isset($decoded['status']) && is_string($decoded['status']) && strtolower($decoded['status']) === 'error') ||
                ($message_text !== '' && stripos($message_text, 'error') !== false)
            ) {
                log_message('error', 'UniSMS rejected message for ' . $recipient . '. Response: ' . $response);
                return false;
            }
        }

        return true;
    }
}
