<?php
header('Content-Type: application/json; charset=utf-8');

$api_url = "https://api-gavang.gvtv1.com/api/live"; 

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0");
$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);
$matches = $data['data'] ?? [];

$output = [
    "id" => "HFB-AUTO",
    "name" => "HFB LIVE",
    "groups" => [
        [
            "id" => "live",
            "name" => "🔴 Hôm nay (" . date("d/m") . ")",
            "display" => "grid", 
            "grid_number" => 2,
            "channels" => []
        ]
    ]
];

foreach ($matches as $m) {
    // Lấy giờ thi đấu từ API
    $time = isset($m['start_time']) ? date("H\hi", $m['start_time']) : "00h00";
    
    $output['groups'][0]['channels'][] = [
        "id" => "gv-" . $m['id'],
        "name" => $m['title'], 
        "description" => $time . " • " . ($m['league'] ?? "Bóng đá"),
        "image" => [
            "url" => str_replace('\/', '/', $m['team_1_logo']),
            "display" => "cover"
        ],
        "sources" => [[
            "contents" => [[
                "streams" => [[
                    "stream_links" => [[
                        "name" => "Link HD",
                        "type" => "hls",
                        "url" => str_replace('\/', '/', $m['source_live']),
                        "request_headers" => [["key" => "Referer", "value" => "https://gvtv1.com/"]]
                    ]]
                ]]
            ]]
        ]]
    ];
}

echo json_encode($output, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
