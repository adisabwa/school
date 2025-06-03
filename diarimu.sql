-- Adminer 4.8.1 MySQL 8.0.30 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `mu__juz_quran`;
CREATE TABLE `mu__juz_quran` (
  `id` int NOT NULL,
  `surat_mulai` int NOT NULL,
  `surat_mulai_nama` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `ayat_mulai` int NOT NULL,
  `surat_selesai` int NOT NULL,
  `surat_selesai_nama` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `ayat_selesai` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `mu__juz_quran` (`id`, `surat_mulai`, `surat_mulai_nama`, `ayat_mulai`, `surat_selesai`, `surat_selesai_nama`, `ayat_selesai`) VALUES
(1,	1,	'Al-Fatihah',	1,	2,	'Al-Baqarah',	141),
(2,	2,	'Al-Baqarah',	142,	2,	'Al-Baqarah',	252),
(3,	2,	'Al-Baqarah',	253,	3,	'Ali Imran',	92),
(4,	3,	'Ali Imran',	93,	4,	'An-Nisa',	23),
(5,	4,	'An-Nisa',	24,	4,	'An-Nisa',	147),
(6,	4,	'An-Nisa',	148,	5,	'Al-Ma\'idah',	82),
(7,	5,	'Al-Ma\'idah',	83,	6,	'Al-An\'am',	110),
(8,	6,	'Al-An\'am',	111,	7,	'Al-A\'raf',	87),
(9,	7,	'Al-A\'raf',	88,	8,	'Al-Anfal',	40),
(10,	8,	'Al-Anfal',	41,	9,	'At-Taubah',	92),
(11,	9,	'At-Taubah',	93,	11,	'Hud',	5),
(12,	11,	'Hud',	6,	12,	'Yusuf',	52),
(13,	12,	'Yusuf',	53,	14,	'Ibrahim',	52),
(14,	15,	'Al-Hijr',	1,	16,	'An-Nahl',	128),
(15,	17,	'Al-Isra',	1,	18,	'Al-Kahf',	74),
(16,	18,	'Al-Kahf',	75,	20,	'Ta-Ha',	135),
(17,	21,	'Al-Anbiya',	1,	22,	'Al-Hajj',	78),
(18,	23,	'Al-Mu\'minun',	1,	25,	'Al-Furqan',	20),
(19,	25,	'Al-Furqan',	21,	27,	'An-Naml',	55),
(20,	27,	'An-Naml',	56,	29,	'Al-Ankabut',	45),
(21,	29,	'Al-Ankabut',	46,	33,	'Al-Ahzab',	30),
(22,	33,	'Al-Ahzab',	31,	36,	'Ya-Sin',	27),
(23,	36,	'Ya-Sin',	28,	39,	'Az-Zumar',	31),
(24,	39,	'Az-Zumar',	32,	41,	'Fussilat',	46),
(25,	41,	'Fussilat',	47,	45,	'Al-Jathiyah',	37),
(26,	46,	'Al-Ahqaf',	1,	51,	'Adz-Dzariyat',	30),
(27,	51,	'Adz-Dzariyat',	31,	57,	'Al-Hadid',	29),
(28,	58,	'Al-Mujadilah',	1,	66,	'At-Tahrim',	12),
(29,	67,	'Al-Mulk',	1,	77,	'Al-Mursalat',	50),
(30,	78,	'An-Naba',	1,	114,	'An-Nas',	6);

DROP TABLE IF EXISTS `mu__sholat_sunnah`;
CREATE TABLE `mu__sholat_sunnah` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama_sholat` varchar(30) NOT NULL,
  `rakaat` enum('odd','even') NOT NULL DEFAULT 'even',
  `type` enum('main','additional') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'additional',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_by` int NOT NULL,
  `updated_by` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `mu__sholat_sunnah` (`id`, `nama_sholat`, `rakaat`, `type`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(1,	'Tahajud',	'even',	'main',	NULL,	NULL,	0,	0),
(2,	'Witir',	'odd',	'main',	NULL,	NULL,	0,	0),
(3,	'Qobliyah Shubuh',	'even',	'main',	NULL,	NULL,	0,	0),
(4,	'Syuruq',	'even',	'main',	NULL,	NULL,	0,	0),
(5,	'Dhuha',	'even',	'main',	NULL,	NULL,	0,	0),
(6,	'Qobliyah Dhuhur',	'even',	'main',	NULL,	NULL,	0,	0),
(7,	'Ba\'diyah Dhuhur',	'even',	'main',	NULL,	NULL,	0,	0),
(8,	'Qobliyah Asar',	'even',	'main',	NULL,	NULL,	0,	0),
(9,	'Qobliyah Maghrib',	'even',	'main',	NULL,	NULL,	0,	0),
(10,	'Ba\'diyah Maghrib',	'even',	'main',	NULL,	NULL,	0,	0),
(11,	'Ba\'diyah Isya',	'even',	'main',	NULL,	NULL,	0,	0),
(12,	'Tahiyatul Masjid',	'even',	'additional',	NULL,	NULL,	0,	0),
(13,	'Tahiyatul Masjid',	'even',	'additional',	NULL,	NULL,	0,	0),
(14,	'Tahiyatul Masjid',	'even',	'additional',	NULL,	NULL,	0,	0),
(15,	'Tahiyatul Masjid',	'even',	'additional',	NULL,	NULL,	0,	0),
(17,	'Tahiyatul Masjid',	'even',	'additional',	NULL,	NULL,	0,	0),
(18,	'Tahiyatul Masjid',	'even',	'additional',	NULL,	NULL,	0,	0),
(19,	'Tahiyatul Masjid',	'even',	'additional',	NULL,	NULL,	0,	0),
(20,	'Tahiyatul Masjid',	'even',	'additional',	NULL,	NULL,	0,	0),
(24,	'Tarawih',	'even',	'additional',	'2025-05-10 18:22:21',	'2025-05-10 18:22:21',	14,	0),
(25,	'Sunnah Wudhu',	'even',	'additional',	'2025-05-10 18:32:25',	'2025-05-10 18:32:25',	14,	0);

DROP TABLE IF EXISTS `mu__surat_quran`;
CREATE TABLE `mu__surat_quran` (
  `id` int NOT NULL,
  `nama_arab` varchar(50) NOT NULL,
  `nama_latin` varchar(50) NOT NULL,
  `nama_terjemahan` varchar(50) NOT NULL,
  `jumlah_ayat` int NOT NULL,
  `tempat_turun` varchar(10) NOT NULL,
  `urutan_turun` int NOT NULL,
  `deskripsi` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `mu__surat_quran` (`id`, `nama_arab`, `nama_latin`, `nama_terjemahan`, `jumlah_ayat`, `tempat_turun`, `urutan_turun`, `deskripsi`, `created_at`) VALUES
(1,	'الْفَاتِحَة',	'Al-Fatihah',	'Pembukaan',	7,	'Makkiyah',	5,	NULL,	'2025-05-04 02:14:01'),
(2,	'الْبَقَرَة',	'Al-Baqarah',	'Sapi Betina',	286,	'Madaniyah',	87,	NULL,	'2025-05-04 02:14:01'),
(3,	'آلِ عِمْرَان',	'Ali Imran',	'Keluarga Imran',	200,	'Madaniyah',	89,	NULL,	'2025-05-04 02:14:01'),
(4,	'النِّسَاء',	'An-Nisa',	'Wanita',	176,	'Madaniyah',	92,	NULL,	'2025-05-04 02:14:01'),
(5,	'الْمَائِدَة',	'Al-Maidah',	'Hidangan',	120,	'Madaniyah',	112,	NULL,	'2025-05-04 02:14:01'),
(6,	'الْأَنْعَام',	'Al-An\'am',	'Binatang Ternak',	165,	'Makkiyah',	55,	NULL,	'2025-05-04 02:14:01'),
(7,	'الْأَعْرَاف',	'Al-A\'raf',	'Tempat Tertinggi',	206,	'Makkiyah',	39,	NULL,	'2025-05-04 02:14:01'),
(8,	'الْأَنْفَال',	'Al-Anfal',	'Rampasan Perang',	75,	'Madaniyah',	88,	NULL,	'2025-05-04 02:14:01'),
(9,	'التَّوْبَة',	'At-Tawbah',	'Pengampunan',	129,	'Madaniyah',	113,	NULL,	'2025-05-04 02:14:01'),
(10,	'يُونُس',	'Yunus',	'Yunus',	109,	'Makkiyah',	51,	NULL,	'2025-05-04 02:14:01'),
(11,	'هُود',	'Hud',	'Hud',	123,	'Makkiyah',	52,	NULL,	'2025-05-04 02:14:01'),
(12,	'يُوسُف',	'Yusuf',	'Yusuf',	111,	'Makkiyah',	53,	NULL,	'2025-05-04 02:14:01'),
(13,	'الرَّعْد',	'Ar-Ra\'d',	'Guruh',	43,	'Madaniyah',	96,	NULL,	'2025-05-04 02:14:01'),
(14,	'إِبْرَاهِيم',	'Ibrahim',	'Ibrahim',	52,	'Makkiyah',	72,	NULL,	'2025-05-04 02:14:01'),
(15,	'الْحِجْر',	'Al-Hijr',	'Hijr',	99,	'Makkiyah',	54,	NULL,	'2025-05-04 02:14:01'),
(16,	'النَّحْل',	'An-Nahl',	'Lebah',	128,	'Makkiyah',	70,	NULL,	'2025-05-04 02:14:01'),
(17,	'الْإِسْرَاء',	'Al-Isra',	'Perjalanan Malam',	111,	'Makkiyah',	50,	NULL,	'2025-05-04 02:14:01'),
(18,	'الْكَهْف',	'Al-Kahf',	'Gua',	110,	'Makkiyah',	69,	NULL,	'2025-05-04 02:14:01'),
(19,	'مَرْيَم',	'Maryam',	'Maryam',	98,	'Makkiyah',	44,	NULL,	'2025-05-04 02:14:01'),
(20,	'طه',	'Taha',	'Taha',	135,	'Makkiyah',	45,	NULL,	'2025-05-04 02:14:01'),
(21,	'الْأَنْبِيَاء',	'Al-Anbiya',	'Para Nabi',	112,	'Makkiyah',	73,	NULL,	'2025-05-04 02:14:01'),
(22,	'الْحَجّ',	'Al-Hajj',	'Haji',	78,	'Madaniyah',	103,	NULL,	'2025-05-04 02:14:01'),
(23,	'الْمُؤْمِنُونَ',	'Al-Mu\'minun',	'Orang-Orang Mukmin',	118,	'Makkiyah',	74,	NULL,	'2025-05-04 02:14:01'),
(24,	'النُّور',	'An-Nur',	'Cahaya',	64,	'Madaniyah',	102,	NULL,	'2025-05-04 02:14:01'),
(25,	'الْفُرْقَان',	'Al-Furqan',	'Pembeda',	77,	'Makkiyah',	42,	NULL,	'2025-05-04 02:14:01'),
(26,	'الشُّعَرَاء',	'Ash-Shu\'ara',	'Penyair',	227,	'Makkiyah',	47,	NULL,	'2025-05-04 02:14:01'),
(27,	'النَّمْل',	'An-Naml',	'Semut',	93,	'Makkiyah',	48,	NULL,	'2025-05-04 02:14:01'),
(28,	'الْقَصَص',	'Al-Qasas',	'Kisah-Kisah',	88,	'Makkiyah',	49,	NULL,	'2025-05-04 02:14:01'),
(29,	'الْعَنْكَبُوت',	'Al-Ankabut',	'Laba-Laba',	69,	'Makkiyah',	85,	NULL,	'2025-05-04 02:14:01'),
(30,	'الرُّوم',	'Ar-Rum',	'Romawi',	60,	'Makkiyah',	84,	NULL,	'2025-05-04 02:14:01'),
(31,	'لُقْمَان',	'Luqman',	'Luqman',	34,	'Makkiyah',	57,	NULL,	'2025-05-04 02:14:01'),
(32,	'السَّجْدَة',	'As-Sajdah',	'Sujud',	30,	'Makkiyah',	75,	NULL,	'2025-05-04 02:14:01'),
(33,	'الْأَحْزَاب',	'Al-Ahzab',	'Golongan yang Bersekutu',	73,	'Madaniyah',	90,	NULL,	'2025-05-04 02:14:01'),
(34,	'سَبَأ',	'Saba',	'Saba\'',	54,	'Makkiyah',	58,	NULL,	'2025-05-04 02:14:01'),
(35,	'فَاطِر',	'Fatir',	'Pencipta',	45,	'Makkiyah',	43,	NULL,	'2025-05-04 02:14:01'),
(36,	'يس',	'Ya-Sin',	'Ya Sin',	83,	'Makkiyah',	41,	NULL,	'2025-05-04 02:14:01'),
(37,	'الصَّافَّات',	'As-Saffat',	'Barisan-Barisan',	182,	'Makkiyah',	56,	NULL,	'2025-05-04 02:14:01'),
(38,	'ص',	'Sad',	'Sad',	88,	'Makkiyah',	38,	NULL,	'2025-05-04 02:14:01'),
(39,	'الزُّمَر',	'Az-Zumar',	'Rombongan',	75,	'Makkiyah',	59,	NULL,	'2025-05-04 02:14:01'),
(40,	'غَافِر',	'Ghafir',	'Yang Mengampuni',	85,	'Makkiyah',	60,	NULL,	'2025-05-04 02:14:01'),
(41,	'فُصِّلَت',	'Fussilat',	'Yang Dijelaskan',	54,	'Makkiyah',	61,	NULL,	'2025-05-04 02:14:01'),
(42,	'الشُّورَى',	'Ash-Shura',	'Musyawarah',	53,	'Makkiyah',	62,	NULL,	'2025-05-04 02:14:01'),
(43,	'الزُّخْرُف',	'Az-Zukhruf',	'Perhiasan',	89,	'Makkiyah',	63,	NULL,	'2025-05-04 02:14:01'),
(44,	'الدُّخَان',	'Ad-Dukhan',	'Kabut',	59,	'Makkiyah',	64,	NULL,	'2025-05-04 02:14:01'),
(45,	'الْجَاثِيَة',	'Al-Jathiyah',	'Yang Bertekuk Lutut',	37,	'Makkiyah',	65,	NULL,	'2025-05-04 02:14:01'),
(46,	'الْأَحْقَاف',	'Al-Ahqaf',	'Bukit Pasir',	35,	'Makkiyah',	66,	NULL,	'2025-05-04 02:14:01'),
(47,	'مُحَمَّد',	'Muhammad',	'Muhammad',	38,	'Madaniyah',	95,	NULL,	'2025-05-04 02:14:01'),
(48,	'الْفَتْح',	'Al-Fath',	'Kemenangan',	29,	'Madaniyah',	111,	NULL,	'2025-05-04 02:14:01'),
(49,	'الْحُجُرَات',	'Al-Hujurat',	'Kamar-Kamar',	18,	'Madaniyah',	106,	NULL,	'2025-05-04 02:14:01'),
(50,	'ق',	'Qaf',	'Qaf',	45,	'Makkiyah',	34,	NULL,	'2025-05-04 02:14:01'),
(51,	'الذَّارِيَات',	'Adh-Dhariyat',	'Angin yang Menerbangkan',	60,	'Makkiyah',	67,	NULL,	'2025-05-04 02:14:01'),
(52,	'الطُّور',	'At-Tur',	'Bukit',	49,	'Makkiyah',	76,	NULL,	'2025-05-04 02:14:01'),
(53,	'النَّجْم',	'An-Najm',	'Bintang',	62,	'Makkiyah',	23,	NULL,	'2025-05-04 02:14:01'),
(54,	'الْقَمَر',	'Al-Qamar',	'Bulan',	55,	'Makkiyah',	37,	NULL,	'2025-05-04 02:14:01'),
(55,	'الرَّحْمَن',	'Ar-Rahman',	'Yang Maha Pengasih',	78,	'Madaniyah',	97,	NULL,	'2025-05-04 02:14:01'),
(56,	'الْوَاقِعَة',	'Al-Waqi\'ah',	'Hari Kiamat',	96,	'Makkiyah',	46,	NULL,	'2025-05-04 02:14:01'),
(57,	'الْحَدِيد',	'Al-Hadid',	'Besi',	29,	'Madaniyah',	94,	NULL,	'2025-05-04 02:14:01'),
(58,	'الْمُجَادَلَة',	'Al-Mujadilah',	'Wanita yang Mengajukan Gugatan',	22,	'Madaniyah',	105,	NULL,	'2025-05-04 02:14:01'),
(59,	'الْحَشْر',	'Al-Hashr',	'Pengusiran',	24,	'Madaniyah',	101,	NULL,	'2025-05-04 02:14:01'),
(60,	'الْمُمْتَحَنَة',	'Al-Mumtahanah',	'Wanita yang Diuji',	13,	'Madaniyah',	91,	NULL,	'2025-05-04 02:14:01'),
(61,	'الصَّفّ',	'As-Saff',	'Barisan',	14,	'Madaniyah',	109,	NULL,	'2025-05-04 02:14:01'),
(62,	'الْجُمُعَة',	'Al-Jumu\'ah',	'Jumat',	11,	'Madaniyah',	110,	NULL,	'2025-05-04 02:14:01'),
(63,	'الْمُنَافِقُونَ',	'Al-Munafiqun',	'Orang-Orang Munafik',	11,	'Madaniyah',	104,	NULL,	'2025-05-04 02:14:01'),
(64,	'التَّغَابُن',	'At-Taghabun',	'Hari Dinampakkan Kesalahan-Kesalahan',	18,	'Madaniyah',	108,	NULL,	'2025-05-04 02:14:01'),
(65,	'الطَّلَاق',	'At-Talaq',	'Talak',	12,	'Madaniyah',	99,	NULL,	'2025-05-04 02:14:01'),
(66,	'التَّحْرِيم',	'At-Tahrim',	'Pengharaman',	12,	'Madaniyah',	107,	NULL,	'2025-05-04 02:14:01'),
(67,	'الْمُلْك',	'Al-Mulk',	'Kerajaan',	30,	'Makkiyah',	77,	NULL,	'2025-05-04 02:14:01'),
(68,	'الْقَلَم',	'Al-Qalam',	'Pena',	52,	'Makkiyah',	2,	NULL,	'2025-05-04 02:14:01'),
(69,	'الْحَاقَّة',	'Al-Haqqah',	'Hari Kiamat',	52,	'Makkiyah',	78,	NULL,	'2025-05-04 02:14:01'),
(70,	'الْمَعَارِج',	'Al-Ma\'arij',	'Tempat Naik',	44,	'Makkiyah',	79,	NULL,	'2025-05-04 02:14:01'),
(71,	'نُوح',	'Nuh',	'Nuh',	28,	'Makkiyah',	71,	NULL,	'2025-05-04 02:14:01'),
(72,	'الْجِنّ',	'Al-Jinn',	'Jin',	28,	'Makkiyah',	40,	NULL,	'2025-05-04 02:14:01'),
(73,	'الْمُزَّمِّل',	'Al-Muzzammil',	'Orang yang Berselimut',	20,	'Makkiyah',	3,	NULL,	'2025-05-04 02:14:01'),
(74,	'الْمُدَّثِّر',	'Al-Muddaththir',	'Orang yang Berkemul',	56,	'Makkiyah',	4,	NULL,	'2025-05-04 02:14:01'),
(75,	'الْقِيَامَة',	'Al-Qiyamah',	'Hari Kiamat',	40,	'Makkiyah',	31,	NULL,	'2025-05-04 02:14:01'),
(76,	'الْإِنْسَان',	'Al-Insan',	'Manusia',	31,	'Madaniyah',	98,	NULL,	'2025-05-04 02:14:01'),
(77,	'الْمُرْسَلَات',	'Al-Mursalat',	'Malaikat yang Diutus',	50,	'Makkiyah',	33,	NULL,	'2025-05-04 02:14:01'),
(78,	'النَّبَأ',	'An-Naba',	'Berita Besar',	40,	'Makkiyah',	80,	NULL,	'2025-05-04 02:14:01'),
(79,	'النَّازِعَات',	'An-Nazi\'at',	'Malaikat yang Mencabut',	46,	'Makkiyah',	81,	NULL,	'2025-05-04 02:14:01'),
(80,	'عَبَس',	'Abasa',	'Ia Bermuka Masam',	42,	'Makkiyah',	24,	NULL,	'2025-05-04 02:14:01'),
(81,	'التَّكْوِير',	'At-Takwir',	'Penggulungan',	29,	'Makkiyah',	7,	NULL,	'2025-05-04 02:14:01'),
(82,	'الْإِنْفِطَار',	'Al-Infitar',	'Terbelah',	19,	'Makkiyah',	82,	NULL,	'2025-05-04 02:14:01'),
(83,	'الْمُطَفِّفِينَ',	'Al-Mutaffifin',	'Orang-Orang yang Curang',	36,	'Makkiyah',	86,	NULL,	'2025-05-04 02:14:01'),
(84,	'الْإِنْشِقَاق',	'Al-Inshiqaq',	'Terbelah',	25,	'Makkiyah',	83,	NULL,	'2025-05-04 02:14:01'),
(85,	'الْبُرُوج',	'Al-Buruj',	'Gugusan Bintang',	22,	'Makkiyah',	27,	NULL,	'2025-05-04 02:14:01'),
(86,	'الطَّارِق',	'At-Tariq',	'Yang Datang di Malam Hari',	17,	'Makkiyah',	36,	NULL,	'2025-05-04 02:14:01'),
(87,	'الْأَعْلَى',	'Al-A\'la',	'Yang Paling Tinggi',	19,	'Makkiyah',	8,	NULL,	'2025-05-04 02:14:01'),
(88,	'الْغَاشِيَة',	'Al-Ghashiyah',	'Hari Pembalasan',	26,	'Makkiyah',	68,	NULL,	'2025-05-04 02:14:01'),
(89,	'الْفَجْر',	'Al-Fajr',	'Fajar',	30,	'Makkiyah',	10,	NULL,	'2025-05-04 02:14:01'),
(90,	'الْبَلَد',	'Al-Balad',	'Negeri',	20,	'Makkiyah',	35,	NULL,	'2025-05-04 02:14:01'),
(91,	'الشَّمْس',	'Ash-Shams',	'Matahari',	15,	'Makkiyah',	26,	NULL,	'2025-05-04 02:14:01'),
(92,	'اللَّيْل',	'Al-Layl',	'Malam',	21,	'Makkiyah',	9,	NULL,	'2025-05-04 02:14:01'),
(93,	'الضُّحَى',	'Ad-Duha',	'Duha',	11,	'Makkiyah',	11,	NULL,	'2025-05-04 02:14:01'),
(94,	'الشَّرْح',	'Ash-Sharh',	'Lapang',	8,	'Makkiyah',	12,	NULL,	'2025-05-04 02:14:01'),
(95,	'التِّين',	'At-Tin',	'Buah Tin',	8,	'Makkiyah',	28,	NULL,	'2025-05-04 02:14:01'),
(96,	'الْعَلَق',	'Al-Alaq',	'Segumpal Darah',	19,	'Makkiyah',	1,	NULL,	'2025-05-04 02:14:01'),
(97,	'الْقَدْر',	'Al-Qadr',	'Kemuliaan',	5,	'Makkiyah',	25,	NULL,	'2025-05-04 02:14:01'),
(98,	'الْبَيِّنَة',	'Al-Bayyinah',	'Bukti Nyata',	8,	'Madaniyah',	100,	NULL,	'2025-05-04 02:14:01'),
(99,	'الزَّلْزَلَة',	'Az-Zalzalah',	'Kegoncangan',	8,	'Madaniyah',	93,	NULL,	'2025-05-04 02:14:01'),
(100,	'الْعَادِيَات',	'Al-Adiyat',	'Kuda Perang yang Berlari Kencang',	11,	'Makkiyah',	14,	NULL,	'2025-05-04 02:14:01'),
(101,	'الْقَارِعَة',	'Al-Qari\'ah',	'Hari Kiamat',	11,	'Makkiyah',	30,	NULL,	'2025-05-04 02:14:01'),
(102,	'التَّكَاثُر',	'At-Takathur',	'Bermegah-megahan',	8,	'Makkiyah',	16,	NULL,	'2025-05-04 02:14:01'),
(103,	'الْعَصْر',	'Al-Asr',	'Asar',	3,	'Makkiyah',	13,	NULL,	'2025-05-04 02:14:01'),
(104,	'الْهُمَزَة',	'Al-Humazah',	'Pengumpat',	9,	'Makkiyah',	32,	NULL,	'2025-05-04 02:14:01'),
(105,	'الْفِيل',	'Al-Fil',	'Gajah',	5,	'Makkiyah',	19,	NULL,	'2025-05-04 02:14:01'),
(106,	'قُرَيْش',	'Quraysh',	'Quraisy',	4,	'Makkiyah',	29,	NULL,	'2025-05-04 02:14:01'),
(107,	'الْمَاعُون',	'Al-Ma\'un',	'Barang yang Berguna',	7,	'Makkiyah',	17,	NULL,	'2025-05-04 02:14:01'),
(108,	'الْكَوْثَر',	'Al-Kawthar',	'Nikmat yang Berlimpah',	3,	'Makkiyah',	15,	NULL,	'2025-05-04 02:14:01'),
(109,	'الْكَافِرُونَ',	'Al-Kafirun',	'Orang-Orang Kafir',	6,	'Makkiyah',	18,	NULL,	'2025-05-04 02:14:01'),
(110,	'النَّصْر',	'An-Nasr',	'Pertolongan',	3,	'Madaniyah',	114,	NULL,	'2025-05-04 02:14:01'),
(111,	'الْمَسَد',	'Al-Masad',	'Sabut',	5,	'Makkiyah',	6,	NULL,	'2025-05-04 02:14:01'),
(112,	'الْإِخْلَاص',	'Al-Ikhlas',	'Ikhlas',	4,	'Makkiyah',	22,	NULL,	'2025-05-04 02:14:01'),
(113,	'الْفَلَق',	'Al-Falaq',	'Subuh',	5,	'Makkiyah',	20,	NULL,	'2025-05-04 02:14:01'),
(114,	'النَّاس',	'An-Nas',	'Manusia',	6,	'Makkiyah',	21,	NULL,	'2025-05-04 02:14:01');

DROP TABLE IF EXISTS `mu_anggota`;
CREATE TABLE `mu_anggota` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nbm` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `nama` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `created_at` datetime NOT NULL,
  `tempat_lahir` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `alamat` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `no_hp` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `email` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `updated_at` datetime NOT NULL,
  `created_by` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `mu_anggota` (`id`, `nbm`, `nama`, `tanggal_lahir`, `created_at`, `tempat_lahir`, `alamat`, `no_hp`, `email`, `updated_at`, `created_by`) VALUES
(1,	'123',	'Adi Sabwa Isti Besari',	'2024-11-28',	'2024-12-05 06:53:30',	'AD',	'ADA',	'6285799441371',	'adi.sabwa@gmail.com',	'2025-05-13 06:40:59',	1),
(2,	'1',	'1A',	'2025-04-30',	'2025-05-03 13:11:52',	'1',	'1',	'1234578',	'',	'2025-05-05 03:52:51',	19),
(18,	'1',	'1V',	'2025-04-30',	'2025-05-03 14:11:37',	'1',	'1',	'122',	'',	'2025-05-03 14:11:37',	0),
(19,	'1',	'1BB',	'2025-04-30',	'2025-05-03 14:12:44',	'1',	'1',	'123456',	'',	'2025-05-03 14:13:08',	0),
(20,	'',	'XX',	'2025-04-30',	'2025-05-03 14:23:44',	'',	'',	'',	'',	'2025-05-03 14:24:00',	0),
(21,	'1',	'1Acc',	'2025-04-30',	'2025-05-03 14:30:14',	'1',	'1',	'1',	'1',	'2025-05-03 14:30:14',	0),
(22,	'1',	'1JJAK',	'2025-04-30',	'2025-05-03 14:30:57',	'1',	'1',	'1',	'1',	'2025-05-03 14:30:57',	0),
(23,	'1',	'1kadkak',	'2025-04-30',	'2025-05-03 14:31:15',	'1',	'1',	'1',	'1',	'2025-05-03 14:31:15',	0),
(24,	'',	'etr asda afa',	'2025-05-07',	'2025-05-03 14:44:21',	'gs',	'afs',	'628123456',	'',	'2025-05-03 14:45:01',	0),
(25,	'1234',	'Adi Sabwa',	'2025-05-22',	'2025-05-05 03:38:52',	'Kendal',	'Kendal',	'62857998902608',	'',	'2025-05-05 03:40:45',	0);

DROP TABLE IF EXISTS `mu_group`;
CREATE TABLE `mu_group` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama_group` varchar(50) NOT NULL,
  `created_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `mu_group` (`id`, `nama_group`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(4,	'A',	'2025-05-13 13:20:49',	'2025-05-13 06:20:49',	1,	NULL),
(14,	'CC',	'2025-05-13 13:20:54',	'2025-05-13 06:20:54',	1,	NULL),
(15,	'Kel 3',	'2025-05-13 06:40:49',	'2025-05-13 06:40:49',	1,	NULL);

DROP TABLE IF EXISTS `mu_group_activity`;
CREATE TABLE `mu_group_activity` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_group` int NOT NULL,
  `tanggal` date NOT NULL,
  `kegiatan` varchar(200) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_group` (`id_group`),
  CONSTRAINT `mu_group_activity_ibfk_1` FOREIGN KEY (`id_group`) REFERENCES `mu_group` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `mu_group_activity` (`id`, `id_group`, `tanggal`, `kegiatan`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(1,	4,	'2025-05-15',	'Percoban Kegiatan Kegiatna',	'2025-05-15 07:39:11',	'2025-05-15 07:39:11',	1,	NULL),
(2,	4,	'2025-05-15',	'SF asdg SF asdg SF asdg SF asdg SF asdg SF asdg SF asdg SF asdg SF asdg SF asdg SF asdg SF asdg SF asdg SF asdg SF asdg SF asdg SF asdg SF asdg SF asdg SF asdg SF asdg SF asdg SF asdg SF asdg SF asdg ',	'2025-05-15 07:49:35',	'2025-05-15 07:49:35',	1,	NULL),
(3,	4,	'2025-05-16',	'SF asdg SF asdg SF asdg SF asdg SF asdg SF asdg SF asdg SF asdg SF asdg SF asdg SF asdg SF asdg SF asdg SF asdg SF asdg SF asdg SF asdg SF asdg SF asdg SF asdg SF asdg SF asdg SF asdg SF asdg SF asdg ',	'2025-05-15 07:49:55',	'2025-05-15 07:49:55',	1,	NULL);

DROP TABLE IF EXISTS `mu_group_anggota`;
CREATE TABLE `mu_group_anggota` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_group` int NOT NULL,
  `id_anggota` int NOT NULL,
  `type` enum('anggota','mentor') NOT NULL DEFAULT 'anggota',
  `created_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_anggota_type` (`id_anggota`,`type`),
  KEY `id_anggota` (`id_anggota`),
  KEY `id_group` (`id_group`),
  CONSTRAINT `mu_group_anggota_ibfk_2` FOREIGN KEY (`id_anggota`) REFERENCES `mu_anggota` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `mu_group_anggota_ibfk_3` FOREIGN KEY (`id_group`) REFERENCES `mu_group` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `mu_group_anggota` (`id`, `id_group`, `id_anggota`, `type`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(33,	4,	2,	'mentor',	'2025-05-13 06:20:49',	'2025-05-13 06:20:49',	NULL,	NULL),
(34,	4,	19,	'anggota',	'2025-05-13 06:20:49',	'2025-05-13 06:20:49',	NULL,	NULL),
(35,	4,	21,	'anggota',	'2025-05-13 06:20:49',	'2025-05-13 06:20:49',	NULL,	NULL),
(36,	14,	22,	'mentor',	'2025-05-13 06:20:54',	'2025-05-13 06:20:54',	NULL,	NULL),
(37,	14,	23,	'anggota',	'2025-05-13 06:20:54',	'2025-05-13 06:20:54',	NULL,	NULL),
(38,	14,	25,	'anggota',	'2025-05-13 06:20:54',	'2025-05-13 06:20:54',	NULL,	NULL),
(39,	15,	1,	'mentor',	'2025-05-13 06:40:49',	'2025-05-13 06:40:49',	NULL,	NULL),
(40,	15,	24,	'anggota',	'2025-05-13 06:40:49',	'2025-05-13 06:40:49',	NULL,	NULL),
(41,	15,	18,	'anggota',	'2025-05-13 06:40:49',	'2025-05-13 06:40:49',	NULL,	NULL);

DROP TABLE IF EXISTS `mu_group_kolom`;
CREATE TABLE `mu_group_kolom` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama_tabel` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `group` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `group_icon` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `mu_group_kolom` (`id`, `nama_tabel`, `group`, `group_icon`) VALUES
(1,	'mu_pengguna',	'Pengguna',	'mdi:users'),
(2,	'mu_pengguna_akses',	'Akses Pengguna',	'mdi:users'),
(3,	'pdm_pcm',	'Daftar PCM',	''),
(4,	'pdm_prm',	'Daftar PRM',	''),
(5,	'mu_anggota',	'Anggota',	''),
(6,	'mu_quran_baca',	'Membaca Al-Qur\'an',	''),
(7,	'mu_quran_hafal',	'Menghafal Al Qur\'an',	''),
(8,	'mu_quran_tarjamah',	'Menterjemah Al Qur\'an',	''),
(9,	'mu_sholat_wajib',	'Sholat Wajib',	''),
(10,	'mu_sholat_sunnah',	'Sholat Sunnah',	''),
(11,	'mu_infaq_shadaqah',	'Infaq / Shadaqah',	''),
(12,	'mu_group',	'Group Mentoring',	'');

DROP TABLE IF EXISTS `mu_infaq_shadaqah`;
CREATE TABLE `mu_infaq_shadaqah` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_anggota` int NOT NULL,
  `tanggal` date NOT NULL,
  `jumlah` int NOT NULL,
  `keterangan` varchar(100) NOT NULL,
  `created_at` datetime NOT NULL ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL,
  `created_by` int NOT NULL,
  `updated_by` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_anggota` (`id_anggota`),
  CONSTRAINT `mu_infaq_shadaqah_ibfk_1` FOREIGN KEY (`id_anggota`) REFERENCES `mu_anggota` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `mu_infaq_shadaqah` (`id`, `id_anggota`, `tanggal`, `jumlah`, `keterangan`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(1,	24,	'2025-05-09',	100000,	'Makan',	'2025-05-09 21:42:36',	'2025-05-09 13:58:52',	14,	0),
(2,	24,	'2025-05-09',	200000,	'Lazismu',	'2025-05-09 21:42:36',	'2025-05-09 13:59:10',	14,	0),
(3,	24,	'2025-05-09',	10000,	'Infaq Masjid',	'2025-05-09 21:42:36',	'2025-05-09 14:07:17',	14,	0),
(4,	24,	'2025-05-09',	100000,	'Makan',	'2025-05-09 21:42:36',	'2025-05-09 13:58:52',	14,	0),
(5,	24,	'2025-05-09',	200000,	'Lazismu',	'2025-05-09 21:42:36',	'2025-05-09 13:59:10',	14,	0),
(6,	24,	'2025-05-09',	10000,	'Infaq Masjid',	'2025-05-09 21:42:36',	'2025-05-09 14:07:17',	14,	0),
(7,	24,	'2025-04-30',	109000,	'Percobaan ERC',	'2025-05-10 23:26:34',	'2025-05-10 16:26:34',	14,	0),
(8,	24,	'2025-05-09',	200000,	'Lazismu',	'2025-05-09 21:42:36',	'2025-05-09 13:59:10',	14,	0),
(9,	24,	'2025-05-09',	10000,	'Infaq Masjid',	'2025-05-09 21:42:36',	'2025-05-09 14:07:17',	14,	0),
(10,	24,	'2025-05-09',	100000,	'Makan',	'2025-05-09 21:42:36',	'2025-05-09 13:58:52',	14,	0),
(11,	24,	'2025-05-09',	200000,	'Lazismu',	'2025-05-09 21:42:36',	'2025-05-09 13:59:10',	14,	0),
(12,	24,	'2025-05-09',	10000,	'Infaq Masjid',	'2025-05-09 21:42:36',	'2025-05-09 14:07:17',	14,	0),
(14,	24,	'2025-05-10',	109000,	'Percobaan ERC',	'2025-05-10 23:25:05',	'2025-05-10 16:25:05',	14,	0),
(15,	24,	'2025-05-11',	100000,	'133saA-AAA',	'2025-05-09 22:08:08',	'2025-05-09 15:08:08',	14,	0),
(16,	24,	'2025-05-12',	1113000,	'CC',	'2025-05-10 23:25:00',	'2025-05-10 16:25:00',	14,	0);

DROP TABLE IF EXISTS `mu_nama_kolom`;
CREATE TABLE `mu_nama_kolom` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_group` int DEFAULT NULL,
  `id_kolom` int DEFAULT NULL,
  `order` int DEFAULT NULL,
  `nama_kolom` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '',
  `is_fk` enum('0','1') CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '0',
  `nama_fk` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `view_kolom` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `tipe` enum('int','string','float','date','table') CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT 'string',
  `label` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '',
  `input` varchar(20) NOT NULL DEFAULT 'input',
  `prepend` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '',
  `append` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '',
  `from_user` enum('0','1') NOT NULL DEFAULT '1',
  `input_only` enum('0','1') NOT NULL DEFAULT '0',
  `required` enum('0','1') NOT NULL DEFAULT '1',
  `rules` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `default` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '',
  `search` enum('0','1') NOT NULL DEFAULT '0',
  `function` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '',
  `function_input` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '',
  `function_submit` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '',
  `show` enum('1','0') NOT NULL DEFAULT '1',
  `width` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '',
  `min_width` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '',
  `width_input` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `align` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '',
  `sortable` enum('0','1') NOT NULL DEFAULT '1',
  `allow_add` enum('0','1') NOT NULL DEFAULT '0',
  `add_href` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '',
  `add_reset` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '',
  `created_at` datetime DEFAULT NULL,
  `pilihan` varchar(1000) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `id_group` (`id_group`),
  KEY `id_kolom` (`id_kolom`),
  CONSTRAINT `mu_nama_kolom_ibfk_2` FOREIGN KEY (`id_group`) REFERENCES `mu_nama_tabel` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `mu_nama_kolom_ibfk_3` FOREIGN KEY (`id_kolom`) REFERENCES `mu_nama_kolom` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

INSERT INTO `mu_nama_kolom` (`id`, `id_group`, `id_kolom`, `order`, `nama_kolom`, `is_fk`, `nama_fk`, `view_kolom`, `tipe`, `label`, `input`, `prepend`, `append`, `from_user`, `input_only`, `required`, `rules`, `default`, `search`, `function`, `function_input`, `function_submit`, `show`, `width`, `min_width`, `width_input`, `align`, `sortable`, `allow_add`, `add_href`, `add_reset`, `created_at`, `pilihan`) VALUES
(2,	1,	NULL,	2,	'password',	'0',	'',	'',	'string',	'Password',	'input',	'',	'',	'1',	'0',	'1',	NULL,	'',	'0',	'',	'',	'md5',	'1',	'',	'',	'',	'',	'1',	'0',	'',	'',	'2024-11-08 09:19:28',	''),
(4,	1,	NULL,	1,	'unit',	'0',	'',	'',	'string',	'Unit Kerja',	'input',	'',	'',	'0',	'0',	'1',	NULL,	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'',	'1',	'0',	'',	'',	'2024-11-08 09:19:28',	''),
(5,	1,	NULL,	1,	'role',	'0',	'',	'',	'string',	'Role',	'select',	'',	'',	'1',	'0',	'1',	NULL,	'user',	'0',	'ucFirst',	'',	'',	'1',	'',	'',	'',	'',	'1',	'0',	'',	'',	'2024-11-08 09:19:28',	'user,operator,admin'),
(7,	2,	NULL,	1,	'id_pengguna',	'1',	'',	'',	'string',	'Pengguna',	'select',	'',	'',	'1',	'0',	'1',	NULL,	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'',	'1',	'0',	'',	'',	'2024-11-08 09:19:28',	'PenggunaModel'),
(8,	2,	NULL,	2,	'id_akses',	'1',	'',	'aplikasi',	'string',	'Aplikasi',	'input',	'',	'',	'1',	'0',	'1',	NULL,	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'',	'1',	'0',	'',	'',	'2024-11-08 09:19:28',	'ListAksesModel'),
(9,	3,	NULL,	3,	'pcm',	'0',	'',	'',	'string',	'Nama PCM',	'input',	'',	'',	'1',	'0',	'1',	NULL,	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'',	'1',	'0',	'',	'',	'2024-11-25 13:17:56',	''),
(10,	4,	NULL,	1,	'prm',	'0',	'',	'',	'string',	'Nama Ranting',	'input',	'',	'',	'1',	'0',	'1',	NULL,	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'',	'1',	'0',	'',	'',	'2024-11-25 13:17:56',	''),
(11,	4,	NULL,	0,	'id_pcm',	'0',	'',	'',	'string',	'Nama Cabang',	'select',	'',	'',	'1',	'0',	'1',	NULL,	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'',	'1',	'0',	'',	'',	'2024-11-25 13:22:36',	'PcmModel'),
(15,	5,	NULL,	2,	'nama',	'0',	'',	'',	'string',	'Nama Anggota',	'input',	'',	'',	'1',	'0',	'1',	NULL,	'',	'0',	'',	'',	'',	'1',	'',	'200px',	'',	'',	'1',	'0',	'',	'',	NULL,	''),
(16,	5,	NULL,	1,	'nbm',	'0',	'',	'',	'string',	'NBM',	'input',	'',	'',	'1',	'0',	'0',	NULL,	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'',	'1',	'0',	'',	'',	NULL,	''),
(17,	5,	NULL,	4,	'tanggal_lahir',	'0',	'',	'',	'string',	'Tanggal Lahir',	'date',	'',	'',	'1',	'0',	'1',	NULL,	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'',	'1',	'0',	'',	'',	NULL,	''),
(18,	5,	NULL,	3,	'tempat_lahir',	'0',	'',	'',	'string',	'Tempat Lahir',	'input',	'',	'',	'1',	'0',	'1',	NULL,	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'',	'1',	'0',	'',	'',	NULL,	''),
(19,	5,	NULL,	5,	'alamat',	'0',	'',	'',	'string',	'Alamat',	'textarea',	'',	'',	'1',	'0',	'1',	NULL,	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'',	'1',	'0',	'',	'',	NULL,	''),
(23,	1,	NULL,	0,	'id_anggota',	'0',	'',	'',	'string',	'Nama Anggota',	'select',	'',	'',	'1',	'0',	'1',	'is_unique[mu_pengguna.id_anggota,id,{id}]',	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'',	'1',	'1',	'data/anggota/store',	'data/anggota/reset_options',	NULL,	'AnggotaModel'),
(31,	5,	NULL,	6,	'no_hp',	'0',	'',	'',	'string',	'Nomor HP',	'input',	'',	'',	'1',	'0',	'1',	'is_unique[mu_anggota.no_hp,id,{id}]',	'',	'0',	'',	'',	'toPhoneNumber',	'1',	'',	'',	'',	'center',	'1',	'0',	'',	'',	NULL,	''),
(32,	5,	NULL,	7,	'email',	'0',	'',	'',	'string',	'Email',	'input',	'',	'',	'1',	'0',	'0',	'is_unique[mu_anggota.email,id,{id}]',	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'center',	'1',	'0',	'',	'',	NULL,	''),
(33,	6,	NULL,	2,	'tanggal',	'0',	'',	'',	'date',	'Tanggal Membaca',	'date',	'',	'',	'1',	'0',	'1',	NULL,	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'',	'1',	'0',	'',	'',	NULL,	''),
(34,	6,	NULL,	3,	'tingkat',	'0',	'',	'',	'string',	'Tingkat Membaca',	'radio',	'',	'',	'1',	'0',	'1',	NULL,	'lanjut',	'0',	'',	'',	'',	'1',	'',	'',	'',	'',	'1',	'0',	'',	'',	NULL,	'pemula,lanjut'),
(35,	6,	NULL,	4,	'jenis_buku',	'0',	'',	'',	'string',	'Buku Pembelajaran',	'select',	'',	'',	'1',	'0',	'0',	NULL,	'al-qur\'an',	'0',	'',	'',	'',	'1',	'',	'',	'',	'',	'1',	'0',	'',	'',	NULL,	'iqra,qiroati,al-qur\'an'),
(37,	6,	NULL,	5,	'surat_mulai',	'0',	'',	'',	'string',	'Surat Mulai',	'select',	'Surat',	'',	'0',	'0',	'1',	NULL,	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'',	'1',	'0',	'',	'',	NULL,	'DataSuratQuranModel'),
(38,	6,	NULL,	6,	'ayat_mulai',	'0',	'',	'',	'string',	'Ayat Mulai',	'input-number',	'Ayat ke ',	'',	'0',	'0',	'0',	NULL,	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'',	'1',	'0',	'',	'',	NULL,	''),
(39,	6,	NULL,	7,	'surat_selesai',	'0',	'',	'',	'string',	'Surat Selesai',	'select',	'Surat',	'',	'1',	'0',	'0',	NULL,	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'',	'1',	'0',	'',	'',	NULL,	'DataSuratQuranModel'),
(40,	6,	NULL,	8,	'ayat_selesai',	'0',	'',	'',	'string',	'Ayat Selesai',	'input-number',	'Ayat ke ',	'',	'1',	'0',	'0',	NULL,	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'',	'1',	'0',	'',	'',	NULL,	''),
(91,	1,	NULL,	3,	'passwordconf',	'0',	'',	'',	'string',	'Konfirmasi Password',	'input',	'',	'',	'1',	'1',	'1',	'matches[password]',	'',	'0',	'',	'',	'md5',	'1',	'',	'',	'',	'',	'1',	'0',	'',	'',	'2024-11-08 09:19:28',	''),
(92,	6,	NULL,	9,	'total_ayat',	'0',	'',	'',	'string',	'Total Ayat',	'input-number',	'',	'',	'0',	'0',	'0',	NULL,	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'',	'1',	'0',	'',	'',	NULL,	''),
(93,	6,	NULL,	6,	'surat_mulai-ayat_mulai',	'0',	'',	'',	'string',	'Awal Bacaan',	'select-double',	' dan Ayat  ',	'',	'1',	'1',	'1',	NULL,	'',	'0',	'',	'',	'',	'1',	'',	'',	'200px',	'',	'1',	'0',	'',	'',	NULL,	'DataSuratQuranModel'),
(94,	6,	NULL,	8,	'surat_selesai-ayat_selesai',	'0',	'',	'',	'string',	'Akhir Bacaan',	'select-double',	' dan Ayat  ',	'',	'1',	'1',	'1',	'double_greater_and_equal_to[surat_mulai-ayat_mulai]',	'',	'0',	'',	'',	'',	'1',	'',	'',	'200px',	'',	'1',	'0',	'',	'',	NULL,	'DataSuratQuranModel'),
(95,	6,	NULL,	1,	'id_anggota',	'0',	'',	'',	'string',	'Nama',	'input',	'',	'',	'1',	'0',	'1',	NULL,	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'',	'1',	'0',	'',	'',	NULL,	'AnggotaModel'),
(96,	7,	NULL,	2,	'tanggal',	'0',	'',	'',	'date',	'Tanggal Kegiatan',	'date',	'',	'',	'1',	'0',	'1',	NULL,	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'',	'1',	'0',	'',	'',	NULL,	''),
(99,	7,	NULL,	5,	'surat_mulai',	'0',	'',	'',	'string',	'Surat Mulai',	'select',	'Surat',	'',	'0',	'0',	'1',	NULL,	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'',	'1',	'0',	'',	'',	NULL,	'DataSuratQuranModel'),
(100,	7,	NULL,	6,	'ayat_mulai',	'0',	'',	'',	'string',	'Ayat Mulai',	'input-number',	'Ayat ke ',	'',	'0',	'0',	'0',	NULL,	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'',	'1',	'0',	'',	'',	NULL,	''),
(101,	7,	NULL,	7,	'surat_selesai',	'0',	'',	'',	'string',	'Surat Selesai',	'select',	'Surat',	'',	'1',	'0',	'0',	NULL,	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'',	'1',	'0',	'',	'',	NULL,	'DataSuratQuranModel'),
(102,	7,	NULL,	8,	'ayat_selesai',	'0',	'',	'',	'string',	'Ayat Selesai',	'input-number',	'Ayat ke ',	'',	'1',	'0',	'0',	NULL,	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'',	'1',	'0',	'',	'',	NULL,	''),
(103,	7,	NULL,	9,	'total_ayat',	'0',	'',	'',	'string',	'Total Ayat',	'input-number',	'',	'',	'0',	'0',	'0',	NULL,	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'',	'1',	'0',	'',	'',	NULL,	''),
(104,	7,	NULL,	6,	'surat_mulai-ayat_mulai',	'0',	'',	'',	'string',	'Awal Bacaan',	'select-double',	' dan Ayat  ',	'',	'1',	'1',	'1',	NULL,	'',	'0',	'',	'',	'',	'1',	'',	'',	'200px',	'',	'1',	'0',	'',	'',	NULL,	'DataSuratQuranModel'),
(105,	7,	NULL,	8,	'surat_selesai-ayat_selesai',	'0',	'',	'',	'string',	'Akhir Bacaan',	'select-double',	' dan Ayat  ',	'',	'1',	'1',	'1',	'double_greater_and_equal_to[surat_mulai-ayat_mulai]',	'',	'0',	'',	'',	'',	'1',	'',	'',	'200px',	'',	'1',	'0',	'',	'',	NULL,	'DataSuratQuranModel'),
(106,	7,	NULL,	1,	'id_anggota',	'0',	'',	'',	'string',	'Nama',	'input',	'',	'',	'1',	'0',	'1',	NULL,	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'',	'1',	'0',	'',	'',	NULL,	'AnggotaModel'),
(107,	8,	NULL,	2,	'tanggal',	'0',	'',	'',	'date',	'Tanggal Aktivitas',	'date',	'',	'',	'1',	'0',	'1',	NULL,	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'',	'1',	'0',	'',	'',	NULL,	''),
(108,	8,	NULL,	5,	'surat_mulai',	'0',	'',	'',	'string',	'Surat Mulai',	'select',	'Surat',	'',	'0',	'0',	'1',	NULL,	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'',	'1',	'0',	'',	'',	NULL,	'DataSuratQuranModel'),
(109,	8,	NULL,	6,	'ayat_mulai',	'0',	'',	'',	'string',	'Ayat Mulai',	'input-number',	'Ayat ke ',	'',	'0',	'0',	'0',	NULL,	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'',	'1',	'0',	'',	'',	NULL,	''),
(110,	8,	NULL,	7,	'surat_selesai',	'0',	'',	'',	'string',	'Surat Selesai',	'select',	'Surat',	'',	'1',	'0',	'0',	NULL,	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'',	'1',	'0',	'',	'',	NULL,	'DataSuratQuranModel'),
(111,	8,	NULL,	8,	'ayat_selesai',	'0',	'',	'',	'string',	'Ayat Selesai',	'input-number',	'Ayat ke ',	'',	'1',	'0',	'0',	NULL,	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'',	'1',	'0',	'',	'',	NULL,	''),
(112,	8,	NULL,	9,	'total_ayat',	'0',	'',	'',	'string',	'Total Ayat',	'input-number',	'',	'',	'0',	'0',	'0',	NULL,	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'',	'1',	'0',	'',	'',	NULL,	''),
(113,	8,	NULL,	6,	'surat_mulai-ayat_mulai',	'0',	'',	'',	'string',	'Awal Ayat',	'select-double',	' dan Ayat  ',	'',	'1',	'1',	'1',	NULL,	'',	'0',	'',	'',	'',	'1',	'',	'',	'200px',	'',	'1',	'0',	'',	'',	NULL,	'DataSuratQuranModel'),
(114,	8,	NULL,	8,	'surat_selesai-ayat_selesai',	'0',	'',	'',	'string',	'Akhir Ayat',	'select-double',	' dan Ayat  ',	'',	'1',	'1',	'1',	'double_greater_and_equal_to[surat_mulai-ayat_mulai]',	'',	'0',	'',	'',	'',	'1',	'',	'',	'200px',	'',	'1',	'0',	'',	'',	NULL,	'DataSuratQuranModel'),
(115,	8,	NULL,	1,	'id_anggota',	'0',	'',	'',	'string',	'Nama',	'input',	'',	'',	'1',	'0',	'1',	NULL,	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'',	'1',	'0',	'',	'',	NULL,	'AnggotaModel'),
(122,	9,	NULL,	2,	'tanggal',	'0',	'',	'',	'date',	'Tanggal Setoran',	'date',	'',	'',	'1',	'0',	'1',	NULL,	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'',	'1',	'0',	'',	'',	NULL,	''),
(123,	9,	NULL,	3,	'shubuh',	'0',	'',	'',	'int',	'Shubuh',	'select',	'',	'',	'1',	'0',	'1',	NULL,	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'',	'1',	'0',	'',	'',	NULL,	'a:4:{i:0;a:2:{s:5:\"value\";s:3:\"100\";s:5:\"label\";s:6:\"Jamaah\";}i:1;a:2:{s:5:\"value\";s:2:\"70\";s:5:\"label\";s:6:\"Masbuq\";}i:2;a:2:{s:5:\"value\";s:2:\"30\";s:5:\"label\";s:7:\"Sendiri\";}i:3;a:2:{s:5:\"value\";s:1:\"0\";s:5:\"label\";s:12:\"Tidak Sholat\";}}'),
(124,	9,	NULL,	1,	'id_anggota',	'0',	'',	'',	'string',	'Nama',	'input',	'',	'',	'1',	'0',	'1',	NULL,	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'',	'1',	'0',	'',	'',	NULL,	'AnggotaModel'),
(125,	9,	NULL,	4,	'dhuhur',	'0',	'',	'',	'int',	'Dhuhur',	'select',	'',	'',	'1',	'0',	'1',	NULL,	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'',	'1',	'0',	'',	'',	NULL,	'a:4:{i:0;a:2:{s:5:\"value\";s:3:\"100\";s:5:\"label\";s:6:\"Jamaah\";}i:1;a:2:{s:5:\"value\";s:2:\"70\";s:5:\"label\";s:6:\"Masbuq\";}i:2;a:2:{s:5:\"value\";s:2:\"30\";s:5:\"label\";s:7:\"Sendiri\";}i:3;a:2:{s:5:\"value\";s:1:\"0\";s:5:\"label\";s:12:\"Tidak Sholat\";}}'),
(126,	9,	NULL,	5,	'asar',	'0',	'',	'',	'int',	'Asar',	'select',	'',	'',	'1',	'0',	'1',	NULL,	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'',	'1',	'0',	'',	'',	NULL,	'a:4:{i:0;a:2:{s:5:\"value\";s:3:\"100\";s:5:\"label\";s:6:\"Jamaah\";}i:1;a:2:{s:5:\"value\";s:2:\"70\";s:5:\"label\";s:6:\"Masbuq\";}i:2;a:2:{s:5:\"value\";s:2:\"30\";s:5:\"label\";s:7:\"Sendiri\";}i:3;a:2:{s:5:\"value\";s:1:\"0\";s:5:\"label\";s:12:\"Tidak Sholat\";}}'),
(127,	9,	NULL,	6,	'maghrib',	'0',	'',	'',	'int',	'Maghrib',	'select',	'',	'',	'1',	'0',	'1',	NULL,	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'',	'1',	'0',	'',	'',	NULL,	'a:4:{i:0;a:2:{s:5:\"value\";s:3:\"100\";s:5:\"label\";s:6:\"Jamaah\";}i:1;a:2:{s:5:\"value\";s:2:\"70\";s:5:\"label\";s:6:\"Masbuq\";}i:2;a:2:{s:5:\"value\";s:2:\"30\";s:5:\"label\";s:7:\"Sendiri\";}i:3;a:2:{s:5:\"value\";s:1:\"0\";s:5:\"label\";s:12:\"Tidak Sholat\";}}'),
(128,	9,	NULL,	7,	'isya',	'0',	'',	'',	'int',	'Isya',	'select',	'',	'',	'1',	'0',	'1',	NULL,	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'',	'1',	'0',	'',	'',	NULL,	'a:4:{i:0;a:2:{s:5:\"value\";s:3:\"100\";s:5:\"label\";s:6:\"Jamaah\";}i:1;a:2:{s:5:\"value\";s:2:\"70\";s:5:\"label\";s:6:\"Masbuq\";}i:2;a:2:{s:5:\"value\";s:2:\"30\";s:5:\"label\";s:7:\"Sendiri\";}i:3;a:2:{s:5:\"value\";s:1:\"0\";s:5:\"label\";s:12:\"Tidak Sholat\";}}'),
(129,	9,	NULL,	7,	'total_score',	'0',	'',	'',	'int',	'Nilai Total',	'input-number',	'',	'',	'0',	'0',	'1',	NULL,	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'',	'1',	'0',	'',	'',	NULL,	'a:4:{i:0;a:2:{s:5:\"value\";s:3:\"100\";s:5:\"label\";s:6:\"Jamaah\";}i:1;a:2:{s:5:\"value\";s:2:\"70\";s:5:\"label\";s:6:\"Masbuq\";}i:2;a:2:{s:5:\"value\";s:2:\"30\";s:5:\"label\";s:7:\"Sendiri\";}i:3;a:2:{s:5:\"value\";s:1:\"0\";s:5:\"label\";s:12:\"Tidak Sholat\";}}'),
(130,	10,	NULL,	1,	'id_anggota',	'0',	'',	'',	'string',	'Nama',	'input',	'',	'',	'1',	'0',	'1',	'',	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'',	'1',	'0',	'',	'',	NULL,	''),
(131,	10,	NULL,	2,	'tanggal',	'0',	'',	'',	'date',	'Tanggal',	'date',	'',	'',	'1',	'0',	'1',	'',	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'',	'1',	'0',	'',	'',	NULL,	''),
(132,	10,	NULL,	3,	'id_sholat',	'0',	'',	'',	'int',	'Jenis Sholat',	'select',	'',	'',	'1',	'0',	'1',	'',	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'',	'1',	'0',	'',	'',	NULL,	''),
(133,	10,	NULL,	4,	'rakaat',	'0',	'',	'',	'int',	'Jumlah Rakaat',	'input-number',	'',	'',	'1',	'0',	'1',	'',	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'',	'1',	'0',	'',	'',	NULL,	''),
(137,	11,	NULL,	1,	'id_anggota',	'0',	'',	'',	'string',	'Nama',	'input',	'',	'',	'1',	'0',	'1',	'',	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'',	'1',	'0',	'',	'',	NULL,	''),
(138,	11,	NULL,	2,	'tanggal',	'0',	'',	'',	'date',	'Tanggal',	'date',	'',	'',	'1',	'0',	'1',	'',	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'',	'1',	'0',	'',	'',	NULL,	''),
(139,	11,	NULL,	3,	'jumlah',	'0',	'',	'',	'int',	'Jumlah Infaq',	'input',	'',	'',	'1',	'0',	'1',	'',	'',	'0',	'',	'setCurrency',	'toNumber',	'1',	'',	'',	'',	'',	'1',	'0',	'',	'',	NULL,	''),
(140,	11,	NULL,	4,	'keterangan',	'0',	'',	'',	'string',	'Keterangan',	'input',	'',	'',	'1',	'0',	'1',	'',	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'',	'1',	'0',	'',	'',	NULL,	''),
(141,	12,	NULL,	1,	'nama_group',	'0',	'',	'',	'string',	'Nama Group',	'input',	'',	'',	'1',	'0',	'1',	NULL,	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'',	'1',	'0',	'',	'',	'2024-11-08 09:19:28',	''),
(142,	12,	NULL,	2,	'mu_group_anggota',	'0',	'id_group',	'',	'table',	'Anggota Group',	'array',	'',	'',	'1',	'0',	'1',	NULL,	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'',	'1',	'0',	'',	'',	'2024-11-08 09:19:28',	''),
(143,	13,	142,	1,	'id_anggota',	'0',	'',	'',	'string',	'Nama Anggota',	'select',	'',	'',	'1',	'0',	'1',	'unique_input[id_anggota,type,{field}]|unique_combination[mu_group_anggota.id_anggota-type,id_group,{id},{field}]',	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'',	'1',	'0',	'',	'',	'2024-11-08 09:19:28',	'AnggotaModel'),
(144,	13,	142,	2,	'type',	'0',	'',	'',	'string',	'Tipe',	'select',	'',	'',	'1',	'0',	'1',	'',	'',	'0',	'',	'',	'',	'1',	'',	'',	'150px',	'',	'1',	'0',	'',	'',	'2024-11-08 09:19:28',	'mentor,anggota'),
(146,	14,	NULL,	1,	'id_group',	'0',	'',	'',	'string',	'Nama Group',	'select',	'',	'',	'1',	'0',	'1',	'',	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'',	'1',	'0',	'',	'',	'2024-11-08 09:19:28',	'GroupModel'),
(147,	14,	NULL,	2,	'tanggal',	'0',	'',	'',	'date',	'Tanggal Kegiatan',	'date',	'',	'',	'1',	'0',	'1',	NULL,	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'',	'1',	'0',	'',	'',	NULL,	''),
(148,	14,	NULL,	3,	'kegiatan',	'0',	'',	'',	'string',	'Keterangan Kegiatan',	'textarea',	'',	'',	'1',	'0',	'1',	NULL,	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'',	'1',	'0',	'',	'',	NULL,	'');

DROP TABLE IF EXISTS `mu_nama_tabel`;
CREATE TABLE `mu_nama_tabel` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama_tabel` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `group` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `group_icon` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `mu_nama_tabel` (`id`, `nama_tabel`, `group`, `group_icon`) VALUES
(1,	'mu_pengguna',	'Pengguna',	'mdi:users'),
(2,	'mu_pengguna_akses',	'Akses Pengguna',	'mdi:users'),
(3,	'pdm_pcm',	'Daftar PCM',	''),
(4,	'pdm_prm',	'Daftar PRM',	''),
(5,	'mu_anggota',	'Anggota',	''),
(6,	'mu_quran_baca',	'Membaca Al-Qur\'an',	''),
(7,	'mu_quran_hafal',	'Menghafal Al Qur\'an',	''),
(8,	'mu_quran_tarjamah',	'Menterjemah Al Qur\'an',	''),
(9,	'mu_sholat_wajib',	'Sholat Wajib',	''),
(10,	'mu_sholat_sunnah',	'Sholat Sunnah',	''),
(11,	'mu_infaq_shadaqah',	'Infaq / Shadaqah',	''),
(12,	'mu_group',	'Group Mentoring',	''),
(13,	'mu_group_anggota',	'Anggota Group',	''),
(14,	'mu_group_activity',	'Aktifitas Grup',	'');

DROP TABLE IF EXISTS `mu_pengguna`;
CREATE TABLE `mu_pengguna` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_anggota` int NOT NULL,
  `password` varchar(200) NOT NULL,
  `unit` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `role` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT 'user',
  `vertical` enum('0','1') CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_by` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_anggota` (`id_anggota`),
  CONSTRAINT `mu_pengguna_ibfk_3` FOREIGN KEY (`id_anggota`) REFERENCES `mu_anggota` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

INSERT INTO `mu_pengguna` (`id`, `id_anggota`, `password`, `unit`, `role`, `vertical`, `created_at`, `updated_at`, `created_by`) VALUES
(1,	1,	'4f19ecc280c47d1d7ee7b581f764c9d7',	'Pondok Darul Arqom',	'admin',	'0',	'2024-09-21 14:38:11',	'2025-05-01 07:11:57',	0),
(12,	19,	'77963b7a931377ad4ab5ad6a9cd718aa',	NULL,	'user',	'1',	'2025-05-03 14:12:44',	'2025-05-03 14:13:08',	0),
(13,	20,	'd41d8cd98f00b204e9800998ecf8427e',	NULL,	'user',	'1',	'2025-05-03 14:23:44',	'2025-05-03 14:24:01',	0),
(14,	24,	'47bce5c74f589f4867dbd57e9ca9f808',	NULL,	'user',	'1',	'2025-05-03 14:45:02',	'2025-05-03 14:45:02',	0),
(15,	25,	'47bce5c74f589f4867dbd57e9ca9f808',	NULL,	'user',	'1',	'2025-05-05 03:38:52',	'2025-05-05 03:40:45',	0),
(20,	2,	'47bce5c74f589f4867dbd57e9ca9f808',	NULL,	'user',	'1',	'2025-05-05 03:52:52',	'2025-05-05 03:52:52',	19);

DROP TABLE IF EXISTS `mu_quran_baca`;
CREATE TABLE `mu_quran_baca` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_anggota` int NOT NULL,
  `tanggal` date NOT NULL,
  `tingkat` enum('pemula','lanjut') NOT NULL DEFAULT 'lanjut',
  `jenis_buku` varchar(20) DEFAULT NULL,
  `surat_mulai` int DEFAULT NULL,
  `ayat_mulai` int DEFAULT NULL,
  `surat_selesai` int DEFAULT NULL,
  `ayat_selesai` int DEFAULT NULL,
  `total_ayat` int DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_anggota` (`id_anggota`),
  KEY `surat_selesai` (`surat_selesai`),
  KEY `surat_mulai` (`surat_mulai`),
  CONSTRAINT `mu_quran_baca_ibfk_5` FOREIGN KEY (`id_anggota`) REFERENCES `mu_anggota` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `mu_quran_baca_ibfk_6` FOREIGN KEY (`surat_selesai`) REFERENCES `mu__surat_quran` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `mu_quran_baca_ibfk_7` FOREIGN KEY (`surat_mulai`) REFERENCES `mu__surat_quran` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `mu_quran_baca` (`id`, `id_anggota`, `tanggal`, `tingkat`, `jenis_buku`, `surat_mulai`, `ayat_mulai`, `surat_selesai`, `ayat_selesai`, `total_ayat`, `created_at`, `created_by`, `updated_at`, `updated_by`) VALUES
(4,	24,	'2025-05-01',	'lanjut',	'al-qur\'an',	1,	1,	1,	1,	NULL,	'2025-05-04 14:14:10',	14,	'2025-05-04 14:14:10',	NULL),
(5,	24,	'2025-05-01',	'lanjut',	'al-qur\'an',	1,	1,	1,	1,	NULL,	'2025-05-04 14:14:34',	14,	'2025-05-04 14:14:34',	NULL),
(6,	24,	'2025-05-02',	'lanjut',	'al-qur\'an',	1,	1,	1,	7,	7,	'2025-05-04 14:23:14',	14,	'2025-05-04 14:24:32',	NULL),
(7,	24,	'2025-05-02',	'lanjut',	'al-qur\'an',	1,	1,	2,	9,	16,	'2025-05-04 14:32:54',	14,	'2025-05-04 14:32:54',	NULL),
(8,	24,	'2025-05-03',	'lanjut',	'al-qur\'an',	2,	7,	5,	9,	289,	'2025-05-04 14:33:42',	14,	'2025-05-04 14:33:42',	NULL),
(9,	24,	'2025-05-03',	'lanjut',	'al-qur\'an',	2,	7,	5,	9,	289,	'2025-05-04 14:33:59',	14,	'2025-05-04 14:33:59',	NULL),
(10,	24,	'2025-05-04',	'lanjut',	'al-qur\'an',	5,	8,	6,	8,	121,	'2025-05-04 14:35:15',	14,	'2025-05-04 14:35:15',	NULL),
(11,	24,	'2025-05-04',	'lanjut',	'al-qur\'an',	1,	2,	6,	9,	15,	'2025-05-04 14:40:20',	14,	'2025-05-04 14:40:20',	NULL),
(12,	24,	'2025-05-05',	'lanjut',	'al-qur\'an',	1,	1,	6,	9,	16,	'2025-05-04 14:46:47',	14,	'2025-05-04 14:46:47',	NULL),
(13,	24,	'2025-05-06',	'lanjut',	'al-qur\'an',	6,	1,	6,	110,	110,	'2025-05-05 02:01:38',	14,	'2025-05-05 02:01:38',	NULL),
(14,	2,	'2025-05-05',	'lanjut',	'al-qur\'an',	1,	1,	2,	8,	15,	'2025-05-05 10:20:01',	20,	'2025-05-05 10:20:01',	NULL),
(15,	2,	'2025-05-06',	'lanjut',	'al-qur\'an',	2,	8,	3,	9,	288,	'2025-05-05 10:20:23',	20,	'2025-05-05 10:20:23',	NULL),
(16,	24,	'2025-05-08',	'lanjut',	'al-qur\'an',	4,	1,	4,	9,	9,	'2025-05-08 15:03:31',	14,	'2025-05-08 15:03:31',	NULL),
(17,	24,	'2025-05-08',	'lanjut',	'al-qur\'an',	3,	1,	3,	200,	200,	'2025-05-08 15:21:57',	14,	'2025-05-08 15:21:57',	NULL),
(18,	1,	'2025-05-02',	'lanjut',	'al-qur\'an',	1,	1,	1,	1,	NULL,	'2025-05-04 14:14:10',	14,	'2025-05-04 14:14:10',	NULL),
(19,	1,	'2025-05-01',	'lanjut',	'al-qur\'an',	1,	1,	1,	1,	NULL,	'2025-05-04 14:14:34',	14,	'2025-05-04 14:14:34',	NULL),
(20,	1,	'2025-05-02',	'lanjut',	'al-qur\'an',	1,	1,	1,	7,	7,	'2025-05-04 14:23:14',	14,	'2025-05-04 14:24:32',	NULL),
(21,	1,	'2025-05-02',	'lanjut',	'al-qur\'an',	1,	1,	2,	9,	16,	'2025-05-04 14:32:54',	14,	'2025-05-04 14:32:54',	NULL),
(22,	1,	'2025-05-03',	'lanjut',	'al-qur\'an',	2,	7,	5,	9,	289,	'2025-05-04 14:33:42',	14,	'2025-05-04 14:33:42',	NULL),
(23,	1,	'2025-05-03',	'lanjut',	'al-qur\'an',	2,	7,	5,	9,	289,	'2025-05-04 14:33:59',	14,	'2025-05-04 14:33:59',	NULL),
(24,	1,	'2025-05-02',	'lanjut',	'al-qur\'an',	5,	8,	6,	8,	121,	'2025-05-04 14:35:15',	14,	'2025-05-04 14:35:15',	NULL),
(25,	1,	'2025-05-04',	'lanjut',	'al-qur\'an',	1,	2,	6,	9,	15,	'2025-05-04 14:40:20',	14,	'2025-05-04 14:40:20',	NULL),
(26,	1,	'2025-05-05',	'lanjut',	'al-qur\'an',	1,	1,	6,	9,	16,	'2025-05-04 14:46:47',	14,	'2025-05-04 14:46:47',	NULL),
(27,	1,	'2025-05-03',	'lanjut',	'al-qur\'an',	6,	1,	6,	110,	110,	'2025-05-05 02:01:38',	14,	'2025-05-05 02:01:38',	NULL),
(28,	1,	'2025-05-08',	'lanjut',	'al-qur\'an',	4,	1,	4,	9,	9,	'2025-05-08 15:03:31',	14,	'2025-05-08 15:03:31',	NULL),
(29,	1,	'2025-05-03',	'lanjut',	'al-qur\'an',	3,	1,	3,	200,	200,	'2025-05-08 15:21:57',	14,	'2025-05-08 15:21:57',	NULL),
(33,	18,	'2025-05-01',	'lanjut',	'al-qur\'an',	1,	1,	1,	1,	NULL,	'2025-05-04 14:14:10',	14,	'2025-05-04 14:14:10',	NULL),
(34,	18,	'2025-05-01',	'lanjut',	'al-qur\'an',	1,	1,	1,	1,	NULL,	'2025-05-04 14:14:34',	14,	'2025-05-04 14:14:34',	NULL),
(35,	18,	'2025-05-02',	'lanjut',	'al-qur\'an',	1,	1,	1,	7,	7,	'2025-05-04 14:23:14',	14,	'2025-05-04 14:24:32',	NULL),
(36,	18,	'2025-05-05',	'lanjut',	'al-qur\'an',	1,	1,	2,	9,	16,	'2025-05-04 14:32:54',	14,	'2025-05-04 14:32:54',	NULL),
(37,	18,	'2025-05-03',	'lanjut',	'al-qur\'an',	2,	7,	5,	9,	289,	'2025-05-04 14:33:42',	14,	'2025-05-04 14:33:42',	NULL),
(38,	18,	'2025-05-03',	'lanjut',	'al-qur\'an',	2,	7,	5,	9,	289,	'2025-05-04 14:33:59',	14,	'2025-05-04 14:33:59',	NULL),
(39,	18,	'2025-05-03',	'lanjut',	'al-qur\'an',	5,	8,	6,	8,	121,	'2025-05-04 14:35:15',	14,	'2025-05-04 14:35:15',	NULL),
(40,	18,	'2025-05-04',	'lanjut',	'al-qur\'an',	1,	2,	6,	9,	15,	'2025-05-04 14:40:20',	14,	'2025-05-04 14:40:20',	NULL),
(41,	18,	'2025-05-05',	'lanjut',	'al-qur\'an',	1,	1,	6,	9,	16,	'2025-05-04 14:46:47',	14,	'2025-05-04 14:46:47',	NULL),
(42,	18,	'2025-05-06',	'lanjut',	'al-qur\'an',	6,	1,	6,	110,	110,	'2025-05-05 02:01:38',	14,	'2025-05-05 02:01:38',	NULL),
(43,	18,	'2025-05-06',	'lanjut',	'al-qur\'an',	4,	1,	4,	9,	9,	'2025-05-08 15:03:31',	14,	'2025-05-08 15:03:31',	NULL),
(44,	18,	'2025-05-08',	'lanjut',	'al-qur\'an',	3,	1,	3,	200,	200,	'2025-05-08 15:21:57',	14,	'2025-05-08 15:21:57',	NULL);

DROP TABLE IF EXISTS `mu_quran_hafal`;
CREATE TABLE `mu_quran_hafal` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_anggota` int NOT NULL,
  `tanggal` date NOT NULL,
  `surat_mulai` int DEFAULT NULL,
  `ayat_mulai` int DEFAULT NULL,
  `surat_selesai` int DEFAULT NULL,
  `ayat_selesai` int DEFAULT NULL,
  `total_ayat` int DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_anggota` (`id_anggota`),
  KEY `surat_selesai` (`surat_selesai`),
  KEY `surat_mulai` (`surat_mulai`),
  CONSTRAINT `mu_quran_hafal_ibfk_1` FOREIGN KEY (`id_anggota`) REFERENCES `mu_anggota` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `mu_quran_hafal_ibfk_2` FOREIGN KEY (`surat_mulai`) REFERENCES `mu__surat_quran` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `mu_quran_hafal_ibfk_3` FOREIGN KEY (`surat_selesai`) REFERENCES `mu__surat_quran` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `mu_quran_hafal` (`id`, `id_anggota`, `tanggal`, `surat_mulai`, `ayat_mulai`, `surat_selesai`, `ayat_selesai`, `total_ayat`, `created_at`, `created_by`, `updated_at`, `updated_by`) VALUES
(4,	24,	'2025-05-01',	1,	1,	1,	1,	NULL,	'2025-05-04 14:14:10',	14,	'2025-05-04 14:14:10',	NULL),
(5,	24,	'2025-05-01',	1,	1,	1,	1,	NULL,	'2025-05-04 14:14:34',	14,	'2025-05-04 14:14:34',	NULL),
(6,	24,	'2025-05-02',	1,	1,	1,	7,	NULL,	'2025-05-04 14:23:14',	14,	'2025-05-04 14:24:32',	NULL),
(7,	24,	'2025-05-02',	1,	1,	2,	9,	NULL,	'2025-05-04 14:32:54',	14,	'2025-05-04 14:32:54',	NULL),
(8,	24,	'2025-05-03',	2,	7,	5,	9,	NULL,	'2025-05-04 14:33:42',	14,	'2025-05-04 14:33:42',	NULL),
(9,	24,	'2025-05-03',	2,	7,	5,	9,	NULL,	'2025-05-04 14:33:59',	14,	'2025-05-04 14:33:59',	NULL),
(10,	24,	'2025-05-04',	5,	8,	6,	8,	NULL,	'2025-05-04 14:35:15',	14,	'2025-05-04 14:35:15',	NULL),
(11,	24,	'2025-05-04',	1,	2,	6,	9,	NULL,	'2025-05-04 14:40:20',	14,	'2025-05-04 14:40:20',	NULL),
(12,	24,	'2025-05-05',	1,	1,	6,	9,	NULL,	'2025-05-04 14:46:47',	14,	'2025-05-04 14:46:47',	NULL),
(13,	24,	'2025-05-06',	6,	1,	6,	110,	NULL,	'2025-05-05 02:01:38',	14,	'2025-05-05 02:01:38',	NULL),
(14,	2,	'2025-05-05',	5,	8,	5,	40,	33,	'2025-05-05 06:45:40',	20,	'2025-05-05 06:45:40',	NULL),
(15,	2,	'2025-05-05',	5,	1,	5,	9,	9,	'2025-05-05 06:48:06',	20,	'2025-05-05 06:48:06',	NULL),
(16,	2,	'2025-05-05',	4,	9,	5,	4,	172,	'2025-05-05 06:48:53',	20,	'2025-05-05 06:48:53',	NULL),
(17,	2,	'2025-05-05',	4,	1,	5,	1,	177,	'2025-05-05 06:50:12',	20,	'2025-05-05 06:50:12',	NULL),
(18,	2,	'2025-05-05',	1,	1,	2,	45,	52,	'2025-05-05 10:07:44',	20,	'2025-05-05 10:07:44',	NULL);

DROP TABLE IF EXISTS `mu_quran_hafal_data`;
CREATE TABLE `mu_quran_hafal_data` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_anggota` int NOT NULL,
  `surat_mulai` int DEFAULT NULL,
  `ayat_mulai` int DEFAULT NULL,
  `surat_selesai` int DEFAULT NULL,
  `ayat_selesai` int DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_anggota` (`id_anggota`),
  KEY `surat_selesai` (`surat_selesai`),
  KEY `surat_mulai` (`surat_mulai`),
  CONSTRAINT `mu_quran_hafal_data_ibfk_1` FOREIGN KEY (`id_anggota`) REFERENCES `mu_anggota` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `mu_quran_hafal_data` (`id`, `id_anggota`, `surat_mulai`, `ayat_mulai`, `surat_selesai`, `ayat_selesai`, `created_at`, `created_by`, `updated_at`, `updated_by`) VALUES
(10,	24,	1,	1,	6,	110,	NULL,	NULL,	'2025-05-05 06:44:29',	NULL),
(22,	24,	10,	1,	10,	110,	NULL,	NULL,	'2025-05-05 06:44:29',	NULL),
(23,	2,	4,	1,	5,	9,	'2025-05-05 06:48:06',	NULL,	'2025-05-05 06:52:01',	NULL),
(26,	2,	1,	1,	2,	45,	'2025-05-05 10:07:44',	NULL,	'2025-05-05 10:07:44',	NULL);

DROP TABLE IF EXISTS `mu_quran_kaji`;
CREATE TABLE `mu_quran_kaji` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_anggota` int NOT NULL,
  `tanggal` datetime NOT NULL,
  `hal_mulai` int NOT NULL,
  `hal_selesai` int NOT NULL,
  `juz_mulai` int DEFAULT NULL,
  `juz_selesai` int DEFAULT NULL,
  `surat_mulai` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `surat_selesai` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `ayat_mulai` int DEFAULT NULL,
  `ayat_selesai` int DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_anggota` (`id_anggota`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


DROP TABLE IF EXISTS `mu_quran_tarjamah`;
CREATE TABLE `mu_quran_tarjamah` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_anggota` int NOT NULL,
  `tanggal` date NOT NULL,
  `surat_mulai` int DEFAULT NULL,
  `ayat_mulai` int DEFAULT NULL,
  `surat_selesai` int DEFAULT NULL,
  `ayat_selesai` int DEFAULT NULL,
  `total_ayat` int DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_anggota` (`id_anggota`),
  KEY `surat_selesai` (`surat_selesai`),
  KEY `surat_mulai` (`surat_mulai`),
  CONSTRAINT `mu_quran_tarjamah_ibfk_1` FOREIGN KEY (`id_anggota`) REFERENCES `mu_anggota` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `mu_quran_tarjamah_ibfk_2` FOREIGN KEY (`surat_mulai`) REFERENCES `mu__surat_quran` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `mu_quran_tarjamah_ibfk_3` FOREIGN KEY (`surat_selesai`) REFERENCES `mu__surat_quran` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `mu_quran_tarjamah` (`id`, `id_anggota`, `tanggal`, `surat_mulai`, `ayat_mulai`, `surat_selesai`, `ayat_selesai`, `total_ayat`, `created_at`, `created_by`, `updated_at`, `updated_by`) VALUES
(4,	24,	'2025-05-01',	1,	1,	1,	1,	NULL,	'2025-05-04 14:14:10',	14,	'2025-05-04 14:14:10',	NULL),
(5,	24,	'2025-05-01',	1,	1,	1,	1,	NULL,	'2025-05-04 14:14:34',	14,	'2025-05-04 14:14:34',	NULL),
(6,	24,	'2025-05-02',	1,	1,	1,	7,	NULL,	'2025-05-04 14:23:14',	14,	'2025-05-04 14:24:32',	NULL),
(7,	24,	'2025-05-02',	1,	1,	2,	9,	NULL,	'2025-05-04 14:32:54',	14,	'2025-05-04 14:32:54',	NULL),
(8,	24,	'2025-05-03',	2,	7,	5,	9,	NULL,	'2025-05-04 14:33:42',	14,	'2025-05-04 14:33:42',	NULL),
(9,	24,	'2025-05-03',	2,	7,	5,	9,	NULL,	'2025-05-04 14:33:59',	14,	'2025-05-04 14:33:59',	NULL),
(10,	24,	'2025-05-04',	5,	8,	6,	8,	NULL,	'2025-05-04 14:35:15',	14,	'2025-05-04 14:35:15',	NULL),
(11,	24,	'2025-05-04',	1,	2,	6,	9,	NULL,	'2025-05-04 14:40:20',	14,	'2025-05-04 14:40:20',	NULL),
(12,	24,	'2025-05-05',	1,	1,	6,	9,	NULL,	'2025-05-04 14:46:47',	14,	'2025-05-04 14:46:47',	NULL),
(13,	24,	'2025-05-06',	6,	1,	6,	110,	NULL,	'2025-05-05 02:01:38',	14,	'2025-05-05 02:01:38',	NULL),
(14,	2,	'2025-05-05',	5,	8,	5,	40,	NULL,	'2025-05-05 06:45:40',	20,	'2025-05-05 06:45:40',	NULL),
(15,	2,	'2025-05-05',	5,	1,	5,	9,	NULL,	'2025-05-05 06:48:06',	20,	'2025-05-05 06:48:06',	NULL),
(16,	2,	'2025-05-05',	4,	9,	5,	4,	NULL,	'2025-05-05 06:48:53',	20,	'2025-05-05 06:48:53',	NULL),
(17,	2,	'2025-05-05',	4,	1,	5,	1,	NULL,	'2025-05-05 06:50:12',	20,	'2025-05-05 06:50:12',	NULL),
(18,	2,	'2025-05-05',	1,	1,	2,	45,	NULL,	'2025-05-05 10:07:44',	20,	'2025-05-05 10:07:44',	NULL),
(19,	24,	'2025-05-05',	1,	1,	1,	7,	7,	'2025-05-05 13:01:20',	14,	'2025-05-05 13:01:20',	NULL);

DROP TABLE IF EXISTS `mu_sholat_sunnah`;
CREATE TABLE `mu_sholat_sunnah` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_anggota` int NOT NULL,
  `tanggal` date NOT NULL,
  `id_sholat` int DEFAULT NULL,
  `rakaat` int NOT NULL,
  `created_at` datetime NOT NULL ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL,
  `created_by` int NOT NULL,
  `updated_by` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_anggota` (`id_anggota`),
  KEY `id_sholat` (`id_sholat`),
  CONSTRAINT `mu_sholat_sunnah_ibfk_4` FOREIGN KEY (`id_anggota`) REFERENCES `mu_anggota` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `mu_sholat_sunnah_ibfk_6` FOREIGN KEY (`id_sholat`) REFERENCES `mu__sholat_sunnah` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `mu_sholat_sunnah` (`id`, `id_anggota`, `tanggal`, `id_sholat`, `rakaat`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(2,	24,	'2025-05-01',	1,	2,	'2025-05-08 14:34:43',	'2025-05-08 14:34:43',	0,	0),
(6,	24,	'2025-05-01',	2,	7,	'2025-05-08 14:35:42',	'2025-05-08 14:35:42',	0,	0),
(7,	24,	'2025-05-01',	3,	2,	'2025-05-10 16:32:11',	'2025-05-10 16:32:11',	0,	0),
(10,	24,	'2025-05-01',	4,	4,	'2025-05-10 16:32:33',	'2025-05-10 16:32:33',	0,	0),
(12,	24,	'2025-05-01',	10,	2,	'2025-05-10 16:58:40',	'2025-05-10 16:58:40',	0,	0),
(13,	24,	'2025-05-01',	11,	2,	'2025-05-10 17:03:44',	'2025-05-10 17:03:44',	0,	0),
(15,	24,	'2025-05-01',	5,	2,	'2025-05-10 17:08:17',	'2025-05-10 17:08:17',	0,	0),
(17,	24,	'2025-05-01',	17,	2,	'2025-05-10 18:46:34',	'2025-05-10 18:46:34',	0,	0),
(22,	24,	'2025-05-01',	24,	2,	'2025-05-10 19:14:26',	'2025-05-10 19:14:26',	0,	0),
(23,	24,	'2025-05-02',	1,	2,	'2025-05-10 19:46:10',	'2025-05-10 19:46:10',	0,	0),
(24,	24,	'2025-05-02',	2,	1,	'2025-05-10 19:46:11',	'2025-05-10 19:46:11',	0,	0),
(25,	24,	'2025-05-02',	3,	2,	'2025-05-10 19:46:12',	'2025-05-10 19:46:12',	0,	0),
(26,	24,	'2025-05-02',	4,	2,	'2025-05-10 19:46:12',	'2025-05-10 19:46:12',	0,	0),
(27,	24,	'2025-05-02',	5,	2,	'2025-05-10 19:46:14',	'2025-05-10 19:46:14',	0,	0),
(28,	24,	'2025-05-02',	6,	2,	'2025-05-10 19:46:14',	'2025-05-10 19:46:14',	0,	0),
(29,	24,	'2025-05-11',	7,	2,	'2025-05-10 20:05:17',	'2025-05-10 20:05:17',	0,	0),
(30,	24,	'2025-05-11',	6,	2,	'2025-05-10 20:05:18',	'2025-05-10 20:05:18',	0,	0),
(31,	24,	'2025-05-11',	5,	2,	'2025-05-10 20:05:19',	'2025-05-10 20:05:19',	0,	0);

DROP TABLE IF EXISTS `mu_sholat_wajib`;
CREATE TABLE `mu_sholat_wajib` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_anggota` int NOT NULL,
  `tanggal` date NOT NULL,
  `shubuh` int DEFAULT NULL,
  `dhuhur` int DEFAULT NULL,
  `asar` int DEFAULT NULL,
  `maghrib` int DEFAULT NULL,
  `isya` int DEFAULT NULL,
  `total_score` int NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `created_by` int NOT NULL,
  `updated_by` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_anggota` (`id_anggota`),
  CONSTRAINT `mu_sholat_wajib_ibfk_1` FOREIGN KEY (`id_anggota`) REFERENCES `mu_anggota` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `mu_sholat_wajib` (`id`, `id_anggota`, `tanggal`, `shubuh`, `dhuhur`, `asar`, `maghrib`, `isya`, `total_score`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(4,	24,	'2025-05-06',	75,	100,	100,	100,	100,	475,	'2025-05-06 03:51:57',	'2025-05-07 07:06:19',	14,	0),
(5,	24,	'2025-05-07',	75,	100,	100,	100,	100,	475,	'2025-05-07 06:13:11',	'2025-05-07 07:06:26',	14,	0),
(7,	24,	'2025-05-08',	100,	100,	75,	NULL,	NULL,	275,	'2025-05-07 06:16:25',	'2025-05-08 03:05:04',	14,	0),
(8,	24,	'2025-05-09',	100,	50,	NULL,	NULL,	NULL,	150,	'2025-05-07 06:45:54',	'2025-05-07 07:07:08',	14,	0),
(10,	24,	'2025-05-01',	100,	25,	100,	0,	0,	225,	'2025-05-07 06:47:10',	'2025-05-08 02:13:45',	14,	0),
(13,	24,	'2025-05-10',	100,	100,	100,	NULL,	NULL,	300,	'2025-05-07 07:15:00',	'2025-05-07 12:58:42',	14,	0),
(14,	24,	'2025-05-11',	100,	100,	75,	50,	25,	350,	'2025-05-10 20:39:29',	'2025-05-10 21:13:23',	14,	0),
(15,	24,	'2025-05-11',	75,	NULL,	NULL,	NULL,	NULL,	75,	'2025-05-10 20:39:32',	'2025-05-10 20:39:32',	14,	0),
(16,	24,	'2025-05-11',	75,	NULL,	NULL,	NULL,	NULL,	75,	'2025-05-10 20:39:35',	'2025-05-10 20:39:35',	14,	0);

-- 2025-05-15 08:01:37
