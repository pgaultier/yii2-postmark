<?php
/**
 * Mail.php
 *
 * PHP version 5.6+
 *
 * @author Philippe Gaultier <pgaultier@sweelix.net>
 * @copyright 2010-2017 Philippe Gaultier
 * @license http://www.sweelix.net/license license
 * @version XXX
 * @link http://www.sweelix.net
 * @package sweelix\postmark
 */

namespace sweelix\postmark;


use Postmark\Models\PostmarkException;
use Postmark\PostmarkClient;
use yii\base\InvalidConfigException;
use yii\mail\BaseMailer;

/**
 * This component allow user to send an email
 *
 * @author Philippe Gaultier <pgaultier@sweelix.net>
 * @copyright 2010-2017 Philippe Gaultier
 * @license http://www.sweelix.net/license license
 * @version XXX
 * @link http://www.sweelix.net
 * @package sweelix\postmark
 * @since XXX
 * @todo implement batch messages using API
 */
class Mailer extends BaseMailer
{
    /**
     * @var string
     */
    public $token;

    /**
     * @var string
     */
    public $apiUri;

    /**
     * @var boolean
     */
    public $verifySsl;

    /**
     * @var int
     */
    public $timeOut = 30;
    /**
     * @inheritdoc
     */
    public $messageClass = 'sweelix\postmark\Message';
    /**
     * @param Message $message
     * @since XXX
     * @throws InvalidConfigException
     */
    public function sendMessage($message)
    {
        try {
            if ($this->token === null) {
                throw new InvalidConfigException('Token is missing');
            }
            if ($this->apiUri !== null) {
                PostmarkClient::$BASE_URL = $this->apiUri;
            }
            if ($this->verifySsl !== null) {
                PostmarkClient::$VERIFY_SSL = $this->verifySsl;
            }
            $client = new PostmarkClient($this->token, $this->timeOut);
            $templateId = $message->getTemplateId();
            if ($templateId === null) {
                $sendResult = $client->sendEmail(
                    $message->getFrom(),
                    Message::stringifyEmails($message->getTo()),
                    $message->getSubject(),
                    $message->getHtmlBody(), $message->getTextBody(),
                    $message->getTag(),
                    $message->getTrackOpens(),
                    $message->getReplyTo(),
                    Message::stringifyEmails($message->getCc()),
                    Message::stringifyEmails($message->getBcc()),
                    $message->getHeaders(),
                    $message->getAttachments()
                );
            } else {
                $sendResult = $client->sendEmailWithTemplate(
                    $message->getFrom(),
                    Message::stringifyEmails($message->getTo()),
                    $message->getTemplateId(), $message->getTemplateModel(),
                    $message->getInlineCss(),
                    $message->getTag(),
                    $message->getTrackOpens(),
                    $message->getReplyTo(),
                    Message::stringifyEmails($message->getCc()),
                    Message::stringifyEmails($message->getBcc()),
                    $message->getHeaders(),
                    $message->getAttachments()
                );
            }
            //TODO: handle error codes and log stuff
            return isset($sendResult['ErrorCode']) ? ($sendResult['ErrorCode'] == 0) : false;
        } catch (PostmarkException $e) {
            throw $e;
        }
    }
}