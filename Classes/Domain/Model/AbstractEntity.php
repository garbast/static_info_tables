<?php
namespace SJBR\StaticInfoTables\Domain\Model;

/*
 *  Copyright notice
 *
 *  (c) 2013-2023 Stanislas Rolland <typo3AAAA(arobas)sjbr.ca>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 */

use SJBR\StaticInfoTables\Utility\LocalizationUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Abstract model for static entities
 */
class AbstractEntity extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * Name of the table of this model
     *
     * @var string
     */
    protected $tableName = '';

    /**
     * Localized name of the entity
     *
     * @var string
     */
    protected $nameLocalized = '';

    /**
     * Sets the localized name of the entity
     *
     * @param string $nameLocalized
     *
     * @return void
     */
    public function setNameLocalized($nameLocalized)
    {
        $this->nameLocalized = $nameLocalized;
    }

    /**
     * Gets the localized name of the entity
     *
     * @return string
     */
    public function getNameLocalized()
    {
        $language = LocalizationUtility::getCurrentLanguage();
        $labelFields = LocalizationUtility::getLabelFields($this->tableName, $language);
        foreach ($labelFields as $labelField => $map) {
            if ($this->_hasProperty($map['mapOnProperty'] ?? '')) {
                $value = $this->_getProperty($map['mapOnProperty'] ?? '');
                if ($value) {
                    $this->nameLocalized = $value;
                    break;
                }
            }
        }
        return $this->nameLocalized;
    }

    /**
     * Gets the table name
     *
     * @return string
     */
    public function getTableName()
    {
        return $this->tableName;
    }
}