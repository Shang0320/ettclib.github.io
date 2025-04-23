<?php
header('Content-Type: application/json');

$data = file_get_contents('php://input');
$filename = 'data/borrow_records.json';

// 確保目錄存在
if (!file_exists('data')) {
    mkdir('data', 0777, true);
}

if (file_put_contents($filename, $data)) {
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['error' => '無法儲存記錄']);
}
?>

<script src="js/pagination.js"></script>
<script src="js/storage.js"></script>