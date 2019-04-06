<?php
/**
 * Copyright (C) 2019 thirty bees
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@thirtybees.com so we can send you a copy immediately.
 *
 * @author    thirty bees <contact@thirtybees.com>
 * @copyright 2019 thirty bees
 * @license   Open Software License (OSL 3.0)
 */

namespace CoreUpdater;
use \ObjectModel;

if (!defined('_TB_VERSION_')) {
    exit;
}

/**
 * Class ColumnSchema
 *
 * This class holds information about specific database column
 *
 * @since 1.1.0
 */
class ColumnSchema
{
    /**
     * @var string column name
     */
    protected $columnName;

    /**
     * @var string full data type, such as 'int(11) unsigned'
     */
    protected $dataType;

    /**
     * @var boolean contains true if this column can hold NULL value.
     *
     * If this property is not a boolean value, then nullable property
     * is determined using other heuristics
     */
    protected $nullable = null;

    /**
     * @var boolean auto increment flag
     */
    protected $autoIncrement = false;

    /**
     * @var string column default value.
     *
     * PHP `null` value represents no default value exists, while static::DEFAULT_NULL
     * means that column has default value NULL (sql null)
     */
    protected $defaultValue = null;

    /**
     * @var DatabaseCharset default character set, such as utf8mb4
     */
    protected $charset;


    /**
     * TableSchema constructor.
     *
     * @param string $columnName name of the database column
     */
    public function __construct($columnName)
    {
        $this->columnName = $columnName;
        $this->charset = new DatabaseCharset();
    }

    /**
     * Returns column name
     *
     * @return string
     */
    public function getName()
    {
        return $this->columnName;
    }

    /**
     * Set full database type, for example 'decimal(20,6)'
     *
     * @param string $dataType database type
     */
    public function setDataType($dataType)
    {
        $this->dataType = $dataType;
    }

    /**
     * Returns full database type
     *
     * @return string
     */
    public function getDataType()
    {
        return $this->dataType;
    }

    /**
     * Returns true, if this column can hold database NULL value.
     *
     * @return bool
     */
    public function isNullable()
    {
        // explicitly set nullable
        if (! is_null($this->nullable)) {
            return $this->nullable;
        }
        // auto increments should not be nullable
        if ($this->autoIncrement) {
            return false;
        }
        // if field has default value, it's usually NOT NULL
        if (! is_null($this->defaultValue)) {
            return ($this->defaultValue === ObjectModel::DEFAULT_NULL);
        }
        return true;
    }

    /**
     * Explicitly sets ability to hold NULL value
     *
     * @param bool $nullable
     */
    public function setNullable($nullable)
    {
        $this->nullable = $nullable;
    }

    /**
     * Returns true, if this column is AUTO_INCREMENT
     *
     * @return bool
     */
    public function isAutoIncrement()
    {
        return $this->autoIncrement;
    }

    /**
     * Sets AUTO_INCREMENT flag
     *
     * @param bool $autoIncrement
     */
    public function setAutoIncrement($autoIncrement)
    {
        $this->autoIncrement = $autoIncrement;
    }

    /**
     * Returns default column value. If default value is sql NULL, then return value is php null
     *
     * Do not use this function to test whether default value exists, because this test would
     * fail for any field with DEFAULT NULL value
     *
     * @return string
     */
    public function getDefaultValue()
    {
        return $this->defaultValue === ObjectModel::DEFAULT_NULL ? null : $this->defaultValue;
    }

    /**
     * returns true, if table has default value (including NULL)
     *
     * @return bool
     */
    public function hasDefaultValue()
    {
        return !is_null($this->defaultValue);
    }

    /**
     * Sets column default value. PHP null means no default value exists. If column should have
     * DEFAULT NULL, then pass ColumnSchema::DEFAULT_NULL
     *
     * @param string $defaultValue
     */
    public function setDefaultValue($defaultValue)
    {
        if (is_null($defaultValue)) {
           $this->defaultValue = null;
        } else if (is_string($defaultValue)) {
            $this->defaultValue = $defaultValue;
        } else {
            $this->defaultValue = "$defaultValue";
        }
    }

    /**
     * Sets character set and collation
     *
     * @param DatabaseCharset $charset character set, ie. utf8mb4
     */
    public function setCharset(DatabaseCharset $charset)
    {
        $this->charset = $charset;
    }

    /**
     * Returns character set for this column, or null if none is set
     *
     * @return DatabaseCharset
     */
    public function getCharset()
    {
        return $this->charset;
    }

    /**
     * Returns DDL statement to create this column
     *
     * @param TableSchema $table
     * @return string
     */
    public function getDDLStatement(TableSchema $table)
    {
        $col =  '`' . $this->getName() . '` ' . $this->getDataType();
        $charset = $this->getCharset()->getCharset();
        $collate = $this->getCharset()->getCollate();
        if ($charset && $collate && $this->getCharset()->isDefaultCollate()) {
            if ($table->getCharset()->getCharset() !== $charset) {
                $col .= ' CHARACTER SET ' . $charset;
            }
        } else if ($collate) {
            $col .= ' COLLATE ' . $collate;
        }
        if (! $this->isNullable()) {
            $col .= ' NOT NULL';
        }
        if ($this->hasDefaultValue()) {
            $default = $this->getDefaultValue();
            if (is_null($default)) {
                if (! in_array($this->getDataType(), ['text', 'mediumtext', 'longtext'])) {
                    $col .= ' DEFAULT NULL';
                }
            } elseif ($default === ObjectModel::DEFAULT_CURRENT_TIMESTAMP) {
                $col .= ' DEFAULT CURRENT_TIMESTAMP';
            } else {
                $col .= ' DEFAULT \'' . $default . '\'';
            }
        }
        if ($this->isAutoIncrement()) {
            $col .= ' AUTO_INCREMENT';
        }
        return $col;
    }
}
