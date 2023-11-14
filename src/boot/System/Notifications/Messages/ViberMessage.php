<?php

namespace Boot\System\Notifications\Messages;

use Illuminate\Support\Facades\View;
use JsonSerializable;
use NotificationChannels\Telegram\Traits\HasSharedLogic;

/**
 * Class TelegramMessage.
 */
class ViberMessage implements JsonSerializable
{
    protected $payload = [];
    
    public function __construct(string $content = '')
    {
        $this->content($content);
        $this->payload['parse_mode'] = 'Markdown';
    }

    public function to($chatId): self
    {
        $this->payload['chat_id'] = $chatId;

        return $this;
    }

    /**
     * Get payload value for given key.
     *
     * @return null|mixed
     */
    public function getPayloadValue(string $key)
    {
        return $this->payload[$key] ?? null;
    }

    /**
     * Returns params payload.
     */
    public function toArray(): array
    {
        return $this->payload;
    }

    /**
     * Convert the object into something JSON serializable.
     *
     * @return mixed
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

 

    public static function create(string $content = ''): self
    {
        return new self($content);
    }

    /**
     * Notification message (Supports Markdown).
     *
     * @return $this
     */
    public function content(string $content, int $limit = null): self
    {
        $this->payload['text'] = $content;

        if ($limit) {
            $this->chunkSize = $limit;
        }

        return $this;
    }

    /**
     * Attach a view file as the content for the notification.
     * Supports Laravel blade template.
     *
     * @return $this
     */
    public function view(string $view, array $data = [], array $mergeData = []): self
    {
        return $this->content(View::make($view, $data, $mergeData)->render());
    }

   
    /**
     * Should the message be chunked.
     */
    public function shouldChunk(): bool
    {
        return null !== $this->chunkSize;
    }
}
