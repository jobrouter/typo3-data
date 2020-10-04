<?php
declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Tests\Unit\ViewHelpers;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3Fluid\Fluid\View\TemplateView;

class ViewHelperTestCase extends TestCase
{
    protected const VIEWHELPER_NAMESPACE = '{namespace jobRouterData=Brotkrueml\JobRouterData\ViewHelpers}';

    /** @var vfsStreamDirectory */
    protected $root;

    /** @var TemplateView */
    protected $view;

    protected function setUp(): void
    {
        $this->root = vfsStream::setup('test-dir');
        $this->view = new TemplateView();
    }

    protected function renderTemplate(string $template, array $variables = [])
    {
        \file_put_contents(vfsStream::url('test-dir') . '/template.html', self::VIEWHELPER_NAMESPACE . $template);

        $this->view->getTemplatePaths()->setTemplatePathAndFilename(vfsStream::url('test-dir') . '/template.html');

        if (!empty($variables)) {
            $this->view->assignMultiple($variables);
        }

        return $this->view->render();
    }

    protected function initialiseLanguageServiceStub()
    {
        $GLOBALS['LANG'] = $this->createStub(LanguageService::class);

        return $GLOBALS['LANG'];
    }
}
