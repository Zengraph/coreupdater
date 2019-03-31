<?php
/**
 * Copyright (C) 2019 thirty bees
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@thirtybees.com so we can send you a copy immediately.
 *
 * @author    thirty bees <contact@thirtybees.com>
 * @copyright 2019 thirty bees
 * @license   Open Software License (OSL 3.0)
 */

namespace CoreUpdater;
use \Translate;

if (!defined('_TB_VERSION_')) {
    exit;
}

/**
 * Class ExtraColumn
 *
 * Difference in column AUTO_INCREMENT settings
 *
 * @since 1.1.0
 */
class DifferentAutoIncrement implements SchemaDifference
{
    private $table;
    private $column;
    private $currentColumn;

    public function __construct(TableSchema $table, ColumnSchema $column, ColumnSchema $currentColumn)
    {
        $this->table = $table;
        $this->column = $column;
        $this->currentColumn = $currentColumn;
    }

    public function describe()
    {
        $table = $this->table->getName();
        $col = $this->column->getName();
        return $this->column->isAutoIncrement()
            ? sprintf(Translate::getModuleTranslation('coreupdater', 'Column `%1$s`.`%2$s` should be marked as AUTO_INCREMENT', 'coreupdater'), $table, $col)
            : sprintf(Translate::getModuleTranslation('coreupdater', 'Column `%1$s`.`%2$s` should NOT be marked as AUTO_INCREMENT', 'coreupdater'), $table, $col);
    }
}
