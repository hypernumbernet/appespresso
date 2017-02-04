<?php

namespace Ae;

/**
 * test \Ae\Utl
 */
class UtlTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @covers \Ae\Utl::textToHtml
     */
    public function testTextToHtml()
    {
        $this->assertEquals(Utl::textToHtml('abc'), 'abc');
        $this->assertEquals(Utl::textToHtml('abc', 1), '...');
        $sign = [
            '!' => '!',
            '"' => '&quot;',
            '#' => '#',
            '$' => '$',
            '%' => '%',
            '&' => '&amp;',
            '\'' => '&#039;',
            '(' => '(',
            ')' => ')',
            '*' => '*',
            '+' => '+',
            ',' => ',',
            '-' => '-',
            '.' => '.',
            '/' => '/',
            ':' => ':',
            ';' => ';',
            '<' => '&lt;',
            '=' => '=',
            '>' => '&gt;',
            '?' => '?',
            '@' => '@',
            '[' => '[',
            '\\' => '\\',
            ']' => ']',
            '^' => '^',
            '_' => '_',
            '`' => '`',
            '{' => '{',
            '|' => '|',
            '}' => '}',
            '~' => '~',
        ];
        foreach ($sign as $key => $value) {
            $this->assertEquals(Utl::textToHtml($key), $value);
        }
        $this->assertEquals(Utl::textToHtml('!"#$%&\'()*+,-./:;<=>?@[\\]^_`{|}~'),
                '!&quot;#$%&amp;&#039;()*+,-./:;&lt;=&gt;?@[\]^_`{|}~');
    }

}
