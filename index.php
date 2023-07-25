<?php
header("Access-Control-Allow-Origin: *");
// get request method
$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'GET') {

    $args = explode("&",$_SERVER['QUERY_STRING']);
    $request = [];
    for($i = 0; $i<count($args); $i++){
        $key = explode("=",$args[$i]);
        $request[$key[0]]  = $key[1];
    }
    
    switch($request['api']){
        case "heros":
            GetAllHeros();
            break;
        case "hero":
            GetHero($request['id']);
            break;
    }
}

function GetAllHeros(){
    $response = file_get_contents('https://www.dota2.com/datafeed/herolist?language=french');
    $response = object_to_array(json_decode($response));
    for($i = 0; $i < count($response['result']['data']['heroes']); $i++){
        $response['result']['data']['heroes'][$i]['base64'] = "https://cdn.cloudflare.steamstatic.com/apps/dota2/images/dota_react/heroes/".substr($response['result']['data']['heroes'][$i]['name'], 14).".png";
    }
    echo $response['result']['data']['heroes'];
}

function GetHero($id){
    $response = file_get_contents('https://www.dota2.com/datafeed/herodata?language=french&hero_id='.$id);
    $response = object_to_array(json_decode($response));
    $hero = $response['result']['data']['heroes'][0];
    $hero['avatar'] = "https://cdn.cloudflare.steamstatic.com/apps/dota2/images/dota_react/heroes/".substr($hero['name'], 14).".png";
    for($i = 0; $i < count($hero['abilities']); ++$i){
        $hero['abilities'][$i]['base64'] = base64_encode(file_get_contents('https://cdn.cloudflare.steamstatic.com/apps/dota2/images/dota_react/abilities/'.$hero['abilities'][$i]['name'].'.png'));
    }
    echo $hero;
}

function object_to_array($obj) {
    if(is_object($obj)) $obj = (array) $obj;
    if(is_array($obj)) {
        $new = array();
        foreach($obj as $key => $val) {
            $new[$key] = object_to_array($val);
        }
    }
    else $new = $obj;
    return $new;
}

?>
<html>
    <head></head>
    <meta http-equiv="Content-Type" content="text/json; charset=UTF-8">
    <body></body>
</html>
