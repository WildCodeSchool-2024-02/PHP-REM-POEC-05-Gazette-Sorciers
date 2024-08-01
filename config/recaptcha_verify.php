<?php

function verifyRecaptcha($recaptchaResponse, $secretKey, $remoteIp)
{
    if (empty($recaptchaResponse)) {
        return ['success' => false, 'error' => 'reCAPTCHA n\'a pas été soumis.'];
    }

    $url = "https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$recaptchaResponse";
    $response = file_get_contents("$url&remoteip=$remoteIp");

    $responseKeys = json_decode($response, true);

    if (intval($responseKeys["success"]) !== 1) {
        return ['success' => false, 'error' => 'Veuillez prouver que vous n\'êtes pas un robot.'];
    }

    return ['success' => true];
}
