Postmark Yii2 integration
=========================

This extension allow the developper to use [PostmarkApp](https://postmarkapp.com/) as an email transport.

Installation
------------

If you use Packagist for installing packages, then you can update your composer.json like this :

``` json
{
    "require": {
        "sweelix/yii2-postmark": "*"
    }
}
```

Howto use it
------------

Add extension to your configuration

``` php
return [
    //....
    'components' => [
        'mailer' => [
            'class' => 'sweelix\postmark\Mailer',
            'token' => '<your postmark token>',
        ],
    ],
];
```

You can send email as follow (using postmark templates)

``` php
Yii::$app->mailer->compose('contact/html')
     ->setFrom('from@domain.com')
     ->setTo($form->email)
     ->setSubject($form->subject)
     ->setTemplateId(12345)
     ->setTemplateModel([
         'firstname' => $form->firstname,
         'lastname' => $form->lastname,
     ->send();

```

For further instructions refer to the [related section in the Yii Definitive Guide](http://www.yiiframework.com/doc-2.0/guide-tutorial-mailing.html)


Running the tests
-----------------

Before running the tests, you should edit the file tests/_bootstrap.php and change the defines :

``` php
// ...
define('POSTMARK_FROM', '<sender>');
define('POSTMARK_TOKEN', '<token>');
define('POSTMARK_TO', '<target>');
define('POSTMARK_TEMPLATE', 575741);

define('POSTMARK_TEST_SEND', false);
// ...

```

to match your [PostmarkApp](https://postmarkapp.com) configuration.

Contributing
------------

All code contributions - including those of people having commit access -
must go through a pull request and approved by a core developer before being
merged. This is to ensure proper review of all the code.

Fork the project, create a [feature branch ](http://nvie.com/posts/a-successful-git-branching-model/), and send us a pull request.