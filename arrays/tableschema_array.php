<?php
/** 
 * Database tables information for past and new versions.
 * 
 * This file is not fully in use yet. The intention is to migrate it to the
 * installation class and rather than an array I will simply store every version
 * of each tables query. Each query can be broken down to compare against existing 
 * tables. I find this array approach too hard to maintain over many plugins.
 * 
 * @todo move this to installation class but also reduce the array to actual queries per version
 * 
 * @package WTG Portal Manager
 * @author Ryan Bayne   
 * @since 0.0.1
 * @version 8.1.2
 */

// load in WordPress only
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );
 
 
/*   Column Array Example Returned From "mysql_query( "SHOW COLUMNS FROM..."
        
          array(6) {
            [0]=>
            string(5) "row_id"
            [1]=>
            string(7) "int(11)"
            [2]=>
            string(2) "NO"
            [3]=>
            string(3) "PRI"
            [4]=>
            NULL
            [5]=>
            string(14) "auto_increment"
          }
                  
    +------------+----------+------+-----+---------+----------------+
    | Field      | Type     | Null | Key | Default | Extra          |
    +------------+----------+------+-----+---------+----------------+
    | Id         | int(11)  | NO   | PRI | NULL    | auto_increment |
    | Name       | char(35) | NO   |     |         |                |
    | Country    | char(3)  | NO   | UNI |         |                |
    | District   | char(20) | YES  | MUL |         |                |
    | Population | int(11)  | NO   |     | 0       |                |
    +------------+----------+------+-----+---------+----------------+            
*/
   
global $wpdb;   
$c2p_tables_array =  array();
##################################################################################
#                                 webtechglobal_log                                         #
##################################################################################        
$c2p_tables_array['tables']['webtechglobal_log']['name'] = $wpdb->prefix . 'webtechglobal_log';
$c2p_tables_array['tables']['webtechglobal_log']['required'] = false;// required for all installations or not (boolean)
$c2p_tables_array['tables']['webtechglobal_log']['pluginversion'] = '0.0.1';
$c2p_tables_array['tables']['webtechglobal_log']['usercreated'] = false;// if the table is created as a result of user actions rather than core installation put true
$c2p_tables_array['tables']['webtechglobal_log']['version'] = '0.0.1';// used to force updates based on version alone rather than individual differences
$c2p_tables_array['tables']['webtechglobal_log']['primarykey'] = 'row_id';
$c2p_tables_array['tables']['webtechglobal_log']['uniquekey'] = 'row_id';
// webtechglobal_log - row_id
$c2p_tables_array['tables']['webtechglobal_log']['columns']['row_id']['type'] = 'bigint(20)';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['row_id']['null'] = 'NOT NULL';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['row_id']['key'] = '';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['row_id']['default'] = '';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['row_id']['extra'] = 'AUTO_INCREMENT';
// webtechglobal_log - outcome
$c2p_tables_array['tables']['webtechglobal_log']['columns']['outcome']['type'] = 'tinyint(1)';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['outcome']['null'] = 'NOT NULL';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['outcome']['key'] = '';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['outcome']['default'] = '1';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['outcome']['extra'] = '';
// webtechglobal_log - timestamp
$c2p_tables_array['tables']['webtechglobal_log']['columns']['timestamp']['type'] = 'timestamp';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['timestamp']['null'] = 'NOT NULL';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['timestamp']['key'] = '';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['timestamp']['default'] = 'CURRENT_TIMESTAMP';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['timestamp']['extra'] = '';
// webtechglobal_log - line
$c2p_tables_array['tables']['webtechglobal_log']['columns']['line']['type'] = 'int(11)';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['line']['null'] = '';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['line']['key'] = '';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['line']['default'] = 'NULL';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['line']['extra'] = '';
// webtechglobal_log - file
$c2p_tables_array['tables']['webtechglobal_log']['columns']['file']['type'] = 'varchar(250)';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['file']['null'] = '';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['file']['key'] = '';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['file']['default'] = 'NULL';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['file']['extra'] = '';
// webtechglobal_log - function
$c2p_tables_array['tables']['webtechglobal_log']['columns']['function']['type'] = 'varchar(250)';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['function']['null'] = '';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['function']['key'] = '';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['function']['default'] = 'NULL';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['function']['extra'] = '';
// webtechglobal_log - sqlresult
$c2p_tables_array['tables']['webtechglobal_log']['columns']['sqlresult']['type'] = 'blob';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['sqlresult']['null'] = '';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['sqlresult']['key'] = '';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['sqlresult']['default'] = 'NULL';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['sqlresult']['extra'] = '';
// webtechglobal_log - sqlquery
$c2p_tables_array['tables']['webtechglobal_log']['columns']['sqlquery']['type'] = 'varchar(45)';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['sqlquery']['null'] = '';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['sqlquery']['key'] = '';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['sqlquery']['default'] = 'NULL';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['sqlquery']['extra'] = '';
// webtechglobal_log - sqlerror
$c2p_tables_array['tables']['webtechglobal_log']['columns']['sqlerror']['type'] = 'mediumtext';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['sqlerror']['null'] = '';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['sqlerror']['key'] = '';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['sqlerror']['default'] = 'NULL';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['sqlerror']['extra'] = '';
// webtechglobal_log - wordpresserror
$c2p_tables_array['tables']['webtechglobal_log']['columns']['wordpresserror']['type'] = 'mediumtext';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['wordpresserror']['null'] = '';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['wordpresserror']['key'] = '';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['wordpresserror']['default'] = 'NULL';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['wordpresserror']['extra'] = '';
// webtechglobal_log - screenshoturl
$c2p_tables_array['tables']['webtechglobal_log']['columns']['screenshoturl']['type'] = 'varchar(500)';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['screenshoturl']['null'] = '';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['screenshoturl']['key'] = '';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['screenshoturl']['default'] = 'NULL';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['screenshoturl']['extra'] = '';
// webtechglobal_log - userscomment
$c2p_tables_array['tables']['webtechglobal_log']['columns']['userscomment']['type'] = 'mediumtext';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['userscomment']['null'] = '';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['userscomment']['key'] = '';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['userscomment']['default'] = 'NULL';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['userscomment']['extra'] = '';
// webtechglobal_log - page
$c2p_tables_array['tables']['webtechglobal_log']['columns']['page']['type'] = 'varchar(45)';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['page']['null'] = '';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['page']['key'] = '';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['page']['default'] = 'NULL';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['page']['extra'] = '';
// webtechglobal_log - version
$c2p_tables_array['tables']['webtechglobal_log']['columns']['version']['type'] = 'varchar(45)';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['version']['null'] = '';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['version']['key'] = '';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['version']['default'] = 'NULL';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['version']['extra'] = '';
// webtechglobal_log - panelid
$c2p_tables_array['tables']['webtechglobal_log']['columns']['panelid']['type'] = 'varchar(45)';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['panelid']['null'] = '';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['panelid']['key'] = '';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['panelid']['default'] = 'NULL';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['panelid']['extra'] = '';
// webtechglobal_log - panelname
$c2p_tables_array['tables']['webtechglobal_log']['columns']['panelname']['type'] = 'varchar(45)';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['panelname']['null'] = '';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['panelname']['key'] = '';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['panelname']['default'] = 'NULL';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['panelname']['extra'] = '';
// webtechglobal_log - tabscreenid
$c2p_tables_array['tables']['webtechglobal_log']['columns']['tabscreenid']['type'] = 'varchar(45)';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['tabscreenid']['null'] = '';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['tabscreenid']['key'] = '';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['tabscreenid']['default'] = 'NULL';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['tabscreenid']['extra'] = '';
// webtechglobal_log - tabscreenname
$c2p_tables_array['tables']['webtechglobal_log']['columns']['tabscreenname']['type'] = 'varchar(45)';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['tabscreenname']['null'] = '';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['tabscreenname']['key'] = '';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['tabscreenname']['default'] = 'NULL';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['tabscreenname']['extra'] = '';
// webtechglobal_log - dump
$c2p_tables_array['tables']['webtechglobal_log']['columns']['dump']['type'] = 'longblob';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['dump']['null'] = '';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['dump']['key'] = '';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['dump']['default'] = 'NULL';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['dump']['extra'] = '';
// webtechglobal_log - ipaddress
$c2p_tables_array['tables']['webtechglobal_log']['columns']['ipaddress']['type'] = 'varchar(45)';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['ipaddress']['null'] = '';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['ipaddress']['key'] = '';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['ipaddress']['default'] = 'NULL';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['ipaddress']['extra'] = '';
// webtechglobal_log - userid
$c2p_tables_array['tables']['webtechglobal_log']['columns']['userid']['type'] = 'int(11)';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['userid']['null'] = '';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['userid']['key'] = '';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['userid']['default'] = 'NULL';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['userid']['extra'] = '';
// webtechglobal_log - comment
$c2p_tables_array['tables']['webtechglobal_log']['columns']['comment']['type'] = 'mediumtext';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['comment']['null'] = '';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['comment']['key'] = '';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['comment']['default'] = 'NULL';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['comment']['extra'] = '';
// webtechglobal_log - type
$c2p_tables_array['tables']['webtechglobal_log']['columns']['type']['type'] = 'varchar(45)';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['type']['null'] = '';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['type']['key'] = '';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['type']['default'] = 'NULL';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['type']['extra'] = '';
// webtechglobal_log - category
$c2p_tables_array['tables']['webtechglobal_log']['columns']['category']['type'] = 'varchar(45)';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['category']['null'] = '';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['category']['key'] = '';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['category']['default'] = 'NULL';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['category']['extra'] = '';
// webtechglobal_log - action
$c2p_tables_array['tables']['webtechglobal_log']['columns']['action']['type'] = 'varchar(45)';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['action']['null'] = '';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['action']['key'] = '';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['action']['default'] = 'NULL';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['action']['extra'] = '';
// webtechglobal_log - priority
$c2p_tables_array['tables']['webtechglobal_log']['columns']['priority']['type'] = 'varchar(45)';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['priority']['null'] = '';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['priority']['key'] = '';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['priority']['default'] = 'NULL';
$c2p_tables_array['tables']['webtechglobal_log']['columns']['priority']['extra'] = '';              
?>