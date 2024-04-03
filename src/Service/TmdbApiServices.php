<?php
namespace App\Service;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class TmdbApiServices
{
    private $apiKey;
    private $bearer;
    private $client;

    public function __construct(string $apiKey, string $bearer, HttpClientInterface $client)
    {
        $this->apiKey = $apiKey;
        $this->bearer = $bearer;
        $this->client = $client;
    }

    private function makeApiRequest(string $method, string $url, ?array $data): array
    {
        //$options = ['query' => ['api_key' => $this->apiKey]];
        //if($method == "POST"){
        $options = [
            // 'body' => json_encode($data),
            'headers' => [
                'Authorization' => 'Bearer '.$this->bearer,
                'Content-Type' => 'application/json;charset=utf-8',
                'accept' => 'application/json',
            ]
        ];
        if($method == "POST" && !empty($data)){
            $options['body'] =  json_encode($data); 
        }
        try {
            $response = $this->client->request($method, $url, $options);            
            $content = $response->toArray();
            $statusCode = $response->getStatusCode();

            return ['content' => $content, 'status_code' => $statusCode];

        } catch (\Exception $exception) {
            throw new \Exception('Erreur lors de la requête vers l\'API : ' . $exception->getMessage());
        }
    }

    public function movies(?int $genres, ?string $keywords, bool $popular): array
    {
        $type = "discover";
        $url = 'https://api.themoviedb.org/3/discover/movie?language=fr';
        if(!empty($genres)){
            $url .= '&with_genres='.$genres;
        }

        if(!empty($keywords)){
            $url = 'https://api.themoviedb.org/3/search/movie?language=fr&query='.$keywords;
        }  
        
        if($popular == true){
            $url = 'https://api.themoviedb.org/3/movie/popular?language=fr';
        }        
        $data = [];
        $results = $this->makeApiRequest('GET', $url,$data);
        if ($results['status_code'] === 200) {
            $movies = $results['content']['results'];
            foreach ($movies as $key => $movie) {
                $productionCompanies = $this->productionCompanies($movie['id']);
                $aProdComp = [];
                $sProdComp = '';
                foreach ($productionCompanies as $productionCompanie) {
                    $aProdComp[] =  $productionCompanie['name'];
                }
                $sProdComp = implode(', ', $aProdComp);
                $OfficialTrailer = $this->getOfficialTrailer($movie['id']);                
                $movies[$key]['production_companies'] = $sProdComp;
                $movies[$key]['official_trailer'] = $OfficialTrailer;
                if($popular == true){
                    unset($movies);
                    $movie['production_companies'] = $sProdComp;
                    $movie['official_trailer'] = $OfficialTrailer;
                    $movies[] = $movie;
                    break;
                }
            }
            return $movies;
        } else {
            throw new \Exception('Erreur lors de la requête vers l\'API Discover Movie');
        }

    }

    public function genres(): array
    {
        $url = 'https://api.themoviedb.org/3/genre/tv/list?language=fr';
        $data = [];
        $results = $this->makeApiRequest('GET', $url, $data);
        if ($results['status_code'] === 200) {
            return $results['content']['genres'];
        } else {
            return [];
        }        
    }

    public function productionCompanies(int $movieId): array
    {
        $url = 'https://api.themoviedb.org/3/movie/'.$movieId.'?language=fr';
        $data = [];
        $response = $this->makeApiRequest('GET', $url, $data);
        if ($response['status_code'] === 200) {
            return $response['content']['production_companies'];
        } else {
            return [];
        }
    }

    public function getOfficialTrailer(int $movieId): array
    {
        $url = 'https://api.themoviedb.org/3/movie/'.$movieId.'/videos?language=en-US';
        $data = [];
        $response = $this->makeApiRequest('GET', $url, $data);
       
        if ($response['status_code'] === 200) {
            $key = array_search('Trailer', array_column($response['content']['results'], 'type'));
            if($key !== false){
                return $response['content']['results'][$key];
            }
            return [];
        } else {
            return [];
        }
    }
    
    public function getKeywords(string $keyword): JsonResponse
    {
        $url = 'https://api.themoviedb.org/3/search/keyword?query='.$keyword;
        $data = [];
        $response = $this->makeApiRequest('GET', $url, $data);
        if ($response['status_code'] === 200) {
            return new JsonResponse($response['content']['results']);
        } else {
            return [];
        }
    }

    public function ratings(int $movieId, float $score): JsonResponse
    {
        $url = 'https://api.themoviedb.org/3/movie/'.$movieId.'/rating';
        $data = [
            'value' => $score,
        ];
        $response = $this->makeApiRequest('POST', $url, $data);
        if ($response['status_code'] === 201) {
            return new JsonResponse($response['content']);
        } else {
            return [];
        }
    }    
}
