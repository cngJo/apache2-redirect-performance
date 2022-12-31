<?php

function main(array $args) {
	$amount = $args[1] ?? null;
	if (!$amount) {
		echo "No amount found";
	}

	$filename = __DIR__ . "/.htaccess";
	$template = "Redirect 301 /{#} /{#}-redirected\n";

	$result = file_get_contents("{$filename}-base");

	for ($i = 1; $i <= $amount; $i++) {
		$result .= str_replace("{#}", $i, $template);
	}

	unlink($filename);
	file_put_contents($filename, $result);
}

main($argv);
