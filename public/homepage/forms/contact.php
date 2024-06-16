<?php declare(strict_types=1);

/*
 * The Software is licensed, not sold. You may use the Software only as
 * permitted under the terms of the license agreement. Unauthorized use,
 * modification, distribution, or copying of the Software is strictly prohibited.
 *
 * @copyright  Copyright (c) Lukas Völler.
 * @author     Lukas Völler
 * @license    Proprietary License
 * @link       https://www.vllr.lu
 */

// Replace contact@example.com with your real receiving email address
$receiving_email_address = 'voeller.lukas@gmail.com';

if( file_exists($php_email_form = '../vendor/php-email-form/php-email-form.php' )) {
  include( $php_email_form );
} else {
  die('Unable to load the "PHP Email Form" Library!');
}

$contact = new PHP_Email_Form;
$contact->ajax = true;

$contact->to = $receiving_email_address;
$contact->from_name = $_POST['name'];
$contact->from_email = $_POST['email'];
$contact->subject = $_POST['subject'];

// Uncomment below code if you want to use SMTP to send emails. You need to enter your correct SMTP credentials
$contact->smtp = array(
    'host' => 'mail.your-server.de',
    'username' => ' contact@vllr.lu',
    'password' => 'MYPS@mail3107',
    'port' => '587'
);

$contact->add_message( $_POST['name'], 'From');
$contact->add_message( $_POST['email'], 'Email');
$contact->add_message( $_POST['message'], 'Message', 10);

echo $contact->send();
?>
