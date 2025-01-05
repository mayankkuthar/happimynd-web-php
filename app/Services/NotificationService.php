<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\SendNotification;
use Illuminate\Support\Facades\Notification;

class NotificationService
{
    const NOTIFICATION_SENT_STATUS = FALSE;
    const DIRECT_MESSAGE = "Direct Message";
    const ORDER = "Order";
    const QUOTE = "Quote";

    protected $notification_data = [
        'web' => [],
        'email' => []
    ];

    public function preprareWebData($message)
    {
        $this->notification_data['web'] = [
            'header' => $this->header,
            'type' => $this->type,
            'msg' => $message
        ];
    }
    public function toUser($user)
    {
        $this->user = $user;
        return $this;
    }
    public function sendNotification($message)
    {
        $this->preprareWebData($message);
        if (Notification::send($this->user, new $this->notificationType($this->notification_data))) {
            $this->NOTIFICATION_SENT_STATUS = TRUE;
        }
        return $this;
    }

    public function ofType($type)
    {
        switch ($type) {
            case "Direct Message":
                $this->header = "New Direct Message";
                $this->type = $type;
                $this->notificationType = SendNotification::class;
                break;
            case "Order":
                $this->header = "Order Confirmed";
                $this->type = $type;
                $this->notificationType = SendNotification::class;
                break;
            case "Quote":
                $this->header = "Quote";
                $this->type = $type;
                $this->notificationType = SendNotification::class;
                break;
        }
        return $this;
    }

    public function sendDirectMessage($message)
    {
        $this->ofType(self::DIRECT_MESSAGE)->sendNotification($message);
    }

    public function sendQuoteMessage($message)
    {
        $this->ofType(self::QUOTE)->sendNotification($message);
    }

    public function sendOrderMessage($message)
    {
        $this->ofType(self::ORDER)->sendNotification($message);
    }
}
