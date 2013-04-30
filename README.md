ZF2 BoilerApp "Messenger" module
=====================

_100% Code Coverage_

NOTE : This module is in heavy development, it's not usable yet.
If you want to contribute don't hesitate, I'll review any PR.

Introduction
------------

__ZF2 BoilerApp "Messenger" module__ is a Zend Framework 2 module that provides message managment for ZF2 Boiler-App

Requirements
------------

* [Zend Framework 2](https://github.com/zendframework/zf2) (latest master)

Installation
------------

### Main Setup

#### By cloning project

1. Clone this project into your `./vendor/` directory.

#### With composer

1. Add this project in your composer.json:

    ```json
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
            'BoilerAppMessenger',
        ),
        // ...
    );
    ```

## Features