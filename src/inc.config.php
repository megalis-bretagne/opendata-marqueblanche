<?php
/**
 * @license Apache 2.0
 */

namespace DataSearchEngine;

/**
 * Search engine properties file
 *
 * @package DataSearchEngine
 * @author  Xavier MADIOT <x.madiot@girondenumerique.fr>
 */

// General properties
define('ADMIN_NAME',    'xxxxxxxxxxxxx');
define('ADMIN_MAIL',    'xxxxxxxxxxxx');

// Environment properties
define('DEVELOPMENT',   true);
define('PRODUCTION',    false);
define('DEBUG',         true);
define('LOGGING',       true);

define('SOLR_SERVER',   'xxxx');
define('SOLR_USER',     'xxx');
define('SOLR_PASSWORD', 'xxxxxxxx');
define('SOLR_PORT',     8983);
define('SOLR_CORE',     'xxxxxxxx');

// OpenLDAP server properties
//define('LDAP_SERVER',   'sgnautn1.gndatacenter1.fr');
//define('LDAP_PORT',     '389');
// or use this user settings and comment the two previous lines
 define('ADMIN_LOGIN',   'xxxxxxxxxxxxxxx');
 define('ADMIN_PASSWORD','xxxxxxxxxxxxxxxxx');

// INSEE Sirene API properties
define('SIRENE_KEY',    'xxxxxxxxxxxxxxx');
define('SIRENE_SECRET', 'xxxxxxxxxxxxxxxxxxx');
