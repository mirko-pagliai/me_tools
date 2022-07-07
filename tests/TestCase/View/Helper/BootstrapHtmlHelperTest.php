<?php
declare(strict_types=1);

/**
 * This file is part of me-tools.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright   Copyright (c) Mirko Pagliai
 * @link        https://github.com/mirko-pagliai/me-tools
 * @license     https://opensource.org/licenses/mit-license.php MIT License
 */
namespace MeTools\Test\TestCase\View\Helper;

use ErrorException;
use MeTools\TestSuite\HelperTestCase;

/**
 * HtmlHelperTest class
 * @property \MeTools\View\Helper\BootstrapHtmlHelper $Helper
 */
class BootstrapHtmlHelperTest extends HelperTestCase
{
    /**
     * Tests for `__call()` method
     * @test
     * @uses \MeTools\View\Helper\BootstrapHtmlHelper::__call()
     */
    public function testCall(): void
    {
        //The `h3()` method should not exist, otherwise the `__call()` method
        //  will not be called
        $this->assertFalse(method_exists($this->Helper, 'h3'));

        $expected = '<h3 class="my-class">my h3 text</h3>';
        $result = $this->Helper->h3('my h3 text', ['class' => 'my-class']);
        $this->assertSame($expected, $result);

        $expected = '<h3 class="my-class"><i class="fas fa-home"> </i> my h3 text</h3>';
        $result = $this->Helper->h3('my h3 text', ['class' => 'my-class', 'icon' => 'home']);
        $this->assertSame($expected, $result);

        $expected = '<h3 class="my-class">my h3 text <i class="fas fa-home"> </i></h3>';
        $result = $this->Helper->h3('my h3 text', ['class' => 'my-class', 'icon' => 'home', 'icon-align' => 'right']);
        $this->assertSame($expected, $result);

        //With a no existing method
        $this->expectException(ErrorException::class);
        $this->expectExceptionMessage('Method `' . get_class($this->Helper) . '::noExistingMethod()` does not exist');
        /** @phpstan-ignore-next-line */
        $this->Helper->noExistingMethod(null, null, null);
    }

    /**
     * Test for `image()` and `img()` methods
     * @test
     * @uses \MeTools\View\Helper\BootstrapHtmlHelper::image()
     * @uses \MeTools\View\Helper\BootstrapHtmlHelper::img()
     */
    public function testImage(): void
    {
        $expected = '<img src="/img/image.gif" alt="image.gif" class="img-fluid my-class"/>';
        $result = $this->Helper->image('image.gif', ['class' => 'my-class']);
        $this->assertSame($expected, $result);

        //With `img()` method
        $result = $this->Helper->img('image.gif', ['class' => 'my-class']);
        $this->assertSame($expected, $result);

        $expected = '<img src="/img/image.gif" alt="my-alt" class="img-fluid"/>';
        $result = $this->Helper->image('image.gif', ['alt' => 'my-alt']);
        $this->assertSame($expected, $result);

        $expected = '<img src="http://url/image.gif" alt="image.gif" class="img-fluid"/>';
        $result = $this->Helper->image('http://url/image.gif');
        $this->assertSame($expected, $result);

        $this->loadPlugins(['TestPlugin' => []]);
        $expected = '<img src="/pages" alt="pages" class="img-fluid"/>';
        $result = $this->Helper->image(['controller' => 'Pages', 'plugin' => 'TestPlugin']);
        $this->assertSame($expected, $result);

        $expected = '<a href="/pages"><img src="/img/image.gif" alt="image.gif" class="img-fluid"/></a>';
        $result = $this->Helper->image('image.gif', ['url' => ['controller' => 'Pages', 'plugin' => 'TestPlugin']]);
        $this->assertSame($expected, $result);
    }

    /**
     * Test for `button()` method
     * @test
     * @uses \MeTools\View\Helper\BootstrapHtmlHelper::button()
     */
    public function testButton(): void
    {
        $title = 'My title';

        $expected = '<a href="http://link" class="btn btn-light" role="button" title="my-title">My title</a>';
        $result = $this->Helper->button($title, 'http://link', ['title' => 'my-title']);
        $this->assertSame($expected, $result);

        $expected = '<a href="#" class="btn btn-light" role="button" title="My title">My title <i class="fas fa-home"> </i></a>';
        $result = $this->Helper->button($title, '#', ['icon' => 'home', 'icon-align' => 'right']);
        $this->assertSame($expected, $result);

        //Code on text
        $expected = '<a href="#" class="btn btn-light" role="button" title="Code"><u>Code</u> </a>';
        $result = $this->Helper->button('<u>Code</u> ', '#');
        $this->assertSame($expected, $result);

        //Code on custom title
        $expected = '<a href="#" class="btn btn-light" role="button" title="Code">My title</a>';
        $result = $this->Helper->button($title, '#', ['title' => '<u>Code</u>']);
        $this->assertSame($expected, $result);

        $expected = '<a href="/" class="btn btn-light" role="button" title="/">/</a>';
        $result = $this->Helper->button('/');
        $this->assertSame($expected, $result);

        //With a button class
        $expected = '<a href="http://link" class="btn btn-success" role="button" title="my-title">My title</a>';
        $result = $this->Helper->button($title, 'http://link', ['class' => 'btn-success', 'title' => 'my-title']);
        $this->assertSame($expected, $result);

        $this->loadPlugins(['TestPlugin' => []]);
        $expected = '<a href="/pages" class="btn btn-light" role="button"></a>';
        $result = $this->Helper->button(['controller' => 'Pages', 'plugin' => 'TestPlugin']);
        $this->assertSame($expected, $result);

    }

    /**
     * Test for `link()` method
     * @test
     * @uses \MeTools\View\Helper\BootstrapHtmlHelper::link()
     */
    public function testLink(): void
    {
        $title = 'My title';

        $expected = '<a href="http://link" title="my-title">My title</a>';
        $result = $this->Helper->link($title, 'http://link', ['title' => 'my-title']);
        $this->assertSame($expected, $result);

        $expected = '<a href="#" title="My title">My title <i class="fas fa-home"> </i></a>';
        $result = $this->Helper->link($title, '#', ['icon' => 'home', 'icon-align' => 'right']);
        $this->assertSame($expected, $result);

        //Code on text
        $expected = '<a href="#" title="Code"><u>Code</u> </a>';
        $result = $this->Helper->link('<u>Code</u> ', '#');
        $this->assertSame($expected, $result);

        //Code on custom title
        $expected = '<a href="#" title="Code">My title</a>';
        $result = $this->Helper->link($title, '#', ['title' => '<u>Code</u>']);
        $this->assertSame($expected, $result);

        $this->assertSame('<a href="/" title="/">/</a>', $this->Helper->link('/'));

        $this->loadPlugins(['TestPlugin' => []]);
        $expected = '<a href="/pages"></a>';
        $result = $this->Helper->link(['controller' => 'Pages', 'plugin' => 'TestPlugin']);
        $this->assertSame($expected, $result);
    }

    /**
     * Test for `ol()` and `ul()` methods
     * @test
     * @uses \MeTools\View\Helper\BootstrapHtmlHelper::ol()
     * @uses \MeTools\View\Helper\BootstrapHtmlHelper::ul()
     */
    public function testOlAndUl(): void
    {
        $expected = [
            'ul' => ['class' => 'parent-class'],
            ['li' => ['class' => 'li-class']],
            ['i' => ['class' => 'fas fa-home']],
            '/i',
            'First',
            '/li',
            ['li' => ['class' => 'li-class']],
            ['i' => ['class' => 'fas fa-home']],
            '/i',
            'Second',
            '/li',
            '/ul',
        ];
        $result = $this->Helper->ul(['First', 'Second'], ['class' => 'parent-class'], ['class' => 'li-class', 'icon' => 'home']);
        $this->assertHtml($expected, $result);

        array_shift($expected) && array_pop($expected);
        $expected = ['ol' => ['class' => 'parent-class'], ...$expected, '/ol'];
        $result = $this->Helper->ol(['First', 'Second'], ['class' => 'parent-class'], ['class' => 'li-class', 'icon' => 'home']);
        $this->assertHtml($expected, $result);
    }

    /**
     * Test for `tag()` method
     * @test
     * @uses \MeTools\View\Helper\BootstrapHtmlHelper::tag()
     */
    public function testTag(): void
    {
        $this->assertSame('<h3>My header</h3>', $this->Helper->tag('h3', 'My header'));

        $expected = '<h3 class="my-class"><i class="fas fa-home"> </i> My text</h3>';
        $result = $this->Helper->tag('h3', 'My text', ['class' => 'my-class', 'icon' => 'home']);
        $this->assertSame($expected, $result);

        $expected = '<h3 class="my-class">My text <i class="fas fa-home"> </i></h3>';
        $result = $this->Helper->tag('h3', 'My text', ['class' => 'my-class', 'icon' => 'home', 'icon-align' => 'right']);
        $this->assertSame($expected, $result);
    }
}
