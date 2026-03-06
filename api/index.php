<?php
header('Content-Type: application/json; charset=utf-8');

// Địa chỉ API gốc của Gà Vàng
$api_url = "https://api-gavang.gvtv1.com/api/live"; 

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (iPhone; CPU iPhone OS 14_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.0.3 Mobile/15E148 Safari/104.1");

$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);
$matches = $data['data'] ?? [];

// Khung JSON cho App HFB
$output = [
    "id" => "HFB-AUTO",
    "name" => "HFB LIVE - GÀ VÀNG AUTO",
    "groups" => [
        [
            "id" => "live-now",
            "name" => "🔴 TRẬN ĐẤU ĐANG DIỄN RA",
            "display" => "vertical",
            "grid_number" => 2,
            "channels" => []
        ]
    ]
];

// Nếu không có trận nào đang đá
if (empty($matches)) {
    $output['groups'][0]['channels'][] = [
        "name" => "Hiện chưa có trận đấu nào mới",
        "description" => "Vui lòng quay lại sau",
        "image" => ["url" => "https://i.imgur.com/vHdfXk8.png", "display" => "cover"]
    ];
} else {
    // Duyệt qua từng trận đấu để lấy dữ liệu
    foreach ($matches as $m) {
        $output['groups'][0]['channels'][] = [
            "id" => "gv-" . ($m['id'] ?? uniqid()),
            "name" => $m['title'], // Tự động lấy tên 2 đội
            "description" => ($m['league'] ?? 'Bóng đá') . " • Trực tiếp",
            "image" => [
                "url" => str_replace('\/', '/', $m['team_1_logo']),
                "display" => "cover"
            ],
            "sources" => [[
                "contents" => [[
                    "streams" => [[
                        "stream_links" => [[
                            "name" => "Link HD (Auto)",
                            "type" => "hls",
                            "url" => str_replace('\/', '/', $m['source_live']), // Link m3u8 tự cập nhật
                            "request_headers" => [["key" => "Referer", "value" => "https://gvtv1.com/"]]
                        ]]
                    ]]
                ]]
            ]]
        ];
    }
}

echo json_encode($output, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
