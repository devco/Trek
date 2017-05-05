<?php

namespace MigrationsTwo;
use Trek\MigrationAbstract;

class FakeMigration extends MigrationAbstract
{
    public function down()
    {
        DoFakeWork::down();
    }

    public function up()
    {
        DoFakeWork::up();
    }
}
