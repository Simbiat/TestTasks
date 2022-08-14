<?php
declare(strict_types=1);
namespace Lamia;

use InvalidArgumentException;

class ISBN extends Media
{
    private string $baseUrl = 'https://openlibrary.org/isbn/';
    
    protected function genUrl(): string
    {
        #Check if we have ISBN type and its value is valid
        $isbn = $_GET['isbn'] ?? null;
        if (empty($isbn) || preg_match('/^(97(8|9))?\d{9}(\d|X)$/u', $isbn) !== 1) {
            @header($_SERVER['SERVER_PROTOCOL'].' 400');
            throw new InvalidArgumentException('No or bad ISBN provided');
        }
        #Generate URL
        return $this->baseUrl.$isbn.'.json';
    }
    
    protected function isFound(string $page): bool
    {
        #Not required for ISBN, since it will fail on Curl (it will return 404)
        return true;
    }
}
