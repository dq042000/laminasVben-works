<?php
/**
 * 其他 tool 功能
 *
 * Created by VsCode.
 * User: Mike
 * Date: 2024/2/2
 * Time: 下午 9:31
 */

namespace Base\Model;

use DateInterval;
use DatePeriod;
use DateTime;
use Laminas\Form\Form;

class ToolModel
{
    /**
     * 數字 轉 國字
     *
     * @var string[]
     */
    public const NUM_TO_WORD = [
        0 => '零',
        1 => '一',
        2 => '二',
        3 => '三',
        4 => '四',
        5 => '五',
        6 => '六',
        7 => '七',
        8 => '八',
        9 => '九',
        10 => '十',
        11 => '十一',
        12 => '十二',
    ];

    /**
     * @param mixed $id
     *
     * @return boolean
     */
    public function check($id)
    {
        $id = strtoupper((string) $id);
        //建立字母分數陣列
        $headPoint = [
            'A' => 1, 'I' => 39, 'O' => 48, 'B' => 10, 'C' => 19, 'D' => 28,
            'E' => 37, 'F' => 46, 'G' => 55, 'H' => 64, 'J' => 73, 'K' => 82,
            'L' => 2, 'M' => 11, 'N' => 20, 'P' => 29, 'Q' => 38, 'R' => 47,
            'S' => 56, 'T' => 65, 'U' => 74, 'V' => 83, 'W' => 21, 'X' => 3,
            'Y' => 12, 'Z' => 30,
        ];

        //建立加權基數陣列
        $multiply = [8, 7, 6, 5, 4, 3, 2, 1];

        //檢查身份字格式是否正確
        if (preg_match("/^[a-zA-Z][1-2][0-9]+$/", $id) and strlen($id) == 10) {
            //切開字串
            $len = strlen($id);
            for ($i = 0; $i < $len; $i++) {
                $stringArray[$i] = substr($id, $i, 1);
            }

            //取得字母分數
            $total = $headPoint[array_shift($stringArray)];

            //取得比對碼
            $point = array_pop($stringArray);

            //取得數字分數
            $len = count($stringArray);
            for ($j = 0; $j < $len; $j++) {
                $total += $stringArray[$j] * $multiply[$j];
            }

            //計算餘數碼並比對
            $last = (($total % 10) == 0) ? 0 : (10 - ($total % 10));
            if ($last != $point) {
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

    /**
     * 驗證台灣手機號碼
     *
     * @return boolean
     */
    public function isPhone($str)
    {
        if (preg_match("/^09[0-9]{2}-[0-9]{3}-[0-9]{3}$/", $str)) {
            return true; // 09xx-xxx-xxx
        } else if (preg_match("/^09[0-9]{2}-[0-9]{6}$/", $str)) {
            return true; // 09xx-xxxxxx
        } else if (preg_match("/^09[0-9]{8}$/", $str)) {
            return true; // 09xxxxxxxx
        } else {
            return false;
        }
    }

    /**
     * 驗證信箱
     *
     * @return boolean
     */
    public function isEmail($str)
    {
        if (filter_var($str, FILTER_VALIDATE_EMAIL)) {
            return true; // valid
        } else {
            return false; // invalid
        }
    }

    /**
     * 產生指定數量的隨機身分證字號
     *
     * @param int $num
     * @return array
     */
    public function getRandIDs($num)
    {
        $ids = [];

        for ($n = 0; $n < $num; $n++) {
            //建立字母分數陣列
            $headPoint = [
                'A' => 1, 'I' => 39, 'O' => 48, 'B' => 10, 'C' => 19, 'D' => 28,
                'E' => 37, 'F' => 46, 'G' => 55, 'H' => 64, 'J' => 73, 'K' => 82,
                'L' => 2, 'M' => 11, 'N' => 20, 'P' => 29, 'Q' => 38, 'R' => 47,
                'S' => 56, 'T' => 65, 'U' => 74, 'V' => 83, 'W' => 21, 'X' => 3,
                'Y' => 12, 'Z' => 30,
            ];

            //建立加權基數陣列
            $multiply = [8, 7, 6, 5, 4, 3, 2, 1];

            //取得隨機數字，第一個數字為性別
            $number = mt_rand(1, 2);
            for ($i = 0; $i < 7; $i++) {
                $number .= mt_rand(0, 9);
            }

            //切開字串
            $len = strlen($number);
            for ($i = 0; $i < $len; $i++) {
                $stringArray[$i] = substr($number, $i, 1);
            }

            //取得隨機字母分數
            $index = chr(mt_rand(65, 90));
            $total = $headPoint[$index];

            //取得數字分數
            $len = count($stringArray);
            for ($j = 0; $j < $len; $j++) {
                $total += $stringArray[$j] * $multiply[$j];
            }

            //取得檢查比對碼
            if ($total % 10 == 0) {
                $ids[] = $index . $number . 0;
            } else {
                $ids[] = $index . $number . (10 - $total % 10);
            }
        }

        return $ids;
    }

    /**
     * 隨機產生日期
     *
     * @param mixed $s_year  // 啟始年份
     * @param mixed $e_year  // 結束年份
     * @param mixed $mod // dt: 產生日期+時間
     * @param boolean $limit
     *
     * @return string
     */
    public function getRandDateTime($s_year, $e_year, $mod = 'dt', $limit = true)
    {
        $rand_source1 = mktime(0, 0, 0, 1, 1, $s_year);
        $rand_source2 = $limit ? mktime(0, 0, 0, date("m"), date("d"), $e_year) : mktime(0, 0, 0, 12, 31, $e_year);
        $rand_time = rand($rand_source1, $rand_source2);
        return $mod == 'dt' ? date("Y-m-d H:i:s", $rand_time) : date("Y-m-d", $rand_time);
    }

    /**
     * 隨機產生手機號碼
     *
     * @param integer $s
     *
     * @return array
     */
    public function phonenubers($s = 1)
    {
        for ($i = 0; $i < $s; $i++) {
            $phone[] = '09' . mt_rand(1000, 9999) . mt_rand(1000, 9999);
        }
        return array_unique($phone);
    }

    /**
     * 隨機產生Email
     *
     * @param integer $len
     * @param string $format
     *
     * @return string
     */
    public function randStr($len = 6, $format = 'default')
    {
        switch ($format) {
            case 'ALL':
                $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-@#~';
                break;
            case 'CHAR':
                $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz-@#~';
                break;
            case 'NUMBER':
                $chars = '0123456789';
                break;
            default:
                $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
                break;
        }
        mt_srand((double) microtime() * 1000000 * getmypid());
        $password = "";
        while (strlen($password) < $len) {
            $password .= substr($chars, (mt_rand() % strlen($chars)), 1);
        }

        return $password;
    }

    /**
     * 產生不含特殊符號隨機亂數密碼
     *
     * @param integer $len
     *
     * @return string
     */
    public function generatorNoSpecialPassword($len = 0)
    {
        if ($len > 0) {
            $password_len = $len;
            $word = 'abcdefghijkmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ';
        } else {
            $password_len = 8;
            $word = 'abcdefghijkmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ02345689';
        }

        $password = '';

        $len = strlen($word);

        for ($i = 0; $i < $password_len; $i++) {
            $password .= $word[rand() % $len];
        }

        return $password;
    }

    /**
     * 產生含特殊符號隨機亂數密碼
     *
     * @param integer $len
     *
     * @return string
     */
    public function generatorSpecialPassword($len = 8)
    {
        $lowercase = 'abcdefghijkmnpqrstuvwxyz';
        $uppercase = 'ABCDEFGHIJKLMNPQRSTUVWXYZ';
        $numbers = '0123456789';
        $symbols = '$@$!%*#?&';

        // 確保每種類型的字元都被選中
        $password = $lowercase[rand(0, strlen($lowercase) - 1)];
        $password .= $uppercase[rand(0, strlen($uppercase) - 1)];
        $password .= $numbers[rand(0, strlen($numbers) - 1)];
        $password .= $symbols[rand(0, strlen($symbols) - 1)];

        // 隨機選擇剩餘的字元
        $all = $lowercase . $uppercase . $numbers . $symbols;
        for ($i = 4; $i < $len; $i++) {
            $password .= $all[rand(0, strlen($all) - 1)];
        }

        // 打亂密碼字元的順序
        $password = str_shuffle($password);

        return $password;
    }

    /**
     * 判斷日期是否合法
     *
     * @param string $date
     * @param string $format
     *
     * @return boolean
     */
    public function validateDate(string $date, string $format = 'Y-m-d')
    {
        return date($format, strtotime($date)) == $date;
    }

    /**
     * 隨機產生電話號碼
     *
     * @return string
     */
    public function randPhone()
    {
        $arr = ['02', '03', '037', '04', '049', '05', '06', '07', '08', '089', '082', '0826', '0836'];
        $phone = $arr[array_rand($arr)] . '-' . mt_rand(1000, 9999) . mt_rand(1000, 9999);
        return $phone;
    }

    /**
     * 計算年齡
     *
     * @param string $birthday
     * @param string $nowday
     *
     * @return boolean|int
     */
    public function calculateAgeFromBirthday($birthday, $nowday = '')
    {
        $birthDate = DateTime::createFromFormat('Y-m-d', $birthday);
        if ($birthDate === false) {
            return false;
        }

        $now = ($nowday != '') ? DateTime::createFromFormat('Y-m-d', $nowday) : new DateTime();

        $interval = $birthDate->diff($now);
        $age = $interval->y;

        if ($interval->invert == 1) {
            $age -= 1;
        }

        return $age;
    }

    /**
     * 將日期轉換成秒數
     *
     * @param string $date // 日期
     * @param boolean $isTime // 是否有時間
     *
     * @return int|false
     */
    public function convertDateToTimestamp($date, $isTime = false)
    {
        $dateTimeFormat = $isTime ? "Y-m-d H:i:s" : "Y-m-d";
        $dateTime = DateTime::createFromFormat($dateTimeFormat, $date);

        if ($dateTime === false) {
            // 輸入的日期不是有效的日期
            return false;
        }

        return $dateTime->getTimestamp();
    }

    /**
     * 取得每周的開始結束日期
     *
     * @param mixed $getDate
     *
     * @return array
     */
    public function getWeekInfo($getDate)
    {
        $first = 0; // 每週的第一天從星期日開始
        $stateDay = $getDate->format('Y-m-d');
        $weekDay = $getDate->format('w');
        $delDay = $weekDay - $first;
        $weekStartDay = date("Y-m-d", strtotime("$stateDay -" . $delDay . " days"));
        $weekEndDay = date("Y-m-d", strtotime("$weekStartDay +6 days"));
        return ['start' => new \DateTime($weekStartDay), 'end' => new \DateTime($weekEndDay), 'week' => $weekDay];
    }

    /**
     * 驗證日期
     *
     * @param string $date
     * @param array $formats
     *
     * @return boolean
     */
    public function checkDateIsValid($date, $formats = array("Y-m-d", "Y/m/d"))
    {
        $unixTime = strtotime($date);
        if (!$unixTime) { //strtotime轉換不對，日期格式顯然不對。
            return false;
        }

        //校驗日期的有效性，只要滿足其中一個格式就OK
        foreach ($formats as $format) {
            if (date($format, $unixTime) == $date) {
                return true;
            }
        }

        return false;
    }

    /**
     * 列出指定 日期範圍 內所有日期
     *
     * @param string $first // 開始日期
     * @param string $last  // 結束日期
     *
     * @return array
     */
    public function dateRange($first, $last)
    {
        $last = date('Y-m-d', strtotime($last . '+1 day'));
        $period = new DatePeriod(
            new DateTime($first),
            new DateInterval('P1D'),
            new DateTime($last)
        );
        $dates = [];
        foreach ($period as $date) {
            $dates[$date->format('Y-m-d')] = $date->format('w');
        }
        return $dates;
    }

    /**
     * 將數字轉換為字串
     * 例如: 99 => 九九, 89 => 八九, 2 => 二
     *
     * @param int $number
     *
     * @return string
     */
    public function convertToChinese($number)
    {
        $chineseDigits = $this::NUM_TO_WORD;
        $chineseNumber = '';
        if ($number < 0 || $number > 9) {
            // 將數字轉換為字串
            $numberString = strval($number);
            $numberLength = strlen($numberString);
            for ($i = 0; $i < $numberLength; $i++) {
                $digit = intval($numberString[$i]);
                $chineseNumber .= $chineseDigits[$digit];
            }
        } else {
            $chineseNumber = $chineseDigits[$number];
        }
        return $chineseNumber;
    }

    /**
     * 計算兩個日期之間的天數
     *
     * @param string $first // 開始日期
     * @param string $last  // 結束日期
     *
     * @return int
     */
    public function getDays($first, $last)
    {
        $startDate = new \DateTime($first);
        $endDate = new \DateTime($last);
        $interval = $startDate->diff($endDate);
        $days = $interval->days;
        return $days;
    }

    /**
     * 取得指定日期的星期
     *
     * @return string
     */
    public function convertToChineseWeekday($inputDate, $prefix = '')
    {
        $weekdays = ['一', '二', '三', '四', '五', '六', '日'];
        $englishWeekday = date('N', strtotime($inputDate));
        return $prefix . $weekdays[$englishWeekday - 1];
    }

    /**
     * 取得指定日期的星期
     *
     * @return string
     */
    public function dayOfWeekInChinese($dayOfWeek)
    {
        $days = ['日', '一', '二', '三', '四', '五', '六'];
        return $days[$dayOfWeek];
    }

    /**
     * 判斷某個日期是否過期
     *
     * @param string $date // 日期
     *
     * @return boolean
     */
    public function isExpired($date)
    {
        $currentDate = new \DateTime();
        $expirationDate = \DateTime::createFromFormat('Y-m-d H:i', $date);
        return $currentDate > $expirationDate;
    }

    /**
     * 西元轉民國
     *
     * @param string $date  // YYYY-MM-DD => YYY.MM.DD
     * @param string $split // 分隔字串
     * @param string $dot   // 取代字串
     *
     * @return string
     */
    public function yearChange($date = '', $split = '-', $dot = '.')
    {
        $exDate = explode($split, $date);
        $year = (int) $exDate[0] - 1911;
        $newDate = (string) $year . $dot . $exDate[1] . $dot . $exDate[2];
        return $newDate;
    }

    /**
     * 西元split
     *
     * @param string $date  // YYYY-MM-DD => YYYY.MM.DD
     * @param string $split // 分隔字串
     * @param string $dot   // 取代字串
     *
     * @return string
     */
    public function dotChange($date = '', $split = '-', $dot = '.')
    {
        $newDate = str_replace($split, $dot, $date);
        return $newDate;
    }

    /**
     * 指定年齡範圍、數量，隨機產生生日
     *
     * $tools = new \Base\Model\ToolModel();
     * $gbs = $tools->generateBirthdays([7, 8], 30, "Ym");
     * foreach ($gbs as $value) {
     *     echo $value . "<br>";
     * }
     * @param array $ageRange // 年齡範圍，例如：[20, 30]
     * @param int $num // 數量
     * @param string $format // 日期格式，例如："Y-m-d"
     *
     * @return array
     */
    public function generateBirthdays($ageRange, $num, $format = "Y-m-d")
    {
        $birthdays = [];
        $currentYear = date("Y");

        for ($i = 0; $i < $num; $i++) {
            $age = rand($ageRange[0], $ageRange[1]);
            $year = $currentYear - $age;
            $month = rand(1, 12);
            $day = rand(1, 28); // 為了簡單起見，假設每個月只有28天

            $birthday = date($format, mktime(0, 0, 0, $month, $day, $year));
            array_push($birthdays, $birthday);
        }

        return $birthdays;
    }

    /**
     * 過濾身分證字號
     *
     * @param string $inputString
     *
     * @return string
     */
    public function filterIdentityNumber($inputString)
    {
        // 將全形字元轉換為半形字元
        $identityNumber = mb_convert_kana($inputString, 'rnaskhc');

        // 將所有字母轉換為大寫
        $identityNumber = strtoupper((string) $identityNumber);

        // 移除所有非字母和數字的字符
        $identityNumber = preg_replace('/[^A-Z0-9]/', '', $identityNumber);

        return $identityNumber;
    }

    /**
     * 取得加密、解密物件
     *
     * @param string $type 加密類型
     * - encrypt: 加密
     * - decrypt: 解密
     * @param string $value 加密、解密的值
     *
     * @return string|bool
     */
    public function encryptOrDecryptValue($type, $value)
    {
        $method = $_ENV['PHP_ENCRYPT_METHOD'];
        $key = $_ENV['PHP_ENCRYPT_KEY'];
        $options = $_ENV['PHP_ENCRYPT_OPTION'];
        $iv = $_ENV['PHP_ENCRYPT_IV'];

        switch ($type) {
            case 'encrypt':
                return openssl_encrypt($value, $method, $key, $options, $iv);
            case 'decrypt':
                return openssl_decrypt($value, $method, $key, $options, $iv);
            default:
                return false;
        }
    }

    /**
     * 產生日期
     *
     * @param int $totalDays // 產生的日期總天數
     * @param int $backtrackDays // 回溯天數
     *
     * @return array
     */
    public function generateDates($totalDays, $backtrackDays)
    {
        $dates = [];
        $currentDate = new \DateTime();

        $interval = new \DateInterval('P' . $backtrackDays . 'D');
        $currentDate->sub($interval);

        for ($i = 0; $i < $totalDays; $i++) {
            $dateItem = [
                'title' => $currentDate->format('Y年 m月 d日 (') . $this->dayOfWeekInChinese($currentDate->format('w')) . ')',
                'value' => $currentDate->format('Y-m-d'),
            ];
            array_push($dates, $dateItem);
            $currentDate->add(new \DateInterval('P1D'));
        }

        return $dates;
    }
}
