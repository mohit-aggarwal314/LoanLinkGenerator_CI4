<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Loans - Generate Payment Link</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-4">
  <h2>Borrowers</h2>
  <table class="table table-bordered" id="loans-table">
    <thead><tr><th>ID</th><th>Name</th><th>Phone</th><th>Amount Due</th><th>Action</th></tr></thead>
    <tbody>
      <?php foreach($loans as $loan): ?>
      <tr data-loan-id="<?= $loan['id'] ?>">
        <td><?= $loan['id'] ?></td>
        <td><?= esc($loan['name']) ?></td>
        <td><?= esc($loan['phone']) ?></td>
        <td>₹<?= number_format($loan['amount_due'],2) ?></td>
        <td>
          <button class="btn btn-primary btn-create-link" 
                  data-id="<?= $loan['id'] ?>"
                  data-name="<?= esc($loan['name']) ?>"
                  data-amount="<?= $loan['amount_due'] ?>">
            Create Link
          </button>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<!-- Modal -->
<div class="modal fade" id="linkModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="generateLinkForm">
        <div class="modal-header">
          <h5 class="modal-title">Generate Payment Link for <span id="borrowerName"></span></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="loan_id" id="loan_id" />
          <div class="mb-2">
            <div class="form-check">
              <input class="form-check-input" type="radio" name="type" id="type_full" value="full" checked>
              <label class="form-check-label" for="type_full">Full payment</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="type" id="type_custom" value="custom">
              <label class="form-check-label" for="type_custom">Custom amount</label>
            </div>
          </div>

          <div id="customAmountWrap" style="display:none;">
            <label>Enter amount (₹)</label>
            <input type="number" step="0.01" name="custom_amount" id="custom_amount" class="form-control" />
            <small id="dueInfo" class="form-text text-muted"></small>
          </div>

          <div id="resultArea" class="mt-3" style="display:none;">
            <label>Generated Link</label>
            <div class="input-group">
              <input type="text" readonly id="generatedLink" class="form-control" />
              <button class="btn btn-outline-secondary" type="button" id="copyBtn">Copy</button>
            </div>
          </div>

          <div id="modalError" class="text-danger mt-2" style="display:none;"></div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button id="generateBtn" type="submit" class="btn btn-success">Generate Link</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function(){
  const modalEl = document.getElementById('linkModal');
  const bsModal = new bootstrap.Modal(modalEl);
  const btns = document.querySelectorAll('.btn-create-link');
  const borrowerName = document.getElementById('borrowerName');
  const loan_id_input = document.getElementById('loan_id');
  const dueInfo = document.getElementById('dueInfo');
  const customWrap = document.getElementById('customAmountWrap');
  const customAmountInput = document.getElementById('custom_amount');
  const resultArea = document.getElementById('resultArea');
  const generatedLink = document.getElementById('generatedLink');
  const modalError = document.getElementById('modalError');

  btns.forEach(b=>{
    b.addEventListener('click', function(){
      const id = this.dataset.id;
      const name = this.dataset.name;
      const amt = this.dataset.amount;
      borrowerName.textContent = name;
      loan_id_input.value = id;
      dueInfo.textContent = 'Amount due: ₹' + parseFloat(amt).toFixed(2);
      // reset
      document.getElementById('type_full').checked = true;
      customAmountInput.value = '';
      customWrap.style.display = 'none';
      resultArea.style.display = 'none';
      modalError.style.display = 'none';
      bsModal.show();
    });
  });

  // show/hide custom amount
  const radios = document.querySelectorAll('input[name="type"]');
  radios.forEach(r=>{
    r.addEventListener('change', function(){
      if (this.value === 'custom') {
        customWrap.style.display = 'block';
      } else {
        customWrap.style.display = 'none';
      }
    });
  });

  document.getElementById('generateLinkForm').addEventListener('submit', function(e){
    e.preventDefault();
    modalError.style.display = 'none';
    resultArea.style.display = 'none';

    const form = new FormData(this);

    // basic client-side check for custom amount
    if (form.get('type') === 'custom') {
      const ca = parseFloat(form.get('custom_amount') || 0);
      if (!(ca > 0)) {
        modalError.textContent = 'Enter a valid custom amount.';
        modalError.style.display = 'block';
        return;
      }
    }

    // send AJAX
    fetch('<?= site_url("loans/generate-link") ?>', {
      method: 'POST',
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
      body: form
    })
    .then(r=>r.json())
    .then(json=>{
      if (json.status === 'ok') {
        generatedLink.value = json.link;
        resultArea.style.display = 'block';
      } else {
        modalError.textContent = json.message || 'Something went wrong';
        modalError.style.display = 'block';
      }
    })
    .catch(err=>{
      modalError.textContent = 'Request failed';
      modalError.style.display = 'block';
    });
  });

  document.getElementById('copyBtn').addEventListener('click', function(){
    generatedLink.select();
    generatedLink.setSelectionRange(0, 99999);
    document.execCommand('copy');
    this.textContent = 'Copied';
    setTimeout(()=> this.textContent = 'Copy', 2000);
  });
});
</script>
</body>
</html>
