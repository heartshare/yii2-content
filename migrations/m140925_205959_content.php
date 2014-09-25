<?php

use yii\db\Schema;
use yii\db\Migration;

class m140925_205959_content extends Migration
{
    public function safeUp()
    {
        $tableOptions = "";
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        $this->createTable(
            'IF NOT EXISTS {{%article}}', [
                'id' => 'INT(11) NOT NULL AUTO_INCREMENT',
                'cat_id' => 'INT(11) NOT NULL',
                'cover_id' => 'INT(11) NULL',
                'name' => 'VARCHAR(200) NOT NULL',
                'slug' => 'VARCHAR(255) NOT NULL',
                'anons' => 'TEXT NOT NULL',
                'full' => 'TEXT NOT NULL',
                'full_parsed' => 'TEXT NOT NULL',
                'active' => 'TINYINT(1) NOT NULL',
                'views' => 'INT(11) NOT NULL',
                'created' => 'DATETIME NOT NULL',
                'updated' => 'TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ',
                'bymanager' => 'INT(11) NOT NULL',
                'metak' => 'VARCHAR(255) NOT NULL',
                'metadesc' => 'VARCHAR(255) NOT NULL',
                'publishto' => 'DATETIME NOT NULL',
                'PRIMARY KEY (`id`)'
            ], $tableOptions
        );

        $this->createIndex('category', '{{%article}}', 'cat_id');
        $this->createIndex('cov', '{{%article}}', 'cover_id');
        $this->createIndex('public', '{{%article}}', ['active', 'publishto']);
        $this->createIndex('slug', '{{%article}}', 'slug');

        /* MYSQL */
        $this->createTable(
            'IF NOT EXISTS {{%attachments}}', [
                'id' => 'INT(11) NOT NULL AUTO_INCREMENT',
                'filename' => 'VARCHAR(255) NOT NULL',
                'filetitle' => 'VARCHAR(255) NOT NULL',
                'filesize' => 'VARCHAR(15) NOT NULL',
                'type' => 'TINYINT(1) NOT NULL',
                'updated' => 'TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ',
                'PRIMARY KEY (`id`)'
            ], $tableOptions
        );

        $this->createIndex('type_attach', '{{%attachments}}', 'type');

        /* MYSQL */
        $this->createTable(
            'IF NOT EXISTS {{%category}}', [
                'id' => 'INT(11) NOT NULL AUTO_INCREMENT',
                'name' => 'VARCHAR(200) NOT NULL',
                'slug' => 'VARCHAR(255) NOT NULL',
                'metaKey' => 'VARCHAR(255) NOT NULL',
                'metaDesc' => 'VARCHAR(255) NOT NULL',
                'ord' => 'SMALLINT(6) NOT NULL',
                'cnt' => 'SMALLINT(6) NOT NULL',
                'updated' => 'TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ',
                'bymanager' => 'INT(11) NOT NULL',
                'PRIMARY KEY (`id`)'
            ], $tableOptions
        );

        $this->createIndex('ord', '{{%category}}', 'ord');
        $this->createIndex('slug', '{{%category}}', 'slug');


        /* MYSQL */
        $this->createTable(
            'IF NOT EXISTS {{%covers}}', [
                'id' => 'INT(11) NOT NULL AUTO_INCREMENT',
                'filename' => 'VARCHAR(255) NOT NULL',
                'conttype' => 'ENUM(\'news\',\'art\') NOT NULL DEFAULT \'news\'',
                'filesize' => 'VARCHAR(15) NOT NULL',
                'updated' => 'TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ',
                'PRIMARY KEY (`id`)'
            ], $tableOptions
        );

        /* MYSQL */
        $this->createTable(
            'IF NOT EXISTS {{%feedback}}', [
                'id' => 'INT(11) NOT NULL AUTO_INCREMENT',
                'name' => 'VARCHAR(200) NOT NULL',
                'mail' => 'VARCHAR(200) NOT NULL',
                'text' => 'TEXT NOT NULL',
                'created' => 'TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ',
                'ip' => 'VARCHAR(15) NOT NULL',
                'mailed' => 'TINYINT(1) NOT NULL',
                'PRIMARY KEY (`id`)'
            ], $tableOptions
        );

        /* MYSQL */
        $this->createTable(
            'IF NOT EXISTS {{%meta}}', [
                'id' => 'INT(11) NOT NULL AUTO_INCREMENT',
                'model_id' => 'VARCHAR(50) NOT NULL',
                'model' => 'VARCHAR(255) NOT NULL',
                'metak' => 'VARCHAR(255) NOT NULL',
                'metad' => 'VARCHAR(255) NOT NULL',
                'updated' => 'TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ',
                'PRIMARY KEY (`id`)'
            ], $tableOptions
        );

        /* MYSQL */
        $this->createTable(
            'IF NOT EXISTS {{%news}}', [
                'id' => 'INT(11) NOT NULL AUTO_INCREMENT',
                'cover_id' => 'INT(11) NULL',
                'name' => 'VARCHAR(200) NOT NULL',
                'slug' => 'VARCHAR(255) NOT NULL',
                'anons' => 'TEXT NOT NULL',
                'full' => 'TEXT NOT NULL',
                'full_parsed' => 'TEXT NOT NULL',
                'active' => 'TINYINT(1) NOT NULL',
                'views' => 'INT(11) NOT NULL',
                'created' => 'DATETIME NOT NULL',
                'updated' => 'TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ',
                'bymanager' => 'INT(11) NOT NULL',
                'metak' => 'VARCHAR(255) NOT NULL',
                'metadesc' => 'VARCHAR(255) NOT NULL',
                'publishto' => 'DATETIME NOT NULL',
                'PRIMARY KEY (`id`)'
            ], $tableOptions
        );
        $this->createIndex('cov', '{{%news}}', 'cover_id');
        $this->createIndex('public', '{{%news}}', ['active', 'publishto']);
        $this->createIndex('slug', '{{%news}}', ['slug'], true);

        /* MYSQL */
        $this->createTable(
            'IF NOT EXISTS {{%page}}', [
                'id' => 'INT(11) NOT NULL AUTO_INCREMENT',
                'name' => 'VARCHAR(200) NOT NULL',
                'slug' => 'VARCHAR(255) NOT NULL',
                'full' => 'TEXT NOT NULL',
                'full_parsed' => 'TEXT NOT NULL',
                'menuid' => 'INT(11) NULL',
                'views' => 'INT(11) NOT NULL',
                'updated' => 'TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ',
                'bymanager' => 'INT(11) NOT NULL',
                'metak' => 'VARCHAR(255) NOT NULL',
                'metadesc' => 'VARCHAR(255) NOT NULL',
                'PRIMARY KEY (`id`)'
            ], $tableOptions
        );

        $this->createIndex('slug', '{{%page}}', ['slug'], true);

        /* MYSQL */
        $this->createTable(
            'IF NOT EXISTS {{%smallnews}}', [
                'id' => 'INT(11) NOT NULL AUTO_INCREMENT',
                'name' => 'VARCHAR(255) NOT NULL',
                'text' => 'TEXT NOT NULL',
                'active' => 'TINYINT(1) NOT NULL',
                'created' => 'DATETIME NOT NULL',
                'updated' => 'TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ',
                'bymanager' => 'INT(11) NOT NULL',
                'PRIMARY KEY (`id`)'
            ], $tableOptions
        );

        $this->createIndex('public', '{{%smallnews}}', ['active']);

        /* MYSQL */
        $this->createTable(
            'IF NOT EXISTS {{%tagged}}', [
                'tid' => 'INT(11) NOT NULL AUTO_INCREMENT',
                'tagid' => 'INT(11) NOT NULL',
                'contid' => 'INT(11) NOT NULL',
                'conttype' => 'ENUM(\'art\',\'news\') NOT NULL DEFAULT \'news\'',
                'PRIMARY KEY (`tid`)'
            ], $tableOptions
        );
        $this->createIndex('tagid', '{{%tagged}}', 'tagid');
        $this->createIndex('contid', '{{%tagged}}', 'contid');
        $this->createIndex('tag_typr', '{{%tagged}}', ['conttype']);

        /* MYSQL */
        $this->createTable(
            'IF NOT EXISTS {{%tags}}', [
                'tag_id' => 'INT(11) NOT NULL AUTO_INCREMENT',
                'tagname' => 'VARCHAR(50) NOT NULL',
                'freeq' => 'INT(11) NOT NULL',
                'updated' => 'TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ',
                'PRIMARY KEY (`tag_id`)'
            ], $tableOptions
        );

        $this->addForeignKey('fk_covers_news', '{{%news}}', 'cover_id', '{{%covers}}', 'id', 'CASCADE', 'DELETE');


        $this->addForeignKey(
            'fk_tags_tagged', '{{%tagged}}', 'tagid', '{{%tags}}', 'tag_id', 'CASCADE', 'DELETE'
        );

        $this->addForeignKey(
            'fk_category_article', '{{%article}}', 'cat_id', '{{%category}}', 'id', 'CASCADE', 'DELETE'
        );

        $this->addForeignKey(
            'fk_covers_article', '{{%article}}', 'cover_id', '{{%covers}}', 'id', 'CASCADE', 'DELETE'
        );
    }

    public function safeDown()
    {
        $this->dropTable('{{%tags}}');
        $this->dropTable('{{%smallnews}}');
        $this->dropTable('{{%page}}');
        $this->dropTable('{{%news}}');
        $this->dropTable('{{%meta}}');
        $this->dropTable('{{%feedback}}');
        $this->dropTable('{{%article}}');
        $this->dropTable('{{%attachments}}');
        $this->dropTable('{{%covers}}');
        $this->dropTable('{{%category}}');
        return false;
    }
}
