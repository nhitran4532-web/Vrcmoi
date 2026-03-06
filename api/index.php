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
    "name" => "HFB LIVE AUTO",
    "groups" => [
        [
            "name" => "🔴 Trực Tiếp Hôm Nay",
            "display" => "grid",
            "channels" => []
        ]
    ]
];

if (empty($matches)) {
    $output['groups'][0]['channels'][] = [
        "name" => "Hiện chưa có trận đấu",
        "image" => "https://i.imgur.com/vHdfXk8.png",
        "url" => ""
    ];
} else {
    foreach ($matches as $m) {
        $output['groups'][0]['channels'][] = [
            "name" => $m['title'],
            "description" => ($m['league'] ?? "Bóng đá"),
            "image" => str_replace('\/', '/', $m['team_1_logo']),
            "url" => str_replace('\/', '/', $m['source_live']),
            "headers" => ["Referer" => "https://gvtv1.com/"]
        ];
    }
}

echo json_encode($output, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
?>
