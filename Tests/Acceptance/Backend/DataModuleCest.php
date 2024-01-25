<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\Typo3Data\Tests\Acceptance\Backend;

use JobRouter\AddOn\Typo3Data\Tests\Acceptance\Support\BackendTester;
use TYPO3\CMS\Core\Information\Typo3Version;

final class DataModuleCest
{
    private const DATA_MODULE_SELECTOR = 'jobrouter_data';

    public function _before(BackendTester $I): void
    {
        $I->loginAs('admin');
    }

    public function _after(BackendTester $I): void
    {
        $I->truncateDataTables();
    }

    public function onVeryFirstCallModuleShowsHintThatNoTableLinksAreFound(BackendTester $I): void
    {
        $I->click($this->moduleIdentifier());
        $I->switchToContentFrame();
        $I->canSee('JobData Table Links', 'h1');

        $I->canSee('No table links found');
    }

    public function onFirstCallModuleClickOnCreateNewTableLinkShowsCreateForm(BackendTester $I): void
    {
        $I->click($this->moduleIdentifier());
        $I->switchToContentFrame();
        $I->canSee('JobData Table Links', 'h1');

        $I->click('Create new table link');
        $I->waitForText('Create new JobData Table Link on root level');
    }

    public function simpleSynchronisationTableLinkIsDisplayedCorrectly(BackendTester $I): void
    {
        $I->importXmlDatabaseFixture('tableLinkDefinitionWithSimpleSynchronisation.xml');

        $I->click($this->moduleIdentifier());
        $I->switchToContentFrame();
        $I->canSee('JobData Table Links', 'h1');
        $I->canSee('Simple synchronisation', 'h2');

        $I->canSeeElement('#jobrouter-data-table-list');
        $I->canSee('Name for simple sync', '#jobrouter-data-list-name-1');
        $I->canSee('handle_simple_sync', '#jobrouter-data-list-handle-1');
        $I->canSee('Mockserver', '#jobrouter-data-list-connection-name-1');
        $I->canSee('8BD9BABA-7E2A-9C98-DA8B-41CDE4BD3412', '#jobrouter-data-list-table-guid-1');
        $I->canSee('Text field with maximum length', '#jobrouter-data-list-columns-1');
        $I->canSee('Text field with maximum length', '#jobrouter-data-list-columns-1');
        $I->canSee('Text field with undefined maximum length', '#jobrouter-data-list-columns-1');
        $I->canSee('Number field', '#jobrouter-data-list-columns-1');
        $I->canSee('field_without_label', '#jobrouter-data-list-columns-1');
    }

    public function whenSimpleSynchronisationTableIsAvailableAClickOnTheEditButtonOpensTheEditForm(BackendTester $I): void
    {
        $I->importXmlDatabaseFixture('tableLinkDefinitionWithSimpleSynchronisation.xml');

        $I->click($this->moduleIdentifier());
        $I->switchToContentFrame();
        $I->canSee('JobData Table Links', 'h1');
        $I->canSee('Simple synchronisation', 'h2');

        $I->canSeeElement('#jobrouter-data-table-list');
        $I->click('#jobrouter-data-list-edit-1');
        $I->waitForText('Edit JobData Table Link "Name for simple sync" on root level');
    }

    private function moduleIdentifier(): string
    {
        if ((new Typo3Version())->getMajorVersion() < 12) {
            return '#' . self::DATA_MODULE_SELECTOR;
        }
        return \sprintf('[data-moduleroute-identifier="%s"]', self::DATA_MODULE_SELECTOR);
    }

    // Test is not used as testing framework version 6.14 uses non-composer mode for extensions.
    // Now "wrong" path for the key file is used in test
    // See: https://github.com/TYPO3/testing-framework/commit/1e91755df7037d5157bb62c57626596f44103237
    //    public function whenSimpleSynchronisationTableIsAvailableAClickOnTheCheckButtonShowsSuccessfulNotification(BackendTester $I): void
    //    {
    //        $I->importXmlDatabaseFixture('tableLinkDefinitionWithSimpleSynchronisation.xml');
    //        $I->createMockServerExpectationForConnection();
    //        $I->createMockServerExpectationForGetJobDataDataSets('8BD9BABA-7E2A-9C98-DA8B-41CDE4BD3412');
    //
    //        $I->click(self::DATA_MODULE_SELECTOR);
    //        $I->switchToContentFrame();
    //        $I->canSee('JobData Table Links', 'h1');
    //        $I->canSee('Simple synchronisation', 'h2');
    //
    //        $I->canSeeElement('#jobrouter-data-table-list');
    //        $I->click('#jobrouter-data-list-check-1');
    //        $I->switchToMainFrame();
    //        $I->waitForText('JobData table accessed successfully');
    //    }
}
