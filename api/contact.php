<?php require __DIR__ . '/../vendor/autoload.php';

use Respect\Validation\Validator as v;

// load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->safeLoad();

// only post requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    exit;
}

// validate request body
$validation = v::key('full_name', v::stringType()->length(5, 100))->key('email', v::email())->key('mobile', v::phone());
if ($validation->validate($_POST) === false) {
    http_response_code(422); // Unprocessable Entity
    exit;
}

// create and send email using SendGrid API
$sendgrid = new \SendGrid($_ENV['SENDGRID_API_KEY']);

$email = new \SendGrid\Mail\Mail();
$email->setFrom($_ENV['SENDGRID_SEND_FROM']);
$email->addTo($_ENV['SENDGRID_SEND_TO']);
$email->setSubject('New Contact Request');
$email->addContent('text/plain', 'Full Name: '.$_POST['full_name'].'\nMobile: '.$_POST['mobile'].'\nEmail: '.$_POST['email']);

try {
    if ($sendgrid->send($email)->statusCode() !== 202) {
        throw new Exception('An error occurred while sending contact email.');
    }
} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    exit;
}