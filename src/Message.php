<?php
/**
 * Message.php
 *
 * PHP version 5.6+
 *
 * @author Philippe Gaultier <pgaultier@sweelix.net>
 * @copyright 2010-2016 Philippe Gaultier
 * @license http://www.sweelix.net/license license
 * @version XXX
 * @link http://www.sweelix.net
 * @package sweelix\postmark
 */

namespace sweelix\postmark;


use yii\base\InvalidConfigException;
use yii\base\InvalidParamException;
use yii\base\NotSupportedException;
use yii\helpers\ArrayHelper;
use yii\mail\BaseMessage;
use Yii;
use yii\mail\MailerInterface;

/**
 * This component allow user to send an email
 *
 * @author Philippe Gaultier <pgaultier@sweelix.net>
 * @copyright 2010-2016 Philippe Gaultier
 * @license http://www.sweelix.net/license license
 * @version XXX
 * @link http://www.sweelix.net
 * @package sweelix\postmark
 * @since XXX
 */
class Message extends BaseMessage
{
    /**
     * @var string|array from
     */
    protected $from;

    /**
     * @var array
     */
    protected $to = [];

    /**
     * @var string reply to
     */
    protected $replyTo;

    /**
     * @var array
     */
    protected $cc = [];

    /**
     * @var array
     */
    protected $bcc = [];

    /**
     * @var string
     */
    protected $subject;

    /**
     * @var string
     */
    protected $textBody;

    /**
     * @var string
     */
    protected $htmlBody;

    /**
     * @var array
     */
    protected $attachments = [];

    /**
     * @var string
     */
    protected $tag;

    /**
     * @var bool
     */
    protected $trackOpens = true;

    /**
     * @var array
     */
    protected $headers = [];

    /**
     * @var string
     */
    protected $templateId;

    /**
     * @var array
     */
    protected $templateModel;

    /**
     * @var bool
     */
    protected $inlineCss = true;

    /**
     * @inheritdoc
     */
    public function getCharset()
    {
        throw new NotSupportedException();
    }

    /**
     * @inheritdoc
     */
    public function setCharset($charset)
    {
        throw new NotSupportedException();
    }

    /**
     * @inheritdoc
     */
    public function getFrom()
    {
        return $this->extractEmails($this->from);
    }

    /**
     * @inheritdoc
     */
    public function setFrom($from)
    {
        $this->from = $from;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getTo()
    {
        return $this->extractEmails($this->to);
    }

    /**
     * @inheritdoc
     */
    public function setTo($to)
    {
        $this->to = $to;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getReplyTo()
    {
        return $this->extractEmails($this->replyTo);
    }

    /**
     * @inheritdoc
     */
    public function setReplyTo($replyTo)
    {
        $this->replyTo = $replyTo;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCc()
    {
        return $this->extractEmails($this->cc);
    }

    /**
     * @inheritdoc
     */
    public function setCc($cc)
    {
        $this->cc = $cc;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getBcc()
    {
        return $this->extractEmails($this->bcc);
    }

    /**
     * @inheritdoc
     */
    public function setBcc($bcc)
    {
        $this->bcc = $bcc;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @inheritdoc
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * @return string|null text body of the message
     * @since XXX
     */
    public function getTextBody()
    {
        return $this->textBody;
    }

    /**
     * @inheritdoc
     */
    public function setTextBody($text)
    {
        $this->textBody = $text;
        return $this;
    }

    /**
     * @return string|null html body of the message
     * @since XXX
     */
    public function getHtmlBody()
    {
        return $this->htmlBody;
    }

    /**
     * @inheritdoc
     */
    public function setHtmlBody($html)
    {
        $this->htmlBody = $html;
        return $this;
    }

    /**
     * @return string tag associated to the email
     * @since XXX
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * @param string $tag tag which should be associated to the email
     * @return $this
     * @since XXX
     */
    public function setTag($tag)
    {
        $this->tag = $tag;
        return $this;
    }

    /**
     * @param bool $trackOpens define if mail should be tracked
     * @return $this
     * @since XXX
     */
    public function setTrackOpens($trackOpens)
    {
        $this->trackOpens = $trackOpens;
        return $this;
    }

    /**
     * @return bool tracking status
     * @since XXX
     */
    public function getTrackOpens()
    {
        return $this->trackOpens;
    }

    /**
     * @param integer $templateId template Id used. in this case, Subject / HtmlBody / TextBody are discarded
     * @return $this
     * @since XXX
     */
    public function setTemplateId($templateId)
    {
        $this->templateId = $templateId;
        return $this;
    }

    /**
     * @return integer|null current templateId
     * @since XXX
     */
    public function getTemplateId()
    {
        return $this->templateId;
    }

    /**
     * @param array $templateModel model associated with the template
     * @return $this
     * @since XXX
     */
    public function setTemplateModel($templateModel)
    {
        $this->templateModel = $templateModel;
        return $this;
    }

    /**
     * @return array current template model
     * @since XXX
     */
    public function getTemplateModel()
    {
        return $this->templateModel;
    }

    /**
     * @param bool $inlineCss define if css should be inlined
     * @return $this
     * @since XXX
     */
    public function setInlineCss($inlineCss)
    {
        $this->inlineCss = $inlineCss;
        return $this;
    }

    /**
     * @return bool define if css should be inlined
     * @since XXX
     */
    public function getInlineCss()
    {
        return $this->inlineCss;
    }

    /**
     * @param array $header add custom header to the mail
     * @since XXX
     */
    public function addHeader($header)
    {
        $this->headers[] = $header;
    }

    /**
     * @return array|null headers which should be added to the mail
     * @since XXX
     */
    public function getHeaders()
    {
        return empty($this->headers) ? null : $this->headers;
    }

    /**
     * @return array|null list of attachments
     * @since XXX
     */
    public function getAttachments()
    {
        return empty($this->attachments) ? null : $this->attachments;
    }

    /**
     * @inheritdoc
     */
    public function attach($fileName, array $options = [])
    {
        $attachment = [
            'Content' => base64_encode(file_get_contents($fileName))
        ];
        if (!empty($options['fileName'])) {
            $attachment['Name'] = $options['fileName'];
        } else {
            $attachment['Name'] = pathinfo($fileName, PATHINFO_BASENAME);
        }
        if (!empty($options['contentType'])) {
            $attachment['ContentType'] = $options['contentType'];
        } else {
            $attachment['ContentType'] = 'application/octet-stream';
        }
        $this->attachments[] = $attachment;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function attachContent($content, array $options = [])
    {
        $attachment = [
            'Content' => base64_encode($content)
        ];
        if (!empty($options['fileName'])) {
            $attachment['Name'] = $options['fileName'];
        } else {
            throw new InvalidParamException('Filename is missing');
        }
        if (!empty($options['contentType'])) {
            $attachment['ContentType'] = $options['contentType'];
        } else {
            $attachment['ContentType'] = 'application/octet-stream';
        }
        $this->attachments[] = $attachment;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function embed($fileName, array $options = [])
    {
        $embed = [
            'Content' => base64_encode(file_get_contents($fileName))
        ];
        if (!empty($options['fileName'])) {
            $embed['Name'] = $options['fileName'];
        } else {
            $embed['Name'] = pathinfo($fileName, PATHINFO_BASENAME);
        }
        if (!empty($options['contentType'])) {
            $embed['ContentType'] = $options['contentType'];
        } else {
            $embed['ContentType'] = 'application/octet-stream';
        }
        $embed['ContentID'] = 'cid:' . uniqid();
        $this->attachments[] = $embed;
        return $embed['ContentID'];
    }

    /**
     * @inheritdoc
     */
    public function embedContent($content, array $options = [])
    {
        $embed = [
            'Content' => base64_encode($content)
        ];
        if (!empty($options['fileName'])) {
            $embed['Name'] = $options['fileName'];
        } else {
            throw new InvalidParamException('Filename is missing');
        }
        if (!empty($options['contentType'])) {
            $embed['ContentType'] = $options['contentType'];
        } else {
            $embed['ContentType'] = 'application/octet-stream';
        }
        $embed['ContentID'] = 'cid:' . uniqid();
        $this->attachments[] = $embed;
        return $embed['ContentID'];
    }

    /**
     * @inheritdoc
     * @todo make real serialization to make message compliant with PostmarkAPI
     */
    public function toString()
    {
        return serialize($this);
    }


    /**
     * @param array|string $emailsData email can be defined as string. In this case no transformation is done
     *                                 or as an array ['email@test.com', 'email2@test.com' => 'Email 2']
     * @return string|null
     * @since XXX
     */
    private function extractEmails($emailsData)
    {
        $emails = null;
        if (empty($emailsData) === false) {
            if (is_array($emailsData) === true) {
                foreach ($emailsData as $key => $email) {
                    if (is_int($key) === true) {
                        $emails[] = $email;
                    } else {
                        if (preg_match('/[.,:]/', $email) > 0) {
                            $email = '"'. $email .'"';
                        }
                        $emails[] = $email . ' ' . '<' . $key . '>';
                    }
                }
                $emails = implode(', ', $emails);
            } elseif (is_string($emailsData) === true) {
                $emails = $emailsData;
            }
        }
        return $emails;
    }


}