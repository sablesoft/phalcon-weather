<?php declare(strict_types=1);

namespace Weather\Component;

use Phalcon\Storage\Adapter\AbstractAdapter;

/**
 * Class Cache
 * @package Weather\Component
 */
class Cache
{
    const KEY_CITY = 'CITY';
    const KEY_PREFIX_ALIAS = 'ALIAS:';

    /**
     * @var AbstractAdapter
     */
    protected $adapter;

    /**
     * Cache constructor.
     * @param AbstractAdapter $adapter
     */
    public function __construct(AbstractAdapter $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * Update cities cache
     *
     * @param string $name
     * @return $this
     */
    public function addCity(string $name) : self
    {
        $cities = $this->adapter->get(static::KEY_CITY, []);
        $cities[$name] = isset($cities[$name]) ?
            ++$cities[$name] :
            1;
        $this->adapter->set(static::KEY_CITY, $cities);

        return $this;
    }

    /**
     * @param string $name
     * @param string $alias
     * @return $this
     */
    public function addAlias(string $alias, string $name) : self
    {
        $prefix = static::KEY_PREFIX_ALIAS;
        $this->adapter->set("$prefix$alias", $name);

        return $this;
    }

    /**
     * @param string $alias
     * @return string|null
     */
    public function getNameByAlias(string $alias) : ?string
    {
        $prefix = static::KEY_PREFIX_ALIAS;

        return $this->adapter->get("$prefix$alias");
    }

    /**
     * @return array
     */
    public function getCities() : array
    {
        $cities = $this->adapter->get(static::KEY_CITY, []);
        arsort($cities);

        return array_keys($cities);
    }
}