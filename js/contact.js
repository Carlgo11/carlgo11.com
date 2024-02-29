let c_token = null;
window.onloadTurnstileCallback = function() {
  turnstile.render('#captcha', {
    sitekey: '0x4AAAAAAABavw7IIXAqu0yj',
    callback: (token) => {
      c_token = token
      document.getElementById('submit').disabled = false
    }
  });
};

function displayElement(element, displayStyle) {
  document.getElementById(element).style.display = displayStyle;
}

async function handleSubmit(e) {
  e.preventDefault();
  displayElement('mail-form', 'none');
  displayElement('form-sending', 'block');

  const formData = new FormData(mailForm);
  formData.append('token', c_token);
  const res = await fetch('https://mail.carlgo11.workers.dev', {
    method: 'POST',
    body: JSON.stringify(formData)
  });
  displayElement('form-sending', 'none');
  if (res.status !== 201) {
    console.error(await res.json());
    displayElement('form-error', 'block');
  } else
    displayElement('form-success', 'block');
}

const mailForm = document.getElementById('mail-form');
mailForm.addEventListener('submit', handleSubmit);
