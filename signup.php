<?php
// signup.php
session_start();
require_once 'db.php';

$err = $ok = "";

if ($_SERVER['REQUEST_METHOD']==='POST') {
  // استلام وتطهير المدخلات
  $full_name = trim($_POST['full_name'] ?? '');
  $email     = trim($_POST['email'] ?? '');
  $phone     = trim($_POST['phone'] ?? '');
  $password  = $_POST['password'] ?? '';
  $user_type = $_POST['user_type'] ?? '';

  // تحقق بسيط
  if ($full_name==='' || $email==='' || $phone==='' || $password==='' || !in_array($user_type,['provider','recipient'])) {
    $err = "رجاءً املئ كل الحقول.";
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $err = "البريد الإلكتروني غير صالح.";
  } else {
    // فحص وجود إيميل/اسم مسبقًا
    $q = mysqli_prepare($conn, "SELECT id FROM users WHERE email=? OR full_name=? LIMIT 1");
    mysqli_stmt_bind_param($q, "ss", $email, $full_name);
    mysqli_stmt_execute($q);
    mysqli_stmt_store_result($q);

    if (mysqli_stmt_num_rows($q) > 0) {
      $err = "الاسم أو البريد مسجل مسبقًا.";
    } else {
      // تشفير كلمة المرور
      $hash = password_hash($password, PASSWORD_DEFAULT);

      $ins = mysqli_prepare($conn, "INSERT INTO users(full_name,email,phone,password,user_type) VALUES(?,?,?,?,?)");
      mysqli_stmt_bind_param($ins, "sssss", $full_name, $email, $phone, $hash, $user_type);
      if (mysqli_stmt_execute($ins)) {
        $ok = "تم إنشاء الحساب بنجاح. يمكنك تسجيل الدخول الآن.";
        // خيار: إعادة توجيه مباشر إلى تسجيل الدخول مع باراميتر نجاح
        header("Location: login.php?registered=1");
        exit;
      } else {
        $err = "تعذر إنشاء الحساب. حاول لاحقًا.";
      }
    }
  }
}
?>
<!DOCTYPE html>
<html lang="ar">
<head>
  <meta charset="UTF-8">
  <title>إنشاء حساب - مِهَن</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="mihn_style.css">
</head>
<body>
    
    <header class="site-header">
  <div class="brand">
    <a href="index.php" aria-label="الصفحة الرئيسية">
      <img src="assets/logo.jpg" alt="شعار مِهَن" class="logo">
    </a>
  </div>
</header>
    
  <div class="container">
    <h2>إنشاء حساب جديد</h2>
    <p class="desc">سجل حسابك للبدء كمستفيد أو مزوّد خدمة.</p>

    <?php if($err): ?><div class="msg error"><?php echo htmlspecialchars($err, ENT_QUOTES,'UTF-8'); ?></div><?php endif; ?>
    <?php if($ok):  ?><div class="msg success"><?php echo htmlspecialchars($ok, ENT_QUOTES,'UTF-8'); ?></div><?php endif; ?>

    <form method="post" action="signup.php" novalidate>
      <label>الاسم الكامل</label>
      <input type="text" name="full_name" required value="<?php if(isset($_POST['full_name'])) echo htmlspecialchars($_POST['full_name']); ?>">

      <label>البريد الإلكتروني</label>
      <input type="email" name="email" required value="<?php if(isset($_POST['email'])) echo htmlspecialchars($_POST['email']); ?>">

      <label>رقم الجوال</label>
      <input type="tel" name="phone" placeholder="مثال: 05XXXXXXXX" required value="<?php if(isset($_POST['phone'])) echo htmlspecialchars($_POST['phone']); ?>">

      <label>كلمة المرور</label>
<div class="password-container">
  <input type="password" id="password" name="password" minlength="6" required>
  <span id="togglePassword" class="toggle-icon">🔒</span>
</div>
      <label>نوع المستخدم</label>
      <div class="radio-group">
        <label><input type="radio" name="user_type" value="provider" required> مزود خدمة</label>
        <label><input type="radio" name="user_type" value="recipient" required> مستفيد</label>
      </div>

      <button type="submit">إنشاء الحساب</button>
    </form>

    <p class="center small" style="margin-top:14px;">
      لديك حساب؟ <a href="login.php">سجّل الدخول من هنا</a>
    </p>
  </div>
    
    <footer class="site-footer" role="contentinfo" aria-label="تذييل الموقع">
    <div class="footer-inner">
      <a href="mailto:contact@mihan.sa" class="footer-email">contact@mihn.sa</a>
      <span class="separator">•</span>
      <span>© 2025 مِهَن — جميع الحقوق محفوظة</span>
    </div>
  </footer>
    
    <script>
document.querySelector("form").addEventListener("submit", function (e) {
  const form = this;
  const msgBox = document.querySelector(".dynamic-error");

  // إزالة رسالة سابقة
  if (msgBox) msgBox.remove();

  const phone = form.querySelector('input[name="phone"]').value.trim();
  const phonePattern = /^05\d{8}$/; // يبدأ بـ05 + 8 أرقام (المجموع 10)
  const radios = form.querySelectorAll('input[name="user_type"]');
  let radioChecked = false;

  for (let r of radios) {
    if (r.checked) {
      radioChecked = true;
      break;
    }
  }

  let errorText = "";

  if (!phonePattern.test(phone)) {
    errorText = "يرجى إدخال رقم جوال صحيح يبدأ بـ05 ويتكوّن من 10 أرقام.";
  } else if (!radioChecked) {
    errorText = "يرجى اختيار نوع المستخدم (مزود خدمة أو مستفيد).";
  }

  if (errorText !== "") {
    e.preventDefault();

    // إنشاء عنصر رسالة الخطأ داخل الصفحة بنفس ستايل msg.error
    const errorDiv = document.createElement("div");
    errorDiv.className = "msg error dynamic-error";
    errorDiv.textContent = errorText;

    form.parentNode.insertBefore(errorDiv, form);
    window.scrollTo({ top: 0, behavior: "smooth" });
  }
});

const toggle = document.getElementById('togglePassword');
const password = document.getElementById('password');

toggle.addEventListener('click', () => {
  const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
  password.setAttribute('type', type);
  toggle.textContent = type === 'password' ? '🔒' : '🔓';
});
</script>

</body>
</html>
