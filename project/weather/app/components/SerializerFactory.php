<?php declare(strict_types=1);

namespace Weather\Component;

use Phalcon\Storage\Exception;
use Phalcon\Storage\Serializer\SerializerInterface;

/**
 * Class SerializerFactory
 * @package Weather\Component
 */
class SerializerFactory extends \Phalcon\Storage\SerializerFactory
{
    /**
     * @param string $name
     * @return SerializerInterface
     * @throws Exception
     */
    public function newInstance(string $name): SerializerInterface
    {
        if (ucfirst($name) === 'Json') {
            return new Serializer();
        }
        return parent::newInstance($name);
    }
}
