<?php
declare(strict_types=1);

namespace SJBR\StaticInfoTables\EventListener\Bootstrap;

/*
 *  Copyright notice
 *
 *  (c) 2023 Stanislas Rolland <typo3AAAA(arobas)sjbr.ca>
 *  All rights reserved
 *
 *  This script is part of the Typo3 project. The Typo3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
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

use SJBR\StaticInfoTables\Cache\CachedClassLoader;
use TYPO3\CMS\Core\Core\Event\BootCompletedEvent;

/**
 * Order records according to language field of current language
 */
final class BootCompletedEventListener
{
    /**
     * @param BootCompletedEvent $event
     * @return void
     */
    public function __invoke(BootCompletedEvent $event): void
    {
    	CachedClassLoader::registerAutoloader();
    }

}