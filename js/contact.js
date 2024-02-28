let c_token = null;
window.onloadTurnstileCallback = function() {
  turnstile.render('#captcha', {
    sitekey: '0x4AAAAAAABavw7IIXAqu0yj',
    callback: token => c_token = token
  });
};

const mailForm = document.getElementById('mail-form');
const sending = document.getElementById('form-sending');
const sent = document.getElementById('form-success');

mailForm.addEventListener('submit', async (e) => {
  e.preventDefault();
  sending.style.display = 'block';
  mailForm.style.display = 'none';

  const data = {
    'response': c_token,
    'name': document.getElementById('name').value,
    'email': document.getElementById('email').value,
    'subject': document.getElementById('subject').value,
    'message': document.getElementById('message').value
  };
  await fetch('https://mail.carlgo11.workers.dev', {
    method: 'POST',

    body: JSON.stringify(data)
  });
  sending.style.display = 'none';
  sent.style.display = 'block';
})
