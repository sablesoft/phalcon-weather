<?php declare(strict_types=1);

namespace Weather\Component;

use Phalcon\Storage\Serializer\Json;

/**
 * Class Serializer
 * @package Weather\Component
 */
class Serializer extends Json
{
    /**
     * @param mixed $data
     * @return void
     */
    public function unserialize($data) : void
    {
        $this->data = json_decode($data, true);
    }
}
