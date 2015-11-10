<?php

namespace Booking\Store\Adapter;


use InfluxDB\Point;
use Symfony\Component\PropertyAccess\PropertyAccess;

class JsonToPoint
{

    /**
     * @param string $jsonData
     * @return Point[]
     */
    public function convert($jsonData)
    {
        $trainsList = json_decode($jsonData);
        $accessor = PropertyAccess::createPropertyAccessor();
        $measure = [];
        $trains = $accessor->getValue($trainsList, 'value');
        if (empty($trains) || !is_array($trains)) {
          return [];
        }
        foreach($trains as $train) {
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
            $date = \DateTime::createFromFormat('Y-m-d H:i:s', $tags['train_from_date_rc']);
            $tags['train_date_day'] = $date->format('Ymd'); // need to be not date format
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