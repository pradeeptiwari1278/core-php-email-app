<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Send Email</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f4f6f9;
    }

    .container {
      max-width: 720px;
      background: white;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }

    .recipient-row {
      display: flex;
      gap: 8px;
      margin-bottom: 8px;
      align-items: stretch;
    }

    .recipient-row input {
      flex: 1;
      height: 45px;
    }

    .email-group {
      margin-bottom: 1rem;
    }

    .alert {
      position: fixed;
      top: 20px;
      right: 20px;
      z-index: 9999;
      min-width: 250px;
    }

    .remove-btn {
      background: none;
      border: none;
      color: red;
      font-weight: bold;
      font-size: 1.2rem;
      line-height: 1;
      padding: 0 10px;
      height: 45px;
      align-self: center;
      cursor: pointer;
    }

    .invisible {
      visibility: hidden;
    }

    input.form-control,
    textarea.form-control {
      height: 45px;
    }

    textarea.form-control {
      height: auto;
    }
  </style>
</head>

<body>
  <div class="container mt-5">
    <h3 class="mb-4">📧 Send Email</h3>
    <form id="emailForm" enctype="multipart/form-data">

      <!-- SUBJECT -->
      <div class="mb-3">
        <label class="form-label">Subject</label>
        <input type="text" class="form-control" name="subject">
      </div>

      <!-- TO -->
      <div class="mb-3">
        <label class="form-label">To</label>
        <div class="email-group" id="to-group"></div>
        <input type="hidden" name="to">
      </div>

      <!-- CC -->
      <div class="mb-3">
        <label class="form-label">CC</label>
        <div class="email-group" id="cc-group"></div>
        <input type="hidden" name="cc">
      </div>

      <!-- BCC -->
      <div class="mb-3">
        <label class="form-label">BCC</label>
        <div class="email-group" id="bcc-group"></div>
        <input type="hidden" name="bcc">
      </div>

      <!-- MESSAGE -->
      <div class="mb-3">
        <label class="form-label">Message</label>
        <textarea class="form-control" name="message" rows="5"></textarea>
      </div>

      <!-- ATTACHMENT -->
      <div class="mb-3">
        <label class="form-label">Attachment</label>
        <input type="file" class="form-control" name="attachment">
      </div>

      <!-- Email Provider -->
      <div class="mb-3">
        <label class="form-label">Send With</label>
        <select class="form-select" name="provider" required>
          <option value="gmail" selected>Gmail SMTP</option>
          <option value="sendgrid">SendGrid</option>
          <option value="mailgun">Mailgun</option>
          <option value="ses">Amazon SES</option>
          <!-- Add more as needed -->
        </select>
      </div>

      <!-- SUBMIT BUTTON -->
      <button type="submit" id="sendBtn" class="btn btn-primary">
        <span id="btnText">Send Email</span>
        <span id="btnLoader" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
      </button>
    </form>
  </div>

  <div id="alertContainer" class="position-fixed top-0 end-0 p-3" style="z-index: 9999;"></div>

  <script>
    function showAlert(message, type = 'success') {
      const alertContainer = document.getElementById('alertContainer');
      const wrapper = document.createElement('div');
      wrapper.innerHTML = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
          ${message}
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>`;
      alertContainer.append(wrapper);
      setTimeout(() => wrapper.remove(), 5000);
    }

    function setupRecipientGroup(groupId, hiddenInputName) {
      const group = document.getElementById(groupId);
      const hiddenInput = document.querySelector(`input[name="${hiddenInputName}"]`);
      let entries = [];

      function updateHiddenInput() {
        hiddenInput.value = entries.join(',');
      }

      function processLastRow() {
        const rows = group.querySelectorAll('.recipient-row');
        const validEntries = [];

        rows.forEach(row => {
          const nameInput = row.querySelector('input[type="text"]');
          const emailInput = row.querySelector('input[type="email"]');
          const nameVal = nameInput.value.trim();
          const emailVal = emailInput.value.trim();
          const isValidEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailVal);

          if (emailVal && isValidEmail) {
            const entry = nameVal ? `${nameVal}<${emailVal}>` : emailVal;
            validEntries.push(entry);
          }
        });

        hiddenInput.value = validEntries.join(',');
      }

      function renderRow(name = '', email = '') {
        const row = document.createElement('div');
        row.className = 'recipient-row';

        const nameInput = document.createElement('input');
        nameInput.type = 'text';
        nameInput.placeholder = 'Name';
        nameInput.className = 'form-control';
        nameInput.value = name;

        const emailInput = document.createElement('input');
        emailInput.type = 'email';
        emailInput.placeholder = 'Email';
        emailInput.className = 'form-control';
        emailInput.value = email;

        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.innerHTML = '&times;';
        removeBtn.className = 'remove-btn';
        removeBtn.onclick = () => {
          group.removeChild(row);
          entries = entries.filter(e => e !== (nameInput.value ? `${nameInput.value}<${emailInput.value}>` : emailInput.value));
          updateHiddenInput();
        };

        function addNextIfFilled() {
          const nameVal = nameInput.value.trim();
          const emailVal = emailInput.value.trim();
          const isValidEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailVal);
          if (emailVal && isValidEmail && !row.classList.contains('submitted')) {
            row.classList.add('submitted');
            const entry = nameVal ? `${nameVal}<${emailVal}>` : emailVal;
            entries.push(entry);
            updateHiddenInput();
            renderRow(); // next input
          }
        }

        emailInput.addEventListener('keydown', function(e) {
          if (['Enter', ' '].includes(e.key)) {
            e.preventDefault();
            addNextIfFilled();
          }
        });

        row.appendChild(nameInput);
        row.appendChild(emailInput);
        row.appendChild(removeBtn);
        group.appendChild(row);
      }

      renderRow();
      return processLastRow;
    }

    const processTo = setupRecipientGroup('to-group', 'to');
    const processCc = setupRecipientGroup('cc-group', 'cc');
    const processBcc = setupRecipientGroup('bcc-group', 'bcc');

    document.getElementById('emailForm').addEventListener('submit', function(e) {
      e.preventDefault();

      // Process dynamic recipients before submit
      processTo();
      processCc();
      processBcc();

      const form = e.target;
      const formData = new FormData(form);
      const sendBtn = document.getElementById('sendBtn');
      const btnText = document.getElementById('btnText');
      const btnLoader = document.getElementById('btnLoader');

      sendBtn.disabled = true;
      btnText.textContent = 'Sending...';
      btnLoader.classList.remove('d-none');

      fetch('send.php', {
          method: 'POST',
          body: formData
        })
        .then(res => res.text())
        .then(data => {
          if (data.toLowerCase().includes('error')) {
            showAlert(data, 'danger');
          } else {
            showAlert(data, 'success');
            form.reset();
            document.querySelectorAll('.email-group').forEach(group => group.innerHTML = '');
            setupRecipientGroup('to-group', 'to');
            setupRecipientGroup('cc-group', 'cc');
            setupRecipientGroup('bcc-group', 'bcc');
          }
        })
        .catch(() => showAlert("Something went wrong", 'danger'))
        .finally(() => {
          sendBtn.disabled = false;
          btnText.textContent = 'Send Email';
          btnLoader.classList.add('d-none');
        });
    });
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>