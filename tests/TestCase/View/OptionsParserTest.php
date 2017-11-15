<?php
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
namespace MeTools\Test\TestCase;

use MeTools\TestSuite\TestCase;
use MeTools\View\OptionsParser;
use Reflection\ReflectionTrait;

/**
 * MeTools\View\OptionsParser Test Case
 */
class OptionsParserTest extends TestCase
{
    use ReflectionTrait;

    /**
     * @var \MeTools\View\OptionsParser
     */
    public $OptionsParser;

    /**
     * setUp method
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $options = [
            'alt' => 'this is a string',
            'false' => false,
            'class' => 'first second third fourth',
            'true' => true,
            'null' => null,
            'zero' => 0,
            'zeroAsString' => '0',
            'zeroDotOne' => 0.1,
            'negative' => -1,
        ];

        $defaults = [
            'alt' => 'this value will not be used',
            'defaultKey' => 'defaultValue',
        ];

        $this->OptionsParser = new OptionsParser($options, $defaults);
    }

    /**
     * Tests for `buildValue()` method
     * @test
     */
    public function testBuildValue()
    {
        $buildValueMethod = function ($value, $key) {
            return $this->invokeMethod($this->OptionsParser, 'buildValue', [&$value, $key]);
        };

        //Always returns the same string
        foreach (['aaa', ' aaa', ' aaa  '] as $value) {
            $this->assertEquals('aaa', $buildValueMethod($value, 'someKey'));
        }

        //Returns the original value
        foreach ([true, false, 0, '0', 0.1, -1, null, [], [[]]] as $value) {
            $this->assertEquals($value, $buildValueMethod($value, 'someKey'));
        }

        foreach (['class', 'data-toggle'] as $key) {
            //Returns a string with the ordered values
            foreach ([
                'first  second third fourth first  ',
                ['first', 'second', 'third', '', ' ', 'fourth', 'first', null, false],
                ['first', ['second', ['third']], ['fourth']],
            ] as $value) {
                $this->assertEquals('first fourth second third', $buildValueMethod($value, $key));
            }
        }
    }

    /**
     * Tests for `delete()` method
     * @test
     */
    public function testDelete()
    {
        $result = $this->OptionsParser->delete('class');
        $this->assertInstanceOf('MeTools\View\OptionsParser', $result);
        $this->assertFalse($this->OptionsParser->exists('class'));

        //As array of keys
        $this->OptionsParser->delete(['zero', 'zeroAsString']);
        $this->assertFalse($this->OptionsParser->exists('zero'));
        $this->assertFalse($this->OptionsParser->exists('zeroAsString'));

        //This returns `true,` because it exists as the default value
        $this->OptionsParser->delete('alt');
        $this->assertTrue($this->OptionsParser->exists('alt'));
        $this->assertEquals('this value will not be used', $this->OptionsParser->get('alt'));
    }

    /**
     * Tests for `exists()` method
     * @test
     */
    public function testExists()
    {
        $this->assertTrue($this->OptionsParser->exists('alt'));

        //This key exists in each case, because it is present as the default value
        $this->assertTrue($this->OptionsParser->exists('defaultKey'));

        //The value is empty (`false`), but the key exists anyway
        $this->assertTrue($this->OptionsParser->exists('false'));

        $this->assertFalse($this->OptionsParser->exists('noExistingKey'));
    }

    /**
     * Tests for `get()` method
     * @test
     */
    public function testGet()
    {
        $this->assertEquals('this is a string', $this->OptionsParser->get('alt'));
        $this->assertEquals('first fourth second third', $this->OptionsParser->get('class'));

        //Default value
        $this->assertEquals('defaultValue', $this->OptionsParser->get('defaultKey'));

        $this->assertNull($this->OptionsParser->get('noExistingKey'));
    }

    /**
     * Tests for `toArray()` method
     * @test
     */
    public function testToArray()
    {
        $this->assertEquals([
            'alt' => 'this is a string',
            'class' => 'first fourth second third',
            'defaultKey' => 'defaultValue',
            'false' => false,
            'negative' => -1,
            'null' => null,
            'true' => true,
            'zero' => 0,
            'zeroAsString' => '0',
            'zeroDotOne' => 0.1,
        ], $this->OptionsParser->toArray());
    }

    /**
     * Tests for `toArray()` method
     * @test
     */
    public function testToString()
    {
        $this->assertEquals(
            'alt="this is a string" class="first fourth second third" ' .
            'defaultKey="defaultValue" false="false" negative="-1" null="null" ' .
            'true="true" zero="0" zeroAsString="0" zeroDotOne="0.1"',
            $this->OptionsParser->toString()
        );
    }
}
