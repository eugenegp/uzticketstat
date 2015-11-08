<?php

namespace Booking\Adapter;


use InfluxDB\Point;
use Symfony\Component\PropertyAccess\PropertyAccess;

class ArrayToPoint
{

    /**
     * @param array $trainsList
     * @return Point[]
     */
    public static function convert($trainsList)
    {
        $accessor = PropertyAccess::createPropertyAccessor();
        $measure = [];
        foreach($accessor->getValue($trainsList, 'value') as $train) {
            $tags = [];
            $tags['train_num'] = $accessor->getValue($train, 'num');
            $tags['train_from_station_id'] = $accessor->getValue($train, 'from.station_id');
            $tags['train_from_station'] = $accessor->getValue($train, 'from.station');
            $tags['train_from_date'] = $accessor->getValue($train, 'from.date');
            $tags['train_from_date_rc'] = $accessor->getValue($train, 'from.src_date');
            $tags['train_to_station_id'] = $accessor->getValue($train, 'till.station_id');
            $tags['train_to_station'] = $accessor->getValue($train, 'till.station');
            $tags['train_to_date'] = $accessor->getValue($train, 'till.date');
            $tags['train_to_date_rc'] = $accessor->getValue($train, 'till.src_date');
            foreach($accessor->getValue($train, 'types') as $type) {
                $additionalTags = [];
                $additionalTags['train_type_title'] = $accessor->getValue($type, 'title');
                $additionalTags['train_type_letter'] = $accessor->getValue($type, 'letter');
                $measure[] = new Point(
                    'tickets.amount',
                    $accessor->getValue($type, 'places'),
                    array_merge($tags, $additionalTags)
                );
            }
        }
        return $measure;
    }
}