import './bootstrap';
import './tracer-study-dashboard';

/**
 * SMKN 7 Jember - Go Global Frontend Interactions
 */

document.addEventListener('DOMContentLoaded', () => {
    initNavbarDropdowns();
    initMobileMenu();
    initSiteSearch();
    initBackToTop();
});

/* ==========================================================================
   1. NAVBAR DROPDOWNS & MOBILE ACCORDIONS
   ========================================================================== */

function initNavbarDropdowns() {
    const dropdownContainers = document.querySelectorAll('.dropdown-container');

    dropdownContainers.forEach((container) => {
        const trigger = container.querySelector('.dropdown-trigger');
        const menu = container.querySelector('.dropdown-menu');
        const chevron = container.querySelector('.chevron-icon');

        if (!trigger || !menu) return;

        function openDropdown() {
            // Close other open desktop dropdowns
            dropdownContainers.forEach((c) => {
                if (c !== container) {
                    const m = c.querySelector('.dropdown-menu');
                    const t = c.querySelector('.dropdown-trigger');
                    const ch = c.querySelector('.chevron-icon');
                    if (m) {
                        m.classList.add('hidden', 'opacity-0', 'translate-y-2');
                        m.classList.remove('opacity-100', 'translate-y-0');
                    }
                    if (t) t.setAttribute('aria-expanded', 'false');
                    if (ch) ch.classList.remove('rotate-180');
                }
            });

            menu.classList.remove('hidden');
            requestAnimationFrame(() => {
                menu.classList.remove('opacity-0', 'translate-y-2');
                menu.classList.add('opacity-100', 'translate-y-0');
            });
            trigger.setAttribute('aria-expanded', 'true');
            if (chevron) chevron.classList.add('rotate-180');
        }

        function closeDropdown() {
            menu.classList.add('opacity-0', 'translate-y-2');
            menu.classList.remove('opacity-100', 'translate-y-0');
            setTimeout(() => {
                if (trigger.getAttribute('aria-expanded') === 'false') {
                    menu.classList.add('hidden');
                }
            }, 180);
            trigger.setAttribute('aria-expanded', 'false');
            if (chevron) chevron.classList.remove('rotate-180');
        }

        // Toggle on click
        trigger.addEventListener('click', (e) => {
            e.stopPropagation();
            const isOpen = trigger.getAttribute('aria-expanded') === 'true';
            if (isOpen) {
                closeDropdown();
            } else {
                openDropdown();
            }
        });

        // Hover support on desktop
        container.addEventListener('mouseenter', () => {
            if (window.innerWidth >= 1024) openDropdown();
        });

        container.addEventListener('mouseleave', () => {
            if (window.innerWidth >= 1024) closeDropdown();
        });
    });

    // Close dropdowns on outside click
    document.addEventListener('click', (e) => {
        dropdownContainers.forEach((c) => {
            const menu = c.querySelector('.dropdown-menu');
            const trigger = c.querySelector('.dropdown-trigger');
            const chevron = c.querySelector('.chevron-icon');
            if (menu && !c.contains(e.target)) {
                menu.classList.add('hidden', 'opacity-0', 'translate-y-2');
                menu.classList.remove('opacity-100', 'translate-y-0');
                if (trigger) trigger.setAttribute('aria-expanded', 'false');
                if (chevron) chevron.classList.remove('rotate-180');
            }
        });
    });

    // Close on Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            dropdownContainers.forEach((c) => {
                const menu = c.querySelector('.dropdown-menu');
                const trigger = c.querySelector('.dropdown-trigger');
                const chevron = c.querySelector('.chevron-icon');
                if (menu) {
                    menu.classList.add('hidden', 'opacity-0', 'translate-y-2');
                    menu.classList.remove('opacity-100', 'translate-y-0');
                }
                if (trigger) trigger.setAttribute('aria-expanded', 'false');
                if (chevron) chevron.classList.remove('rotate-180');
            });
        }
    });
}

function initMobileMenu() {
    const hamburgerBtn = document.getElementById('hamburger-btn');
    const mobileDrawer = document.getElementById('mobile-drawer');
    const iconOpen = document.getElementById('hamburger-icon-open');
    const iconClose = document.getElementById('hamburger-icon-close');
    const mobileSearchToggle = document.getElementById('mobile-search-toggle');
    const mobileSearchBar = document.getElementById('mobile-search-bar');

    if (!hamburgerBtn || !mobileDrawer) return;

    hamburgerBtn.addEventListener('click', () => {
        const isOpen = hamburgerBtn.getAttribute('aria-expanded') === 'true';
        hamburgerBtn.setAttribute('aria-expanded', String(!isOpen));
        mobileDrawer.classList.toggle('hidden', isOpen);
        iconOpen?.classList.toggle('hidden', !isOpen);
        iconClose?.classList.toggle('hidden', isOpen);
    });

    // Close drawer when any mobile nav link is clicked
    document.querySelectorAll('.mobile-nav-link').forEach((link) => {
        link.addEventListener('click', () => {
            hamburgerBtn.setAttribute('aria-expanded', 'false');
            mobileDrawer.classList.add('hidden');
            iconOpen?.classList.remove('hidden');
            iconClose?.classList.add('hidden');
        });
    });

    // Mobile Search Bar Toggle
    mobileSearchToggle?.addEventListener('click', () => {
        mobileSearchBar?.classList.toggle('hidden');
        if (!mobileSearchBar?.classList.contains('hidden')) {
            document.getElementById('mobile-site-search-input')?.focus();
        }
    });

    // Mobile Accordion items
    document.querySelectorAll('.mobile-accordion-btn').forEach((btn) => {
        btn.addEventListener('click', () => {
            const content = btn.nextElementSibling;
            const chevron = btn.querySelector('svg');
            const isHidden = content?.classList.contains('hidden');

            // Close siblings
            document.querySelectorAll('.mobile-accordion-content').forEach((c) => {
                if (c !== content) c.classList.add('hidden');
            });
            document.querySelectorAll('.mobile-accordion-btn svg').forEach((svg) => {
                if (svg !== chevron) svg.classList.remove('rotate-180');
            });

            if (isHidden) {
                content?.classList.remove('hidden');
                chevron?.classList.add('rotate-180');
            } else {
                content?.classList.add('hidden');
                chevron?.classList.remove('rotate-180');
            }
        });
    });
}

/* ==========================================================================
   2. SITE-WIDE QUICK SEARCH & FILTERING
   ========================================================================== */

function initSiteSearch() {
    const searchInputs = [
        document.getElementById('site-search-input'),
        document.getElementById('mobile-site-search-input'),
    ].filter(Boolean);

    const clearBtn = document.getElementById('search-clear-btn');
    const featureCards = document.querySelectorAll('#feature-cards-grid .feature-card');
    const announcements = document.querySelectorAll('#announcements-grid article');
    const noResults = document.getElementById('no-feature-results');

    function executeSearch(query) {
        const q = query.trim().toLowerCase();

        if (clearBtn) {
            clearBtn.classList.toggle('hidden', q.length === 0);
        }

        if (q.length === 0) {
            featureCards.forEach((c) => c.classList.remove('hidden'));
            announcements.forEach((a) => a.classList.remove('hidden'));
            noResults?.classList.add('hidden');
            return;
        }

        let visibleCards = 0;
        featureCards.forEach((card) => {
            const text = (card.getAttribute('data-searchable') || '') + ' ' + card.innerText.toLowerCase();
            const matches = text.includes(q);
            card.classList.toggle('hidden', !matches);
            if (matches) visibleCards++;
        });

        announcements.forEach((item) => {
            const text = (item.getAttribute('data-searchable') || '') + ' ' + item.innerText.toLowerCase();
            const matches = text.includes(q);
            item.classList.toggle('hidden', !matches);
        });

        if (noResults) {
            noResults.classList.toggle('hidden', visibleCards > 0);
        }
    }

    searchInputs.forEach((input) => {
        input.addEventListener('input', (e) => {
            executeSearch(e.target.value);
            // Synchronize the other input if open
            searchInputs.forEach((other) => {
                if (other !== input) other.value = e.target.value;
            });
        });

        // Press Enter scrolls to Akses Cepat section
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('akses')?.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });

    clearBtn?.addEventListener('click', () => {
        searchInputs.forEach((i) => (i.value = ''));
        executeSearch('');
        searchInputs[0]?.focus();
    });

    window.clearSiteSearch = () => {
        searchInputs.forEach((i) => (i.value = ''));
        executeSearch('');
    };
}

/* ==========================================================================
   3. ALUMNI INTERACTIVE SWITCHER
   ========================================================================== */

const alumniData = [
    {
        name: 'Siti Rohma',
        year: 'Alumni 2024',
        major: 'Jurusan KKR',
        role: 'Caregiver di Jepang',
        quote: '"Terima kasih, SMKN 7 Jember, tempat di mana saya belajar, berkembang, dan menemukan arah masa depan. Ilmu, bimbingan, dan dukungan dari guru-guru hebat di sini menjadi bekal berharga untuk melangkah lebih jauh."'
    },
    {
        name: 'Elbim',
        year: 'Alumni 2024',
        major: 'Jurusan Teknik Sepeda Motor',
        role: 'Peserta magang di Jepang',
        quote: '"Terima kasih kepada SMKN 7 Jember yang telah memberikan ilmu, bimbingan, dan dukungan yang luar biasa sehingga saya mendapatkan kesempatan magang di Jepang. Melalui pengalaman ini, saya belajar disiplin, budaya kerja yang profesional, serta mengembangkan keterampilan yang sangat berharga di dunia industri."'
    },
    {
        name: 'Sofia',
        year: 'Alumni 2024',
        major: 'Jurusan Kecantikan Kulit dan Rambut',
        role: 'Peserta magang di Jepang',
        quote: '"Terima kasih, SMKN 7 Jember telah menjadi tempat saya menimba ilmu, mengembangkan potensi, dan mempersiapkan masa depan. Bekal pengetahuan, keterampilan, serta bimbingan dari para guru menjadi langkah awal bagi saya untuk berani meraih pengalaman hingga ke Jepang. Perjalanan ini mengajarkan saya untuk terus belajar, mandiri, dan tidak takut menghadapi tantangan baru."'
    }
];

window.selectAlumni = function (index) {
    if (index < 0 || index >= alumniData.length) return;
    const data = alumniData[index];

    const poster = document.getElementById('alumni-poster');
    const activeButton = document.querySelector(`.alumni-btn[data-index="${index}"]`);
    const year = document.getElementById('alumni-year');
    const name = document.getElementById('alumni-name');
    const major = document.getElementById('alumni-major');
    const role = document.getElementById('alumni-role');
    const quote = document.getElementById('alumni-quote');

    if (poster && activeButton) {
        poster.src = activeButton.dataset.image;
        poster.alt = activeButton.dataset.alt;
    }
    if (year) year.textContent = data.year;
    if (name) name.textContent = data.name;
    if (major) major.textContent = data.major;
    if (role) role.textContent = data.role;
    if (quote) quote.textContent = data.quote;

    // Update active button state
    document.querySelectorAll('.alumni-btn').forEach((btn) => {
        const btnIndex = parseInt(btn.getAttribute('data-index') || '0', 10);
        if (btnIndex === index) {
            btn.classList.add('bg-white', 'text-[#0F2F5F]');
            btn.classList.remove('bg-white/20', 'text-white');
        } else {
            btn.classList.remove('bg-white', 'text-[#0F2F5F]');
            btn.classList.add('bg-white/20', 'text-white');
        }
    });
};

/* ==========================================================================
   4. GALLERY FILTERING & LIGHTBOX
   ========================================================================== */

const galleryItemsData = [
    {
        category: 'Tahap Seleksi',
        title: 'Tes Fisik Peserta',
        image: '/images/test-fisik.jpg',
        desc: 'Kegiatan tes fisik peserta sebagai bagian dari tahapan seleksi Program Go Global.'
    },
    {
        category: 'Tahap Seleksi',
        title: 'Psikotes Peserta',
        image: '/images/psikotes.jpg',
        desc: 'Pelaksanaan tes psikologi untuk mengenali kesiapan dan potensi peserta dalam mengikuti Program Go Global.'
    },
    {
        category: 'Kesehatan',
        title: 'Pemeriksaan Kesehatan (MCU)',
        image: '/images/mcu.jpg',
        desc: 'Pemeriksaan kesehatan atau Medical Check Up (MCU) sebagai bagian dari persiapan peserta mengikuti program.'
    },
    {
        category: 'Pembekalan',
        title: 'Pembekalan Kewiraan',
        image: '/images/kewiraan.jpg',
        desc: 'Kegiatan penguatan kewiraan untuk membangun karakter, kedisiplinan, dan wawasan kebangsaan peserta.'
    },
    {
        category: 'Pembekalan',
        title: 'Penguatan Bahasa Jepang',
        image: '/images/penguatan-bahasa-jepang.jpg',
        desc: 'Kegiatan pembelajaran dan penguatan kemampuan bahasa Jepang sebagai bekal komunikasi peserta dalam kehidupan dan lingkungan kerja di Jepang.'
    }
];

let activeLightboxIndex = 0;
const visibleGalleryItemsData = galleryItemsData;

window.filterGallery = function (category) {
    const items = document.querySelectorAll('#gallery-grid .gallery-item');
    const filterBtns = document.querySelectorAll('#gallery-filter-tabs .gallery-filter-btn');

    filterBtns.forEach((btn) => {
        const f = btn.getAttribute('data-filter');
        if (f === category) {
            btn.classList.add('bg-[#0F2F5F]', 'text-white');
            btn.classList.remove('bg-slate-100', 'text-slate-700');
        } else {
            btn.classList.remove('bg-[#0F2F5F]', 'text-white');
            btn.classList.add('bg-slate-100', 'text-slate-700');
        }
    });

    items.forEach((item) => {
        const itemCat = item.getAttribute('data-category');
        if (category === 'all' || itemCat === category) {
            item.classList.remove('hidden');
        } else {
            item.classList.add('hidden');
        }
    });
};

window.openGalleryLightbox = function (index) {
    activeLightboxIndex = index;
    updateLightboxContent();
    const modal = document.getElementById('gallery-lightbox');
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }
};

window.closeGalleryLightbox = function () {
    const modal = document.getElementById('gallery-lightbox');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    }
};

window.nextGalleryImage = function () {
    activeLightboxIndex = (activeLightboxIndex + 1) % visibleGalleryItemsData.length;
    updateLightboxContent();
};

window.prevGalleryImage = function () {
    activeLightboxIndex = (activeLightboxIndex - 1 + visibleGalleryItemsData.length) % visibleGalleryItemsData.length;
    updateLightboxContent();
};

function updateLightboxContent() {
    const item = visibleGalleryItemsData[activeLightboxIndex];
    if (!item) return;

    const badge = document.getElementById('lightbox-badge');
    const title = document.getElementById('lightbox-title');
    const image = document.getElementById('lightbox-image');
    const desc = document.getElementById('lightbox-desc');

    if (badge) badge.innerText = item.category;
    if (title) title.innerText = item.title;
    if (image) {
        image.src = item.image;
        image.alt = item.title;
    }
    if (desc) desc.innerText = item.desc;
}

// Lightbox keyboard arrows
document.addEventListener('keydown', (e) => {
    const lightbox = document.getElementById('gallery-lightbox');
    if (lightbox && !lightbox.classList.contains('hidden')) {
        if (e.key === 'ArrowRight') window.nextGalleryImage();
        if (e.key === 'ArrowLeft') window.prevGalleryImage();
        if (e.key === 'Escape') window.closeGalleryLightbox();
    }
});

/* ==========================================================================
   5. FINANCIAL SIMULATION CALCULATOR
   ========================================================================== */

const financialRates = {
    tokutei: {
        shizuoka: { gross: 205000, expense: 75000 },
        aichi: { gross: 215000, expense: 80000 },
        tokyo: { gross: 230000, expense: 90000 },
        osaka: { gross: 210000, expense: 78000 }
    },
    magang: {
        shizuoka: { gross: 175000, expense: 65000 },
        aichi: { gross: 180000, expense: 68000 },
        tokyo: { gross: 195000, expense: 78000 },
        osaka: { gross: 178000, expense: 67000 }
    }
};

window.updateFinancialSimulation = function () {
    const programSelect = document.getElementById('sim-program');
    const regionSelect = document.getElementById('sim-region');

    const program = programSelect?.value || 'tokutei';
    const region = regionSelect?.value || 'shizuoka';

    const rates = financialRates[program]?.[region] || { gross: 200000, expense: 75000 };
    const gross = rates.gross;
    const expense = rates.expense;
    const net = gross - expense;

    const jpyToIdr = 105;

    const outGross = document.getElementById('sim-out-gross');
    const outGrossIdr = document.getElementById('sim-out-gross-idr');
    const outExpense = document.getElementById('sim-out-expense');
    const outExpenseIdr = document.getElementById('sim-out-expense-idr');
    const outNet = document.getElementById('sim-out-net');
    const outNetIdr = document.getElementById('sim-out-net-idr');

    if (outGross) outGross.innerText = `¥${gross.toLocaleString()}`;
    if (outGrossIdr) outGrossIdr.innerText = `± Rp ${(gross * jpyToIdr).toLocaleString('id-ID')} / bln`;

    if (outExpense) outExpense.innerText = `¥${expense.toLocaleString()}`;
    if (outExpenseIdr) outExpenseIdr.innerText = `± Rp ${(expense * jpyToIdr).toLocaleString('id-ID')} / bln`;

    if (outNet) outNet.innerText = `¥${net.toLocaleString()}`;
    if (outNetIdr) outNetIdr.innerText = `± Rp ${(net * jpyToIdr).toLocaleString('id-ID')} / bln`;
};

/* ==========================================================================
   6. MODALS POPUP CONTROLLER
   ========================================================================== */

window.openLoginModal = function () {
    const modal = document.getElementById('login-modal');
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }
};

window.closeLoginModal = function () {
    const modal = document.getElementById('login-modal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    }
};

window.openCounselingModal = function () {
    const modal = document.getElementById('counseling-modal');
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
        document.getElementById('counseling-form')?.classList.remove('hidden');
        document.getElementById('counseling-success')?.classList.add('hidden');
    }
};

window.closeCounselingModal = function () {
    const modal = document.getElementById('counseling-modal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    }
};

window.handleCounselingSubmit = function (e) {
    e.preventDefault();
    const form = document.getElementById('counseling-form');
    const success = document.getElementById('counseling-success');
    if (form && success) {
        form.classList.add('hidden');
        success.classList.remove('hidden');
        form.reset();
    }
};

window.openSimulationModal = function () {
    const modal = document.getElementById('simulation-modal');
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
        window.updateFinancialSimulation();
    }
};

window.closeSimulationModal = function () {
    const modal = document.getElementById('simulation-modal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    }
};

window.openCareerProgramModal = function () {
    const modal = document.getElementById('career-modal');
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }
};

window.closeCareerProgramModal = function () {
    const modal = document.getElementById('career-modal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    }
};

window.openCareerCounselingModal = function () {
    const modal = document.getElementById('career-counseling-modal');
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
        document.getElementById('career-counseling-form')?.classList.remove('hidden');
        document.getElementById('career-counseling-success')?.classList.add('hidden');
    }
};

window.closeCareerCounselingModal = function () {
    const modal = document.getElementById('career-counseling-modal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    }
};

window.handleCareerCounselingSubmit = function (e) {
    e.preventDefault();
    const form = document.getElementById('career-counseling-form');
    const success = document.getElementById('career-counseling-success');
    if (form && success) {
        form.classList.add('hidden');
        success.classList.remove('hidden');
        form.reset();
    }
};

const announcementsContent = {
    1: {
        category: 'Seleksi & Karir',
        date: '04 November 2026',
        title: 'Pembukaan Seleksi Program Magang Industri Jepang Batch 2026/2027',
        body: `
            <p>SMKN 7 Jember secara resmi membuka pendaftaran seleksi awal program magang kerja ke Jepang untuk bidang Teknik Manufaktur, Mekatronika, dan Pertanian Modern.</p>
            <p><strong>Persyaratan Utama:</strong></p>
            <ul class="list-disc pl-5 space-y-1">
                <li>Siswa kelas XII atau alumni lulusan maksimal 3 tahun terakhir.</li>
                <li>Tinggi badan proporsional, tidak buta warna, dan sehat jasmani/rohani.</li>
                <li>Mendapat persetujuan dan izin tertulis dari orang tua/wali murid.</li>
                <li>Bersedia mengikuti kelas diklat bahasa Jepang intensif selama 6 bulan.</li>
            </ul>
            <p>Pendaftaran tahap awal dapat dilakukan melalui ruang Bimbingan Konseling (BK) atau kanal bimbingan digital di website ini sebelum tanggal 20 November 2026.</p>
        `
    },
    2: {
        category: 'Diklat Vokasi',
        date: '01 November 2026',
        title: 'Pelatihan Intensif Bahasa Jepang (N4/N5) & Etika Budaya Kerja Kaizen',
        body: `
            <p>Program kursus intensif bahasa Jepang akan diselenggarakan mulai 15 November 2026. Kelas diadakan setiap hari Senin hingga Kamis (pukul 15.30 - 17.30 WIB) di Laboratorium Bahasa SMKN 7 Jember.</p>
            <p><strong>Materi Pelatihan:</strong></p>
            <ul class="list-disc pl-5 space-y-1">
                <li>Penguasaan Hiragana, Katakana, dan 150 Kanji esensial industri.</li>
                <li>Simulasi percakapan kerja sehari-hari (Aisatsu, Hou-Ren-So).</li>
                <li>Latihan soal ujian resmi Japanese Language Proficiency Test (JLPT) N4.</li>
                <li>Simulasi wawancara kerja teknis dengan instruktur native speaker.</li>
            </ul>
        `
    },
    3: {
        category: 'Kesehatan & MCU',
        date: '28 Oktober 2026',
        title: 'Sosialisasi Standar Kebugaran Fisik & Skrining Medical Check-Up (MCU)',
        body: `
            <p>Pertemuan sosialisasi persiapan MCU dihadiri oleh dokter spesialis okupasi dan tim bimbingan konseling SMKN 7 Jember. Sosialisasi ini bertujuan memberikan edukasi awal kepada calon peserta program Go Global mengenai standar kelayakan medis imigrasi Jepang.</p>
            <p><strong>Poin Kebugaran Penting:</strong></p>
            <ul class="list-disc pl-5 space-y-1">
                <li>Bebas penyakit menular (TBC, Hepatitis B/C, HIV).</li>
                <li>Rontgen paru-paru bersih tanpa flek atau bekas luka pleura.</li>
                <li>Tekanan darah normal dan tidak memiliki riwayat asma akut.</li>
                <li>Edukasi kebiasaan tidak merokok dan menjaga pola istirahat 8 jam.</li>
            </ul>
        `
    },
    4: {
        category: 'Kemitraan Global',
        date: '24 Oktober 2026',
        title: 'Kunjungan Delegasi Asosiasi Kerja Sama Industri Tokyo ke SMKN 7 Jember',
        body: `
            <p>Delegasi pimpinan asosiasi kerja sama industri Tokyo melakukan kunjungan supervisi ke kampus SMKN 7 Jember. Dalam kunjungan ini, perwakilan Jepang meninjau workshop mesin CNC, lab otomotif, serta sarana budidaya pertanian modern.</p>
            <p>Hasil pertemuan menyepakati kuota penempatan 60 lulusan SMKN 7 Jember untuk periode 2026/2027 dengan skema Tokutei Ginou dan Magang Terbimbing, disertai jaminan perlindungan hak pekerja dan subsidi tempat tinggal.</p>
        `
    }
};

window.openAnnouncementModal = function (id) {
    const item = announcementsContent[id];
    if (!item) return;

    window.openAnnouncementModalCustom({
        category: item.category,
        title: item.title,
        date: item.date,
        body: item.body
    });
};

window.openAnnouncementModalCustom = function ({ category, title, date, body }) {
    const modal = document.getElementById('announcement-modal');
    const catEl = document.getElementById('modal-announcement-category');
    const titleEl = document.getElementById('announcement-modal-title');
    const dateEl = document.getElementById('modal-announcement-date');
    const bodyEl = document.getElementById('modal-announcement-body');

    if (catEl) catEl.innerHTML = category;
    if (titleEl) titleEl.innerText = title;
    if (dateEl) dateEl.innerText = date;
    if (bodyEl) bodyEl.innerHTML = body;

    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }
};

window.closeAnnouncementModal = function () {
    const modal = document.getElementById('announcement-modal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    }
};

window.showAllAnnouncementsToast = function () {
    document.getElementById('pengumuman')?.scrollIntoView({ behavior: 'smooth' });
};

/* ==========================================================================
   7. BACK TO TOP BUTTON
   ========================================================================== */

function initBackToTop() {
    const btn = document.getElementById('btn-back-to-top');
    if (!btn) return;

    window.addEventListener('scroll', () => {
        if (window.scrollY > 320) {
            btn.classList.remove('opacity-0', 'pointer-events-none', 'translate-y-4');
            btn.classList.add('opacity-100', 'translate-y-0');
        } else {
            btn.classList.add('opacity-0', 'pointer-events-none', 'translate-y-4');
            btn.classList.remove('opacity-100', 'translate-y-0');
        }
    });

    btn.addEventListener('click', () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
}
