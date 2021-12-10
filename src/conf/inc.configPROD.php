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
define('ADMIN_NAME',    'Equipe CSM');
define('ADMIN_MAIL',    'csm@sib.fr');

// Environment properties
define('DEVELOPMENT',   true);
define('PRODUCTION',    false);
define('DEBUG',         true);
define('LOGGING',       true);

define('SOLR_SERVER',   'solr');
define('SOLR_USER',     'solr');
define('SOLR_PASSWORD', 'SolrRocks');
define('SOLR_PORT',     8983);
define('SOLR_CORE',     'publication_core');

// OpenLDAP server properties
//define('LDAP_SERVER',   'sgnautn1.gndatacenter1.fr');
//define('LDAP_PORT',     '389');
// or use this user settings and comment the two previous lines
define('ADMIN_LOGIN',   'csm@sib.fr');
define('ADMIN_PASSWORD','**************');

// INSEE Sirene API properties
define('SIRENE_KEY',    '**************');
define('SIRENE_SECRET', '**************');
