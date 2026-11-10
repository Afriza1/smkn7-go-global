<?php

namespace App\Events;

use App\Models\PesanKonseling;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PesanKonselingTerkirim implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public PesanKonseling $pesan;

    public function __construct(PesanKonseling $pesan)
    {
        $this->pesan = $pesan->load('sender');
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('konseling.' . $this->pesan->konseling_id),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'pesan.terkirim';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->pesan->id,
            'konseling_id' => $this->pesan->konseling_id,
            'sender_id' => $this->pesan->sender_id,
            'sender_name' => $this->pesan->sender->name,
            'sender_role' => $this->pesan->sender->role,
            'pesan' => $this->pesan->pesan,
            'created_at' => $this->pesan->created_at->format('H:i') . ' WIB',
        ];
    }
}
