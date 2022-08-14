<?php
declare(strict_types=1);
namespace Lamia;

use InvalidArgumentException;

class IMDB extends Media
{
    private string $apiKey = 'f45503a8';
    private string $baseUrl = 'https://www.omdbapi.com/?r=json&apikey=';
    
    public function genUrl(): string
    {
        #Standardize GET values. Technically not really needed, since omdbapi should handle those, but I prefer to handle
        #that here, in case something changes on their side
        #Check if we have plot type and its value is valid
        $plot = $_GET['plot'] ?? 'short';
        if (!in_array(strtolower($plot), ['short', 'full'])) {
            $plot = 'short';
        }
        $year = $_GET['year'] ?? null;
        #Empty year, if it's provided but is not numeric or older than 1888 (year of the 1st known movie)
        if (!empty($year) && (!is_numeric($year) || intval($year) < 1888)) {
            $year = null;
        }
        $title = $_GET['title'] ?? null;
        if (empty($title)) {
            @header($_SERVER['SERVER_PROTOCOL'].' 400');
            throw new InvalidArgumentException('No title provided');
        }
        #Generate URL
        return $this->baseUrl.$this->apiKey.'&plot='.$plot.(empty($year) ? '' : '&y='.$year).'&t='.$title;
    }
    
    protected function isFound(string $page): bool
    {
        if (preg_match('/"Movie not found!"/ui', $page) === 1 ) {
            return false;
        }
        return true;
    }
}
