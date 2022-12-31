<?php

require __DIR__ . "/conf.php";

function test(int $redirects)
{
    $serverHostname = SERVER_HOST;
    $serverUsername = SERVER_USER;

    // 1. Update .htaccess on remote server
    exec("ssh {$serverUsername}@{$serverHostname}  'php /var/www/html/generate-htaccess.php {$redirects}'");

    // 2. Determine which redirect to test
    $testRedirect = rand(1, $redirects);

    // 3. Do the curl request
    $curl = curl_init("http://{$serverHostname}/{$testRedirect}");
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_exec($curl);
    curl_close($curl);

    // get the info and return the total time it took to execute the request
    $info = curl_getinfo($curl);
    return $info["total_time"] ?? null;
}

function main()
{
    $tests = [
        100,
        1000,
        10000,
        15000,
        20000,
        30000,
        50000,
        100000,
        250000,
        500000,
        750000,
        1000000,
    ];

    $results = [];
    $results[] = [
        "Redirects",
        "Avg Seconds",
    ];

    foreach ($tests as $test) {
        $times = [];

        for ($i = 0; $i < 10; $i++) {
            $times[] = test($test);
        }

        $average = array_sum($times) / count($times);
        $results[] = [$test, $average];
    }

    $file = fopen(__DIR__ . "/../result.csv", 'wb');
    foreach ($results as $line) {
        fputcsv($file, $line, ";");
    }
}

main();
