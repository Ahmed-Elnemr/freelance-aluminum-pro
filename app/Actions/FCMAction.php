<?php

namespace App\Actions;

use Illuminate\Database\Eloquent\Model;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\AndroidConfig;
use Kreait\Firebase\Messaging\ApnsConfig;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Messaging\WebPushConfig;

class FCMAction
{
    /** @var list<string> */
    protected array $firebaseTokens = [];

    protected ?string $messageType = 'default';

    protected ?string $topic = 'topic';

    protected ?Messaging $messaging;

    protected array $data = ['fcmData' => 'Start data'];

    private string $title = '';

    private string $body = '';

    private string $imageUrl = '';

    private ?string $clickAction = null;

    public function __construct(?Model $user = null)
    {
        $this->messaging = app('firebase.messaging');
        $this->firebaseTokens = $user
            ? $user->devices()
                ->whereNotNull('token')
                ->pluck('token')
                ->filter()
                ->unique()
                ->values()
                ->all()
            : [];
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
        $this->data['url'] = $clickAction;
        $this->data['click_action'] = $clickAction;

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

    public function sendMessage($type = 'topic'): void
    {
        $message = $this->createMessage()
            ->withHighestPossiblePriority()
            ->withApnsConfig(ApnsConfig::fromArray([
                'payload' => [
                    'aps' => [
                        'sound' => 'note.mp3',
                    ],
                ],
                'fcm_options' => [],
                'headers' => [],
            ]))
            ->withAndroidConfig(
                AndroidConfig::fromArray([
                    'notification' => [
                        'sound' => 'note.mp3',
                    ],
                ])
            );

        if ($this->clickAction) {
            $message = $message->withWebPushConfig(WebPushConfig::fromArray([
                'fcm_options' => [
                    'link' => $this->clickAction,
                ],
            ]));
        }

        if ($type === 'tokens') {
            if ($this->firebaseTokens !== []) {
                $this->messaging->sendMulticast($message, $this->firebaseTokens);
            }
        }

        if ($type === 'topic') {
            $this->messaging->send($message);
        }
    }

    private function createMessage(): CloudMessage
    {
        return CloudMessage::withTarget('topic', $this->topic)
            ->withNotification(Notification::create($this->title, $this->body, $this->imageUrl ?: null))
            ->withData($this->data);
    }
}
