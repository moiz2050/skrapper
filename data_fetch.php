<?php
if (isset($_POST['keyword']) && $_POST['keyword']){
    $keyword = $_POST['keyword'];
} else {
    die("usage: php data missing [keyword]");
}

require_once("Core/curl.php");

    $curl = new Curl();
    $page = $curl->get("http://www.amazon.com/s/?url=search-alias%3Daps&field-keywords=".urlencode($keyword)."");

if ($page && $curl->getHttpCode()>=200 && $curl->getHttpCode()<400){
    
    $dom = new DOMDocument("utf-8");
    @$dom->loadHTML($page);

    $items = $dom->getElementById('s-results-list-atf')->childNodes;
    $result_array = [];

    for($i=0;$i<$items->length;$i++){

        $items_with_advertisement = $items->item($i)->getElementsByTagName('div');
        if($items_with_advertisement->item(0)->childNodes->item(0)->getAttribute('class') == "a-row a-spacing-mini")
            continue;


        $item_titles = $items->item($i)->getElementsByTagName('h2');
        if (!$item_titles->length){
            continue;
        }

        $item_url = $item_titles->item(0)->parentNode->getAttribute('href');

        $item_images = $items->item($i)->getElementsByTagName('img');
        if (!$item_images->length){
            continue;
        }

        $links = $items->item($i)->getElementsByTagName('a');
        $item_price = "";

        for($j=0; $j <= $links->length; $j++){
            if($links->item($j)->childNodes->item(0)->nodeName == "span"){
                $item_price = $links->item($j)->childNodes->item(0)->nodeValue;
                break;
            }
        }

        $item_price_with_currency = $item_price;
        $item_price_float_value = (float)str_replace('$','',$item_price);
        $currency = preg_replace('/[0-9]+/', '', str_replace('.', '', $item_price));

        $result_array[$i]['url'] = $item_url;
        $result_array[$i]['title'] = $item_titles->item(0)->textContent;
        $result_array[$i]['image'] = $item_images->item(0)->getAttribute("src");
        $result_array[$i]['currency'] = $currency;
        $result_array[$i]['price'] = $item_price_float_value;

    }

    usort($result_array, function($a, $b) { //Sorting array with respect to price in ascending order
        return $a['price'] - $b['price'];
    });

    $final_arr = [];

    foreach ($result_array as $key=>$value){ // Final array of complete data
        $final_arr[$key]['url'] = $value['url'];
        $final_arr[$key]['title'] = $value['title'];
        $final_arr[$key]['image'] = $value['image'];
        $final_arr[$key]['price'] = $value['currency'].$value['price'];
    }

    header("Content-Type: application/json", true);
    echo json_encode($final_arr);

} else {
    print("unexpected error occured");
}