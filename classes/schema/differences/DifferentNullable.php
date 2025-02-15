<?php
/**
 * Copyright (C) 2019 thirty bees
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@thirtybees.com so we can send you a copy immediately.
 *
 * @author    thirty bees <modules@thirtybees.com>
 * @copyright 2019 thirty bees
 * @license   Academic Free License (AFL 3.0)
 */

namespace CoreUpdater;
use \Translate;

if (!defined('_TB_VERSION_')) {
    exit;
}

/**
 * Class ExtraColumn
 *
 * Difference in column's nullable settings
 *
 * @version 1.1.0 Initial version.
 */
class DifferentNullable implements SchemaDifference
{
    private $table;
    private $column;
    private $currentColumn;

    /**
     * DifferentNullable constructor.
     *
     * @param TableSchema $table
     * @param ColumnSchema $column
     * @param ColumnSchema $currentColumn
     *
     * @version 1.1.0 Initial version.
     */
    public function __construct(TableSchema $table, ColumnSchema $column, ColumnSchema $currentColumn)
    {
        $this->table = $table;
        $this->column = $column;
        $this->currentColumn = $currentColumn;
    }

    /**
     * Return description of the difference.
     *
     * @return string
     *
     * @version 1.1.0 Initial version.
     */
    public function describe()
    {
        $table = $this->table->getName();
        $col = $this->column->getName();
        return $this->column->isNullable()
            ? sprintf(Translate::getModuleTranslation('coreupdater', 'Column `%1$s`.`%2$s` should be marked as NULL', 'coreupdater'), $table, $col)
            : sprintf(Translate::getModuleTranslation('coreupdater', 'Column `%1$s`.`%2$s` should be marked as NOT NULL', 'coreupdater'), $table, $col);
    }
}
