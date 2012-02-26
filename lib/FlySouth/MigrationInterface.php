<?php

namespace Trek;

interface MigrationInterface
{
	public function down();
	
	public function up();
}