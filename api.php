<?php
function extract_data_from_element($html, $data_lyrics_container, $css_class) {
    $dom = new DOMDocument();
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    $query = "//div[@data-lyrics-container='$data_lyrics_container' and contains(concat(' ', normalize-space(@class), ' '), ' $css_class ')]";
    $elements = $xpath->query($query);
    $data = "";
    foreach ($elements as $element) {
        $data .= strip_tags($element->nodeValue) ."<br>";
    }
    return $data;
}
function add_space_and_newline($string) {
    return preg_replace_callback('/[A-Z]{2,}/', function($matches) {
        $length = strlen($matches[0]);
        if ($length === 2) {
            return $matches[0][0] .'<br>'. $matches[0][1];
        } else {
            return preg_replace('/([A-Z])/', "<br>$1", $matches[0]);
        }
    }, $string);
}
function split_words($string) {
    return preg_replace('/([a-z])([A-Z])/', "$1<br>$2", $string);
}
function add_newline_before_curly_brackets($string) {
    return preg_replace('/\(/', "<br>(", preg_replace('/\)/', ")<br>",$string));
}
function updo($string) {
    return preg_replace('/([A-Z])/', "<br>$1", $string);
}
function extract_span_from_div($html) {
    $dom = new DOMDocument();
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    $query = "//div[contains(concat(' ', normalize-space(@class), ' '), ' HeaderArtistAndTracklistdesktop__Container-sc-4vdeb8-0 hjExsS ')]//span[contains(concat(' ', normalize-space(@class), ' '), ' PortalTooltip__Container-yc1x8c-0 bOCNdp ')]";
    $elements = $xpath->query($query);
    $data = "";
    foreach ($elements as $element) {
        $data .= $element->textContent;
    }
    return $data;
}

function extract_span_text($url, $class_name) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $html = curl_exec($ch);
    curl_close($ch);
    $dom = new DOMDocument();
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    $query = "//div[@class='SongHeaderdesktop__SongDetails-sc-1effuo1-5 dhqXbj']//span[@class='$class_name']";
    $elements = $xpath->query($query);
    $data = "";
    foreach ($elements as $element) {
        $data .= $element->textContent;
    }
    return $data;
}


// Example usag
// Example usage
$start_time = microtime(true);
$artist = preg_replace('/\s+/', '-', $_GET['artist']);
$song =  preg_replace('/\s+/', '-', $_GET['song']);
$url = "https://genius.com/".$artist."-".$song."-lyrics";
$aria_label = 'Lyrics';
$data_lyrics_container = 'true';
$html = file_get_contents($url);
$data_lyrics_container = 'true';
$class_name = 'SongHeaderdesktop__HiddenMask-sc-1effuo1-11 iMpFIj';
$css_class = 'Lyrics__Container-sc-1ynbvzw-5 Dzxov';
$data = extract_data_from_element($html, $data_lyrics_container, $css_class);
$data = add_space_and_newline(add_newline_before_curly_brackets(split_words(preg_replace('/\[[^\]]*\]/', "\n\n",$data))));
$result = array(
    'artist' => extract_span_from_div($html),
    'song' => extract_span_text($html, $class_name),
    'lyrics' => preg_replace('/\s+/', '&nbsp;', $data),
    'time' => date('H:i:s'),
    'date' => date('Y-m-d'),
    'api_time' => microtime(true) - $start_time

);
$json = json_encode($result,JSON_PRETTY_PRINT);
print_r($json);


?>