ZF2 BoilerApp "Messenger" module
=====================

[![Build Status](https://travis-ci.org/zf2-boiler-app/app-messenger.png?branch=master)](https://travis-ci.org/zf2-boiler-app/app-messenger)
[![Latest Stable Version](https://poser.pugx.org/zf2-boiler-app/app-messenger/v/stable.png)](https://packagist.org/packages/zf2-boiler-app/app-messenger)
[![Total Downloads](https://poser.pugx.org/zf2-boiler-app/app-messenger/downloads.png)](https://packagist.org/packages/zf2-boiler-app/app-messenger)
![Code coverage](https://raw.github.com/zf2-boiler-app/app-test/master/ressources/100%25-code-coverage.png "100% code coverage")

NOTE : This module is in heavy development, it's not usable yet.
If you want to contribute don't hesitate, I'll review any PR.

Introduction
------------

__ZF2 BoilerApp "Messenger" module__ is a Zend Framework 2 module that provides message managment for ZF2 Boiler-App

Requirements
------------

* [Zend Framework 2](https://github.com/zendframework/zf2) (latest master)
* [InlineStyle](https://github.com/christiaan/InlineStyle) (latest master)
* [ZF2 BoilerApp "User" module](https://github.com/zf2-boiler-app/app-user) (latest master)

Installation
------------

### Main Setup

#### By cloning project

1. Clone this project into your `./vendor/` directory.

#### With composer

1. Add this project in your composer.json:

    ```json
    "repositories":[        
        {
	        "type": "vcs",
	        "url": "http://github.com/Nodge/lessphp"
	    },
        {
            "type": "package",
            "package": {
                "version": "dev-master",
                "name": "fabiomcosta/mootools-meio-mask",
                "source": {"url": "https://github.com/fabiomcosta/mootools-meio-mask.git","type": "git","reference": "master"}
            }
        },
        {
            "type": "package",
            "package": {
                "version": "dev-master",
                "name": "arian/iFrameFormRequest",
                "source": {"url": "https://github.com/arian/iFrameFormRequest.git","type": "git","reference": "master"}
            }
        },
        {
            "type": "package",
            "package": {
                "version": "dev-master",
                "name": "nak5ive/Form.PasswordStrength",
                "source": {"url": "https://github.com/nak5ive/Form.PasswordStrength.git","type": "git","reference": "master"}
            }
        }
    ],
    "require": {
        "zf2-boiler-app/app-messenger": "1.0.*"
    }
    ```

2. Now tell composer to download __ZF2 BoilerApp "Messenger" module__ by running the command:

    ```bash
    $ php composer.phar update
    ```

#### Post installation

1. Enabling it in your `application.config.php` file.

    ```php
    return array(
        'modules' => array(
            // ...
            'BoilerAppMessenger'
        ),
        // ...
    );
    ```

## Features

####Common
- Single "Message" entity : One Message to rule them all
- Provides multi types message (different transporters) : 
	- Email
	- ...

####Email
- Tree layout stack ([TreeLayoutStack](https://github.com/neilime/zf2-tree-layout-stack))
- Assets management ([AssetsBundle](https://github.com/neilime/zf2-assets-bundle)) :

Just add `AssetsBundle` in your `application.config.php` file to enable it.

- Inline style processing ([CssToInlineStyles](https://github.com/tijsverkoyen/CssToInlineStyles) [InlineStyle](https://github.com/christiaan/InlineStyle))
- Manage attachments
- Manage inline images as "inline attachments"