<?php

namespace MigrationsTwo;


class DoFakeWork
{
    public static $counterUp = 0;
    public static $counterDown = 0;

    public static function up()
    {
        self::$counterUp++;
    }

    public static function down()
    {
        self::$counterDown++;
    }
}
