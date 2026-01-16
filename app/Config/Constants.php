<?php

/*
 | --------------------------------------------------------------------
 | App Namespace
 | --------------------------------------------------------------------
 |
 | This defines the default Namespace that is used throughout
 | CodeIgniter to refer to the Application directory. Change
 | this constant to change the namespace that all application
 | classes should use.
 |
 | NOTE: changing this will require manually modifying the
 | existing namespaces of App\* namespaced-classes.
 */
defined('APP_NAMESPACE') || define('APP_NAMESPACE', 'App');

/*
 | --------------------------------------------------------------------------
 | Composer Path
 | --------------------------------------------------------------------------
 |
 | The path that Composer's autoload file is expected to live. By default,
 | the vendor folder is in the Root directory, but you can customize that here.
 */
defined('COMPOSER_PATH') || define('COMPOSER_PATH', ROOTPATH . 'vendor/autoload.php');

/*
 |--------------------------------------------------------------------------
 | Timing Constants
 |--------------------------------------------------------------------------
 |
 | Provide simple ways to work with the myriad of PHP functions that
 | require information to be in seconds.
 */
defined('SECOND') || define('SECOND', 1);
defined('MINUTE') || define('MINUTE', 60);
defined('HOUR')   || define('HOUR', 3600);
defined('DAY')    || define('DAY', 86400);
defined('WEEK')   || define('WEEK', 604800);
defined('MONTH')  || define('MONTH', 2_592_000);
defined('YEAR')   || define('YEAR', 31_536_000);
defined('DECADE') || define('DECADE', 315_360_000);

/*
 | --------------------------------------------------------------------------
 | Exit Status Codes
 | --------------------------------------------------------------------------
 |
 | Used to indicate the conditions under which the script is exit()ing.
 | While there is no universal standard for error codes, there are some
 | broad conventions.  Three such conventions are mentioned below, for
 | those who wish to make use of them.  The CodeIgniter defaults were
 | chosen for the least overlap with these conventions, while still
 | leaving room for others to be defined in future versions and user
 | applications.
 |
 | The three main conventions used for determining exit status codes
 | are as follows:
 |
 |    Standard C/C++ Library (stdlibc):
 |       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
 |       (This link also contains other GNU-specific conventions)
 |    BSD sysexits.h:
 |       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
 |    Bash scripting:
 |       http://tldp.org/LDP/abs/html/exitcodes.html
 |
 */
defined('EXIT_SUCCESS')        || define('EXIT_SUCCESS', 0);        // no errors
defined('EXIT_ERROR')          || define('EXIT_ERROR', 1);          // generic error
defined('EXIT_CONFIG')         || define('EXIT_CONFIG', 3);         // configuration error
defined('EXIT_UNKNOWN_FILE')   || define('EXIT_UNKNOWN_FILE', 4);   // file not found
defined('EXIT_UNKNOWN_CLASS')  || define('EXIT_UNKNOWN_CLASS', 5);  // unknown class
defined('EXIT_UNKNOWN_METHOD') || define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT')     || define('EXIT_USER_INPUT', 7);     // invalid user input
defined('EXIT_DATABASE')       || define('EXIT_DATABASE', 8);       // database error
defined('EXIT__AUTO_MIN')      || define('EXIT__AUTO_MIN', 9);      // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX')      || define('EXIT__AUTO_MAX', 125);    // highest automatically-assigned error code

/**
 * @deprecated Use \CodeIgniter\Events\Events::PRIORITY_LOW instead.
 */
define('EVENT_PRIORITY_LOW', 200);

/**
 * @deprecated Use \CodeIgniter\Events\Events::PRIORITY_NORMAL instead.
 */
define('EVENT_PRIORITY_NORMAL', 100);

/**
 * @deprecated Use \CodeIgniter\Events\Events::PRIORITY_HIGH instead.
 */
define('EVENT_PRIORITY_HIGH', 10);

// Table list


define('AUTH_SESS_NAME', 'logged_in');
define('AUTH_VISIT_SESS_NAME', 'visited_url');
define('AUTH_BASE_ACCOUNT', 'base_account');

define('BASE_ACCESS', serialize([
    [
        'app' => 'nilai',
        'role' => 'guru',
    ]
]));

define('NILAI_PENGASUHAN_KATEGORI', serialize([
    // "Menghindari Tahayul, Bid’ah dan Khurofat",
    // "Menghindari Kemusyrikan",
    "Melakukan shalat berjamaah 5 waktu",
    "Melakukan shalat 40 rokaat dalam sehari",
    "Melakukan puasa sunah",
    "Tahsinul Qiro’ah",
    "Khatam Al Qur’an sebulan sekali",
    "Hafal Al Qur’an sesuai target",
    "Kelakuan dan Kesopanan",
    "Pengendalian diri (ucapan/perbuatan)",
    "Jujur dan Amanah",
    "Kedisiplinan  dan Kerajinan",
    "Kerapian dan Kebersihan",
    "Mengisi Lembar Muhasabah Yaumiyah",
    "Mengikuti Ta’lim dan Taushiyah",
    "Menggunakan Bahasa Arab dan Inggris",
    "Mengikuti Kegiatan Ekstrakurikuler",
]));


/* Daftar Rapor SMK, mapel dan keterangan
dibagi berdasarkan JURUSAN, TINGKAT, MAPEL */
define('RAPOR_SMK_MAPEL_KET', serialize( [
    '4' => [
        '4' => [
            'umum' => [
                '72' => [
                    'label' => 'Pendidikan Agama Isalam dan Budi Pekerti',
                    'kompetensi' => 'Menunjukkan penugasan yang baik dalam hal Memahami ayat Al Qur’an, Hadits tentang berpikir kritis, cabang iman dan menghindari penyakit sosial serta baik dalam hal memahami pelaksanaan khutbah, tablig dan dakwah, memahami peran tokoh ulama yang mendunia.',
                ],
                '59' => [
                    'label' => 'Pendidikan Pancasila',
                    'kompetensi' => 'Perlu penguatan dalam hal Menganalisis kedudukan Pancasila dalam Ideologi Terbuka di Era globalisasi sesuai yang di atur dalam UUD RI 1945.',
                ],
                '15' => [
                    'label' => 'Bahasa Indonesia',
                    'kompetensi' => 'Menunjukkan penugasan yang baik dalam hal Menganalisis Teks Berita Berdasarkan Strukturnya.',
                ],
                '50' => [
                    'label' => 'Pendidikan Jasmani, Olahraga, dan Kesehatan',
                    'kompetensi' => 'Menunjukkan penugasan yang baik dalam hal Menganalisis strategi permainan dalam situasi menyerang dan bertahan pada bola besar serta baik dalam hal Menganalisis strategi permainan inovasi pada bola besar (sepak bola).',
                ],
                '46' => [
                    'label' => 'Sejarah',
                    'kompetensi' => 'Menunjukkan penugasan yang baik dalam hal Menganalisis kebijakan kolonialisme dan perlawanan bangsa Indonesia serta baik dalam hal Menganalisis pergerakan kebangsaan Indonesia. ',
                ],
                '27' => [
                    'label' => 'Bahasa Jawa',
                    'kompetensi' => 'Mencapai Kompetensi dengan sangat baik dalam hal Peserta didik mampu menulis gagasan dan pikiran dalam bentuk sastra tembang macapat, Peserta didik mampu menulis sastra berupa teks cerkak dan pariwara berbagai tujuan secara tritis dan kreatif.',
                ],
                '48' => [
                    'label' => 'Kemuhammadiyahan',
                    'kompetensi' => 'Menunjukkan penugasan yang sangat baik dalam hal Memahami jalur kaderisasi Muhammadiyah, serta sangat baik dalam hal Memahami AD / ART, struktur organisasi dan permusyawaratan dalam organisasi Muhammadiyah.',
                ],
                '53' => [
                    'label' => 'Matematika (Umum)',
                    'kompetensi' => 'Menunjukkan penugasan yang baik dalam hal Menyatakan data dalam bentuk matriks dan melakukan operasi aljabar pada matriks Perlu penguatan dalam hal Menentukan Komposisi Fungsi dan Fungsi Invers.',
                ],
                '16' => [
                    'label' => 'Bahasa Inggris',
                    'kompetensi' => 'Menunjukkan penugasan yang baik dalam hal  Mengidentifikasi fungsi sosial, struktur teks, dan unsur kebahasaan teks naratif Perlu penguatan dalam hal Mengidentifikasi fungsi sosial, struktur teks, dan unsur kebahasaan teks deskriptif (Things).',
                ],
            ],
            'kejuruan' => [
                '19' => [
                    'label' => 'Kreativitas, Inovasi dan Kewirausahaan',
                    'kompetensi' => 'Perlu penguatan dalam hal Membuat desain/rancangan produk layanan dan perlu penguatan dalam Menyusun proses kerja pembuatan prototipe.',
                ],
                '20' => [
                    'label' => 'Desain Komunikasi Visual',
                    'kompetensi' => "Menunjukkan penugasan yang baik dalam menerapkan dan mengelola proses produksi desaindimulai dari pra produksi, produksi dan pasca produksi sesuai dengan konsentrasi keahlian dalam lingkup Desain Komunikasi Visual. Melakukan pembiasaan sesauai dengan tim maupun pihak terkait. Proses produksi desain disesuaikan dengan sub konsentrasi keahlian (permintaan) dalam lingkup Desain Komunikasi Visual, peserta didik mampu dalam merancang visual secara sistematis mulai dari pemahaman terhadap permasalahan, diskusi pencarian ide (brainstorring). pengembangan alternatif, hinga menjadi karya akhir. Proses tersebut dapat menggunakan metode design thinking maupun metode lainnya. Peserta didik mampu melakukan pembiasaan sesuai POS, mampu berkolaborasi dan komunikasi dengan tim maupun pihak terkait. Karya desain yang dihasilkan disesuaikan dengan sub konsentrasi keahlian (peminatan) dalam lingkup Desain Komunikasi Visual Print Design, Videografi, Fotografo, Typeface Design, Story Boarding, Ilustrasi, Sequential Art, Motion Graphic, Web dan App Design, UI-UX Design, Concept Art, Motion Graphic Design, Environmental Graphic Design, dan lainnya yang terkait."
                ],
            ],
        ],
        '5' => [
            'umum' => [
                '71' => [
                    'label' => 'Pendidikan Agama Isalam dan Budi Pekerti',
                    'kompetensi' => 'Menunjukkan penugasan yang baik dalam hal Memahami ayat Al Qur’an, Hadits tentang berpikir kritis, cabang iman dan menghindari penyakit sosial serta baik dalam hal memahami pelaksanaan khutbah, tablig dan dakwah, memahami peran tokoh ulama yang mendunia.',
                ],
                '59' => [
                    'label' => 'Pendidikan Pancasila',
                    'kompetensi' => 'Perlu penguatan dalam hal Menganalisis kedudukan Pancasila dalam Ideologi Terbuka di Era globalisasi sesuai yang di atur dalam UUD RI 1945.',
                ],
                '15' => [
                    'label' => 'Bahasa Indonesia',
                    'kompetensi' => 'Menunjukkan penugasan yang baik dalam hal Menganalisis Teks Berita Berdasarkan Strukturnya.',
                ],
                '50' => [
                    'label' => 'Pendidikan Jasmani, Olahraga, dan Kesehatan',
                    'kompetensi' => 'Menunjukkan penugasan yang baik dalam hal Menganalisis strategi permainan dalam situasi menyerang dan bertahan pada bola besar serta baik dalam hal Menganalisis strategi permainan inovasi pada bola besar (sepak bola).',
                ],
                '68' => [
                    'label' => 'Sejarah',
                    'kompetensi' => 'Menunjukkan penugasan yang baik dalam hal Menganalisis kebijakan kolonialisme dan perlawanan bangsa Indonesia serta baik dalam hal Menganalisis pergerakan kebangsaan Indonesia. ',
                ],
                '27' => [
                    'label' => 'Bahasa Jawa',
                    'kompetensi' => 'Mencapai Kompetensi dengan sangat baik dalam hal Peserta didik mampu menulis gagasan dan pikiran dalam bentuk sastra tembang macapat, Peserta didik mampu menulis sastra berupa teks cerkak dan pariwara berbagai tujuan secara tritis dan kreatif.',
                ],
                '48' => [
                    'label' => 'Kemuhammadiyahan',
                    'kompetensi' => 'Menunjukkan penugasan yang sangat baik dalam hal Memahami jalur kaderisasi Muhammadiyah, serta sangat baik dalam hal Memahami AD / ART, struktur organisasi dan permusyawaratan dalam organisasi Muhammadiyah.',
                ],
                '53' => [
                    'label' => 'Matematika (Umum)',
                    'kompetensi' => 'Menunjukkan penugasan yang baik dalam hal Menyatakan data dalam bentuk matriks dan melakukan operasi aljabar pada matriks Perlu penguatan dalam hal Menentukan Komposisi Fungsi dan Fungsi Invers.',
                ],
                '16' => [
                    'label' => 'Bahasa Inggris',
                    'kompetensi' => 'Menunjukkan penugasan yang baik dalam hal  Mengidentifikasi fungsi sosial, struktur teks, dan unsur kebahasaan teks naratif Perlu penguatan dalam hal Mengidentifikasi fungsi sosial, struktur teks, dan unsur kebahasaan teks deskriptif (Things).',
                ],
            ],
            'kejuruan' => [
                '22' => [
                    'label' => 'Kreativitas, Inovasi dan Kewirausahaan',
                    'kompetensi' => 'Perlu penguatan dalam hal Membuat desain/rancangan produk layanan dan perlu penguatan dalam Menyusun proses kerja pembuatan prototipe.',
                ],
                '26' => [
                    'label' => 'Desain Komunikasi Visual',
                    'kompetensi' => "Menunjukkan penugasan yang baik dalam menerapkan dan mengelola proses produksi desaindimulai dari pra produksi, produksi dan pasca produksi sesuai dengan konsentrasi keahlian dalam lingkup Desain Komunikasi Visual. Melakukan pembiasaan sesauai dengan tim maupun pihak terkait. Proses produksi desain disesuaikan dengan sub konsentrasi keahlian (permintaan) dalam lingkup Desain Komunikasi Visual, peserta didik mampu dalam merancang visual secara sistematis mulai dari pemahaman terhadap permasalahan, diskusi pencarian ide (brainstorring). pengembangan alternatif, hinga menjadi karya akhir. Proses tersebut dapat menggunakan metode design thinking maupun metode lainnya. Peserta didik mampu melakukan pembiasaan sesuai POS, mampu berkolaborasi dan komunikasi dengan tim maupun pihak terkait. Karya desain yang dihasilkan disesuaikan dengan sub konsentrasi keahlian (peminatan) dalam lingkup Desain Komunikasi Visual Print Design, Videografi, Fotografo, Typeface Design, Story Boarding, Ilustrasi, Sequential Art, Motion Graphic, Web dan App Design, UI-UX Design, Concept Art, Motion Graphic Design, Environmental Graphic Design, dan lainnya yang terkait."
                ],
                '25' => [
                    'label' => 'Mapel Animasi',
                    'kompetensi' => "Menunjukkan penugasan yang baik dalam peserta didik mampu menjelaskan tentang proses produksi dan teknologi yang diaplikasikan dalam ndustri animasi secara tekun dan teliti, memahami perangkat kerja, pemakaian aplikasi atau tools untuk dioperasikan dalam ekosistem industri animasi (perangkat kerja produksi animasi), memahami sikap kerja dalam melakukan komunikasi dan kerja sama tim, produksi animasi yang meliputi istilah teknis atau bahasa, unit kerja, proses (pipeline), workflow pekerjaan, fungsi kerja (job desk) serta SOP pada produksi animasi.",
                ],
            ],
        ],
        '6' => [
            'umum' => [
                '71' => [
                    'label' => 'Pendidikan Agama Isalam dan Budi Pekerti',
                    'kompetensi' => 'Menunjukkan penugasan yang baik dalam hal Memahami ayat Al Qur’an, Hadits tentang berpikir kritis, cabang iman dan menghindari penyakit sosial serta baik dalam hal memahami pelaksanaan khutbah, tablig dan dakwah, memahami peran tokoh ulama yang mendunia.',
                ],
                '59' => [
                    'label' => 'Pendidikan Pancasila',
                    'kompetensi' => 'Perlu penguatan dalam hal Menganalisis kedudukan Pancasila dalam Ideologi Terbuka di Era globalisasi sesuai yang di atur dalam UUD RI 1945.',
                ],
                '15' => [
                    'label' => 'Bahasa Indonesia',
                    'kompetensi' => 'Menunjukkan penugasan yang baik dalam hal Menganalisis Teks Berita Berdasarkan Strukturnya.',
                ],
                '50' => [
                    'label' => 'Pendidikan Jasmani, Olahraga, dan Kesehatan',
                    'kompetensi' => 'Menunjukkan penugasan yang baik dalam hal Menganalisis strategi permainan dalam situasi menyerang dan bertahan pada bola besar serta baik dalam hal Menganalisis strategi permainan inovasi pada bola besar (sepak bola).',
                ],
                '68' => [
                    'label' => 'Sejarah',
                    'kompetensi' => 'Menunjukkan penugasan yang baik dalam hal Menganalisis kebijakan kolonialisme dan perlawanan bangsa Indonesia serta baik dalam hal Menganalisis pergerakan kebangsaan Indonesia. ',
                ],
                '27' => [
                    'label' => 'Bahasa Jawa',
                    'kompetensi' => 'Mencapai Kompetensi dengan sangat baik dalam hal Peserta didik mampu menulis gagasan dan pikiran dalam bentuk sastra tembang macapat, Peserta didik mampu menulis sastra berupa teks cerkak dan pariwara berbagai tujuan secara tritis dan kreatif.',
                ],
                '48' => [
                    'label' => 'Kemuhammadiyahan',
                    'kompetensi' => 'Menunjukkan penugasan yang sangat baik dalam hal Memahami jalur kaderisasi Muhammadiyah, serta sangat baik dalam hal Memahami AD / ART, struktur organisasi dan permusyawaratan dalam organisasi Muhammadiyah.',
                ],
                '53' => [
                    'label' => 'Matematika (Umum)',
                    'kompetensi' => 'Menunjukkan penugasan yang baik dalam hal Menyatakan data dalam bentuk matriks dan melakukan operasi aljabar pada matriks Perlu penguatan dalam hal Menentukan Komposisi Fungsi dan Fungsi Invers.',
                ],
                '16' => [
                    'label' => 'Bahasa Inggris',
                    'kompetensi' => 'Menunjukkan penugasan yang baik dalam hal  Mengidentifikasi fungsi sosial, struktur teks, dan unsur kebahasaan teks naratif Perlu penguatan dalam hal Mengidentifikasi fungsi sosial, struktur teks, dan unsur kebahasaan teks deskriptif (Things).',
                ],
            ],
            'kejuruan' => [
                '22' => [
                    'label' => 'Kreativitas, Inovasi dan Kewirausahaan',
                    'kompetensi' => 'Perlu penguatan dalam hal Membuat desain/rancangan produk layanan dan perlu penguatan dalam Menyusun proses kerja pembuatan prototipe.',
                ],
                '26' => [
                    'label' => 'Desain Komunikasi Visual',
                    'kompetensi' => "Menunjukkan penugasan yang baik dalam menerapkan dan mengelola proses produksi desaindimulai dari pra produksi, produksi dan pasca produksi sesuai dengan konsentrasi keahlian dalam lingkup Desain Komunikasi Visual. Melakukan pembiasaan sesauai dengan tim maupun pihak terkait. Proses produksi desain disesuaikan dengan sub konsentrasi keahlian (permintaan) dalam lingkup Desain Komunikasi Visual, peserta didik mampu dalam merancang visual secara sistematis mulai dari pemahaman terhadap permasalahan, diskusi pencarian ide (brainstorring). pengembangan alternatif, hinga menjadi karya akhir. Proses tersebut dapat menggunakan metode design thinking maupun metode lainnya. Peserta didik mampu melakukan pembiasaan sesuai POS, mampu berkolaborasi dan komunikasi dengan tim maupun pihak terkait. Karya desain yang dihasilkan disesuaikan dengan sub konsentrasi keahlian (peminatan) dalam lingkup Desain Komunikasi Visual Print Design, Videografi, Fotografo, Typeface Design, Story Boarding, Ilustrasi, Sequential Art, Motion Graphic, Web dan App Design, UI-UX Design, Concept Art, Motion Graphic Design, Environmental Graphic Design, dan lainnya yang terkait."
                ],
                '25' => [
                    'label' => 'Mapel Animasi',
                    'kompetensi' => "Menunjukkan penugasan yang baik dalam peserta didik mampu menjelaskan tentang proses produksi dan teknologi yang diaplikasikan dalam ndustri animasi secara tekun dan teliti, memahami perangkat kerja, pemakaian aplikasi atau tools untuk dioperasikan dalam ekosistem industri animasi (perangkat kerja produksi animasi), memahami sikap kerja dalam melakukan komunikasi dan kerja sama tim, produksi animasi yang meliputi istilah teknis atau bahasa, unit kerja, proses (pipeline), workflow pekerjaan, fungsi kerja (job desk) serta SOP pada produksi animasi.",
                ],
            ],
        ],
    ],
    '5' => [
        '4' => [
            'umum' => [
                '72' => [
                    'label' => 'Pendidikan Agama Isalam dan Budi Pekerti',
                    'kompetensi' => 'Menunjukkan penugasan yang baik dalam hal Memahami ayat Al Qur’an, Hadits tentang berpikir kritis, cabang iman dan menghindari penyakit sosial serta baik dalam hal memahami pelaksanaan khutbah, tablig dan dakwah, memahami peran tokoh ulama yang mendunia.',
                ],
                '59' => [
                    'label' => 'Pendidikan Pancasila',
                    'kompetensi' => 'Perlu penguatan dalam hal Menganalisis kedudukan Pancasila dalam Ideologi Terbuka di Era globalisasi sesuai yang di atur dalam UUD RI 1945.',
                ],
                '15' => [
                    'label' => 'Bahasa Indonesia',
                    'kompetensi' => 'Menunjukkan penugasan yang baik dalam hal Menganalisis Teks Berita Berdasarkan Strukturnya.',
                ],
                '50' => [
                    'label' => 'Pendidikan Jasmani, Olahraga, dan Kesehatan',
                    'kompetensi' => 'Menunjukkan penugasan yang baik dalam hal Menganalisis strategi permainan dalam situasi menyerang dan bertahan pada bola besar serta baik dalam hal Menganalisis strategi permainan inovasi pada bola besar (sepak bola).',
                ],
                '46' => [
                    'label' => 'Sejarah',
                    'kompetensi' => 'Menunjukkan penugasan yang baik dalam hal Menganalisis kebijakan kolonialisme dan perlawanan bangsa Indonesia serta baik dalam hal Menganalisis pergerakan kebangsaan Indonesia. ',
                ],
                '27' => [
                    'label' => 'Bahasa Jawa',
                    'kompetensi' => 'Mencapai Kompetensi dengan sangat baik dalam hal Peserta didik mampu menulis gagasan dan pikiran dalam bentuk sastra tembang macapat, Peserta didik mampu menulis sastra berupa teks cerkak dan pariwara berbagai tujuan secara tritis dan kreatif.',
                ],
                '48' => [
                    'label' => 'Kemuhammadiyahan',
                    'kompetensi' => 'Menunjukkan penugasan yang sangat baik dalam hal Memahami jalur kaderisasi Muhammadiyah, serta sangat baik dalam hal Memahami AD / ART, struktur organisasi dan permusyawaratan dalam organisasi Muhammadiyah.',
                ],
                '53' => [
                    'label' => 'Matematika (Umum)',
                    'kompetensi' => 'Menunjukkan penugasan yang baik dalam hal Menyatakan data dalam bentuk matriks dan melakukan operasi aljabar pada matriks Perlu penguatan dalam hal Menentukan Komposisi Fungsi dan Fungsi Invers.',
                ],
                '16' => [
                    'label' => 'Bahasa Inggris',
                    'kompetensi' => 'Menunjukkan penugasan yang baik dalam hal  Mengidentifikasi fungsi sosial, struktur teks, dan unsur kebahasaan teks naratif Perlu penguatan dalam hal Mengidentifikasi fungsi sosial, struktur teks, dan unsur kebahasaan teks deskriptif (Things).',
                ],
            ],
            'kejuruan' => [
                '30' => [
                    'label' => 'Kreativitas, Inovasi dan Kewirausahaan',
                    'kompetensi' => 'Perlu penguatan dalam hal Membuat desain/rancangan produk layanan dan perlu penguatan dalam Menyusun proses kerja pembuatan prototipe.',
                ],
                '32' => [
                    'label' => 'Kefarmasian Klinis dan Komunitas',
                    'kompetensi' => 'Menunjukkan penugasan yang baik dalam hal memahami dan menerapkan administrasi farmasi serta perbekalan obat dan alat kesehatan, serta sangat baik dalam hal memahami tanaman obat tradisional, mampu membuat sediaan jamu dan jamu kekinian secara sederhana.',
                ],
            ],
        ],
        '5' => [
            'umum' => [
                '71' => [
                    'label' => 'Pendidikan Agama Isalam dan Budi Pekerti',
                    'kompetensi' => 'Menunjukkan penugasan yang baik dalam hal Memahami ayat Al Qur’an, Hadits tentang berpikir kritis, cabang iman dan menghindari penyakit sosial serta baik dalam hal memahami pelaksanaan khutbah, tablig dan dakwah, memahami peran tokoh ulama yang mendunia.',
                ],
                '59' => [
                    'label' => 'Pendidikan Pancasila',
                    'kompetensi' => 'Perlu penguatan dalam hal Menganalisis kedudukan Pancasila dalam Ideologi Terbuka di Era globalisasi sesuai yang di atur dalam UUD RI 1945.',
                ],
                '15' => [
                    'label' => 'Bahasa Indonesia',
                    'kompetensi' => 'Menunjukkan penugasan yang baik dalam hal Menganalisis Teks Berita Berdasarkan Strukturnya.',
                ],
                '50' => [
                    'label' => 'Pendidikan Jasmani, Olahraga, dan Kesehatan',
                    'kompetensi' => 'Menunjukkan penugasan yang baik dalam hal Menganalisis strategi permainan dalam situasi menyerang dan bertahan pada bola besar serta baik dalam hal Menganalisis strategi permainan inovasi pada bola besar (sepak bola).',
                ],
                '68' => [
                    'label' => 'Sejarah',
                    'kompetensi' => 'Menunjukkan penugasan yang baik dalam hal Menganalisis kebijakan kolonialisme dan perlawanan bangsa Indonesia serta baik dalam hal Menganalisis pergerakan kebangsaan Indonesia. ',
                ],
                '27' => [
                    'label' => 'Bahasa Jawa',
                    'kompetensi' => 'Mencapai Kompetensi dengan sangat baik dalam hal Peserta didik mampu menulis gagasan dan pikiran dalam bentuk sastra tembang macapat, Peserta didik mampu menulis sastra berupa teks cerkak dan pariwara berbagai tujuan secara tritis dan kreatif.',
                ],
                '48' => [
                    'label' => 'Kemuhammadiyahan',
                    'kompetensi' => 'Menunjukkan penugasan yang sangat baik dalam hal Memahami jalur kaderisasi Muhammadiyah, serta sangat baik dalam hal Memahami AD / ART, struktur organisasi dan permusyawaratan dalam organisasi Muhammadiyah.',
                ],
                '53' => [
                    'label' => 'Matematika (Umum)',
                    'kompetensi' => 'Menunjukkan penugasan yang baik dalam hal Menyatakan data dalam bentuk matriks dan melakukan operasi aljabar pada matriks Perlu penguatan dalam hal Menentukan Komposisi Fungsi dan Fungsi Invers.',
                ],
                '16' => [
                    'label' => 'Bahasa Inggris',
                    'kompetensi' => 'Menunjukkan penugasan yang baik dalam hal  Mengidentifikasi fungsi sosial, struktur teks, dan unsur kebahasaan teks naratif Perlu penguatan dalam hal Mengidentifikasi fungsi sosial, struktur teks, dan unsur kebahasaan teks deskriptif (Things).',
                ],
            ],
            'kejuruan' => [
                '34' => [
                    'label' => 'Kreativitas, Inovasi dan Kewirausahaan',
                    'kompetensi' => 'Perlu penguatan dalam hal Membuat desain/rancangan produk layanan dan perlu penguatan dalam Menyusun proses kerja pembuatan prototipe.',
                ],
                '35' => [
                    'label' => 'Kefarmasian Klinis dan Komunitas',
                    'kompetensi' => 'Menunjukkan penugasan yang baik dalam hal memahami dan menerapkan administrasi farmasi serta perbekalan obat dan alat kesehatan, serta sangat baik dalam hal memahami tanaman obat tradisional, mampu membuat sediaan jamu dan jamu kekinian secara sederhana.',
                ],
                '37' => [
                    'label' => 'Farmasi Industri',
                    'kompetensi' => 'Menunjukkan penugasan yang baik dalam hal menerapkan formulasi dasar sediaan cair. Perlu penguatan dalam hal menerapkan prosedur operasional (personalia, bangunan, peralatan, produksi)',
                ],
            ],
        ],
        '6' => [
            'umum' => [
                '71' => [
                    'label' => 'Pendidikan Agama Isalam dan Budi Pekerti',
                    'kompetensi' => 'Menunjukkan penugasan yang baik dalam hal Memahami ayat Al Qur’an, Hadits tentang berpikir kritis, cabang iman dan menghindari penyakit sosial serta baik dalam hal memahami pelaksanaan khutbah, tablig dan dakwah, memahami peran tokoh ulama yang mendunia.',
                ],
                '59' => [
                    'label' => 'Pendidikan Pancasila',
                    'kompetensi' => 'Perlu penguatan dalam hal Menganalisis kedudukan Pancasila dalam Ideologi Terbuka di Era globalisasi sesuai yang di atur dalam UUD RI 1945.',
                ],
                '15' => [
                    'label' => 'Bahasa Indonesia',
                    'kompetensi' => 'Menunjukkan penugasan yang baik dalam hal Menganalisis Teks Berita Berdasarkan Strukturnya.',
                ],
                '50' => [
                    'label' => 'Pendidikan Jasmani, Olahraga, dan Kesehatan',
                    'kompetensi' => 'Menunjukkan penugasan yang baik dalam hal Menganalisis strategi permainan dalam situasi menyerang dan bertahan pada bola besar serta baik dalam hal Menganalisis strategi permainan inovasi pada bola besar (sepak bola).',
                ],
                '68' => [
                    'label' => 'Sejarah',
                    'kompetensi' => 'Menunjukkan penugasan yang baik dalam hal Menganalisis kebijakan kolonialisme dan perlawanan bangsa Indonesia serta baik dalam hal Menganalisis pergerakan kebangsaan Indonesia. ',
                ],
                '27' => [
                    'label' => 'Bahasa Jawa',
                    'kompetensi' => 'Mencapai Kompetensi dengan sangat baik dalam hal Peserta didik mampu menulis gagasan dan pikiran dalam bentuk sastra tembang macapat, Peserta didik mampu menulis sastra berupa teks cerkak dan pariwara berbagai tujuan secara tritis dan kreatif.',
                ],
                '48' => [
                    'label' => 'Kemuhammadiyahan',
                    'kompetensi' => 'Menunjukkan penugasan yang sangat baik dalam hal Memahami jalur kaderisasi Muhammadiyah, serta sangat baik dalam hal Memahami AD / ART, struktur organisasi dan permusyawaratan dalam organisasi Muhammadiyah.',
                ],
                '53' => [
                    'label' => 'Matematika (Umum)',
                    'kompetensi' => 'Menunjukkan penugasan yang baik dalam hal Menyatakan data dalam bentuk matriks dan melakukan operasi aljabar pada matriks Perlu penguatan dalam hal Menentukan Komposisi Fungsi dan Fungsi Invers.',
                ],
                '16' => [
                    'label' => 'Bahasa Inggris',
                    'kompetensi' => 'Menunjukkan penugasan yang baik dalam hal  Mengidentifikasi fungsi sosial, struktur teks, dan unsur kebahasaan teks naratif Perlu penguatan dalam hal Mengidentifikasi fungsi sosial, struktur teks, dan unsur kebahasaan teks deskriptif (Things).',
                ],
            ],
            'kejuruan' => [
                '34' => [
                    'label' => 'Kreativitas, Inovasi dan Kewirausahaan',
                    'kompetensi' => 'Perlu penguatan dalam hal Membuat desain/rancangan produk layanan dan perlu penguatan dalam Menyusun proses kerja pembuatan prototipe.',
                ],
                '35' => [
                    'label' => 'Kefarmasian Klinis dan Komunitas',
                    'kompetensi' => 'Menunjukkan penugasan yang baik dalam hal memahami dan menerapkan administrasi farmasi serta perbekalan obat dan alat kesehatan, serta sangat baik dalam hal memahami tanaman obat tradisional, mampu membuat sediaan jamu dan jamu kekinian secara sederhana.',
                ],
                '37' => [
                    'label' => 'Farmasi Industri',
                    'kompetensi' => 'Menunjukkan penugasan yang baik dalam hal menerapkan formulasi dasar sediaan cair. Perlu penguatan dalam hal menerapkan prosedur operasional (personalia, bangunan, peralatan, produksi)',
                ],
            ],
        ],
    ],
]));