<?php
use NumberToWords\NumberToWords;
use CodeIgniter\HTTP\ResponseInterface;

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


function get_hari($tanggal){
    $haris = ['ahad','senin','selasa','rabu','kamis','jumat','sabtu'];
    $day = date('w', strtotime($tanggal));
    return $haris[$day];
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

if (! function_exists('number_to_words')) {
    function number_to_words($number, $locale = 'id') {
        $numberToWords = new NumberToWords();
        $numberTransformer = $numberToWords->getNumberTransformer($locale);
        return ucwords($numberTransformer->toWords($number));
    }
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
        $zip = new \ZipArchive();

        // Full path to save the zip temporarily
        $zipPath = WRITEPATH . 'temp/' . $zipName;

        // Ensure temp directory exists
        if (!is_dir(WRITEPATH . 'temp')) {
            mkdir(WRITEPATH . 'temp', 0777, true);
        }

        // Create or overwrite ZIP file
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException("Cannot create ZIP file.");
        }

        // ✅ Add individual files
        // var_dump($files, $folders);
        foreach ($files as $filePath) {
            if (file_exists($filePath)) {
                $zip->addFile($filePath, basename($filePath));
            }
        }

        // ✅ Add folders recursively
        foreach ($folders as $folderPath) {
            if (is_dir($folderPath)) {
                $folderNameInZip = basename($folderPath);
                add_folder_to_zip($folderPath, $zip, $folderNameInZip);
            }
        }

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

function get_predikat($score) {
    if ($score >= 85) {
        return 'Istimewa';
    } elseif ($score >= 70) {
        return 'Baik Sekali';
    } elseif ($score >= 60) {
        return 'Baik';
    } elseif ($score >= 50) {
        return 'Cukup';
    } else {
        return 'Gagal';
    }
}

function get_predikat_arab($score) {
    if ($score >= 85) {
        return 'مُمتاز';
    } elseif ($score >= 70) {
        return 'جَيِّد جِدًّا';
    } elseif ($score >= 60) {
        return 'جَيِّد';
    } elseif ($score >= 50) {
        return 'مَقْبُول';
    } else {
        return 'رَاسِب';
    }
}
