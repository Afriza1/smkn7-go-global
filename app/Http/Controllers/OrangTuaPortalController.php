<?php

namespace App\Http\Controllers;

use App\Models\ParentStudentLink;
use App\Models\ParentStudentLinkDecision;
use App\Models\PendaftaranKarierJepang;
use App\Models\User;
use App\Services\StudentIdentity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrangTuaPortalController extends Controller
{
    public function index(Request $request)
    {
        $link = $request->user()->parentStudentLink()->with(['student', 'reviewer'])->first();
        if (! $link || $link->status !== 'approved') {
            return view('dashboard.orang-tua.status', compact('link'));
        }

        $student = $link->student;
        $applications = PendaftaranKarierJepang::with('lpk')->where('user_id', $student->id)->latest()->get();
        $pendaftaranAktif = PendaftaranKarierJepang::with(['lpk', 'adminChecker', 'bkChecker', 'kepalaSekolahChecker'])
            ->where('user_id', $student->id)
            ->whereNotIn('status', ['ditolak_admin', 'ditolak_kepala_sekolah', 'tidak_lolos_seleksi', 'mengundurkan_diri', 'proses_dihentikan'])
            ->latest()
            ->first();
        $trackerSteps = $pendaftaranAktif?->getTrackerSteps() ?? [];

        return view('dashboard.orang-tua.index', compact('link', 'student', 'applications', 'pendaftaranAktif', 'trackerSteps'));
    }

    public function storeIdentity(Request $request)
    {
        $user = $request->user();
        abort_unless($user->role === 'siswa', 403);

        if ($user->student_identity_type === 'nisn' && filled($user->student_identity_encrypted)) {
            throw ValidationException::withMessages([
                'identity_number' => 'NISN sudah tersimpan dan tidak dapat diubah melalui formulir ini.',
            ]);
        }

        $nisn = $this->validatedNisn($request);

        if ($user->studentParentLink()->whereIn('status', ['pending', 'approved'])->exists()) {
            throw ValidationException::withMessages(['identity_number' => 'Identitas tidak dapat diubah selama pengajuan atau hubungan orang tua masih aktif.']);
        }
        $user->forceFill([
            'student_identity_type' => 'nisn',
            'student_identity_encrypted' => app(StudentIdentity::class)->encryptNisn($user, $nisn),
            'student_identity_hash' => hash_hmac('sha256', 'nisn:'.$nisn, (string) config('app.key')),
        ])->save();

        return back()->with('success', 'Identitas berhasil disimpan dengan aman.');
    }

    public function submit(Request $request)
    {
        $nisn = $this->validatedNisn($request);
        $identityHash = hash_hmac('sha256', 'nisn:'.$nisn, (string) config('app.key'));
        $student = User::where('role', 'siswa')->where('student_identity_hash', $identityHash)->first();
        if (! $student) {
            throw ValidationException::withMessages(['identity_number' => 'NISN belum dapat dicocokkan. Periksa kembali 10 digit NISN yang dimasukkan.']);
        }

        DB::transaction(function () use ($request, $student): void {
            User::whereKey($student->id)->lockForUpdate()->first();
            $parent = $request->user();
            User::whereKey($parent->id)->lockForUpdate()->first();
            if (DB::table('parent_student_active_links')->where('parent_id', $parent->id)->exists()) {
                throw ValidationException::withMessages(['identity_number' => 'Akun ini sudah memiliki pengajuan hubungan.']);
            }
            if (DB::table('parent_student_active_links')->where('student_id', $student->id)->exists()) {
                throw ValidationException::withMessages(['identity_number' => 'NISN belum dapat dicocokkan. Periksa kembali 10 digit NISN yang dimasukkan.']);
            }
            $link = ParentStudentLink::create(['parent_id' => $parent->id, 'student_id' => $student->id, 'parent_name_snapshot' => $parent->name, 'student_name_snapshot' => $student->name, 'identity_type' => 'nisn', 'status' => 'pending', 'submitted_at' => now()]);
            DB::table('parent_student_active_links')->insert(['parent_id' => $parent->id, 'student_id' => $student->id, 'parent_student_link_id' => $link->id, 'created_at' => now(), 'updated_at' => now()]);
            ParentStudentLinkDecision::create(['parent_student_link_id' => $link->id, 'actor_id' => $parent->id, 'actor_name_snapshot' => $parent->name, 'decision' => 'submitted', 'decided_at' => now()]);
        });

        return redirect()->route('dashboard.orang-tua')->with('success', 'Pengajuan telah dikirim dan menunggu pemeriksaan Guru BK.');
    }

    private function validatedNisn(Request $request): string
    {
        $request->session()->flashInput($request->except(['identity_number', 'identity_type']));

        return $request->validate([
            'identity_type' => ['prohibited'],
            'identity_number' => ['required', 'string', 'digits:10'],
        ], [
            'identity_type.prohibited' => 'Pengajuan baru hanya menggunakan NISN tanpa pilihan jenis identitas.',
            'identity_number.digits' => 'NISN harus terdiri dari tepat 10 digit angka.',
        ])['identity_number'];
    }

    public function reviewQueue()
    {
        $links = ParentStudentLink::latest('submitted_at')->paginate(20);
        return view('dashboard.guru.orang-tua.index', compact('links'));
    }

    public function reviewShow(ParentStudentLink $link)
    {
        return view('dashboard.guru.orang-tua.show', compact('link'));
    }

    public function decide(Request $request, ParentStudentLink $link)
    {
        $request->session()->flashInput($request->except('identity_number'));
        $data = $request->validate(['decision' => ['required', 'in:approved,rejected'], 'note' => ['required_if:decision,rejected', 'nullable', 'string', 'max:2000']]);
        DB::transaction(function () use ($request, $link, $data): void {
            $locked = ParentStudentLink::whereKey($link->id)->lockForUpdate()->firstOrFail();
            if ($locked->status !== 'pending') abort(409, 'Pengajuan ini sudah diproses.');
            if ($data['decision'] === 'approved' && ! DB::table('parent_student_active_links')->where('parent_student_link_id', $locked->id)->exists()) {
                abort(409, 'Siswa telah terhubung ke akun orang tua lain.');
            }
            $locked->update(['status' => $data['decision'], 'reviewed_by' => $request->user()->id, 'reviewer_name_snapshot' => $request->user()->name, 'reviewed_at' => now(), 'review_note' => $data['note'] ?? null]);
            if ($data['decision'] === 'rejected') DB::table('parent_student_active_links')->where('parent_student_link_id', $locked->id)->delete();
            ParentStudentLinkDecision::create(['parent_student_link_id' => $locked->id, 'actor_id' => $request->user()->id, 'actor_name_snapshot' => $request->user()->name, 'decision' => $data['decision'], 'note' => $data['note'] ?? null, 'decided_at' => now()]);
        });
        return redirect()->route('dashboard.guru.orang-tua.index')->with('success', 'Keputusan verifikasi tersimpan.');
    }
}
