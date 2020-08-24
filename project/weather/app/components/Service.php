<?php declare(strict_types=1);

namespace Weather\Component;

use SableSoft\OpenWeather\Response;
use SableSoft\OpenWeather\Service as Api;
use Weather\Model\City;
use Weather\Model\CityAlias;

/**
 * Class Service
 * @package Weather\Components
 */
class Service {

    const PARAM_FETCH_LIMIT = 'fetchLimit';

    /**
     * @var Api
     */
    protected $api;

    /**
     * @var Cache
     */
    protected $cache;

    /**
     * @var string|null
     */
    protected $error;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @var int|null
     */
    protected $fetchLimit;

    /**
     * Service constructor.
     * @param Api $api
     * @param Cache $cache
     * @param array $params
     */
    public function __construct(Api $api, Cache $cache, array $params = [])
    {
        $this->api = $api;
        $this->cache = $cache;
        $this->fetchLimit = $params[static::PARAM_FETCH_LIMIT] ?? null;
    }

    /**
     * @return string|null
     */
    public function getError() : ?string
    {
        return $this->error;
    }

    /**
     * @return array|null
     */
    public function getData() : ?array
    {
        return $this->response ?
            $this->response->getBody() :
            null;
    }

    /**
     * @return string|null
     */
    public function getGeoName() : ?string
    {
        return $this->response ?
            $this->response->getName() :
            null;
    }

    /**
     * @param float $latitude
     * @param float $longitude
     * @return bool
     */
    public function callByGeoCoordinates(float $latitude, float $longitude) : bool
    {
        try {
            // try get api data by geo coordinates:
            $response = $this->api->get($latitude, $longitude);
            if ($response->isValid()) {
                $this->response = $response;
                // update counter if this city already in db:
                $geoName = $this->getGeoName();
                if (City::findByName($geoName)) {
                    $this->addCity();
                }
                return true;
            }
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
        }

        return false;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function callByCityName(string $name) : bool
    {
        // normalize raw input:
        $name = City::normalizeName($name);
        // check city name by aliases:
        $name = $this->checkAlias($name);
        try {
            // try get api data by city name:
            $response = $this->api->getByCity($name);
            if ($response->isValid()) {
                $this->response = $response;
                // update city counter:
                $this->addCity($name);
                return true;
            }
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
        }

        return false;
    }

    /**
     * @param string $check
     * @return string|null
     */
    public function suggestCityName(string $check) : ?string
    {
        $check = City::normalizeName($check);
        $shortest = -1;
        $closest = null;
        $names = $this->cache->getCities() ?: City::fetchNames($check);
        foreach ($names as $name) {
            $lev = levenshtein($check, $name);
            if ($lev == 0) {
                $closest = $name;
                break;
            }
            if ($lev <= $shortest || $shortest < 0) {
                $closest  = $name;
                $shortest = $lev;
            }
        }

        return $closest;
    }

    /**
     * @param string $alias
     * @param string $name
     * @return bool
     */
    public function addAlias(string $alias, string $name) : bool
    {
        $alias = City::normalizeName($alias);
        $name = City::normalizeName($name);
        // check are identical:
        if ($alias === $name) {
            $this->error = 'Alias and name are identical!';
            return false;
        }

        // check alias already exist:
        if ($this->checkAlias($alias) === $name) {
            $this->error = 'Alias already exists!';
            return false;
        }

        // check city with this name exists:
        if (!$city = City::findByName($name)) {
            $this->error = "City with name '$name' not founded!";
            return false;
        }
        $this->cache->addAlias($alias, $name);
        CityAlias::add($city, $alias);

        return true;
    }

    /**
     * @param null|string $alias
     * @return Service
     */
    protected function addCity(string $alias = null) : self
    {
        $apiCityName = $this->getGeoName();
        // add city name in cache and db:
        $this->cache->addCity($apiCityName);
        $city = City::add($apiCityName);
        // add alias in cache and db:
        if ($alias && $apiCityName !== $alias) {
            $this->cache->addAlias($alias, $apiCityName);
            CityAlias::add($city, $alias);
        }

        return $this;
    }

    /**
     * @param string $check
     * @return string
     */
    protected function checkAlias(string $check) : string
    {
        // check alias in cache:
        if ($name = $this->cache->getNameByAlias($check)) {
            return $name;
        }

        // check alias in db:
        $alias = CityAlias::findByAlias($check);
        if ($alias) {
            $name = $alias->getCityName();
            $this->cache->addAlias($alias->getAlias(), $name);
            return $name;
        }

        return $check;
    }
}
