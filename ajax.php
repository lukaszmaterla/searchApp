<?php

header('Content-type: application/json');

if(!empty($_GET['pageNumber'])){
    $page_number = $_GET['pageNumber'];
}else{
    $page_number = 2;
}

if(!empty($_GET['age'])){
    $age = $_GET['age'];
}else{
    $age = '18-80';
}


if(!empty($_GET['names'])){
    $names = $_GET['names'];
}else{
    $names = null;
}


$names_array = explode(',', $names);

foreach ($names_array as $key =>$value){
    $names_array[$key] = trim($value);
}

$url = 'https://badoo.com/pl/randki/poland/wielkopolskie/poznan/girls/page-'.$page_number.'/age-'.$age.'/';

$html_string = file_get_contents($url);
$pattern = '/nophoto/';
preg_match_all($pattern, $html_string, $nophoto);

if(count($nophoto[0]) == 16){
    $error_array = ['error'=>'koniec wyszukiwania'];
    echo json_encode($error_array);

    exit;
}

$persons = [];

if (strpos_array($html_string, $names_array) !== false){
        $pattern = '/<img.*?<\/a>/';
        preg_match_all($pattern, $html_string, $matches);
        if (!empty($matches[0])){
            foreach ($matches[0] as $line ){

                if (strpos_array($line, $names_array) !== false && !strpos($line, 'nophoto')) {
                    $pattern = '/src="(.*?)"/';
                    preg_match_all($pattern, $line, $src);
                    $src = $src[1][0];

                    $pattern = '/<a href="(.*?)"/';
                    preg_match_all($pattern, $line, $href);
                    $href = $href[1][0];

                    $persons[] = [
                        'src' => $src,
                        'href' => $href,
                    ];

                }

            }

        }
}
echo json_encode($persons);

function strpos_array($haystack, $needles) {
    if (is_array($needles)) {
        foreach ($needles as $needle) {
            $pos = strpos_array($haystack, $needle);
            if ($pos !== false) {
                return $pos;
            }
        }
        return false;
    } else {
        return strpos($haystack, $needles);
    }
}