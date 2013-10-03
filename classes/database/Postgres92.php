<?php

/**
 * PostgreSQL 9.2 support
 *
 * $Id: Postgres92.php
 */

include_once('./classes/database/Postgres.php');

class Postgres92 extends Postgres {

    var $major_version = 9.2;


    /**
     * Returns all available process information.
     * @param $database (optional) Find only connections to specified database
     * @return A recordset
     */
    function getProcesses($database = null) {
        if ($database === null)
            $sql = "SELECT * FROM pg_catalog.pg_stat_activity ORDER BY datname, usename, pid";
        else {
            $this->clean($database);
        $sql = "
                SELECT * FROM pg_catalog.pg_stat_activity
                WHERE datname='{$database}' ORDER BY usename, pid";
        }

        return $this->selectSet($sql);
    }

    /**
     * Retrieves information for all tablespaces
     * @param $all Include all tablespaces (necessary when moving objects back to the default space)
     * @return A recordset
     */
    function getTablespaces($all = false) {
            global $conf;

            $sql = "SELECT spcname, pg_catalog.pg_get_userbyid(spcowner) AS spcowner, pg_tablespace_location(oid) as spclocation,
                (SELECT description FROM pg_catalog.pg_shdescription pd WHERE pg_tablespace.oid=pd.objoid) AS spccomment
                                    FROM pg_catalog.pg_tablespace";

            if (!$conf['show_system'] && !$all) {
                    $sql .= ' WHERE spcname NOT LIKE $$pg\_%$$';
            }

            $sql .= " ORDER BY spcname";

            return $this->selectSet($sql);
    }


    /**
     * Retrieves a tablespace's information
     * @return A recordset
     */
    function getTablespace($spcname) {
            $this->clean($spcname);

            $sql = "SELECT spcname, pg_catalog.pg_get_userbyid(spcowner) AS spcowner, pg_tablespace_location(oid) as spclocation,
                (SELECT description FROM pg_catalog.pg_shdescription pd WHERE pg_tablespace.oid=pd.objoid) AS spccomment
                                    FROM pg_catalog.pg_tablespace WHERE spcname='{$spcname}'";

            return $this->selectSet($sql);
    }


}

?>
