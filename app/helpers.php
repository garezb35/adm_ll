<?php
/**
 * MAKE AVATAR FUNCTION
 */
if (!function_exists('getPayGroupType')) {
    function getPayGroupType($type, $store = null)
    {
        switch ($type) {
            case 0:
                if ($store == 1)
                    return 'memberstore';
                return 'user';
            case 1:
                return 'user-filling';
            case 2:
                if ($store == 1)
                    return 'adminstore';
                return 'admin';
            case 4:
                return 'admin-filling';
            case 6:
                return 'burial-process';
            case 5:
                return 'losing-settlement';
            case 8:
                return 'store-part';
        }
        return '';
    }
}

if (!function_exists('getPayVerified')) {
    function getPayVerified($verified)
    {
        switch ($verified) {
            case 0:
                return 'request';
            case 1:
                return 'standby';
            case 2:
                return 'complete';
            case 3:
                return '';
            case 4:
                return 'cancel';
        }
        return '';
    }
}

if (!function_exists('getUserState')) {
    function getUserState($verified)
    {
        switch ($verified) {
            case 1:
                return 'userid';
            case 2:
                return 'bankmaster';
            case 3:
                return 'banknumber';
            case 4:
                return 'nickname';
            case 5:
                return 'phone';
        }
        return '';
    }
}

if (!function_exists('getInqueryState')) {
    function getInqueryState($status)
    {
        switch ($status) {
            case 1:
                return '신규';
            case 2:
                return '대기';
            case 3:
                return '답변';
        }
        return '';
    }
}

if (!function_exists('getPartnerSort')) {
    function getPartnerSort($request, &$data)
    {
        $snzSortType = "";
        $snzSortInfo = "";

        $arrSortType = array('money', 'money_evo', 'money_slot', 'loan', 'point', 'losing', 'created_at', 'charge_at', 'login_at');
        for ($index = 0; $index < count($arrSortType); $index++) {
            if ($request[$arrSortType[$index]] != '') {
                $snzSortType = $arrSortType[$index];
                $snzSortInfo = $request[$arrSortType[$index]];
            }
        }
        if ($snzSortType == '') {
            $snzSortType = 'money';
            $snzSortInfo = 'desc';
        }

        return array('type' => $snzSortType, 'sort' => $snzSortInfo);
    }
}


if (!function_exists('getPwbRemainTime')) {
    function getPwbRemainTime()
    {
        $date = new DateTime();
        $second = $date->format('s');
        $minute = $date->format('i');
        $leftTime = 300 - 30 - $minute % 5 * 60 - $second;
        if ($leftTime < 0) $leftTime += 300;
        return $leftTime;
    }
}

if (!function_exists('getOperationTxt')) {
    function getOperationTxt($state)
    {
        $snzContent = '';
        switch ($state) {
            case 0:
                $snzContent = '숨김';
                break;
            case 1:
                $snzContent = '정상운영';
                break;
            case 2:
                $snzContent = '점검';
                break;
        }
        return $snzContent;
    }
}

if (!function_exists('getSubUserId')) {
    function getSubUserId($data, &$arrUser)
    {
        foreach ($data->all_children as $user) {
            array_push($arrUser, $user->id);
            getSubUserId($user, $arrUser);
        }
    }
}

if (!function_exists('getSubUserId2')) {
    function getSubUserId2($data, &$arrUser)
    {
        foreach ($data->all_children as $user) {
            if ($user->isStore != 1) {
                array_push($arrUser, $user->id);
                getSubUserId($user, $arrUser);
            }
        }
    }
}

if (!function_exists('getTreeUserId')) {
    function getTreeUserId($data, &$arrUser, $deep = 1)
    {
        foreach ($data->all_children as $user) {
            array_push($arrUser, array('id' => $user->id, 'user' => $user, 'deep' => $deep));
            getTreeUserId($user, $arrUser, $deep + 1);
        }
    }
}

if (!function_exists('printColorMoney')) {
    function printColorMoney($money, $dec = 0)
    {
        if ($money > 0) {
            return '<span class="badge bg-danger">' . number_format($money, $dec) . '</span>';
        } else if ($money < 0) {
            return '<span class="badge bg-info">' . number_format($money, $dec) . '</span>';
        } else {
            return '<span class="money ">0</span>';
        }
    }
}

if (!function_exists('printColorMoney2')) {
    function printColorMoney2($money)
    {
        if ($money < 0) {
            return '<span class="money money-plus">' . number_format($money) . '</span>';
        } else if ($money > 0) {
            return '<span class="money money-minus">' . number_format($money) . '</span>';
        } else {
            return '<span class="money ">0</span>';
        }
    }
}

if (!function_exists('getPrevDate')) {
    function getPrevDate($date)
    {
        if (strcmp($date, date('Y-m-d 00:00:00', strtotime(' -60 day'))) >= 0)
            //if (strcmp($date, date('Y-m-d 00:00:00', strtotime(' -30 day'))) >= 0)
            return true;

        return false;
    }
}

if (!function_exists('getLiveRound')) {
    function getLiveRound($date)
    {
        $date = strtotime($date);
        $hour = date('H', $date);
        $minute = date('i', $date);
        $second = date('s', $date);

        return false;
    }
}


if (!function_exists('generateRandomString')) {
    function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}

if (!function_exists('getKeyFromArrayKey')) {
    function getKeyFromArrayKey($arr, $searchKey, $searchValue)
    {
        return array_filter(
            $arr,
            function ($value, $key) use ($searchKey, $searchValue) {

                return (
                    isset($value->$searchKey) && // key $searchKey should exist
                    $value->$searchKey == $searchValue // value matches $searchValue
                );
            },
            ARRAY_FILTER_USE_BOTH
        );
    }

    if (!function_exists('getDateFromRangeDate')) {
        function getDateFromRangeDate($rangeData)
        {
            $splitedRangeData = explode("to", $rangeData);
            if (sizeof($splitedRangeData) == 2) {
                return array(trim($splitedRangeData[0]), trim($splitedRangeData[1]));
            } else {
                return array(trim($splitedRangeData[0]), trim($splitedRangeData[0]));
            }
        }
    }
    if (!function_exists('updateEnv')) {
        function updateEnv($data = array())
        {
            if (!count($data)) {
                return;
            }

            $pattern = '/([^\=]*)\=[^\n]*/';

            $envFile = base_path() . '/.env';
            $lines = file($envFile);
            $newLines = [];
            foreach ($lines as $line) {
                preg_match($pattern, $line, $matches);

                if (!count($matches)) {
                    $newLines[] = $line;
                    continue;
                }

                if (!key_exists(trim($matches[1]), $data)) {
                    $newLines[] = $line;
                    continue;
                }

                $line = trim($matches[1]) . "={$data[trim($matches[1])]}\n";
                $newLines[] = $line;
            }

            $newContent = implode('', $newLines);
            file_put_contents($envFile, $newContent);
        }
    }
}
