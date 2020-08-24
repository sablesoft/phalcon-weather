<?php declare(strict_types=1);

namespace Weather\Controller;

use Phalcon\Mvc\Controller;
use Phalcon\Http\ResponseInterface;
use Weather\Component\Service;

/**
 * Class IndexController
 * @package Weather\Controller
 */
class IndexController extends Controller
{
    /**
     * @var Service
     */
    protected $service;

    public function onConstruct()
    {
        $this->service = $this->di->get('service');
    }

    /**
     * @return ResponseInterface
     */
    public function helpAction()
    {
        return (new \Phalcon\Http\Response())
            ->setJsonContent([
                'help' => [
                    'description' => 'This help',
                    'route' => '/'
                ],
                'callByCityName' => [
                    'route' => '/city/{name}',
                    'params' => ['{name}'],
                    'description' => 'Call OpenWeather API by city {name}',
                    'response'    => [
                        'founded' => [
                            'description' => 'If city is founded in OpenWeather API return weather forecast',
                        ],
                        'notFounded' => [
                            'description' => 'If city is not founded in OpenWeather API return city name suggest',
                            'template'   => ['suggest' => '{name}']
                        ]
                    ]
                ],
                'callByGeoCoordinates' => [
                    'route' => '/geo/{latitude}/{longitude}',
                    'params' => ['{latitude}', '{longitude}'],
                    'description' => 'Call OpenWeather API by {latitude} and {longitude} geo coordinates',
                    'response' => [
                        'founded' => [
                            'description' => 'If weather forecast founded by given geo coordinates - return it'
                        ],
                        'notFounded' => [
                            'description' => 'If weather forecast not founded by given geo coordinates - return fail message',
                            'template' => ['fail' => 'Weather for this geo coordinates not founded!']
                        ]
                    ]
                ],
                'addAlias' => [
                    'route' => '/alias/{alias}/name/{name}',
                    'params' => ['{alias}', '{name}'],
                    'description' => 'Add city {alias} for given {name}',
                    'response' => [
                        'success' => [
                            'description' => 'Alias for given city name added successfully',
                            'template'  => ['success'=> 'Alias added successfully']
                        ],
                        'error' => [
                            'description' => 'Something was wrong',
                            'template'  => ['error'=> '{error}']
                        ]
                    ]
                ],
                'allCities' => [
                    'route' => '/db/city',
                    'description' => 'Get list of all successfully called city names'
                ],
                'allAliases' => [
                    'route' => '/db/alias',
                    'description' => 'Get list of all successfully added city aliases'
                ]
            ])
            ->send();
    }

    /**
     * @param string $name
     * @return ResponseInterface
     */
    public function cityAction(string $name)
    {
        $name = urldecode($name);
        /** @var Service $api */
        if ($this->service->callByCityName($name)) {
            $data = $this->service->getData();
        } else {
            $data = ['suggest' => $this->service->suggestCityName($name)];
        }

        return (new \Phalcon\Http\Response())
            ->setJsonContent($data)
            ->send();
    }

    /**
     * @param string $alias
     * @param string $name
     * @return ResponseInterface
     */
    public function aliasAction(string $alias, string $name)
    {
        $data = [];
        $name = urldecode($name);
        $alias = urldecode($alias);
        if (!$this->service->addAlias($alias, $name)) {
            $data['error'] = $this->service->getError();
        } else {
            $data['success'] = 'Alias added successfully';
        }

        return (new \Phalcon\Http\Response())
            ->setJsonContent($data)
            ->send();
    }

    /**
     * @param float $latitude
     * @param float $longitude
     * @return ResponseInterface
     */
    public function geoAction(float $latitude, float $longitude)
    {
        if ($this->service->callByGeoCoordinates($latitude, $longitude)) {
            $data = $this->service->getData();
        } else {
            $data = ['fail' => 'Weather for this geo coordinates not founded!'];
        }
        return (new \Phalcon\Http\Response())
            ->setJsonContent($data)
            ->send();
    }
}
