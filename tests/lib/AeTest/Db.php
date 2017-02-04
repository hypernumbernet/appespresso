<?php

/**
 * DBテスト用ライブラリ
 * @copyright Copyright (C) 2009-2017 Tomohito Inoue
 * @license MIT
 * @author Tomohito Inoue <hypernumbernet@users.noreply.github.com>
 */
class AeTest_Db
{

    /**
     * 全テーブル削除
     * @param Ae_Db_Base $db
     */
    static function drop_all_table($db)
    {
        foreach ($db->tables() as $table) {
            $db->exec('DROP TABLE ' . $table);
        }
    }

    /**
     * ランダムな名前のテーブルを作成
     * @param Ae_Db_Base $db
     * @return string 作成したテーブル
     */
    static function create_table_random($db)
    {
        $name = Ae_Password::make(16, Ae_Password::ROMAN_LOWER . Ae_Password::NUMBER);
        $statement = 'CREATE TABLE `' . $name .
            '` (
            `idnew_table` INT NOT NULL,
            `new_tablecol` VARCHAR(45) NULL,
            PRIMARY KEY (`idnew_table`));';
        $db->exec($statement);
        return $name;
    }
}
