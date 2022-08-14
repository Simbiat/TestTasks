<?php
declare(strict_types=1);
namespace Lamia;

use UnexpectedValueException;

class CLI
{
    private array $arguments;
    #m = movie, t: = title, y:: = year, p:: = plot type
    #b = book, c: = ISBN code
    #h = help
    private string $short = 'mt:y::p::bc:h';
    private array $long = [
        'movie', 'title:', 'year::', 'plot::',
        'book', 'code:',
        'help'
    ];
    
    public function __construct()
    {
        #Get arguments
        $arguments = getopt($this->short, $this->long);
        if ($arguments === false) {
            throw new UnexpectedValueException('No arguments provided. Are you properly using CLI?');
        } else {
            $this->arguments = $arguments;
        }
    }
    
    public function run(string $url = 'http://localhost'): void
    {
        #Sanitize arguments
        $arguments = $this->parse();
        #Check if help was requested
        if ($arguments['help']) {
            echo '
Supported arguments:
    -m, --movie - flag indicating, that a movie is requested
        -t, --title - name of the movie (required)
        -y, --year - year of the movie (optional)
        -p, --plot - plot format (optional, either "short" or "full")
    -b, --book - flag indicating, that a book is requested
        -c, --code - 10- or 13-digit ISBN code of the book (required)
    -h, --help - this help message

Examples:
    php client.php -m -t "Star Wars"
    php client.php -b -c 9783161484100

If no errors occur, the return will be a JSON string.
';
            exit;
        }
        #Validate arguments
        if ((isset($arguments['movie']) && isset($arguments['book'])) || (!isset($arguments['movie']) && !isset($arguments['book']))) {
            throw new UnexpectedValueException('Please, decide, whether you need a movie or a book');
        }
        #We can validate the data further here, but it is being validated on API itself, so I think it's safe to go straight to URL generation
        if (isset($arguments['movie'])) {
            $url = $url.'/getMovie/?'.http_build_query($arguments['movie']);
        } else {
            $url = $url.'/getBook/?'.http_build_query($arguments['book']);
        }
        #Generate token. No payload, since we use it only for authorization
        $token = JWT::encode([]);
        #Create Curl object with our standard settings
        $curl = new Curl();
        #Set header for Curl
        curl_setopt($curl::$curlHandle, CURLOPT_HTTPHEADER, ['Accept: application/json', 'Authorization: Bearer '.$token]);
        echo $curl->getPage($url);
    }
    
    private function parse(): array
    {
        $parsed = [];
        #Using isset(), arguments without values return false
        if (isset($this->arguments['h']) || isset($this->arguments['help'])) {
            $parsed['help'] = true;
        } else {
            $parsed['help'] = false;
        }
        if (isset($this->arguments['m']) || isset($this->arguments['movie'])) {
            $parsed['movie']['title'] = $this->arguments['t'] ?? $this->arguments['title'] ?? null;
            $parsed['movie']['year'] = $this->arguments['y'] ?? $this->arguments['year'] ?? null;
            $parsed['movie']['plot'] = $this->arguments['p'] ?? $this->arguments['plot'] ?? 'short';
        }
        if (isset($this->arguments['b']) || isset($this->arguments['book'])) {
            $parsed['book']['code'] = $this->arguments['c'] ?? $this->arguments['code'] ?? null;
        }
        return $parsed;
    }
}
