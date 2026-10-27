<!-- <?php
// admin_management.php


session_start();
header('Content-Type: application/json');
include 'db_connect.php';  // الاتصال بقاعدة البيانات


if (!$conn) {
    echo json_encode(['error' => 'فشل الاتصال بقاعدة البيانات']);
    exit;
}

// عرض الأدمين (GET)
$admins = [];
$sql = "SELECT * FROM Admin";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        unset($row['password']);  // إخفاء كلمة المرور المشفرة
        $admins[] = $row;
    }
}

// إضافة أدمين جديد (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['name'], $data['email'], $data['password'], $data['role'], $data['phone'])) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "الحقول المطلوبة مفقودة"]);
        exit;
    }

    // استخراج البيانات
    $name = $data['name'];
    $email = $data['email'];
    $password = password_hash($data['password'], PASSWORD_DEFAULT);  // تشفير كلمة المرور
    $role = $data['role'];
    $phone = $data['phone'];

    // إضافة الأدمين إلى قاعدة البيانات
    $stmt = $conn->prepare("INSERT INTO Admin (name, email, password, role, phone) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $email, $password, $role, $phone);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "تم إضافة الأدمين بنجاح"]);
    } else {
        echo json_encode(["status" => "error", "message" => $stmt->error]);
    }
    $stmt->close();
    $conn->close();
    exit;
}

// تحديث بيانات الأدمين (PUT)
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['admin_id'], $data['name'], $data['email'], $data['role'], $data['phone'])) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "الحقول المطلوبة مفقودة"]);
        exit;
    }

    // استخراج البيانات
    $admin_id = $data['admin_id'];
    $name = $data['name'];
    $email = $data['email'];
    $role = $data['role'];
    $phone = $data['phone'];

    // تحديث بيانات الأدمين في قاعدة البيانات
    $stmt = $conn->prepare("UPDATE Admin SET name = ?, email = ?, role = ?, phone = ? WHERE admin_id = ?");
    $stmt->bind_param("ssssi", $name, $email, $role, $phone, $admin_id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "تم تحديث الأدمين بنجاح"]);
    } else {
        echo json_encode(["status" => "error", "message" => $stmt->error]);
    }
    $stmt->close();
    $conn->close();
    exit;
}

// حذف أدمين (DELETE)
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $data = json_decode(file_get_contents("php://input"), true);
    if (!isset($data['admin_id'])) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "مفقود معرف الأدمين"]);
        exit;
    }

    // استخراج معرف الأدمين
    $admin_id = $data['admin_id'];
    
    // حذف  من قاعدة البيانات
    $stmt = $conn->prepare("DELETE FROM Admin WHERE admin_id = ?");
    $stmt->bind_param("i", $admin_id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "تم حذف الأدمين بنجاح"]);
    } else {
        echo json_encode(["status" => "error", "message" => $stmt->error]);
    }
    $stmt->close();
    $conn->close();
    exit;
}
