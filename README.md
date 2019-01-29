Yii2-gii
========

Additional Gii code generator .

This extension add code generator to Yii2 framework Gii extension.

##Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
$ php composer.phar require pcrt/yii2-gii "*"
```

or add

```
"pcrt/yii2-gii": "*"
```

to the require section of your `composer.json` file.

## Usage

Once the extension is installed, modify your application configuration to include:

```php


  $config['modules']['gii'] = [
      'class' => 'yii\gii\Module',
      ....
      'generators' => [ // HERE
          'pcrtmodel' => [
              'class' => 'pcrt\generators\model\Generator',
              'templates' => [
                  'pcrt' => '@vendor/pcrt/yii2-gii/generators/model/pcrt',
              ]
          ],
          'pcrtcrud' => [
              'class' => 'pcrt\generators\crud\Generator',
              'templates' => [
                  'pcrt' => '@vendor/pcrt/yii2-gii/generators/crud/pcrt',
              ]
          ]
      ],
      ....
      // uncomment the following to add your IP if you are not connecting from localhost.
      // 'allowedIPs' => ['127.0.0.1', '::1'],
  ];

```

## License

Yii2-gii is released under the BSD-3 License. See the bundled `LICENSE.md` for details.

Enjoy!
