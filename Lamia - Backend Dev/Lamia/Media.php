<?php
declare(strict_types=1);
namespace Lamia;

use Throwable;

#In a bigger project this may be an interface instead, but for the task it should be enough to use abstract class
abstract class Media
{
    public function getItem(): string
    {
        try {
            #Check method
            $method = $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'] ?? $_SERVER['REQUEST_METHOD'] ?? null;
            if (!in_array($method, ['GET', 'HEAD', 'OPTIONS'])) {
                @header($_SERVER['SERVER_PROTOCOL'].' 405');
                return $this->error('Method not allowed');
            }
            if (in_array($method, ['HEAD', 'OPTIONS'])) {
                #HEAD and OPTIONS do not expect a body, thus skip any processing and return empty string
                return '';
            }
            #Check if token is present
            $token = $_GET['jwt'] ?? preg_replace('/(Bearer\s)(\S+)/', '$2', trim($_SERVER['Authorization'] ?? $_SERVER['HTTP_AUTHORIZATION'] ?? '')) ?? '';
            if (empty($token)) {
                @header($_SERVER['SERVER_PROTOCOL'].' 403');
                return $this->error('No authentication token provided');
            }
            try {
                JWT::decode($token);
            } catch (Throwable) {
                @header($_SERVER['SERVER_PROTOCOL'].' 403');
                return $this->error('Bad authentication token provided');
            }
            #Generate URL
            $url = $this->genUrl();
            #Get page
            $page = (new Curl())->getPage($url);
            #Check if we got JSON
            if (!$this->isJson($page)) {
                @header($_SERVER['SERVER_PROTOCOL'].' 406');
                return $this->error('Page content for `'.$url.'` is not valid JSON');
            }
            #Check if 3rd party found the item
            if (!$this->isFound($page)) {
                @header($_SERVER['SERVER_PROTOCOL'].' 404');
                return $this->error('No item fitting the search criteria found');
            }
        } catch (Throwable $exception) {
            return $this->error($exception->getMessage());
        }
        @header($_SERVER['SERVER_PROTOCOL'].' 200');
        return $page;
    }
    
    protected function isJson(string &$string): bool
    {
        $json = json_decode($string, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return false;
        #Minor changes to the JSON as well. Done here, since we already decode here.
        #In a bigger project it may be a better idea to do this in separate function
        } else {
            #Add "Response" key
            $json['Response'] = true;
            #Pretty print the value
            $string = json_encode($json, JSON_PRETTY_PRINT);
            return true;
        }
    }
    
    #Standartizing the error output
    protected function error(string $message): string
    {
        return json_encode(['Response' => false, 'Error' => $message], JSON_PRETTY_PRINT);
    }
    
    #Function to generate a URL to grab with cURL
    abstract protected function genUrl(): string;
    
    #Function to parse the page output, to check 3rd party found the item we searched for
    abstract protected function isFound(string $page): bool;
}
