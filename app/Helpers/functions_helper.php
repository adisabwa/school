<?php
use NumberToWords\NumberToWords;
use CodeIgniter\HTTP\ResponseInterface;

function array_average($array) {
    $count = count(array_filter($array, fn($n) => $n > 0));
    $count = $count == 0 ? 1 : $count;
    return array_sum($array) / $count;
}

function cari_elemen_terbanyak($arr) {
    // Counts the occurrences of each value in the array
    $freqArray = array_count_values($arr);

    // Finds the maximum frequency value
    $maxFreq = max($freqArray);

    // Finds the key (original value) associated with the maximum frequency
    $mostFrequent = array_search($maxFreq, $freqArray);

    return $mostFrequent;
}

function setRandomColor() {
    // Generate random values for each RGB component
    $r = rand(0, 255); // Red
    $g = rand(0, 255); // Green
    $b = rand(0, 255); // Blue
    
    // Return RGB format
    return "rgb($r, $g, $b)";
}

function toNumber($number){
    return (int) preg_replace("/[^0-9]/", '', $number);
}


function toPhoneNumber($number){
    preg_match('/^0+/', $number, $matches);
    $number = toNumber($number);
    // var_dump($matches, $number);
    $hp = toNumber($number);
    $hp = ($matches[0] ?? '').$hp;
    if (substr($hp,0,1) == '08')
        $hp = str_replace_first("08","628",$hp);
    return $hp;
}

function str_replace_first($search, $replace, $subject) {
    return implode($replace, explode($search, $subject, 2));
}

function pluckFromObjectsNested(array $objects, string $path): array {
    $keys = explode('.', $path);

    return array_map(function($obj) use ($keys) {
        $value = $obj;
        foreach ($keys as $key) {
            if (is_object($value) && isset($value->$key)) {
                $value = $value->$key;
            } else {
                return null;
            }
        }
        return $value;
    }, $objects);
}

function getDateRange($start, $end)
{
    $start = new DateTime($start);
    $end = new DateTime($end);

    // Include the end date in the range
    $end->modify('+1 day');

    $interval = new DateInterval('P1D'); // 1 day interval
    $period = new DatePeriod($start, $interval, $end);

    $lists = [];
    foreach ($period as $date) {
        $lists[] = $date->format('Y-m-d');
    }

    return $lists;
}

function get_date_interval($start, $end)
{
    $start = new DateTime($start);
    $end = new DateTime($end);

    $interval = $start->diff($end);

    return $interval->d;
}


function getHari($tanggal){
    $haris = ['ahad','senin','selasa','rabu','kamis','jumat','sabtu'];
    $day = date('w', strtotime($tanggal));
    return $haris[$day];
}

function nextDateByDay(string $targetDay, string $timezone = 'Asia/Jakarta'): string
{
    $map = [
        'ahad' => 0,
        'senin' => 1,
        'selasa' => 2,
        'rabu' => 3,
        'kamis' => 4,
        'jumat' => 5,
        'sabtu' => 6,
    ];

    $targetDay = strtolower($targetDay);

    if (!isset($map[$targetDay])) {
        throw new InvalidArgumentException('Nama hari tidak valid');
    }

    $now = new DateTime('now', new DateTimeZone($timezone));
    $todayIndex = (int) $now->format('w'); // 0=Ahad
    $targetIndex = $map[$targetDay];

    $diff = ($targetIndex - $todayIndex + 7) % 7;

    // kalau hari sama → ambil minggu depan
    if ($diff === 0) {
        $diff = 7;
    }

    $now->modify("+$diff days");

    return $now->format('Y-m-d');
}

if ( ! function_exists('dateIndo')) {
	function dateIndo($date, $showDay = false)
	{

		$hari  = array ( 1 => 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu');
		$bulan = array ( 1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus','September', 'Oktober', 'November', 'Desember');
		
		$split 	  = explode('-', $date);
		$tgl_indo = (int) $split[2] . ' ' . $bulan[ (int)$split[1] ] . ' ' . $split[0];
		
		if ($showDay) {
			$num = date('N', strtotime($date));
			return $hari[$num] . ', ' . $tgl_indo;
		}

		return $tgl_indo;
	}
}

function formatTanggalIndonesia($dateString, $withTime = false, $pattern = 'd MMMM yyyy')
{
    $locale = 'id_ID';
    $formatter = new \IntlDateFormatter(
        $locale,
        \IntlDateFormatter::LONG,
        $withTime ? \IntlDateFormatter::SHORT : \IntlDateFormatter::NONE,
        'Asia/Jakarta',
        \IntlDateFormatter::GREGORIAN
    );
    
    $timestamp = strtotime($dateString);
    $formatter->setPattern($pattern); // Only month and year
    return $formatter->format($timestamp);
}


function penulisan_jarak_tanggal($tanggal1, $tanggal2)
{
    $text = '';
    $days = explode(' ', $tanggal1);
    $days2 = explode(' ', $tanggal2);
    if ($tanggal1 == $tanggal2) {
        $text = $tanggal1;
    } else if ($days[2] == $days2[2]) {
        $text = $days[2];
        if ($days[1] == $days2[1]) {
            $text = $days[0].' - '.$days2[0].' '.$days[1]. ' ' .$text;
        } else {
            $text = $days[0] . ' ' . $days[1] . ' - ' . $days2[0] . ' ' . $days2[1] . ' ' . $text;
        }
    } else {
        $text = $tanggal1 . ' - ' . $tanggal2;
    }

    return $text;
    
}

if (! function_exists('number_to_words')) {
    function number_to_words($number, $locale = 'id') {
        $numberToWords = new NumberToWords();
        $numberTransformer = $numberToWords->getNumberTransformer($locale);
        return ucwords($numberTransformer->toWords($number));
    }
}

function class_to_arabic($number) {
    $classes = [
        1 => 'الأول',
        2 => 'الثاني',
        3 => 'الثالث',
        4 => 'الرابع',
        5 => 'الخامس',
        6 => 'السادس',
        7 => 'السابع',
        8 => 'الثامن',
        9 => 'التاسع',
        10 => 'العاشر',
        11 => 'الحادي عشر',
        12 => 'الثاني عشر',
    ];

    return $classes[$number] ?? '';
}

if (!function_exists('to_arabic_number')) {
    function to_arabic_number($number)
    {
        $western_arabic = ['0','1','2','3','4','5','6','7','8','9'];
        $eastern_arabic = ['٠','١','٢','٣','٤','٥','٦','٧','٨','٩'];

        return str_replace($western_arabic, $eastern_arabic, strval($number));
    }
}

if ( ! function_exists('dateIndoArabic')) {
	function dateIndoArabic($date, $showDay = false)
	{

		$hari  = array ( 1 => 'الاِثْنَيْنِ', 'الثُّلاثَاْءِ', 'الثُّلاثَاْءِ', 'الثُّلاثَاْءِ', 'الثُّلاثَاْءِ', 'السَّبْتِ', 'الأَحَدِ');
		$bulan = array ( 1 => 'يَنَاْيِرُ', 'فِبْرَاْيِرُ', 'مَاْرِسُ', 'أَبْرِيْلُ', 'مَاْيُوْ', 'يُوْنِيُوْ', 'يُوْلِيُوْ', 'أُغُسْطُسُ','سِبْتِمْبَرُ', 'أُكْتُوْبَرُ', 'نُوْفِمْبَرُ', 'دِيْسِمْبَرُ');
		
		$split 	  = explode('-', $date);
		$tgl_indo = to_arabic_number((int) $split[2]) . ' ' . $bulan[ (int)$split[1] ] . ' ' . to_arabic_number($split[0]);
		
		if ($showDay) {
			$num = date('N', strtotime($date));
			return $hari[$num] . ', ' . $tgl_indo;
		}

		return $tgl_indo;
	}
}

if (!function_exists('zip_and_download')) {
    function zip_and_download(string $zipName, array $files = [], array $folders = [], $deleteOriginal = FALSE): ResponseInterface
    {
        $compressedExtensions = ['zip', 'jpg', 'jpeg', 'png', 'gif', 'mp4', 'mp3', 'pdf', 'avi', 'mov','docx','xlsx','pptx'];

        $zip = new \ZipArchive();

        // Full path to save the zip temporarily
        $zipPath = WRITEPATH . 'temp/' . $zipName;

        var_dump($zipPath);
        // Ensure temp directory exists
        if (!is_dir(WRITEPATH . 'temp')) {
            mkdir(WRITEPATH . 'temp', 0777, true);
        }

        if (file_exists($zipPath)) {
            unlink($zipPath); // hapus dulu
        }
        $zip = new \ZipArchive();
        $result = $zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        if ($result) {
            // Log or display the error code
            echo "Failed to open zip archive: error code " . $zip->status;
        } else {
            // Add files...
            $zip->close();
        }

        if ($result !== true) {
            die("Zip error: " . $result);
        }

        // $zip->setCompressionIndex($index, \ZipArchive::CM_STORE);
        // ✅ Add individual files
        // var_dump($files, $folders);
        foreach ($files as $filePath) {
            if (file_exists($filePath)) {
                $index = $zip->addFile($filePath, basename($filePath));
                // Skip compression for already compressed file types
                $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                if (in_array($extension, $compressedExtensions) && $index !== false) {
                    $zip->setCompressionIndex($index, \ZipArchive::CM_STORE);
                }
            }
        }

        // // ✅ Add folders recursively
        // foreach ($folders as $folderPath) {
        //     if (is_dir($folderPath)) {
        //         $folderNameInZip = basename($folderPath);
        //         add_folder_to_zip($folderPath, $zip, $folderNameInZip);
        //     }
        // }

        $zip->close();
        
        // ✅ Optionally delete original files/folders
        if ($deleteOriginal) {
            foreach ($files as $filePath) {
                if (file_exists($filePath)) {
                    unlink($filePath); // delete file
                }
            }

            foreach ($folders as $folderPath) {
                if (is_dir($folderPath)) {
                    delete_folder_recursive($folderPath); // custom function below
                }
            }
        }
        // 🔽 Return CI4 download response
        return response()->download($zipPath, null)->setFileName($zipName);
    }
}

if (!function_exists('add_folder_to_zip')) {
    function add_folder_to_zip(string $folder, \ZipArchive $zip, string $zipFolderName)
    {
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($folder),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = $zipFolderName . '/' . substr($filePath, strlen($folder) + 1);
                $zip->addFile($filePath, $relativePath);
            }
        }
    }
}

if (!function_exists('delete_folder_recursive')) {
    function delete_folder_recursive(string $dir)
    {
        $items = array_diff(scandir($dir), ['.', '..']);
        foreach ($items as $item) {
            $path = $dir . DIRECTORY_SEPARATOR . $item;
            is_dir($path) ? delete_folder_recursive($path) : unlink($path);
        }
        rmdir($dir);
    }
}

function shortenName($fullName, $minChar = 20) {
    $parts = explode(" ", trim($fullName));
    $result = [];

    // daftar variasi "Muhammad"
    $muhammadVariants = ["muhammad", "muhamad", "mohammad", "mohamad", "mohd","moechammad"];

    $countChar = 0;
    $prev = "";
    foreach ($parts as $index => $word) {
        // cek nama pertama termasuk variasi Muhammad
        if ($index === 0 && in_array(strtolower($word), $muhammadVariants)) {
            $result[] = "M";
            $countChar += 2;
            continue;
        }

        $countChar += strlen($word) + 1;

        if (strlen($word) <= 2) {
            $prev = $word;
            continue;
        } else {
            $word  = empty($prev) ? $word : $prev." ".$word;
            $prev = '';
        }

        if ($countChar <= $minChar) {
            // 3 kata pertama (kecuali Muhammad) ditulis utuh
            $result[] = $word;
        } else {
            // sisanya inisial
            $countChar += 1;
            $result[] = strtoupper(substr($word, 0, 1)).".";
        }
    }

    return implode(" ", $result);
}

function get_predikat($score) {
    if ($score >= 91) {
        return 'Istimewa';
    } elseif ($score >= 86) {
        return 'Baik Sekali';
    } elseif ($score >= 78) {
        return 'Baik';
    } elseif ($score >= 40) {
        return 'Cukup';
    } else {
        return 'Kurang';
    }
}

function get_predikat_arab($score) {
    if ($score >= 91) {
        return 'مُمتاز';
    } elseif ($score >= 86) {
        return 'جَيِّد جِدًّا';
    } elseif ($score >= 78) {
        return 'جَيِّد';
    } elseif ($score >= 40) {
        return 'مَقْبُول';
    } else {
        return 'ناقص';
    }
}

function get_sikap_arab($score) {
    switch ($score) {
        case '4':
            return 'جَيِّد جِدًّا';
            break;
        case '3':
            return 'جَيِّد';
            break;
        case '2':
            return 'مَقْبُول';
            break;
        case '1':
            return 'ناقص';
            break;
        
        default:
            return '-';
            break;
    }
}

function get_sikap($score) {
    switch ($score) {
        case '4':
            return 'Sangat Baik';
            break;
        case '3':
            return 'Baik';
            break;
        case '2':
            return 'Cukup';
            break;
        case '1':
            return 'Kurang';
            break;
        
        default:
            return '-';
            break;
    }
}

function get_sikap_aktif($score) {
    switch ($score) {
        case '4':
            return 'Sangat Aktif';
            break;
        case '3':
            return 'Aktif';
            break;
        case '2':
            return 'Cukup Aktif';
            break;
        case '1':
            return 'Kurang Aktif';
            break;
        
        default:
            return '-';
            break;
    }
}

function get_sikap_short($score) {
    switch ($score) {
        case '4':
            return 'SB';
            break;
        case '3':
            return 'B';
            break;
        case '2':
            return 'C';
            break;
        case '1':
            return 'K';
            break;
        
        default:
            return '-';
            break;
    }
}