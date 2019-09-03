<?php
declare(strict_types = 1);

namespace Brotkrueml\JobRouterData\Tests\Unit\ViewHelpers;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;
use TYPO3Fluid\Fluid\View\TemplateView;

class ViewHelperTestCase extends TestCase
{
    protected const VIEWHELPER_NAMESPACE = '{namespace jobRouterData=Brotkrueml\JobRouterData\ViewHelpers}';

    /** @var vfsStreamDirectory */
    protected $root;

    /** @var TemplateView */
    protected $view;

    public function setUp(): void
    {
        $this->root = vfsStream::setup('test-dir');
        $this->view = new TemplateView();
    }

    protected function renderTemplate(string $template, array $variables = []): string
    {
        \file_put_contents(vfsStream::url('test-dir') . '/template.html', self::VIEWHELPER_NAMESPACE . $template);

        $this->view->getTemplatePaths()->setTemplatePathAndFilename(vfsStream::url('test-dir') . '/template.html');

        if (!empty($variables)) {
            $this->view->assignMultiple($variables);
        }

        return $this->view->render();
    }
}
