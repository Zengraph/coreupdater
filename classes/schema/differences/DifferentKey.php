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
 * Represents difference in database key definition
 *
 * @version 1.1.0 Initial version.
 */
class DifferentKey implements SchemaDifference
{
    /**
     * @var TableSchema table
     */
    protected $table;

    /**
     * @var TableKey expected key definition
     */
    protected $key;

    /**
     * @var TableKey current key definition
     */
    protected $currentKey;

    /**
     * DifferentKey constructor.
     *
     * @param TableSchema $table
     * @param TableKey $key
     * @param TableKey $currentKey
     *
     * @version 1.1.0 Initial version.
     */
    public function __construct(TableSchema $table, TableKey $key, TableKey $currentKey)
    {
        $this->table = $table;
        $this->key = $key;
        $this->currentKey = $currentKey;
    }

    /**
     * Return description of the difference.
     *
     * @return string
     *
     * @version 1.1.0 Initial version.
     */
    function describe()
    {
        return sprintf(
            Translate::getModuleTranslation('coreupdater', 'Different %1$s in table `%2$s`', 'coreupdater'),
            $this->key->describeKey(),
            $this->table->getName()
        );
    }
}
