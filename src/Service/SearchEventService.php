<?php

namespace App\Service;

use App\Model\SearchEvent;
use Symfony\Component\Serializer\SerializerInterface;

class SearchEventService
{
    private string $url = "https://public.opendatasoft.com/api/records/1.0/search/?dataset=evenements-publics-openagenda";
    private SerializerInterface $serializer;

    public function __construct(
        SerializerInterface $serializer
    ) {
        $this->serializer = $serializer;
    }

    public function searchEvents(
        SearchEvent $event
    ): mixed {

        if ($event->getCity() != null) {
            $this->url .= "&refine.location_city=" . $event->getCity();
        }

        if ($event->getStartDate() != null) {
            $startDate = date_format($event->getStartDate(), "Y-m-d");
            $this->url .= "&refine.firstdate_begin=" . $startDate;
        }

        $content = file_get_contents($this->url);
        $events = $this->serializer->decode($content, 'json')['records'];

        return $events;
    }
}