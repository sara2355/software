<?php
// login.php
session_start();
require_once 'db.php';

$err = "";
if (isset($_GET['registered'])) {
  $ok = "تم إنشاء الحساب بنجاح. سجل دخولك الآن.";
}

if ($_SERVER['REQUEST_METHOD']==='POST') {
  $identifier = trim($_POST['identifier'] ?? ''); // اسم أو إيميل
  $password   = $_POST['password'] ?? '';

  if ($identifier==='' || $password==='') {
    $err = "أدخل الاسم/الإيميل وكلمة المرور.";
  } else {
    // جلب المستخدم بالاسم أو الإيميل
    $q = mysqli_prepare($conn, "SELECT id, full_name, email, password, user_type FROM users WHERE email=? OR full_name=? LIMIT 1");
    mysqli_stmt_bind_param($q, "ss", $identifier, $identifier);
    mysqli_stmt_execute($q);
    $res = mysqli_stmt_get_result($q);
    $user = mysqli_fetch_assoc($res);

    if ($user && password_verify($password, $user['password'])) {
      // بدء الجلسة
      $_SESSION['user_id']   = $user['id'];
      $_SESSION['full_name'] = $user['full_name'];
      $_SESSION['user_type'] = $user['user_type'];

      // تحويل حسب النوع
      if ($user['user_type'] === 'provider') {
        header("Location: provider.php");
      } else {
        header("Location: services.php");
      }
      exit;
    } else {
      $err = "بيانات الدخول غير صحيحة.";
    }
  }
}
?>
<!DOCTYPE html>
<html lang="ar">
<head>
  <meta charset="UTF-8">
  <title>تسجيل الدخول - مِهَن</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="mihn_style.css">
</head>

<header class="site-header">
  <div class="brand">
    <a href="index.php" aria-label="الصفحة الرئيسية">
      <img src="assets/logo.jpg" alt="شعار مِهَن" class="logo">
    </a>
  </div>
</header>

<body>
  <div class="container">
    <h2>تسجيل الدخول</h2>
    <p class="desc">ادخل اسم المستخدم أو بريدك الإلكتروني مع كلمة المرور.</p>

    <?php if(!empty($err)): ?><div class="msg error"><?php echo htmlspecialchars($err, ENT_QUOTES,'UTF-8'); ?></div><?php endif; ?>
    <?php if(!empty($ok)):  ?><div class="msg success"><?php echo htmlspecialchars($ok, ENT_QUOTES,'UTF-8'); ?></div><?php endif; ?>

    <form method="post" action="login.php">
      <label>اسم المستخدم أو البريد الإلكتروني</label>
      <input type="text" name="identifier" required>

      <label>كلمة المرور</label>
      <input type="password" name="password" required>

      <button type="submit">دخول</button>
    </form>

    <p class="center small" style="margin-top:14px;">
      ليس لديك حساب؟ <a href="signup.php">أنشئ حسابًا الآن</a>
    </p>
  </div>
    
    <footer class="site-footer" role="contentinfo" aria-label="تذييل الموقع">
    <div class="footer-inner">
      <a href="mailto:contact@mihan.sa" class="footer-email">contact@mihn.sa</a>
      <span class="separator">•</span>
      <span>© 2025 مِهَن — جميع الحقوق محفوظة</span>
    </div>
  </footer>
    
</body>
</html>

