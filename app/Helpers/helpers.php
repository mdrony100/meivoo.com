<?php

use App\Mail\DynamicTemplateMail;
use App\Models\Central\CentralSetting;
use App\Models\Currency;
use App\Models\Language;
use App\Models\MailTemplate;
use App\Models\Setting;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

if (! function_exists('getSetting')) {
    /**
     * @return mixed
     */
    function getSetting(): Setting
    {
        return Setting::orderBy('id', 'DESC')->first();
    }
}

function checkFrontLanguageSession()
{
    if (Session::has('languageName')) {
        return Session::get('languageName');
    }

    return 'En';
}

if (!function_exists('getLogo')) {
    function getLogo($path = null): string
    {
        if($path){
            $ppath = public_path($path);
            if(file_exists($ppath)){
              return asset($path);
            } else {
                return asset('default.png');
           }
        }else{
            return asset('default.png');
        }
    }
}

if (!function_exists('getIcon')) {
    function getIcon($path = null): string
    {
        if($path){
            $ppath = public_path($path);
            if(file_exists($ppath)){
              return asset($path);
            } else {
                return asset('assets/images/default-icon.png');
           }
        }else{
            return asset('assets/images/default-icon.png');
        }
    }
}

if (! function_exists('isMobile')) {
    function isMobile(): bool
    {
        if (stristr($_SERVER['HTTP_USER_AGENT'], 'Mobile')) {
            return true;
        } else {
            return false;
        }
        // return false;
    }
}

if (! function_exists('get_error_response')) {
    function get_error_response($code, $reason, $errors = [], $error_as_string = '', $description = ''): array
    {
        if ($error_as_string == '') {
            $error_as_string = $reason;
        }
        if ($description == '') {
            $description = $reason;
        }

        return [
            'code' => $code,
            'errors' => $errors,
            'error_as_string' => $error_as_string,
            'reason' => $reason,
            'description' => $description,
            'error_code' => $code,
            'link' => '',
        ];
    }
}

if (! function_exists('getPhoto')) {
    function getPhoto($path): string
    {
        if ($path) {
            // Check if it's an external URL
            if (filter_var($path, FILTER_VALIDATE_URL)) {
                return $path;
            }

            // Check if it's a local file
            $ppath = public_path($path);
            if (file_exists($ppath)) {
                return asset($path);
            } else {
                return asset('default.png');
            }
        } else {
            return asset('default.png');
        }
    }
}

if (! function_exists('getIcon')) {
    function getIcon($path = null): string
    {
        if ($path) {
            $ppath = public_path($path);
            if (file_exists($ppath)) {
                return asset($path);
            } else {
                return asset('default.png');
            }
        } else {
            return asset('default.png');
        }
    }
}
if (! function_exists('getSeoImage')) {
    function getSeoImage($path = null): string
    {
        if ($path) {
            $ppath = public_path($path);
            if (file_exists($ppath)) {
                return asset($path);
            } else {
                return asset('default.png');
            }
        } else {
            return asset('default.png');
        }
    }
}

if (! function_exists('getDesigComp')) {
    function getDesigComp($desig, $comp): string
    {
        if ($desig != '' & $comp != '') {
            return $desig.' At '.$comp;
        } else {
            return $desig.' '.$comp;
        }

    }
}

if (! function_exists('makeUrl')) {
    function makeUrl($url)
    {
        if (! preg_match('~^(?:f|ht)tps?://~i', $url)) {
            $url = 'https://'.$url;
        }

        return $url;
    }
}


if (! function_exists('getDefaultCurrencySymbol')) {
    function getDefaultCurrencySymbol(): string
    {
        return '$';
    }
}

if (! function_exists('getDefaultCurrencyCode')) {
    function getDefaultCurrencyCode(): string
    {
        return 'USD';
    }
}

if (! function_exists('CurrencyFormat')) {
    function CurrencyFormat($number, $decimal = 1) // cents: 0=never, 1=if needed, 2=always
    {if (is_numeric($number)) { // a number
        if (! $number) { // zero
            $money = ($decimal == 2 ? '0.00' : '0'); // output zero
        } else { // value
            if (floor($number) == $number) { // whole number
                $money = number_format($number, ($decimal == 2 ? 2 : 0)); // format
            } else { // cents
                $money = number_format(round($number, 2), ($decimal == 0 ? 0 : 2)); // format
            } // integer or decimal
        } // value

        return $money;
    } else {
        return $number;
    } // numeric
    } //
}

function formatFileName($file): string
{
    $base_name = preg_replace('/\..+$/', '', $file->getClientOriginalName());
    $base_name = explode(' ', $base_name);
    $base_name = implode('-', $base_name);
    $base_name = Str::lower($base_name);

    return $base_name.'-'.uniqid().'.'.$file->getClientOriginalExtension();
}



function uploadGeneralImage(?object $file, string $path, $oldImage = null): string
{
    $directory = "uploads/$path/";
    $pathCreate = public_path($directory);

    if (! File::isDirectory($pathCreate)) {
        File::makeDirectory($pathCreate, 0777, true, true);
    }

    if ($oldImage && ! filter_var($oldImage, FILTER_VALIDATE_URL) && str_starts_with($oldImage, 'uploads/') && File::exists(public_path($oldImage))) {
        File::delete(public_path($oldImage));
    }

    $ext = $file->getClientOriginalExtension();
    $finalName = time() . '_' . uniqid() . '.' . $ext;
    
    $file->move($pathCreate, $finalName);

    return $directory . $finalName;
}

// function uploadGeneralImage(?object $file, string $path, $oldImage = null): string
// {
//     $pathCreate = public_path("uploads/$path/");
//     if (! File::isDirectory($pathCreate)) {
//         File::makeDirectory($pathCreate, 0777, true, true);
//     }

//     // Delete old image
//     if ($oldImage && ! filter_var($oldImage, FILTER_VALIDATE_URL) && str_starts_with($oldImage, 'uploads/') && File::exists(public_path($oldImage))) {
//         File::delete(public_path($oldImage));
//     }

//     // Upload temp file
//     $ext = $file->getClientOriginalExtension();
//     $tempName = 'temp_'.time().'_'.uniqid().'.'.$ext;
//     $tempPath = public_path("uploads/$path/$tempName");
//     $file->move(public_path("uploads/$path/"), $tempName);

//     // Convert original temp file to WebP
//     $finalName = time().'_'.uniqid().'.webp';
//     $finalPath = public_path("uploads/$path/$finalName");

//     $converted = convertToWebp($tempPath, $finalPath, 80);

//     if ($converted === false) {
//         return "uploads/$path/$tempName";
//     } else {
//         File::delete($tempPath);

//         return "uploads/$path/$finalName";
//     }
// }

function compressWithReSmush($filePath)
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, 'https://api.resmush.it/ws.php?qlty=92');
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, [
        'files' => new \CURLFile($filePath),
    ]);

    curl_setopt($curl, CURLOPT_HTTPHEADER, [
        'User-Agent: Laravel-App',
    ]);

    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($curl);

    curl_close($curl);

    $json = json_decode($response, true);

    if (isset($json['dest'])) {
        return $json['dest']; // Compressed image URL
    }

    return false;
}

function convertToWebp($sourcePath, $destinationPath, $quality = 80)
{
    $info = getimagesize($sourcePath);
    $mime = $info['mime'];

    switch ($mime) {
        case 'image/jpeg':
            $image = imagecreatefromjpeg($sourcePath);
            break;
        case 'image/png':
            $image = imagecreatefrompng($sourcePath);
            imagepalettetotruecolor($image);
            break;
        case 'image/gif':
            $image = imagecreatefromgif($sourcePath);
            break;
        case 'image/webp':
            return copy($sourcePath, $destinationPath);
        default:
            return false;
    }

    imagewebp($image, $destinationPath, $quality);
    imagedestroy($image);

    return true;
}

if (! function_exists('checkCustomPage')) {
    function checkCustomPage($slug): bool
    {
        return DB::table('custom_pages')->where('url_slug', $slug)->where('is_active', 1)->exists();
    }
}



if (! function_exists('splitSiteName')) {
    function splitSiteName($siteName = null)
    {
        $siteName = $siteName ?? (getSetting()->site_name ?? '');

        $parts = explode(' ', trim($siteName), 2);

        $firstPart = $parts[0] ?? '';
        $secondPart = $parts[1] ?? '';
        $firstLetter = strtoupper(substr($firstPart, 0, 1));
        $secondLetter = strtoupper(substr($secondPart, 0, 1));

        return [
            'first_part' => $firstPart,
            'second_part' => $secondPart,
            'first_letter' => $firstLetter,
            'two_letter' => $firstLetter.$secondLetter,
        ];
    }
}

if (! function_exists('permission_action')) {
    function permission_action($name)
    {
        return ucfirst(last(explode('.', $name)));
    }
}

if (! function_exists('permission_description')) {
    function permission_description($name)
    {
        $parts = explode('.', $name);

        $resource = str_replace('-', ' ', $parts[1] ?? '');
        $action = str_replace('-', ' ', $parts[2] ?? '');

        return 'Can '.$action.' '.$resource;
    }
}

if (! function_exists('fileUpload')) {
    function fileUpload($file, string $directory = 'uploads'): array
    {
        $uploadPath = public_path($directory);

        if (! File::exists($uploadPath)) {
            File::makeDirectory($uploadPath, 0755, true);
        }

        $filename = time().'_'.$file->getClientOriginalName();
        $file->move($uploadPath, $filename);

        return [
            'filename' => $filename,
            'full_path' => $uploadPath.'/'.$filename,
            'stored_path' => $directory.'/'.$filename,
            'size_kb' => $file->getSize() / 1024,
            'extension' => $file->getClientOriginalExtension(),
        ];
    }
}
