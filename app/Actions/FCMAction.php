<?php

namespace App\Actions;

use Illuminate\Database\Eloquent\Model;

use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\AndroidConfig;
use Kreait\Firebase\Messaging\ApnsConfig;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class FCMAction
{
    protected ?string  $firebaseToken = '';

    protected ?string $messageType = 'default';

    protected ?string $topic = 'topic';

    protected ?Messaging $messaging;

    protected ?array $data = ['fcmData' => 'Start data'];

    private ?string $title = '';

    private ?string $body = '';

    private ?string $imageUrl = '';

    public function __construct(?Model $user = null)
    {

        $this->messaging = app('firebase.messaging');
        $this->firebaseToken = @$user?->devices()?->orderBy("id",'desc')->first()?->token ?? "" ;

    }

    public static function new(?Model $user): self
    {
        return new self($user);
    }

    public function withData(array $array): static
    {
        foreach ($array as $key => $value) {
            if (is_string($value)) {
                $this->data[$key] = $value;
            }

            if (is_array($value)) {
                $this->withData($value);
            }
        }

        return $this;
    }

    public function withMessageType(string $messageType): static
    {
        $this->messageType = $messageType;

        return $this;
    }

    public function withTopic(string $topic): static
    {
        $this->topic = $topic;
        return $this;
    }

    public function withClickAction(string $clickAction): static
    {
        $this->clickAction = $clickAction;

        return $this;
    }

    public function withImageUrl(string $imageUrl): static
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }

    public function withTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function withBody(string $body): static
    {
        $this->body = $body;
        return $this;
    }

    public function sendMessage($type = 'topic')
    {

        $message = $this->createMessage()->withHighestPossiblePriority()->withApnsConfig(ApnsConfig::fromArray([
            'payload' => [
                'aps' => [
                    'sound' => 'note.mp3',
                ],
            ],
            'fcm_options' => [],
            'headers' => [],
        ]))->withAndroidConfig(
            AndroidConfig::fromArray([
                'notification' => [
                    'sound' => 'note.mp3',
                ],
            ])
        );
        if ($type === 'tokens') {

            if (! empty($this->firebaseToken)) {
                $this->messaging->sendMulticast($message, $this->firebaseToken);

            }
        }

        if ($type === 'topic') {
            $this->messaging->send($message);
        }

    }

    private function createMessage()
    {
        return CloudMessage::withTarget('topic', $this->topic)
            ->withNotification(Notification::create($this->title, $this->body, $this->imageUrl))
            ->withData($this->data);
    }
}
