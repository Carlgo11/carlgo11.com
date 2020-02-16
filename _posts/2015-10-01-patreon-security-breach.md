---
category: internet security
layout: post
image: "/img/patreon.svg"
---
As you might know already, yesterday [Patreon](https://www.patreon.com/) discovered they had been breached and personal information such as email addresses, shipping addresses, posts, names and password hashes were compromised.

Once again another security breach. There are both good and bad news to this though.

## The Good News

The good news are that they used a hashing algorithm called `bcrypt` that's fairly secure at the moment.  
bcrypt is also not decryptable so you can't turn the password hash back to a plain text password.

## The Bad News

The bad news is that apart from the passwords nothing else was encrypted which means that you can expect to get spam mails in a near future on the email address you entered upon registration for Patreon.

Bcrypt also won't be secure forever since computers become better and better at cracking hashes. [MD5](https://en.wikipedia.org/wiki/MD5) was once considered safe but nowadays you just have to go on google with a hash to have it instantly decrypted into plain text.

## What lesson can we learn from this?

Once again a big internet site has been breached.

Hopefully Patreon will bump up their security for the users but it almost always stays with that. They do it for the users and not for themselves.

The attackers gained access to Patreon and not it's users. They need to go over their servers and hire a security firm to strengthen their servers security.

That said I have to admit Patreon have been far more transparent than most other companies are. Kudos to them!

I have reached out to Patreon to give more information on how the attack was done and what they'll do to prevent further damage. I'll update this post if/when they reply.

## tl;dr

[Patreon](https://www.patreon.com/) was breached and email addresses, shipping addresses, posts, names and password hashes were compromised. They used a hashing algorithm called `bcrypt` which is secure at the moment so there's no real need to change all passwords on all sites just yet. You do however want to change it within a couple of years as computers get better and better which means it takes far less time to decrypt hashes.
