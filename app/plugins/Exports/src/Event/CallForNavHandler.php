<?php

namespace Exports\Event;

use Cake\Event\EventListenerInterface;
use App\KMP\StaticHelpers;

class CallForNavHandler implements EventListenerInterface
{
    public function implementedEvents(): array
    {
        return [
            // Custom event names let you design your application events
            // as required.
            \App\View\Cell\NavigationCell::VIEW_CALL_EVENT => 'callForNav',
        ];
    }

    public function callForNav($event)
    {
        // if (StaticHelpers::pluginEnabled('Exports') == false) {
        //     return null;
        // }
        $user = $event->getData('user');
        $results = [];
        if ($event->getResult() && is_array($event->getResult())) {
            $results = $event->getResult();
        }
        $appNav = [


            [
                "type" => "link",
                "mergePath" => ["Members"],
                "label" => "Exports",
                "order" => 40,
                "url" => [
                    "controller" => "Exports",
                    "plugin" => "Exports",
                    "action" => "test",

                ],
                "icon" => "bi-archive",

            ],

        ];

        $results = array_merge($results, $appNav);
        return $results;
    }
}