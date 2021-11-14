<?php
namespace SJBR\StaticInfoTables\Domain\Repository;

/*
 *  Copyright notice
 *
 *  (c) 2011-2012 Armin Rüdiger Vieweg <info@professorweb.de>
 *  (c) 2013-2021 Stanislas Rolland <typo3AAAA(arobas)sjbr.ca>
 *
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

use SJBR\StaticInfoTables\Domain\Model\Country;
use SJBR\StaticInfoTables\Domain\Model\Territory;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
/**
 * Repository for \SJBR\StaticInfoTables\Domain\Model\Territory
 */
class TerritoryRepository extends AbstractEntityRepository
{
    /**
     * ISO keys for this static table
     *
     * @var array
     */
    protected $isoKeys = ['tr_iso_nr'];

    /**
     * Finds the territory within which a country is located
     *
     * @param Country $country
     * @return QueryResultInterface|array
     */
    public function findByCountry(Country $country)
    {
        $query = $this->createQuery();
        $query->matching(
            $query->equals('unCodeNumber', $country->getParentTerritoryUnCodeNumber())
        );
        return $query->execute();
    }

    /**
     * Finds territories that have a given territory as parent territory
     *
     * @param Territory $territory
     * @return QueryResultInterface|array
     */
    public function findByTerritory(Territory $territory)
    {
        $query = $this->createQuery();
        $query->matching(
            $query->equals('parentTerritoryUnCodeNumber', $territory->getUnCodeNumber())
        );
        return $query->execute();
    }

    /**
     * Finds all territories within a territory recursively
     *
     * @param Territory $territory
     * @param array $unCodeNumbers array of UN territory code numbers used for recursive retrieval of sub-territories
     * @return QueryResultInterface|array
     */
    public function findWithinTerritory(Territory $territory, &$unCodeNumbers = [])
    {
        if (empty($unCodeNumbers)) {
            $unCodeNumbers = [$territory->getUnCodeNumber()];
        }
        $initialCount = count($unCodeNumbers);

        $query = $this->createQuery();
        $query->matching(
            $query->in('parentTerritoryUnCodeNumber', $unCodeNumbers)
        );
        $territories = $query->execute();

        // Get UN code numbers of new subterritories
        foreach ($territories as $subterritory) {
            $unCodeNumbers[] = $subterritory->getUnCodeNumber();
        }
        $unCodeNumbers = array_unique($unCodeNumbers);

        // Call recursively until no additional subterritories are found
        if (count($unCodeNumbers) > $initialCount) {
            $territories = $this->findWithinTerritory($territory, $unCodeNumbers);
        }

        return $territories;
    }
}