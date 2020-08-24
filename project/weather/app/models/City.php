<?php declare(strict_types=1);

namespace Weather\Model;

use DateTime;
use Exception;
use InvalidArgumentException;
use Phalcon\Di;
use Phalcon\Helper\Arr;
use Phalcon\Mvc\Model;
use Phalcon\Db\Adapter\Pdo\AbstractPdo;

/**
 * Class City
 * @package Weather\Models
 */
class City extends Model
{
    const FETCH_LIMIT = 50;

    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $counter;

    /**
     * @var string
     */
    protected $last_touch;

    /**
     * @return null|int
     */
    public function getId() : ?int
    {
        return $this->id ? (int) $this->id : null;
    }

    /**
     * @return null|string
     */
    public function getName() : ?string
    {
        return $this->name ?: null;
    }

    /**
     * @return int
     */
    public function getCounter() : int
    {
        return (int) $this->counter;
    }

    /**
     * @return DateTime
     * @throws Exception
     */
    public function getLastTouch() : ?DateTime
    {
        return $this->last_touch ?
            new DateTime($this->last_touch) :
            null;
    }

    /**
     * @param string $name
     * @return City
     */
    public function setName(string $name) : self
    {
        if (strlen($name) < 3) {
            throw new InvalidArgumentException(
                'The name is too short'
            );
        }

        $this->name = static::normalizeName($name);

        return $this;
    }

    /**
     * @return bool
     */
    public function touch() : bool
    {
        if (!$this->name) {
            return false;
        }
        $this->counter += 1;
        $this->last_touch = date('Y-m-d H:i:s');
        return $this->save();
    }

    /**
     * @param string $name
     * @return null|City
     */
    public static function add(string $name) : ?City
    {
        $city = static::findFirst([
            'conditions' => 'name = :name:',
            'bind'       => compact('name')
        ]) ?? new City(compact('name'));

        return $city->touch() ? $city : null;
    }

    /**
     * @param string $name
     * @return string
     */
    public static function normalizeName(string $name) : string
    {
        $name = preg_replace("/-+/","-", $name);
        $name = preg_replace("/ +/"," ", $name);
        $name = trim($name, '- ');
        $pieces = explode('-', $name);
        foreach ($pieces as &$piece) {
            $piece = trim($piece, ' - ');
        }

        $name = implode('-', $pieces);

        return ucwords(strtolower($name), ' -');
    }

    /**
     * Fetch names with limit in to parts - by alias first letter
     *
     * @param string $check
     * @param int $limit
     * @return array
     */
    public static function fetchNames(string $check, int $limit = null) : array
    {
        $limit = $limit ?: static::FETCH_LIMIT;
        $firstChar = mb_substr($check, 0, 1, "UTF-8");
        $subQuery = "SELECT name FROM city WHERE name {condition} ORDER BY counter LIMIT $limit";
        $first = str_replace('{condition}', "LIKE '$firstChar%'", $subQuery);
        $second = str_replace('{condition}', "NOT LIKE '$firstChar%'", $subQuery);
        /** @var AbstractPdo $db */
        $db = Di::getDefault()->get('db');
        $names = $db->fetchAll(
            "SELECT name FROM ($first) as first UNION SELECT name FROM ($second) as second;"
        );

        return Arr::pluck($names, 'name');
    }

    /**
     * @param string $name
     * @return City|null
     * @noinspection PhpIncompatibleReturnTypeInspection
     */
    public static function findByName(string $name) : ?City
    {
        return static::findFirst(
            [
                'conditions' => 'name = :name:',
                'bind'       => compact('name')
            ]
        );
    }
}
