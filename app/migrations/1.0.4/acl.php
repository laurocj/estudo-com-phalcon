<?php 

use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phalcon\Migrations\Mvc\Model\Migration;

/**
 * Class AclsMigration_104
 */
class AclMigration_104 extends Migration
{
    /**
     * Define the table structure
     *
     * @return void
     */
    public function morph()
    {
        $this->morphTable('acl', [
                'columns' => [
                    new Column(
                        'id',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'notNull' => true,
                            'autoIncrement' => true,
                            'unsigned'      => true,
                            'size' => 11,
                            'first' => true
                        ]
                    ),
                    new Column(
                        'role_id',
                        [
                            'type'      => Column::TYPE_INTEGER,
                            'notNull'   => true,
                            'unsigned'  => true,
                            'size'      => 11,
                            'after'     => 'id'
                        ]
                    ),
                    new Column(
                        'action_id',
                        [
                            'type'      => Column::TYPE_INTEGER,
                            'notNull'   => true,
                            'unsigned'  => true,
                            'size'      => 11,
                            'after'     => 'role_id'
                        ]
                    ),
                    new Column(
                        'resource_id',
                        [
                            'type'      => Column::TYPE_INTEGER,
                            'notNull'   => true,
                            'unsigned'  => true,
                            'size'      => 11,
                            'after'     => 'action_id'
                        ]
                    ),
                ],
                'indexes' => [
                    new Index('PRIMARY', ['id'], 'PRIMARY'),
                    new Index('role_id', ['role_id']),
                    new Index('action_id', ['action_id']),
                    new Index('resource_id', ['resource_id']),
                ],
                'references' => [
                    new Reference(
                        'role_fk_alc_1',
                        [
                            'referencedTable'   => 'roles',
                            'columns'           => ['role_id'],
                            'referencedColumns' => ['id'],
                        ]
                    ),
                    new Reference(
                        'action_fk_acl_1',
                        [
                            'referencedTable'   => 'actions',
                            'columns'           => ['action_id'],
                            'referencedColumns' => ['id'],
                        ]
                    ),
                    new Reference(
                        'resource_fk_acl_1',
                        [
                            'referencedTable'   => 'resources',
                            'columns'           => ['resource_id'],
                            'referencedColumns' => ['id'],
                        ]
                    ),
                ],
                'options' => [
                    'table_type' => 'BASE TABLE',
                    'auto_increment' => '1',
                    'engine' => 'InnoDB',
                    'table_collation' => 'utf8_general_ci'
                ],
            ]
        );
    }

    /**
     * Run the migrations
     *
     * @return void
     */
    public function up()
    {

    }

    /**
     * Reverse the migrations
     *
     * @return void
     */
    public function down()
    {

    }

}
