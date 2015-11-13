<?php

/**
 * Local Configuration Override
 *
 * This configuration override file is for overriding environment-specific and
 * security-sensitive configuration information. Copy this file without the
 * .dist extension at the end and populate values as needed.
 *
 * @NOTE: This file is ignored from Git by default with the .gitignore included
 * in ZendSkeletonApplication. This is a good practice, as it prevents sensitive
 * credentials from accidentally being committed into version control.
 */


return array(
    /**
     * Smtp Options config
     */
    'smtp_options' => array(
        'name' => 'smtp.gmail.com',
        'host' => 'smtp.gmail.com',
        'connection_class' => 'login',
        'connection_config' => array(
            'name' => 'おもりんく管理',
            'from' => 'Omolink@kobedigitallabo.com',
            'username' => 'kdlOmolink@gmail.com',
            'password' => 'Kdl!810783272280',
            'ssl' => 'tls',
        ),
    ),
);
