<?php

namespace Booking\Sale;


use Booking\Event\TicketEvent;
use Booking\Schedule\Operator;
use Booking\Store\Adapter\UzDataAtapter;
use Booking\Store\InfluxStore;
use InfluxDB\Point;

class SalePerson
{
    /**
     * @var Operator
     */
    private $schedule;
    /**
     * @var InfluxStore
     */
    private $store;
    /**
     * @var UzDataAtapter
     */
    private $adapter;

    /**
     * TicketProcessor constructor.
     * @param Operator $schedule
     * @param InfluxStore $store
     * @param UzDataAtapter $adapter
     */
    public function __construct(Operator $schedule, InfluxStore $store, UzDataAtapter $adapter)
    {
        $this->schedule = $schedule;
        $this->store = $store;
        $this->adapter = $adapter;
    }

    /**
     * @param TicketEvent $event
     * @throws \Exception
     */
    public function checkAvailableTicket(TicketEvent $event)
    {
        $trainsData = $this->schedule->search(
            $event->getFromStationId(),
            $event->getToStationId(),
            $event->getDate()
        );

        $this->storeRequest($event);

        $trainData = $this->adapter->convert($trainsData);

        if (empty($trainData)) {
            return;
        }

        foreach ($trainData as $train) {
            $next = $train['value'];
            $prev = $this->getPreviousValue($train);
            if ($next - $prev > 0) { // new tickets available
                $this->triggerNewTicketsEvent($train, $next - $prev);
            }
        }

        $this->store->write(
            $this->arrayToPoints($trainData)
        );

    }

    /**
     * @param TicketEvent $event
     * @throws \Exception
     */
    private function storeRequest(TicketEvent $event)
    {
        $this->store->writePoints(
            [new Point(
                'ticket.uz.request',
                1,
                [
                    'from' => $event->getFromStationId(),
                    'to' => $event->getToStationId(),
                    'date' => $event->getDate()->format('Y-m-d'),
                ]
            )]
        );
    }

    /**
     * @param $data
     * @return array
     */
    private function arrayToPoints($data)
    {
        $pointsList = [];
        foreach ($data as $item) {
            $pointsList[] = new Point(
                $item['measure'],
                $item['value'],
                $item['tags']
            );
        }
        return $pointsList;
    }

    private function buildQuery($item)
    {
        $fields = ['train_num', 'train_from_station_id', 'train_to_station_id', 'train_date_day', 'train_type_letter'];
        $tags = array_intersect_key($item['tags'], array_combine($fields, $fields));
        $sql = 'SELECT last("value") AS "value" FROM "%s" WHERE %s';

        array_walk(
            $tags,
            function (&$value, $key) {
                $value = sprintf('"%s" = \'%s\'', $key, $value);
            }
        );

        $query = sprintf($sql, $item['measure'], implode(
                ' AND ',
                $tags
            )
        );

        return $query;
    }

    private function getPreviousValue($train)
    {
        $result = $this->store->query($this->buildQuery($train))->getPoints('tickets.amount');
        return isset($result[0]['value']) ? $result[0]['value'] : 0;
    }

    private function triggerNewTicketsEvent($train, $count)
    {
        $sensitivity = [
            'К' => [20, 36],
            'П' => [25, 52],
            'Л' => [10, 18],
            'С1' => [30, 36], // fixme
            'С2' => [50, 50], // fixme
        ];
        $type = $train['train_type_letter'];

        if (false === isset($sensitivity[$type][0])) {
            return;
        }

        if ($count > $sensitivity[$type][0]) {
            // new tickets
            $this->store->writePoints(
                [new Point(
                    'tickets.carriage',
                    ceil($count/$sensitivity[$type][1]),
                    $train['tags']
                )]
            );
        }

        $this->store->writePoints(
            [new Point(
                'tickets.new_tickets',
                $count,
                $train['tags']
            )]
        );
    }
}