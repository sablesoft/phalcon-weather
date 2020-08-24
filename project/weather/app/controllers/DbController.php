<?php declare(strict_types=1);

namespace Weather\Controller;

use Phalcon\Http\Response;
use Phalcon\Mvc\Controller;
use Phalcon\Http\ResponseInterface;
use Weather\Model\City;
use Weather\Model\CityAlias;

/**
 * Class DbController
 */
class DbController extends Controller
{
    /**
     * @return ResponseInterface
     */
    public function cityAction()
    {
        return (new Response())
            ->setJsonContent(City::find()->toArray())
            ->send();
    }

    /**
     * @return ResponseInterface
     */
    public function aliasAction()
    {
        return (new Response())
            ->setJsonContent(CityAlias::find()->toArray())
            ->send();
    }
}
