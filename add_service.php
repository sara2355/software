<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<title>إضافة خدمة جديدة</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="mihn_style.css">
<style>
    body { font-family: Arial; background:#e0e0e0; margin:0; padding:0; min-height: 100vh;}
    main { padding:30px; display:flex; justify-content:center; flex: 1;}
    form { background:#f0f0f0; padding:30px; border-radius:15px; box-shadow:0 4px 12px rgba(0,0,0,0.15); width:400px; }
    label { display:block; margin:15px 0 5px; color:#333; font-weight:bold; }
    input { width:100%; padding:10px; border-radius:8px; border:1px solid #ccc; box-sizing:border-box; }
    button { margin-top:20px; width:100%; padding:12px; background: var(--accent);
    color: var(--white); border:none; border-radius:8px; font-weight:bold; cursor:pointer; transition:0.3s; }
    button:hover { background:#999; }
    .msg { text-align:center; font-weight:bold; margin-bottom:15px; }
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
 

   <?php
session_start(); 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'db.php';
$message = "";

// تحقق من إرسال الفورم
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $serviceName = $_POST['serviceName'] ?? '';
    $serviceDescription = $_POST['serviceDescription'] ?? '';
    $servicePrice = $_POST['servicePrice'] ?? '';
    $serviceTime = $_POST['serviceTime'] ?? '';
       $serviceType = $_POST['serviceType'] ?? '';

    // نأخذ رقم المقدم من الجلسة
    $provider_id = $_SESSION['user_id'];

   if ($serviceName && $serviceDescription && $servicePrice && $serviceTime) {


$sql = "INSERT INTO Services (provider_id, title, description, price, time, type)
        VALUES ('$provider_id', '$serviceName', '$serviceDescription', '$servicePrice', '$serviceTime', '$serviceType')";

     
        if ($conn->query($sql) === TRUE) {
            $message = "<div class='msg' style='color:green;'>تمت إضافة الخدمة بنجاح </div>";
            echo "<script>setTimeout(()=>{ window.location.href='provider.php'; }, 2000);</script>";
        } else {
            $message = "<div class='msg' style='color:red;'>❌ خطأ: " . $conn->error . "</div>";
        }

    } else {
        $message = "<div class='msg' style='color:red;'>❌ يرجى تعبئة جميع الحقول.</div>";
    }
}
?>



    <form method="POST">
                <?= $message ?>

        <label>اسم الخدمة:</label>
        <input type="text" name="serviceName" required>

        <label>الوصف:</label>
        <input type="text" name="serviceDescription" required>

        <label>السعر (ريال):</label>
        <input type="number" name="servicePrice" required>

        <label>الوقت المتوقع (بالساعات):</label>
        <input type="text" name="serviceTime" required>
        
        
        <label>نوع الخدمة:</label>
    <select name="serviceType" required>
        <option value="">اختر النوع</option>
        <option value="صيانه المنازل">صيانه المنازل</option>
        <option value="التنظيف">التنظيف</option>
        <option value="خدمات السيارات">خدمات السيارات</option>
        <option value="صيانه الاجهزه">صيانه الاجهزه </option>
        <option value="التوصيل">التوصيل</option>
                <option value="الخدمات الشخصيه">الخدمات الشخصيه</option>

    </select>

        <button type="submit">إضافة الخدمة</button>
    </form>
</main>
  <footer class="site-footer" role="contentinfo" aria-label="تذييل الموقع">
    <div class="footer-inner">
      <a href="mailto:contact@mihan.sa" class="footer-email">contact@mihn.sa</a>
      <span class="separator">•</span>
      <span>© 2025 مِهَن — جميع الحقوق محفوظة</span>
    </div>
  </footer>
</body>
</html>
