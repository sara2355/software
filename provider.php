<?php
session_start();
require_once 'db.php';

// نفرض أن المستخدم مقدم خدمة مسجل دخول
$provider_id = $_SESSION['user_id'] ;

// رسالة بعد الحذف أو الخطأ
$message = "";

// ✅ حذف الخدمة عند الضغط على زر الحذف
if (isset($_GET['delete'])) {
    $service_id = intval($_GET['delete']);
    $deleteQuery = "DELETE FROM services WHERE id = $service_id AND provider_id = $provider_id";
    if (mysqli_query($conn, $deleteQuery)) {
        $message = "✅ تم حذف الخدمة بنجاح!";
    } else {
        $message = "❌ حدث خطأ أثناء الحذف.";
    }
}

// ✅ جلب الخدمات من قاعدة البيانات
$query = "SELECT * FROM services WHERE provider_id = $provider_id";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<title>لوحة مقدم الخدمة</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="mihn_style.css">
<style>
body { font-family: Arial; background:#e0e0e0; margin:0; padding:0;    min-height: 100vh; /* لضمان امتداد الصفحة */
 }
main { padding:30px; flex: 1;  }
.top-bar { display:flex; justify-content:flex-end; margin-bottom:20px; }
.add-btn {
    padding: 10px 20px;
    background: var(--accent);
    color: var(--white);
    border-radius: 8px;
    font-weight: bold;
    transition: filter 0.2s;
}
.add-btn:hover { filter: brightness(0.9); }
.services-container { display:flex; flex-wrap:wrap; gap:20px; justify-content:center; }
.service-card {
    background: var(--white);
    padding: 20px;
    border-radius: 20px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08); /* أغمق قليلاً من الخلفية */
    width: 280px;
    display: flex;
    flex-direction: column;
    transition: transform 0.3s, box-shadow 0.3s;
}
.service-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
}

.service-card h3 { margin:0 0 10px; color:#333; font-size:1.3em; display:flex; align-items:center; }
.service-card p { margin:5px 0; color:#555; font-size:0.95em; line-height:1.4; }
.service-info { display:flex; justify-content:space-between; background:#ddd; padding:5px 10px; border-radius:10px; margin-top:10px; font-weight:bold; color:#333; }
.btn { 
    padding:6px 12px; 
    margin:10px 5px 0 0; 
    border:none; 
    border-radius:8px; 
    cursor:pointer; 
    font-weight:bold; 
    transition:0.3s; 
    text-decoration:none; 
    text-align:center; 
    display:inline-block;
}
.edit-btn { background: var(--accent);
    color: var(--white); }
.edit-btn:hover { background: #555; }
.delete-btn { background: var(--accent);
    color: var(--white); }
.delete-btn:hover { background: #111; }
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
<script>
    // إخفاء الرسالة بعد 5 ثواني
    setTimeout(() => {
        const msg = document.querySelector('.message');
        if (msg) msg.style.display = 'none';
    }, 3000);
</script>
<main>

    <?php if ($message): ?>
        <div class="message <?= strpos($message, '❌') !== false ? 'error' : '' ?>">
            <?= $message ?>
        </div>
    <?php endif; ?>

    <div class="top-bar" style="flex-direction:column; align-items:flex-end; gap:10px;">
    <a href="add_service.php" class="add-btn">➕ إضافة خدمة جديدة</a>
    <a href="orders.php" class="add-btn" style="background: var(--accent);
    color: var(--white);">📄 عرض الطلبات</a>
</div>

    

    <div class="services-container">
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <div class="service-card">
                    <h3><?= htmlspecialchars($row['title']) ?></h3>
                    <p><?= htmlspecialchars($row['description']) ?></p>
                    <div class="service-info">
                        <span>💰 <?= htmlspecialchars($row['price']) ?> </span>
                        <span>⏱️ <?= htmlspecialchars($row['time']) ?> </span>
                    </div>
                    <a href="edit_service.php?id=<?= $row['id'] ?>" class="btn edit-btn">✏️ تعديل</a>
                    <a href="?delete=<?= $row['id'] ?>" class="btn delete-btn" onclick="return confirm('هل أنت متأكد من حذف هذه الخدمة؟')">🗑️ حذف</a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="text-align:center; font-weight:bold;">لا توجد خدمات بعد.</p>
        <?php endif; ?>
    </div>

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
