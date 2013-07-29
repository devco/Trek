<?php

namespace Trek;

interface MigrationInterface
{
    public function setUp();

    public function tearDown();

    public function up();

    public function down();
}