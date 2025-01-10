<?php

declare(strict_types=1);

namespace Phphleb\WebrotorTests\Tests\Start;

use PHPUnit\Framework\TestCase;

final class A1CorrectSyntaxTest extends TestCase
{
    private const DIRECTORY = __DIR__ . '/../../../webrotor';

    public function testFilesForCorrectSyntax(): void
    {
        if (!\function_exists('exec')) {
            return;
        }
        $directory = realpath(self::DIRECTORY);
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory)
        );
        $result = true;
        $errors = [];
        foreach ($files as $file) {
            if ($file->isFile() && $file->getExtension() === "php") {
                $content = exec("php -l " . $file->getRealPath(), $output, $returnVar);
                if ($returnVar !== 0) {
                    $result = false;
                    $errors[] = $content;
                }
            }
        }
        if ($errors) {
            throw new \Error(implode(PHP_EOL, $errors));
        }
        $this->assertTrue($result);
    }
}