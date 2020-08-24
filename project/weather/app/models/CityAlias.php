<?php declare(strict_types=1);

namespace Weather\Model;

use Phalcon\Mvc\Model;

/**
 * Class CityAlias
 */
class CityAlias extends Model
{
    protected $id;
    protected $alias;
    protected $city_id;

    /**
     * @return int|null
     */
    public function getId() : ?int
    {
        return $this->id ? (int) $this->id : null;
    }

    /**
     * @return string|null
     */
    public function getAlias() : ?string
    {
        return $this->alias ?: null;
    }

    /**
     * @param string $alias
     * @return $this
     */
    public function setAlias(string $alias) : self
    {
        $this->alias = City::normalizeName($alias);
        return $this;
    }

    /**
     * @return City
     */
    public function getCity() : ?City
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->city_id ?
            City::findFirst($this->city_id) :
            null;
    }

    /**
     * @return string|null
     */
    public function getCityName() : ?string
    {
        return $this->getCity() ?
            $this->getCity()->getName() :
            null;
    }

    /**
     * @param City $city
     * @return bool
     */
    public function setCity(City $city) : bool
    {
        if (!$city->getId()) {
            return false;
        }

        $this->city_id = $city->getId();
        return true;
    }

    /**
     * @param City $city
     * @param string $alias
     * @return bool
     */
    public static function add(City $city, string $alias) : bool
    {
        if (!$city->getName() || $city->getName() == $alias) {
            return false;
        }
        $cityAlias = new CityAlias(compact('alias'));
        if (!$cityAlias->setCity($city)) {
            return false;
        }
        return $cityAlias->save();
    }

    /**
     * @param string $alias
     * @return CityAlias|null
     * @noinspection PhpIncompatibleReturnTypeInspection
     */
    public static function findByAlias(string $alias) : ?CityAlias
    {
        return static::findFirst(
            [
                'conditions' => 'alias = :alias:',
                'bind'       => compact('alias')
            ]
        );
    }
}
