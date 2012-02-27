<?php

namespace Migration\Two\Zero\Zero;

class TestRollback
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