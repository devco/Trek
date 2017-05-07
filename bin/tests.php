<?php

use Testes\Autoloader;
use Testes\Coverage\Coverage;
use Testes\Finder\Finder;
use Testes\Suite\Suite;
use Testes\Event\Test as Event;
use Symfony\Component\Finder\Finder as SymfonyFinder;

$base = __DIR__ . '/..';

class Runner
{
    const PADDING = 88;
    public $methodTimer = [];
    public $testTime = 0;
    public $totalTime = 0;
}

$runner = new Runner();

require $base . '/vendor/autoload.php';

Autoloader::register();
Autoloader::addPath($base . '/tests');
Autoloader::addPath($base . '/src');

$suite    = new Suite;
$coverage = new Coverage;
$coverage->start();

echo PHP_EOL;

$suite->addTests(new Finder($base . '/tests', 'Test'));
$suite->run(getTestEvent());

echo "Total Time: \033[1m" . number_format($runner->totalTime, 3) . "\033[0m" . PHP_EOL;

echo PHP_EOL . sprintf('Ran %d test%s.', count($suite), count($suite) === 1 ? '' : 's');

// <Coverage>
$analyzer = $coverage->stop();

$finder = new SymfonyFinder();
$finder->files()->in($base . '/tests');

foreach ($finder as $file) {
    $analyzer->addFile($file->getRealpath());
}

echo PHP_EOL . PHP_EOL . 'Coverage: ' . $analyzer->getPercentTested() . '%' . PHP_EOL . PHP_EOL;
// </Coverage>


if ($suite->getAssertions()->isPassed()) {
    if ($suite->getExceptions()->count()) {
        echo 'Failed!' . PHP_EOL;
    } else {
        echo 'Passed!' . PHP_EOL;
    }
} else {
    echo "Assertions" . PHP_EOL;
    echo "----------" . PHP_EOL;
    echo PHP_EOL;

    foreach ($suite->getAssertions()->getFailed() as $ass) {
        echo $ass->getTestClass() . ' Line: ' .  $ass->getTestLine() . ' ' .
        $ass->getMessage() . PHP_EOL;
    }

    echo PHP_EOL;
}


if ($suite->getExceptions()->count()) {
    echo "Exceptions" . PHP_EOL;
    echo "----------" . PHP_EOL;
    echo PHP_EOL;

    foreach ($suite->getExceptions() as $e) {
        echo str_replace(PHP_EOL, PHP_EOL . '  ',  $e->getException()->__toString());
    }

    echo PHP_EOL;
}

echo PHP_EOL;

if ($suite->isFailed()) {
    exit(1);
}

function getTestEvent() {
    $event = new Event;
    global $runner;

    $event->on('preRun', function($test) {
        echo 'Running Tests for: ' . get_class($test) . PHP_EOL;
    });

    $event->on('preMethod', function ($method, $test) use ($runner) {
        $runner->methodTimer[$method]['start'] = microtime(true);
    });

    $event->on('postMethod', function ($method, $test) use ($runner) {
        $runner->methodTimer[$method]['stop'] = microtime(true);

        $className = get_class($test);

        $start = $runner->methodTimer[$method]['start'];
        $stop = $runner->methodTimer[$method]['stop'];
        $time = $stop - $start;
        $runner->testTime += $time;
        $number = (string) number_format($time, 3);

        echo "\033[" .($test->isMethodPassed($method) ? '42m[PASS]' : "41m[FAIL]") . "\033[0m" .
            str_pad(' ' . $className . '::' . $method . ' ', Runner::PADDING - strlen($number)) .
            $number . PHP_EOL;
    });

    $event->on('postRun', function($test) use ($runner) {
        $number = number_format($runner->testTime, 3);
        $runner->totalTime += $runner->testTime;
        $runner->testTime = 0;

        echo str_pad('Total for ' . get_class($test) . ': ' , Runner::PADDING + 6 - strlen($number), ' ', STR_PAD_LEFT) .
            "\033[1m" . $number . "\033[0m" . PHP_EOL;
    });

    return $event;
}
