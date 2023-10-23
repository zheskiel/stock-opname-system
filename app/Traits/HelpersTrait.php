<?php
namespace App\Traits;

use Illuminate\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

trait HelpersTrait
{
    public $NOT_OWNED_BOTH             = 0;
    public $OWNED_BY_LEADER_KITCHEN    = 1;
    public $OWNED_BY_OUTLET_SUPERVISOR = 2;
    public $OWNED_BY_BOTH              = 3;

    public $SALT           = 'stock-opname:!@#$%^:SALTHASH';
    public $SECRET_KEY     = '%39d15#13P0£df458asdc%/dfr_A!8792*dskjfzaesdfpopdfo45s4dqd8d4fsd+dfd4s"Z1';
    public $SECRET_IV      = ';!@#adsf1213fwerw$%A^';
    public $ENCRYPT_METHOD = 'AES-256-CBC';
    public $HASH_TYPE      = 'sha256';

    public $limit = 50;

    public function getUserLists()
    {
        return ["admin", "manager", "staff"];
    }

    public function hashCommon()
    {
        $key = hash($this->HASH_TYPE, $this->SECRET_KEY); // hash

        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash($this->HASH_TYPE, $this->SECRET_IV), 0, 16);

        return ['key' => $key, 'iv'  => $iv];
    }

    public function encrypt($string)
    {
        $output = false;
        $hash = $this->hashCommon();

        $output = openssl_encrypt($string, $this->ENCRYPT_METHOD, $hash['key'], 0, $hash['iv']);

        return base64_encode($output);
    }

    public function decrypt($string)
    {
        $hash = $this->hashCommon();

        return openssl_decrypt(base64_decode($string), $this->ENCRYPT_METHOD, $hash['key'], 0, $hash['iv']);
    }

    private function isMobile()
    {
        $isMobile = false;

        if (preg_match('/pad|phon|android|opera mini|blackberry|nokia|motorola|sonyericsson|samsung|lg-|sie-/i', $_SERVER['HTTP_USER_AGENT']) === 1) {
            $isMobile = true;
        }

        return $isMobile;
    }

    static function processTitleSlug($string) : string
    {
        return strtolower(preg_replace('~[^\p{L}\p{N}\n]+~u', '-', $string));
    }

    static function sortItemsByParams($items, $param = 'units', $target = 'value')
    {
        $items->each(function($query) use ($param, $target) {
            $items = json_decode($query->{$param}, true);
            $units = SELF::sortItems($items, $target);

            $query->{$param} = $units;

            return $query;
        });

        return $items;
    }

    static function sortItems($items, $target = 'value')
    {
        uasort($items, function ($item1, $item2) use ($target) {
            return $item2[$target] <=> $item1[$target];
        });

        return $items;
    }

    static function usortItems($items, $target = 'value')
    {
        usort($items, function ($item1, $item2) use ($target) {
            return $item2[$target] <=> $item1[$target];
        });

        return $items;
    }

    static function usortItemsAsc($items, $target = 'value')
    {
        usort($items, function ($item1, $item2) use ($target) {
            return $item1[$target] <=> $item2[$target];
        });

        return $items;
    }

    static function getUserIpAddress($ipaddress = '') : string
    {
        switch ($ipaddress)
        {
            case getenv('HTTP_CLIENT_IP'):
                $ipaddress = getenv('HTTP_CLIENT_IP');
                break;

            case getenv('HTTP_X_FORWARDED_FOR'):
                $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
                break;

            case getenv('HTTP_X_FORWARDED'):
                $ipaddress = getenv('HTTP_X_FORWARDED');
                break;

            case getenv('HTTP_FORWARDED_FOR'):
                $ipaddress = getenv('HTTP_FORWARDED_FOR');
                break;

            case getenv('HTTP_FORWARDED'):
                $ipaddress = getenv('HTTP_FORWARDED');
                break;

            case getenv('REMOTE_ADDR'):
                $ipaddress = getenv('REMOTE_ADDR');
                break;

            default:
                $ipaddress = 'UNKNOWN';
                break;
        }

        return $ipaddress;
    }

    public function progressBar($done, $total)
    {
        /*
            Black 0;30
            Blue 0;34
            Green 0;32
            Cyan 0;36
            Red 0;31
            Purple 0;35
            Brown 0;33
            Light Gray 0;37
            Dark Gray 1;30
            Light Blue 1;34
            Light Green 1;32
            Light Cyan 1;36
            Light Red 1;31
            Light Purple 1;35
            Yellow 1;33
            White 1;37
        */
        $perc = floor(($done / $total) * 100);
        $left = 100 - $perc;
        $write = sprintf("\033[0G\033[2K[%'={$perc}s>%-{$left}s] - $perc%% - $done/$total", "", "");

        fwrite(STDERR, $write);
    }

    static function thousandsCurrencyFormat($num)
    {
        if ($num > 1000) {
            $x = round($num);

            $x_number_format = number_format($x);
            $x_array = explode(',', $x_number_format);
            $x_parts = ['k', 'm', 'b', 't'];
            $x_count_parts = count($x_array) - 1;
            $x_display = $x;
            $x_display = $x_array[0] . ((int) $x_array[1][0] !== 0 ? '.' . $x_array[1][0] : '');
            $x_display .= $x_parts[$x_count_parts - 1];

            return $x_display;
        }

        return $num;
    }

    static function generatePagination($itemData, $itemCount, $perPage, $page, $path = "")
    {
        if ($path == "") {
            $path = '/' . request()->path();
        }

        return self::customPaginate(
            $itemData,
            $itemCount,
            $perPage,
            $page,
            [ 'path' => $path ]
        );
    }

    static function customPaginate($items, $itemCount, $perPage = 15, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);

        return new LengthAwarePaginator($items, $itemCount, $perPage, $page, $options);
    }
}