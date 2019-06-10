<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 5/26/17
 * Time: 10:29 AM
 */
namespace App\Normalizer\Subscriber;

use Doctrine\ORM\Tools\Pagination\Paginator;
use JMS\Serializer\Context;
use JMS\Serializer\GraphNavigatorInterface;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\JsonSerializationVisitor;



class PaginatorSubscriber implements SubscribingHandlerInterface
{

    /**
     * Return format:
     *
     *      array(
     *          array(
     *              'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
     *              'format' => 'json',
     *              'type' => 'DateTime',
     *              'method' => 'serializeDateTimeToJson',
     *          ),
     *      )
     *
     * The direction and method keys can be omitted.
     *
     * @return array
     */
    public static function getSubscribingMethods()
    {
        return array(
            array(
                'direction' => GraphNavigatorInterface::DIRECTION_SERIALIZATION,
                'format' => 'json',
                'type' => Paginator::class,
                'method' => 'serializePaginator',
            ),
        );
    }

    public function serializePaginator(JsonSerializationVisitor $visitor, Paginator $date, array $type, Context $context)
    {
        $query = $date->getQuery();
        $count = $date->count();
        $perPage = $query->getMaxResults();
        $offset = $query->getFirstResult();


        return [
            "count" => $count,
            "per_page" => $perPage,
            "page" => (int)ceil( $offset/$perPage),
            "result" =>
                $context->getNavigator()->accept($query->getResult())
        ];
    }
}