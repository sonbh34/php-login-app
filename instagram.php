<?php
function detectInstagram(string $url): array {
    $url = trim($url);

    // URL 형식 보정 (https:// 없으면 추가)
    if (!preg_match('/^https?:\/\//i', $url)) {
        $url = 'https://' . $url;
    }

    // instagram.com 도메인 확인
    if (!preg_match('/instagram\.com/i', $url)) {
        return ['valid' => false, 'type' => null, 'message' => '인스타그램 주소가 아닙니다.'];
    }

    // 피드
    if (preg_match('/instagram\.com\/p\/([\w-]+)/', $url, $m)) {
        return ['valid' => true, 'type' => 'feed', 'code' => $m[1], 'url' => $url];
    }

    // 릴스
    if (preg_match('/instagram\.com\/reel\/([\w-]+)/', $url, $m)) {
        return ['valid' => true, 'type' => 'reel', 'code' => $m[1], 'url' => $url];
    }

    // 계정
    if (preg_match('/instagram\.com\/(?!p\/|reel\/|explore\/|stories\/|accounts\/)([a-zA-Z0-9_.]{1,30})/', $url, $m)) {
        return ['valid' => true, 'type' => 'account', 'username' => $m[1], 'url' => $url];
    }

    return ['valid' => false, 'type' => null, 'message' => '올바른 인스타그램 주소가 아닙니다.'];
}

// 사용 예시
$inputs = [
    'instagram.com/p/ABC123/',
    'https://www.instagram.com/reel/XYZ789/',
    'www.instagram.com/sonbh34',
    'https://instagram.com/explore/',
    '아무말',
];

foreach ($inputs as $input) {
    $result = detectInstagram($input);
    echo $input . ' → ' . json_encode($result, JSON_UNESCAPED_UNICODE) . PHP_EOL;
}
