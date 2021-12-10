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
define('ADMIN_NAME',    'Xavier MADIOT');
define('ADMIN_MAIL',    'x.madiot@girondenumerique.fr');

// Environment properties
define('DEVELOPMENT',   true);
define('PRODUCTION',    false);
define('DEBUG',         true);
define('LOGGING',       true);

define('SOLR_SERVER',   'sgnsolr01.gndatacenter1.fr');
define('SOLR_USER',     'solr');
define('SOLR_PASSWORD', 'test');
define('SOLR_PORT',     8983);
define('SOLR_CORE',     'documents');

// OpenLDAP server properties
define('LDAP_SERVER',   'sgnautn1.gndatacenter1.fr');
define('LDAP_PORT',     '389');

// INSEE Sirene API properties
define('SIRENE_KEY',    '****************************');
define('SIRENE_SECRET', '****************************');
