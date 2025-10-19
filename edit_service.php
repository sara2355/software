<?php
session_start();
require_once 'db.php';

$provider_id = $_SESSION['user_id'] ; // رقم مقدم الخدمة المسجل

// رسالة تأكيد بعد التحديث
$message = "";

if (isset($_GET['id'])) {
    $service_id = intval($_GET['id']);
    $query = "SELECT * FROM services WHERE id = $service_id AND provider_id = $provider_id";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $service = mysqli_fetch_assoc($result);
        $service_name = $service['title'];
        $service_description = $service['description'];
        $service_price = $service['price'];
        $service_time = $service['time'];
$service_type = $service['type'];

    } else {
        die("❌ الخدمة غير موجودة أو ليس لديك صلاحية تعديلها.");
    }
}

// 2️⃣ تحديث الخدمة بعد إرسال الفورم
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $service_id = intval($_POST['serviceId']);
    $service_name = $_POST['serviceName'];
    $service_description = $_POST['serviceDescription'];
    $service_price = $_POST['servicePrice'];
    $service_time = $_POST['serviceTime'];
    $service_type = $_POST['serviceType'];

$updateQuery = "UPDATE services 
                SET title='$service_name', description='$service_description', price='$service_price', time='$service_time',type='$service_type'
                WHERE id=$service_id AND provider_id=$provider_id";


    if (mysqli_query($conn, $updateQuery)) {
        $message = " تم تحديث الخدمة بنجاح!";
    } else {
        $message = "❌ حدث خطأ أثناء التحديث.";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<title>تعديل الخدمة</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="mihn_style.css">
<style>
body { font-family: Arial; background:#e0e0e0; margin:0; padding:0; min-height: 100vh;}
main { padding:30px; display:flex; justify-content:center; flex-direction:column; align-items:center;  flex: 1;}
form { background:#f0f0f0; padding:30px; border-radius:15px; box-shadow:0 4px 12px rgba(0,0,0,0.15); width:400px; }
label { display:block; margin:15px 0 5px; color:#333; font-weight:bold; }
input { width:100%; padding:10px; border-radius:8px; border:1px solid #ccc; box-sizing:border-box; }
button { margin-top:20px; width:100%; padding:12px; background: var(--accent);
    color: var(--white); border:none; border-radius:8px; font-weight:bold; cursor:pointer; transition:0.3s; }
button:hover { background:#777; }
.message {
    text-align:center;
    font-weight:bold;
    background:#d4edda;
    color:#155724;
    border:1px solid #c3e6cb;
    padding:10px;
    border-radius:8px;
    margin-bottom:20px;
}
.error { background:#f8d7da; color:#721c24; border-color:#f5c6cb; }
</style>
</head>
<body>
 <header class="site-header">
  <div class="brand">
    <a href="index.php" aria-label="الصفحة الرئيسية">
      <img src="assets/logo.jpg" alt="شعار مِهَن" class="logo">
    </a>
  </div>
</header>
<main>

    <?php if ($message): ?>
        <div class="message <?= strpos($message, '❌') !== false ? 'error' : '' ?>">
            <?= $message ?>
        </div>
    <?php endif; ?>

    <form action="edit_service.php?id=<?= $service_id ?>" method="POST">
        <input type="hidden" name="serviceId" value="<?= $service_id ?>">

        <label>اسم الخدمة:</label>
        <input type="text" name="serviceName" value="<?= htmlspecialchars($service_name) ?>" required>

        <label>الوصف:</label>
        <input type="text" name="serviceDescription" value="<?= htmlspecialchars($service_description) ?>" required>

        <label>السعر (ريال):</label>
        <input type="number" name="servicePrice" value="<?= htmlspecialchars($service_price) ?>" required>

        <label>الوقت المتوقع (بالساعات):</label>
        <input type="text" name="serviceTime" value="<?= htmlspecialchars($service_time) ?>" required>

        
        
        
        <label>نوع الخدمة:</label>
<select name="serviceType" required>
    <option value="">اختر النوع</option>
    <option value="صيانه المنازل" <?= $service_type=='صيانه المنازل'?'selected':'' ?>>صيانه المنازل</option>
    <option value="التنظيف" <?= $service_type=='التنظيف'?'selected':'' ?>>التنظيف</option>
    <option value="خدمات السيارات" <?= $service_type=='خدمات السيارات'?'selected':'' ?>>خدمات السيارات</option>
    <option value="صيانه الاجهزه" <?= $service_type=='صيانه الاجهزه'?'selected':'' ?>>صيانه الاجهزه</option>
    <option value="التوصيل" <?= $service_type=='التوصيل'?'selected':'' ?>>التوصيل</option>
    <option value="الخدمات الشخصيه" <?= $service_type=='الخدمات الشخصيه'?'selected':'' ?>>الخدمات الشخصيه</option>
</select>

        <button type="submit">حفظ التعديلات</button>
    </form>

</main>

<script>
    const msg = document.querySelector('.message');
    if (msg) {
        setTimeout(() => {
            msg.style.display = 'none';
            window.location.href = 'provider.php';
        }, 2000);
    }
</script>

  <footer class="site-footer" role="contentinfo" aria-label="تذييل الموقع">
    <div class="footer-inner">
      <a href="mailto:contact@mihan.sa" class="footer-email">contact@mihn.sa</a>
      <span class="separator">•</span>
      <span>© 2025 مِهَن — جميع الحقوق محفوظة</span>
    </div>
  </footer>
</body>
</html>
