<?php

namespace App\Livewire;

use App\Events\PesanKonselingTerkirim;
use App\Models\KonselingDigital;
use App\Models\PesanKonseling;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Locked;

class KonselingChatRoom extends Component
{
    #[Locked]
    public int $konselingId;
    public string $pesanBaru = '';

    protected function rules(): array
    {
        return [
            'pesanBaru' => 'required|string|min:1|max:2000',
        ];
    }

    public function mount(int $konselingId): void
    {
        $this->konselingId = $konselingId;

        $konseling = $this->authorizedKonseling();
        $user = Auth::user();

        // Tandai pesan sebagai telah dibaca jika pengirim bukan diri sendiri
        PesanKonseling::where('konseling_id', $konselingId)
            ->where('sender_id', '!=', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }

    public function kirimPesan(): void
    {
        $konseling = $this->authorizedKonseling();
        $this->validate();
        $user = Auth::user();

        // Validasi status sesi
        if (! $konseling->bisaKirimPesan()) {
            $this->addError('pesanBaru', 'Pesan tidak dapat dikirim karena sesi konsultasi belum aktif atau telah selesai.');
            return;
        }

        $pesan = PesanKonseling::create([
            'konseling_id' => $konseling->id,
            'sender_id' => $user->id,
            'pesan' => trim($this->pesanBaru),
            'is_read' => false,
        ]);

        // Broadcast Event jika broadcasting terpasang
        try {
            event(new PesanKonselingTerkirim($pesan));
        } catch (\Throwable $e) {
            // Silently fallback to reactive Livewire polling if broadcast driver fails
        }

        $this->reset('pesanBaru');
        $this->dispatch('pesan-terkirim');
    }

    public function render()
    {
        $konseling = $this->authorizedKonseling(['user', 'guru']);
        $pesans = PesanKonseling::with('sender')
            ->where('konseling_id', $this->konselingId)
            ->oldest()
            ->get();

        // Tandai pesan masuk yang belum dibaca
        $currentUserId = Auth::id();
        PesanKonseling::where('konseling_id', $this->konselingId)
            ->where('sender_id', '!=', $currentUserId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return view('livewire.konseling-chat-room', [
            'konseling' => $konseling,
            'pesans' => $pesans,
            'currentUser' => Auth::user(),
        ]);
    }

    /**
     * Resolve and authorize the current consultation before any read or write.
     * Public Livewire state is treated as untrusted on every component request.
     */
    private function authorizedKonseling(array $with = []): KonselingDigital
    {
        $user = Auth::user();
        abort_unless($user, 403, 'Akses ditolak.');

        $konseling = KonselingDigital::findOrFail($this->konselingId);
        $isParticipant = (int) $user->id === (int) $konseling->user_id;
        $isAssignedGuru = $user->isGuru()
            && $konseling->guru_id !== null
            && (int) $konseling->guru_id === (int) $user->id;

        abort_unless($isParticipant || $isAssignedGuru, 403, 'Anda tidak memiliki hak akses ke ruang konsultasi ini.');

        if ($with !== []) {
            $konseling->load($with);
        }

        return $konseling;
    }
}
