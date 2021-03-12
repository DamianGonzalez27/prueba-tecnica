<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Helpers;

use Aws\Result;
use Aws\Ses\SesClient;

class Mailer
{

    /**
     * @var SesClient
     */
    private SesClient $sesClient;


    public function __construct( SesClient $sesClient )
    {
        $this->sesClient = $sesClient;
    }

    /**
     * @param $htmlBody
     * @param $textBody
     * @param $toEmail
     * @param $subject
     * @param $fromName
     * @param null $replyTo
     * @return Result|false
     */
    public function sendEmailMessage($htmlBody, $textBody,  $toEmail, $subject, $fromName=null, $replyTo = null)
    {
        if(!$toEmail) {
            return false;
        }

        if($fromName)
            $from = "$fromName <{$_ENV['MAILER_USER']}>";
        else
            $from = $_ENV['MAILER_USER'];

        return $this->sesClient->sendEmail([
            'Destination' => [
                'ToAddresses' => [$toEmail],
            ],
            'ReplyToAddresses' => [$replyTo ?? $from],
            'Source' => $from,
            'Message' => [
                'Body' => [
                    'Html' => [
                        'Charset' => 'UTF-8',
                        'Data' => $htmlBody,
                    ],
                    'Text' => [
                        'Charset' => 'UTF-8',
                        'Data' => $textBody,
                    ],
                ],
                'Subject' => [
                    'Charset' => 'UTF-8',
                    'Data' => $subject
                ],
            ],
        ]);

    }




}
