import { EmailMessage } from 'cloudflare:email';
import { createMimeMessage, Mailbox } from 'mimetext';

const headers = {
	'Access-Control-Allow-Origin': '*',
	'Accept': 'application/json',
	'Access-Control-Request-Method': 'POST, OPTIONS'
};

export default {
	async fetch(request, env) {
		try {
			switch (request.method) {
				case 'OPTIONS':
					return new Response(null, { status: 204, headers: headers });
				case 'POST':
					const { token, name, email, subject, message } = await request.json();

					if (!token)
						return new Response(JSON.stringify({ error: 'Missing turnstile token' }), { status: 422, headers });

					const ip = request.headers.get('CF-Connecting-IP');
					const formData = new FormData();
					formData.append('secret', env.SECRET_KEY);
					formData.append('response', token);
					formData.append('remoteip', ip);

					const url = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';
					const result = await fetch(url, { body: formData, method: 'POST' });
					const outcome = await result.json();

					if (outcome.success) {
						if (!name || !email || !subject || !message) {
							return new Response(null, { status: 422, headers });
						}

						const msg = createMimeMessage();
						msg.setSender({ name: name, addr: env.EML_FROM });
						msg.setRecipient(env.EML_TO);
						msg.setHeader('Reply-To', new Mailbox(email));
						msg.setSubject(subject);
						msg.addMessage({
							contentType: 'text/plain',
							data: message
						});

						await env.SEB.send(new EmailMessage(env.EML_FROM, env.EML_TO, msg.asRaw()));
						return new Response(JSON.stringify({ success: true }), { status: 201, headers: headers });
					} else {
						return new Response(JSON.stringify({ error: 'Failed to validate token' }), {
							status: 405,
							headers: headers
						});
					}
				default:
					return new Response(null, { status: 405, headers: headers });
			}
		} catch (e) {
			console.log(e);
			return new Response(null, { status: 500, headers: headers });
		}
	}
};
