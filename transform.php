<?php
/**
 * Created by PhpStorm.
 * User: jerry
 * Date: 2018/9/25
 * Time: 7:50 PM
 */

function get_td_array($table){
    $table = preg_replace("'<table[^>]*?>'si", "", $table);
    $table = preg_replace("'<tr[^>]*?>'si", "", $table);
    $table = preg_replace("'<td[^>]*?>'si", "", $table);
    $table = str_replace("</tr>", "{tr}", $table);
    $table = str_replace("</td>", "{td}", $table);
    //去掉 HTML 标记
    $table = preg_replace("'<[/!]*?[^<>]*?>'si", "", $table);
    //去掉空白字符
    $table = preg_replace("'([rn])[s]+'", "", $table);
    $table = str_replace(" ", "", $table);
    $table = str_replace(" ", "", $table);
    $table = explode('{tr}', $table);
    array_pop($table);
    $td_array = [];
    foreach ($table as $key => $tr) {
        $td = explode('{td}', $tr);
        array_pop($td);
        $td_array[] = $td;
    }
    return $td_array;
}

function transform($file_name){
    $file_name = explode(".", $file_name)[0];
    $array = get_td_array(file_get_contents($file_name));//Your HTML element
    $right = [];
    $current_id = 0;
    $current_course = "";
    $current_score = "";
    $current_class = "";
    $current_teacher = "";
    foreach ($array as $k => $v) {
        //if (empty($v)) {echo "走了次！\n";continue;}
        $array_r = [];
        //if(trim($v[0]) == "") {echo "走了一次！\n";continue;}
        if (mb_strstr(trim($v[0]), "周") === false) {
            for ($i = 0; $i < count($v); $i++) {
                if (trim($v[$i]) == "") continue;
                switch ($i) {
                    case 0:
                        $array_r["id"] = $current_id = trim($v[$i]);
                        break;
                    case 1:
                        $array_r["course"] = $current_course = trim($v[$i]);
                        break;
                    case 2:
                        $array_r["score"] = $current_score = trim($v[$i]);
                        break;
                    case 3:
                    case 5:
                    case 6://///////如果没有删除按钮则需要排除这个6
                        break;
                    case 4:
                        $array_r["class"] = $current_class = trim($v[$i]);
                        break;
                    //case 6:
                    case 7:
                        $array_r["teacher"] = $current_teacher = trim($v[$i]);
                        break;
                    //case 7:
                    case 8:
                        $array_r["week"] = $current_week = trim($v[$i]);
                        break;
                    //case 8:
                    case 9:
                        $array_r["time"] = $current_time = trim($v[$i]);
                        break;
                    //case 9:
                    case 10:
                        $array_r["room"] = $current_room = trim($v[$i]);
                        break;
                }
            }
            $right[] = $array_r;
        }
        else {
            $array_r["id"] = $current_id;
            $array_r["course"] = $current_course;
            $array_r["score"] = $current_score;
            $array_r["class"] = $current_class;
            $array_r["teacher"] = $current_teacher;
            $array_r["week"] = trim($v[0]);
            $array_r["time"] = trim($v[1]);
            $array_r["room"] = trim($v[2]);
            $right[] = $array_r;
        }
    }
    array_shift($right);
    array_pop($right);
    array_pop($right);
    foreach ($right as $k => $v) {
        $right[$k]["id"] = $right[$k]["id"] . "_" . $right[$k]["class"];
        unset($right[$k]["class"]);
        if (!isset($right[$k]["room"])) $right[$k]["room"] = "";
        if ($right[$k]["room"] == "&nbsp;") $right[$k]["room"] = "";
        $right[$k]["week"] = mb_substr($right[$k]["week"], 0, -1);
        $right[$k]["week"] = explode("—", $right[$k]["week"]);
        $right[$k]["time"] = mb_substr($right[$k]["time"], 0, -1);
        $right[$k]["time"] = explode(".", $right[$k]["time"]);
        $week = $this->getWeekCH(array_shift($right[$k]["time"]));
        $s = $right[$k]["time"];
        $right[$k]["time"] = [];
        foreach ($s as $ks => $vs) {
            $right[$k]["time"][] = [
                intval($week), intval($vs)
            ];
        }
    }
    @mkdir("target");
    file_put_contents("target/" . $file_name . ".json", json_encode($right, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
}