<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Draf Surat Pernyataan Orang Tua / Wali | SMKN 7 Jember Go Global</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; font-size: 12pt; }
            .print-container { box-shadow: none !important; border: none !important; padding: 0 !important; }
        }
    </style>
</head>
<body class="bg-slate-100 font-sans text-slate-800 p-4 sm:p-8">

    <!-- Top Floating Toolbar -->
    <div class="max-w-4xl mx-auto mb-6 flex items-center justify-between no-print bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
        <div class="flex items-center gap-3">
            <a href="{{ route('karier-jepang.index') }}" class="text-xs font-bold text-[#155EAA] hover:underline flex items-center gap-1">
                &larr; Kembali ke Informasi Karier
            </a>
            <span class="text-slate-300">|</span>
            <span class="text-xs text-slate-500">Format Draf Surat Pernyataan Persetujuan Orang Tua / Wali</span>
        </div>
        <button type="button" onclick="window.print()" class="px-4 py-2 bg-[#0F2F5F] hover:bg-[#155EAA] text-white text-xs font-bold rounded-xl shadow-xs transition-colors flex items-center gap-1.5 cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            <span>Cetak / Simpan PDF</span>
        </button>
    </div>

    <!-- Official Document Paper -->
    <div class="max-w-4xl mx-auto bg-white p-8 sm:p-12 rounded-2xl border border-slate-200 shadow-md print-container">
        
        <!-- School Header / Kop Surat -->
        <div class="border-b-4 border-double border-slate-800 pb-4 mb-8 text-center relative">
            <h2 class="text-sm font-bold tracking-wider uppercase text-slate-700">Pemerintah Provinsi Jawa Timur &bull; Dinas Pendidikan</h2>
            <h1 class="text-lg sm:text-xl font-extrabold text-[#0F2F5F] uppercase mt-1">SMK NEGERI 7 JEMBER</h1>
            <p class="text-[11px] text-slate-600 mt-1">
                Jalan Randuagung, Semboro, Kabupaten Jember, Jawa Timur 68157 &bull; Website: www.smkn7jember.sch.id
            </p>
            <p class="text-[10px] font-semibold text-[#155EAA] uppercase tracking-widest mt-0.5">
                Program Unggulan SMKN 7 Jember – Go Global
            </p>
        </div>

        <!-- Watermark / Label Simulasi Lomba -->
        <div class="mb-6 p-3 rounded-lg bg-amber-50 border border-amber-200 text-center text-xs text-amber-900 no-print">
            <strong>Pemberitahuan Format Draf Simulasi:</strong> Draf dokumen ini disiapkan untuk kebutuhan seleksi pendaftaran program percontohan Go Global. Format resmi berkop sekolah akan diterbitkan dan diselaraskan oleh Manajemen Sekolah SMKN 7 Jember.
        </div>

        <!-- Title of Document -->
        <div class="text-center mb-8">
            <h3 class="text-base font-extrabold underline tracking-wide uppercase text-slate-900">
                SURAT PERNYATAAN PERSETUJUAN ORANG TUA / WALI
            </h3>
            <span class="text-xs text-slate-500 font-mono">Nomor: 421.5 / BKK-GOGLOBAL / {{ date('Y') }}</span>
        </div>

        <!-- Body Content -->
        <div class="space-y-6 text-xs sm:text-sm leading-relaxed text-slate-800">
            <p>Yang bertanda tangan di bawah ini:</p>

            <table class="w-full text-xs sm:text-sm border-separate" style="border-spacing: 0 6px;">
                <tr>
                    <td class="w-48 font-semibold text-slate-700">Nama Orang Tua / Wali</td>
                    <td class="w-4">:</td>
                    <td class="border-b border-dotted border-slate-400">.................................................................................................................</td>
                </tr>
                <tr>
                    <td class="font-semibold text-slate-700">Pekerjaan</td>
                    <td>:</td>
                    <td class="border-b border-dotted border-slate-400">.................................................................................................................</td>
                </tr>
                <tr>
                    <td class="font-semibold text-slate-700">Nomor Telepon / WhatsApp</td>
                    <td>:</td>
                    <td class="border-b border-dotted border-slate-400">.................................................................................................................</td>
                </tr>
                <tr>
                    <td class="font-semibold text-slate-700">Alamat Lengkap</td>
                    <td>:</td>
                    <td class="border-b border-dotted border-slate-400">.................................................................................................................</td>
                </tr>
            </table>

            <p class="mt-4">Adalah benar orang tua / wali dari calon peserta program Karier Jepang:</p>

            <table class="w-full text-xs sm:text-sm border-separate" style="border-spacing: 0 6px;">
                <tr>
                    <td class="w-48 font-semibold text-slate-700">Nama Lengkap Siswa / Alumni</td>
                    <td class="w-4">:</td>
                    <td class="border-b border-dotted border-slate-400">.................................................................................................................</td>
                </tr>
                <tr>
                    <td class="font-semibold text-slate-700">NISN / Tahun Lulus</td>
                    <td>:</td>
                    <td class="border-b border-dotted border-slate-400">.................................................................................................................</td>
                </tr>
                <tr>
                    <td class="font-semibold text-slate-700">Konsentrasi Keahlian / Jurusan</td>
                    <td>:</td>
                    <td class="border-b border-dotted border-slate-400">.................................................................................................................</td>
                </tr>
            </table>

            <p class="mt-4 text-justify">
                Dengan ini menyatakan <strong>MENYETUJUI SEPENUHNYA</strong> putra/putri kami untuk mendaftar, mengikuti seluruh rangkaian seleksi administrasi, pertimbangan bimbingan konseling, pembekalan intensif bahasa Jepang, pemeriksaan kesehatan (MCU), serta proses penempatan kerja/magang di Jepang melalui Lembaga Pelatihan Kerja (LPK) mitra resmi kerja sama SMKN 7 Jember.
            </p>

            <p class="text-justify">
                Kami berkomitmen memberikan dukungan moril dan mematuhi seluruh peraturan kedisiplinan yang ditetapkan oleh pihak sekolah, LPK, dan perusahaan penerima di Jepang.
            </p>

            <p class="text-justify">
                Demikian surat pernyataan ini dibuat dengan sebenar-benarnya tanpa paksaan dari pihak mana pun untuk dapat dipergunakan sebagaimana mestinya.
            </p>
        </div>

        <!-- Signature Section -->
        <div class="mt-12 pt-4 grid grid-cols-2 gap-8 text-xs sm:text-sm">
            <div class="text-center">
                <p class="text-slate-500 mb-16">Mengetahui,<br>Calon Peserta Program</p>
                <p class="font-bold border-b border-slate-400 inline-block px-8 pb-1">( .................................................... )</p>
            </div>

            <div class="text-center">
                <p class="text-slate-500 mb-4">Jember, ......................................... {{ date('Y') }}<br>Orang Tua / Wali Calon Peserta</p>
                <div class="border border-slate-300 w-24 h-12 mx-auto mb-2 flex items-center justify-center text-[9px] text-slate-400 uppercase">
                    Materai Rp 10.000
                </div>
                <p class="font-bold border-b border-slate-400 inline-block px-8 pb-1">( .................................................... )</p>
            </div>
        </div>

    </div>

</body>
</html>
