<?php

namespace App\Commands;

use App\Application;

use App\Database\SQLite;

use App\EventSender\EventSender;

use App\Models\Event;
use Exception;

//use App\Models\EventDto;

class HandleEventsCommand extends Command

{

    protected Application $app;

    public function __construct(Application $app)

    {

        $this->app = $app;
    }

    // public function run(array $options = []): void

    // {

    //     $event = new Event(new SQLite($this->app));

    //     $events = $event->select();

    //     $eventSender = new EventSender();

    //     foreach ($events as $event) {

    //         if ($this->shouldEventBeRan($event)) {

    //             // $eventSender->sendMessage($event->receiverId, $event->text);
    //             $eventSender->sendMessage($event['receiver_id'], $event['text']);
    //         }
    //     }
    // }

    private function shouldEventBeRan($event): bool

    {
        $currentMinute = date("i");

        $currentHour = date("H");

        $currentDay = date("d");

        $currentMonth = date("m");

        $currentWeekday = date("w");

        return ((!$event['minute'] || $event['minute'] === $currentMinute) &&

            (!$event['hour'] || $event['hour'] === $currentHour) &&

            (!$event['day'] || $event['day'] === $currentDay) &&

            (!$event['month'] || $event['month'] === $currentMonth) &&

            (!$event['day_of_week'] || $event['day_of_week'] === $currentWeekday));
    }

    public function run(array $options = []): void
    {
        $cronlogPath='/home/jane/php-around/seminar1/cron.log';
        $event = new Event(new SQLite($this->app));
        $events = $event->select();
        $eventSender = new EventSender();

        // Пишу логи
        $logMessage = date('Y-m-d H:i:s') . " - Checking events\n";
        error_log($logMessage, 3, $cronlogPath);

        foreach ($events as $event) {
            if ($this->shouldEventBeRan($event)) {
                try {
                    $eventSender->sendMessage($event['receiver_id'], $event['text']);
                    error_log(date('Y-m-d H:i:s') . " - Sent message to {$event['receiver_id']}\n", 3, $cronlogPath);
                } catch (Exception $e) {
                    error_log(date('Y-m-d H:i:s') . " - Error: {$e->getMessage()}\n", 3, $cronlogPath);
                }
            }
        }
    }
}
