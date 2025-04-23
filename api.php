<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// 讀取 CSV 並轉成陣列
function read_csv($file) {
    if (!file_exists($file)) return [];
    $rows = [];
    if (($handle = fopen($file, "r")) !== FALSE) {
        $header = fgetcsv($handle);
        while (($data = fgetcsv($handle)) !== FALSE) {
            $row = [];
            foreach ($header as $i => $col) {
                $row[$col] = $data[$i] ?? '';
            }
            $rows[] = $row;
        }
        fclose($handle);
    }
    return $rows;
}

// 讀寫借閱紀錄
function read_json($file) {
    if (!file_exists($file)) return [];
    $json = file_get_contents($file);
    return json_decode($json, true) ?: [];
}
function write_json($file, $data) {
    file_put_contents($file, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
}

// 取得 action
$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'get_books':
        echo json_encode(read_csv('books.csv'), JSON_UNESCAPED_UNICODE);
        break;
    case 'get_journals':
        echo json_encode(read_csv('journal.csv'), JSON_UNESCAPED_UNICODE);
        break;
    case 'get_thesis':
        echo json_encode(read_csv('thesis.csv'), JSON_UNESCAPED_UNICODE);
        break;
    case 'get_borrowed':
        echo json_encode(read_json('borrowed.json'), JSON_UNESCAPED_UNICODE);
        break;
    case 'borrow_book':
        $class = $_POST['班級'] ?? '';
        $name = $_POST['姓名'] ?? '';
        $book_title = $_POST['書名'] ?? '';
        $return_date = $_POST['應還日'] ?? '';
        $borrow_date = $_POST['借出日'] ?? '';
        if (!$class || !$name || !$book_title || !$return_date || !$borrow_date) {
            echo json_encode(['success'=>false, 'msg'=>'資料不完整']);
            exit;
        }
        $borrowed = read_json('borrowed.json');
        $books = read_csv('books.csv');
        $book = null;
        foreach ($books as $b) {
            if ($b['書名'] == $book_title) {
                $book = $b;
                break;
            }
        }
        if (!$book) {
            echo json_encode(['success'=>false, 'msg'=>'找不到書籍']);
            exit;
        }
        $borrowed_count = 0;
        foreach ($borrowed as $r) {
            if ($r['書名'] == $book_title) $borrowed_count++;
        }
        if ($borrowed_count >= intval($book['數量'])) {
            echo json_encode(['success'=>false, 'msg'=>'此書已無庫存可借']);
            exit;
        }
        $borrowed[] = [
            '班級'=>$class,
            '姓名'=>$name,
            '書名'=>$book_title,
            '借出日'=>$borrow_date,
            '應還日'=>$return_date
        ];
        write_json('borrowed.json', $borrowed);
        echo json_encode(['success'=>true]);
        break;
    case 'return_book':
        $name = $_POST['姓名'] ?? '';
        $class = $_POST['班級'] ?? '';
        $book_title = $_POST['書名'] ?? '';
        $borrowed = read_json('borrowed.json');
        $new_borrowed = [];
        $found = false;
        foreach ($borrowed as $r) {
            if ($r['書名'] == $book_title && $r['姓名'] == $name && $r['班級'] == $class && !$found) {
                $found = true;
                continue;
            }
            $new_borrowed[] = $r;
        }
        write_json('borrowed.json', $new_borrowed);
        echo json_encode(['success'=>true]);
        break;
    default:
        echo json_encode(['success'=>false, 'msg'=>'未知動作']);
} 