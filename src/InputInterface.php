<?php

namespace Lagdo\DbAdmin\Driver;

interface InputInterface
{
    /**
     * Get the query table/trigger name
     *
     * @return string
     */
    public function getTable();

    /**
     * Return true if a table was defined
     *
     * @return bool
     */
    public function hasTable();

    /**
     * Get the select query fields
     *
     * @return array
     */
    public function getSelect();

    /**
     * Get the query filters
     *
     * @return array
     */
    public function getWhere();

    /**
     * Get the query limit
     *
     * @return int
     */
    public function getLimit();

    /**
     * Get the query fields
     *
     * @return array
     */
    public function getFields();

    /**
     * Get the auto increment step
     *
     * @return string
     */
    public function getAutoIncrementStep();

    /**
     * Get the auto increment field
     *
     * @return string
     */
    public function getAutoIncrementField();

    /**
     * Get the ??
     *
     * @return array
     */
    public function getChecks();

    /**
     * Get the ??
     *
     * @return bool
     */
    public function getOverwrite();
}
