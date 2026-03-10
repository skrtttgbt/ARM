<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------
| SMTP / PHPMailer Settings
| -------------------------------------------------------------------
| Update these values with your email provider credentials.
*/
$config['smtp_host']   = 'smtp.gmail.com';
$config['smtp_port']   = 587;
$config['smtp_user']   = 'your-email@example.com';
$config['smtp_pass']   = 'your-app-password';
$config['smtp_crypto'] = 'tls'; // tls or ssl

$config['from_email']  = 'your-email@example.com';
$config['from_name']   = 'PetVax Manager';
