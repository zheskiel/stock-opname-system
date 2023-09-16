<?php
namespace App\Traits;

use App\Support\Collection as CollectionSupport;

use Illuminate\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

trait HelpersTrait
{
    public $NOT_OWNED_BOTH             = 0;
    public $OWNED_BY_LEADER_KITCHEN    = 1;
    public $OWNED_BY_OUTLET_SUPERVISOR = 2;
    public $OWNED_BY_BOTH              = 3;

    public $limit = 10;

    static function processTitleSlug($string) : string
    {
        return strtolower(preg_replace('~[^\p{L}\p{N}\n]+~u', '-', $string));
    }

    static function sortUnitsByValue($query, $param)
    {
        $units = json_decode($query->units, true);

        uasort($units, function ($item1, $item2) use ($param) {
            return $item2[$param] <=> $item1[$param];
        });

        return $units;
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

    static function generatePagination($itemData, $itemCount, $perPage, $page)
    {
        return self::customPaginate(
            $itemData,
            $itemCount,
            $perPage,
            $page,
            [ 'path' => '/' . request()->path() ]
        );
    }

    static function customPaginate($items, $itemCount, $perPage = 15, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);

        return new LengthAwarePaginator($items, $itemCount, $perPage, $page, $options);
    }
}