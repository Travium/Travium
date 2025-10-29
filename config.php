<?php
global $globalConfig;
$globalConfig = [];
$globalConfig['staticParameters'] = [];
$globalConfig['dataSources'] = [];

$globalConfig['staticParameters']['recaptcha_public_key'] = 'INIT_RECAPTCHA_PUBLIC_KEY';
$globalConfig['staticParameters']['recaptcha_private_key'] = 'INIT_RECAPTCHA_PRIVATE_KEY';

// Urls
$globalConfig['staticParameters']['indexUrl'] = 'https://www.INIT_DOMAIN/'; // Note: without www. it will break gpack (Need to fix)
$globalConfig['staticParameters']['forumUrl'] = 'https://forum.INIT_DOMAIN/';
$globalConfig['staticParameters']['answersUrl'] = 'https://answers.travian.com/index.php'; // Broken
$globalConfig['staticParameters']['helpUrl'] = 'https://help.INIT_DOMAIN/';
$globalConfig['staticParameters']['adminEmail'] = 'admin@INIT_DOMAIN';

// Main database
$globalConfig['dataSources']['globalDB']['hostname'] = 'localhost';
$globalConfig['dataSources']['globalDB']['username'] = 'maindb';
$globalConfig['dataSources']['globalDB']['password'] = 'INIT_MAIN_DB_PASSWORD';
$globalConfig['dataSources']['globalDB']['database'] = 'maindb';
$globalConfig['dataSources']['globalDB']['charset'] = 'utf8mb4';

// Voting

// Email
$globalConfig['mailer'] = [
  'driver' 				=> 'local', // 'local' or 'smtp'
  'from_email' 			=> 'noreply@INIT_DOMAIN',
  'from_name'				=> 'Travium',
  // SMTP details (not required with local driver)
  'smtp_host'				=> '',
  'smtp_user'				=> '',
  'smtp_pass'				=> '',
  'smtp_port'				=> '587',
  'smtp_encryption'		=> 'tls',
  'smtp_auth'				=> true,
];

// Installer key
$globalConfig['installer_key'] = 'INIT_INSTALLER_SECRET_KEY';

/* Optional configurations */

// Locale and formatting
$globalConfig['staticParameters']['default_language'] = 'en';
$globalConfig['staticParameters']['default_timezone'] = 'Europe/London';
$globalConfig['staticParameters']['default_direction'] = 'LTR';
$globalConfig['staticParameters']['default_dateFormat'] = 'y.m.d';
$globalConfig['staticParameters']['default_timeFormat'] = 'H:i';

// Misc
$globalConfig['staticParameters']['session_timeout'] = 6*3600;
$globalConfig['staticParameters']['default_payment_location'] = 2;
$globalConfig['staticParameters']['global_css_class'] = 'travium';


/* DO NOT EDIT BELOW THIS POINT UNLESS YOU KNOW WHAT YOU ARE DOING */
$globalConfig['cachingServers'] = ['memcached' => [['127.0.0.1', 11211],],];
$globalConfig['staticParameters']['gpacks'] = [
    'default' => 'a17a8f72',
    'list' => [
        'a17a8f72' => ['hash' => 'a17a8f72', 'name' => 'Travian T4.5', 'isNew' => true],
        '68597666' => ['hash' => '68597666', 'name' => 'Travian T4.4 Seasonized', 'isNew' => false],
        'd11cb434' => ['hash' => 'd11cb434', 'name' => 'Travian T4.4 Seasonized v2', 'isNew' => false],
		'3f73c13f' => ['hash' => '3f73c13f', 'name' => 'Travian T4.6', 'isNew' => false],
		'29c89d54' => ['hash' => '29c89d54', 'name' => 'Travian T4.6', 'isNew' => true],
        'TravianOld' => ['hash' => 'TravianOld', 'name' => 'Travian T4.4 Classic', 'isNew' => false]
    ]
];

