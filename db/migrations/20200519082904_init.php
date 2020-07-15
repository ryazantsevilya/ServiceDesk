<?php

use Phinx\Migration\AbstractMigration;

class Init extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('tickets');
        $table->addColumn('title', 'string', ['limit' => 250])
            ->addColumn('content', 'text')
            ->create();
    }
}
