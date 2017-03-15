<?php
/**
 * MessageTest.php
 *
 * PHP version 5.6+
 *
 * @author Philippe Gaultier <pgaultier@sweelix.net>
 * @copyright 2010-2017 Philippe Gaultier
 * @license http://www.sweelix.net/license license
 * @version XXX
 * @link http://www.sweelix.net
 * @package tests\unit
 */

namespace tests\unit;

use sweelix\postmark\Mailer;
use sweelix\postmark\Message;
use Yii;
use yii\base\InvalidParamException;
use yii\base\NotSupportedException;

/**
 * Test node basic functions
 *
 * @author Philippe Gaultier <pgaultier@sweelix.net>
 * @copyright 2010-2017 Philippe Gaultier
 * @license http://www.sweelix.net/license license
 * @version XXX
 * @link http://www.sweelix.net
 * @package tests\unit
 * @since XXX
 */
class MessageTest extends TestCase
{

    public function setUp()
    {
        $this->mockApplication([
            'components' => [
                'email' => $this->createTestEmailComponent()
            ]
        ]);
    }

    public function tearDown()
    {
        parent::tearDown(); // TODO: Change the autogenerated stub
    }

    protected function createTestEmailComponent()
    {
        $component = new Mailer([
            'token' => POSTMARK_TOKEN
        ]);
        return $component;
    }


    protected function createOtherEmailComponent()
    {
        $component = new Mailer();
        $component->token = POSTMARK_TOKEN;
        $component->apiUri = 'https://api.postmarkapp.com';
        $component->verifySsl = false;
        return $component;
    }

    /**
     * @return string test file path.
     */
    protected function getTestFilePath()
    {
        return Yii::getAlias('@test/runtime') . DIRECTORY_SEPARATOR . basename(get_class($this)) . '_' . getmypid();
    }

    /**
     * @return Message test message instance.
     */
    protected function createTestMessage()
    {
        return Yii::$app->get('mailer')->compose();
    }

    protected function createOtherTestMessage()
    {
        return $this->createOtherEmailComponent()->compose();
    }

    public function testMailerConfigured()
    {
        $mailComponent = $this->createTestEmailComponent();
        $this->assertNotNull($mailComponent->token);
    }

    public function testGetPostmarkMessage()
    {
        $message = new Message();
        $this->assertInstanceOf(Message::className(), $message);
    }

    public function testSetCharsetException()
    {
        $message = new Message();
        $this->expectException(NotSupportedException::class);
        $message->setCharset('utf-8');

    }

    public function testGetCharsetException()
    {
        $message = new Message();
        $charset = $message->getCharset();
        $this->assertEquals('utf-8', $charset);
    }

    public function testGettersSetters()
    {
        $message = new Message();
        $message->setFrom('test@email.com');
        $this->assertEquals('test@email.com', $message->getFrom());
        $message->setFrom(['test@email.com']);
        $this->assertEquals('test@email.com', $message->getFrom());
        $message->setFrom(['test@email.com' => 'Test User']);
        $this->assertEquals('Test User <test@email.com>', $message->getFrom());

        $message->setTo('test@email.com');
        $this->assertTrue(is_array($message->getTo()));
        $this->assertEquals('test@email.com', Message::stringifyEmails($message->getTo()));
        $message->setTo(['test@email.com']);
        $this->assertEquals('test@email.com', Message::stringifyEmails($message->getTo()));
        $message->setTo(['test@email.com' => 'Test User']);
        $this->assertEquals('Test User <test@email.com>', Message::stringifyEmails($message->getTo()));
        $message->setTo(['test@email.com' => 'Test, User']);
        $this->assertEquals('"Test, User" <test@email.com>', Message::stringifyEmails($message->getTo()));
        $message->setTo(['test@email.com' => 'Test User', 'test2@email.com']);
        $this->assertEquals('Test User <test@email.com>, test2@email.com', Message::stringifyEmails($message->getTo()));

        $message->setReplyTo('test@email.com');
        $this->assertEquals('test@email.com', $message->getReplyTo());
        $message->setReplyTo(['test@email.com']);
        $this->assertEquals('test@email.com', $message->getReplyTo());
        $message->setReplyTo(['test@email.com' => 'Test User']);
        $this->assertEquals('Test User <test@email.com>', $message->getReplyTo());
        $message->setReplyTo(['test@email.com' => 'Test, User']);
        $this->assertEquals('"Test, User" <test@email.com>', $message->getReplyTo());
        $message->setReplyTo(['test@email.com' => 'Test User', 'test2@email.com']);
        $this->assertEquals('Test User <test@email.com>, test2@email.com', $message->getReplyTo());

        $message->setCc('test@email.com');
        $this->assertTrue(is_array($message->getCc()));
        $this->assertEquals('test@email.com', Message::stringifyEmails($message->getCc()));
        $message->setCc(['test@email.com']);
        $this->assertEquals('test@email.com', Message::stringifyEmails($message->getCc()));
        $message->setCc(['test@email.com' => 'Test User']);
        $this->assertEquals('Test User <test@email.com>', Message::stringifyEmails($message->getCc()));
        $message->setCc(['test@email.com' => 'Test, User']);
        $this->assertEquals('"Test, User" <test@email.com>', Message::stringifyEmails($message->getCc()));
        $message->setCc(['test@email.com' => 'Test User', 'test2@email.com']);
        $this->assertEquals('Test User <test@email.com>, test2@email.com', Message::stringifyEmails($message->getCc()));

        $message->setBcc('test@email.com');
        $this->assertTrue(is_array($message->getBcc()));
        $this->assertEquals('test@email.com', Message::stringifyEmails($message->getBcc()));
        $message->setBcc(['test@email.com']);
        $this->assertEquals('test@email.com', Message::stringifyEmails($message->getBcc()));
        $message->setBcc(['test@email.com' => 'Test User']);
        $this->assertEquals('Test User <test@email.com>', Message::stringifyEmails($message->getBcc()));
        $message->setBcc(['test@email.com' => 'Test, User']);
        $this->assertEquals('"Test, User" <test@email.com>', Message::stringifyEmails($message->getBcc()));
        $message->setBcc(['test@email.com' => 'Test User', 'test2@email.com']);
        $this->assertEquals('Test User <test@email.com>, test2@email.com', Message::stringifyEmails($message->getBcc()));

        $message->setSubject('Subject');
        $this->assertEquals('Subject', $message->getSubject());

        $message->setTextBody('Body stuff');
        $this->assertEquals('Body stuff', $message->getTextBody());

        $message->setHtmlBody('Body stuff');
        $this->assertEquals('Body stuff', $message->getHtmlBody());

        $message->setTag('tag');
        $this->assertEquals('tag', $message->getTag());

        $this->assertTrue($message->getTrackOpens());
        $message->setTrackOpens(false);
        $this->assertFalse($message->getTrackOpens());

        $message->setTemplateId(1234);
        $this->assertEquals(1234, $message->getTemplateId());

        $message->setTemplateModel(['a' => 'b']);
        $this->assertArrayHasKey('a', $message->getTemplateModel());
        $this->assertEquals('b', $message->getTemplateModel()['a']);

        $this->assertTrue($message->getInlineCss());
        $message->setInlineCss(false);
        $this->assertFalse($message->getInlineCss());

        $this->assertNull($message->getHeaders());
        $message->addHeader(['X-Header' => 'test']);
        $this->assertArrayHasKey('X-Header', $message->getHeaders()[0]);
        $message->addHeader(['X-Secondary' => 'test']);
        $this->assertArrayHasKey('X-Header', $message->getHeaders()[0]);
        $this->assertArrayHasKey('X-Secondary', $message->getHeaders()[1]);

        $this->assertNull($message->getAttachments());
        $message->attach(__FILE__, ['fileName' => 'file.php', 'contentType' => 'text/plain']);
        $this->assertEquals('file.php', $message->getAttachments()[0]['Name']);
        $this->assertEquals('text/plain', $message->getAttachments()[0]['ContentType']);
        $this->assertEquals(base64_encode(file_get_contents(__FILE__)), $message->getAttachments()[0]['Content']);

        $message->attach(__FILE__);
        $this->assertEquals('MessageTest.php', $message->getAttachments()[1]['Name']);
        $this->assertEquals('application/octet-stream', $message->getAttachments()[1]['ContentType']);
        $this->assertEquals(base64_encode(file_get_contents(__FILE__)), $message->getAttachments()[1]['Content']);

        $message->attachContent('plop', ['fileName' => 'file.php', 'contentType' => 'text/plain']);
        $this->assertEquals('file.php', $message->getAttachments()[2]['Name']);
        $this->assertEquals('text/plain', $message->getAttachments()[2]['ContentType']);
        $this->assertEquals(base64_encode('plop'), $message->getAttachments()[2]['Content']);

        $message->attachContent('plop', ['fileName' => 'file.php']);
        $this->assertEquals('file.php', $message->getAttachments()[3]['Name']);
        $this->assertEquals('application/octet-stream', $message->getAttachments()[3]['ContentType']);
        $this->assertEquals(base64_encode('plop'), $message->getAttachments()[3]['Content']);

        $cid = $message->embed(__FILE__, ['fileName' => 'file.php', 'contentType' => 'text/plain']);
        $this->assertEquals('file.php', $message->getAttachments()[4]['Name']);
        $this->assertEquals('text/plain', $message->getAttachments()[4]['ContentType']);
        $this->assertEquals(base64_encode(file_get_contents(__FILE__)), $message->getAttachments()[4]['Content']);
        $this->assertEquals($cid, $message->getAttachments()[4]['ContentID']);

        $cid = $message->embed(__FILE__);
        $this->assertEquals('MessageTest.php', $message->getAttachments()[5]['Name']);
        $this->assertEquals('application/octet-stream', $message->getAttachments()[5]['ContentType']);
        $this->assertEquals(base64_encode(file_get_contents(__FILE__)), $message->getAttachments()[5]['Content']);
        $this->assertEquals($cid, $message->getAttachments()[5]['ContentID']);

        $cid = $message->embedContent('plop', ['fileName' => 'file.php', 'contentType' => 'text/plain']);
        $this->assertEquals('file.php', $message->getAttachments()[6]['Name']);
        $this->assertEquals('text/plain', $message->getAttachments()[6]['ContentType']);
        $this->assertEquals(base64_encode('plop'), $message->getAttachments()[6]['Content']);
        $this->assertEquals($cid, $message->getAttachments()[6]['ContentID']);

        $cid = $message->embedContent('plop', ['fileName' => 'file.php']);
        $this->assertEquals('file.php', $message->getAttachments()[7]['Name']);
        $this->assertEquals('application/octet-stream', $message->getAttachments()[7]['ContentType']);
        $this->assertEquals(base64_encode('plop'), $message->getAttachments()[7]['Content']);
        $this->assertEquals($cid, $message->getAttachments()[7]['ContentID']);

    }

    public function testAttachException()
    {
        $message = new Message();

        $this->expectException(InvalidParamException::class);
        $message->attachContent('plop');
    }

    public function testEmbedException()
    {
        $message = new Message();

        $this->expectException(InvalidParamException::class);
        $message->embedContent('plop');
    }


    public function testBasicSend()
    {
        // allow disabling real tests
        if (POSTMARK_TEST_SEND === true) {
            $message = $this->createTestMessage();
            $message->setFrom(POSTMARK_FROM);
            $message->setTo(POSTMARK_TO);
            $message->setSubject('Yii postmark test message');
            $message->setTextBody('Yii postmark test body');
            $this->assertTrue($message->send());
        }
    }

    public function testParametersSend()
    {
        if (POSTMARK_TEST_SEND === true) {
            $message = $this->createOtherTestMessage();
            $message->setFrom(POSTMARK_FROM);
            $message->setTo(POSTMARK_TO);
            $message->setSubject('Yii postmark test message');
            $message->setTextBody('Yii postmark test body');
            $this->assertTrue($message->send());
        }

    }

    public function testTemplateSend()
    {
        // allow disabling real tests
        if (POSTMARK_TEST_SEND === true) {
            $message = $this->createTestMessage();
            $message->setFrom(POSTMARK_FROM)
                ->setTo(POSTMARK_TO)
                ->setTemplateId(POSTMARK_TEMPLATE)
                ->setTemplateModel([
                    'templateName' => 'test',
                    'userName' => 'Mr test'
                ]);
            $this->assertTrue($message->send());
        }
    }
}
