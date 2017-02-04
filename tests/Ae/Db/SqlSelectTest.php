<?php

namespace Ae\Db;

/**
 * test \Ae\Utl
 */
class SqlSelectTest extends \PHPUnit\Framework\TestCase
{

    public function testNew()
    {
        $obj = new SqlSelect();
        $this->assertTrue(!isset($obj->aaa));
        $this->assertTrue(!isset($obj->table));
        $this->assertTrue($obj->table == null);
        $this->assertTrue($obj->table === null);
        $this->assertTrue(!$obj->table);
        $obj->table = '';
        $this->assertTrue(isset($obj->table));
    }

    public function testSimpleSelect()
    {
        $obj = new SqlSelect();
        $obj->table = 'table1';
        var_dump($GLOBALS);

    }
}
