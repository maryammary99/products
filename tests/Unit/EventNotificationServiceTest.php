<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\EventNotificationService;
use Illuminate\Support\Facades\Log;

class EventNotificationServiceTest extends TestCase
{
    public function test_event_notification_process()
    {
        $notificationService = new EventNotificationService();

        $start = microtime(true);
        $notificationService->processNotifications([]);
        $end = microtime(true);

        $this->assertGreaterThanOrEqual(2, $end - $start); // Check for delay of at least 2 seconds
    }
}
