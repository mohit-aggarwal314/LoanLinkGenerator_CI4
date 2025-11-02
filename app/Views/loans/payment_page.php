<!doctype html>
<html><head><meta charset="utf-8"><title>Pay</title></head>
<body>
  <div style="max-width:600px;margin:40px auto">
    <h3>Payment Link</h3>
    <p>Loan ID: <?= esc($link['loan_id']) ?></p>
    <p>Amount: ₹<?= number_format($link['amount'],2) ?></p>
    <p>Token: <?= esc($link['token']) ?></p>
    <p>This is a test page. Integrate payment gateway here later.</p>
  </div>
</body></html>
