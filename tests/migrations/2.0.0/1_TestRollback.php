<?php

namespace Migration\Two\Zero\Zero;
use Trek\MigrationAbstract;

class TestRollback extends MigrationAbstract
{
	public function up()
	{
		throw new \Exception('Testing rollback.');
	}
	
	public function down()
	{
		throw new \Exception('Should not be called.');
	}
}
