<?php
namespace SJBR\StaticInfoTables\Utility;

/*
 *  Copyright notice
 *
 *  (c) 2013-2022 StanislasRolland <typo3AAAA(arobas)sjbr.ca>
 *  All rights reserved
 *
 *  This script is part of the Typo3 project. The Typo3 project is
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

use Doctrine\DBAL\DBALException;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Schema\SchemaMigrator;
use TYPO3\CMS\Core\Database\Schema\SqlReader;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extensionmanager\Utility\InstallUtility;

/**
 * Utility used by the update script of the base extension and of the language packs
 */
class DatabaseUpdateUtility
{
    /**
     * @var string Name of the extension this class belongs to
     */
    protected $extensionName = 'StaticInfoTables';

    /**
     * Do the language pack update
     *
     * @param string $extensionKey: extension key of the language pack
     * @return void
     */
    public function doUpdate($extensionKey)
    {
        $result = [];
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        $insertStatements = [];
        $updateStatements = [];
        $extPath = ExtensionManagementUtility::extPath($extensionKey);
        $statements = explode(LF, @file_get_contents($extPath . 'ext_tables_static+adt.sql'));

        foreach ($statements as $statement) {
            $statement = trim($statement);
            // Only handle update statements and extract the table at the same time. Extracting
            // the table name is required to perform the inserts on the right connection.
            if (preg_match('/^UPDATE\\s+`?(\\w+)`?(.*)/i', $statement, $matches)) {
                list(, $tableName, $sqlFragment) = $matches;
                $updateStatements[$tableName][] = sprintf(
                    'UPDATE %s %s',
                    $connectionPool->getConnectionForTable($tableName)->quoteIdentifier($tableName),
                    rtrim($sqlFragment, ';')
                );
            }
        }
        foreach ($updateStatements as $tableName => $perTableStatements) {
            $connection = $connectionPool->getConnectionForTable($tableName);
            foreach ((array)$perTableStatements as $statement) {
                try {
                    $connection->executeUpdate($statement);
                    $result[$statement] = '';
                } catch (DBALException $e) {
                    $result[$statement] = $e->getPrevious()->getMessage();
                }
            }
        }
    }

    /**
     * Imports a static tables SQL File (ext_tables_static+adt)
     *
     * @param string $extensionSitePath
     * @return void
     */
    public function importStaticSqlFile($extensionSitePath)
    {
        $extTablesStaticSqlFile = $extensionSitePath . 'ext_tables_static+adt.sql';
        $extTablesStaticSqlContent = '';
        if (file_exists($extTablesStaticSqlFile)) {
            $extTablesStaticSqlContent .= GeneralUtility::getUrl($extTablesStaticSqlFile);
        }
        if ($extTablesStaticSqlContent !== '') {
            $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
            // Drop all tables
            foreach (array_keys($GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['static_info_tables']['tables'] ?? []) as $tableName) {
                $connection = $connectionPool->getConnectionForTable($tableName);
                try {
                    $connection->executeUpdate($connection->getDatabasePlatform()
                        ->getDropTableSQL($connection->quoteIdentifier($tableName)));
                } catch (TableNotFoundException $e) {
                    // Ignore table not found exception
                }
            }
            // Re-create all tables
            $this->processDatabaseUpdates(GeneralUtility::camelCaseToLowerCaseUnderscored($this->extensionName));
            $installTool = GeneralUtility::makeInstance(InstallUtility::class);
            $installTool->importStaticSql($extTablesStaticSqlContent);
        }
    }

    /**
     * Processes the tables SQL File (ext_tables)
     *
     * @param string $extensionKey
     * @return void
     */
    public function processDatabaseUpdates($extensionKey)
    {
        $extensionSitePath = ExtensionManagementUtility::extPath($extensionKey);
        $extTablesSqlFile = $extensionSitePath . 'ext_tables.sql';
        $extTablesSqlContent = '';
        if (file_exists($extTablesSqlFile)) {
            $extTablesSqlContent .= GeneralUtility::getUrl($extTablesSqlFile);
        }
        if ($extTablesSqlContent !== '') {
            	// Prevent the DefaultTcaSchema from enriching our definitions
                $tcaBackup = $GLOBALS['TCA'];
                $GLOBALS['TCA'] = [];
                $sqlReader = GeneralUtility::makeInstance(SqlReader::class);
                $schemaMigrator = GeneralUtility::makeInstance(SchemaMigrator::class);
                $sqlStatements = [];
                $sqlStatements[] = $extTablesSqlContent;
                $sqlStatements = $sqlReader->getCreateTableStatementArray(implode(LF . LF, array_filter($sqlStatements)));
                $updateStatements = $schemaMigrator->getUpdateSuggestions($sqlStatements);
                $updateStatements = array_merge_recursive(...array_values($updateStatements));
                $selectedStatements = [];
                foreach (['add', 'change', 'create_table', 'change_table'] as $action) {
                    if (empty($updateStatements[$action])) {
                        continue;
                    }
                    $selectedStatements = array_merge(
                        $selectedStatements,
                        array_combine(
                            array_keys($updateStatements[$action]),
                            array_fill(0, count($updateStatements[$action]), true)
                        )
                    );
                }
                $schemaMigrator->migrate($sqlStatements, $selectedStatements);
                $GLOBALS['TCA'] = $tcaBackup;
        }
    }
}