---
category: internet security
layout: post
image: "https://res.cloudinary.com/dbsfyc1ry/image/upload/v1581908477/carlgo11.com/posts/ts_i9xxxa.svg"
---

After hosting a TeamSpeak3 server since 2015 I thought it would be a good idea to share  what I've learned under the years and how I keep my TS server safe.
In this post I'll be going over my firewall settings, installation, configuration and App Armor configuration.

## Installation
I'm using Ubuntu Server to host the TeamSpeak3 server. If you're not running GNU/Linux then this chapter won't be relevant to you.

Sadly there are no [snaps](https://snapcraft.io/) or [Debian packages](https://en.wikipedia.org/wiki/Deb_(file_format)) for the TeamSpeak3 client and server and so you'll have to get the files on the [official site](https://teamspeak.com).
Just remember to check for updates regularly.

When you've downloaded it on the server, unpack the archive and run `./ts3server_startscript.sh` file like any other script. And just like any other script that you've downloaded from the internet you should avoid running it as root/sudo.

## Firewall and port forwarding
As I run my TS3 server on an AWS EC2 instance, I get the option to set port rules using the Security Groups dashboard on AWS.
However the process is similar for most services/programs one can use to whitelist/blacklist ports.

First, incoming traffic:

Port | Protocol | Name | Description | Required
--- | --- | --- | --- | --- |
9987 | UDP | Voice | Used for voice communication (VOIP) | Yes
30033 | TCP | File transfers | Used for uploading/downloading files to channels | No
10011 | TCP | Server Query | Used to log in to the server via telnet | No
41144 | TCP | TSDNS | Used for TSDNS resolving. Not necessary unless you use a TSNDS service | No

I have chosen to not use TSDNS, File hosting or telnet connections and thus my only open port is 9987.

As for the outgoing traffic there are no required ports. Only if you want to advertise the server in Teamspeak's "Server List" or use any other license than the free one.

## AppArmor configuration
This is my [AppArmor](https://wiki.ubuntu.com/AppArmor) profile. It's really limiting in what the TS3 server can do and so you shouldn't just copy-paste it but instead you can use this to build your own upon.

Some things that this profile blocks are TSDNS, file sharing and any other storage form than MariaDB.

```SHELL
# Directory of the TeamSpeak3 server.
@{home_dir} = /usr/local/teamspeak3_server

#include <tunables/global>

/usr/local/teamspeak3_server/ts3server_startscript.sh {
  #include <abstractions/base>
  #include <abstractions/bash>
  #include <abstractions/lxc/container-base>

  /bin/cat rux,
  /bin/dash rux,
  /bin/grep rux,
  /bin/ps rux,
  /bin/readlink rux,
  /bin/rm rux,
  /bin/sleep rux,
  /bin/uname rux,
  /usr/bin/dirname rix,
  @{home_dir}/libts3_ssh.so rux,
  @{home_dir}/sql/* rix,
  @{home_dir}/ts3server rix,
  @{home_dir}/ts3server_startscript.sh rix,

}
```

## File permissions

#### If you're using AppArmor as mentioned earlier you shouldn't technically need to set up strict file permissions but it could be a good second line of defense.

I've chosen to create a user called `teamspeak` on my server and run the server from said user. That way the server gets the least amount of permissions on the server and can't interact with other users.

```SHELL
drwx------  4 teamspeak teamspeak     4096 Dec 17 00:43 files/
-r-x------  1 teamspeak teamspeak   306864 Jan  5 06:18 libmariadb.so.2
-r-x------  1 teamspeak teamspeak  1000888 Jan  5 06:15 libts3db_mariadb.so
-r-x------  1 teamspeak teamspeak   936272 Dec 29 22:53 libts3_ssh.so
drwx------  2 teamspeak teamspeak     4096 Jan  7 05:05 logs/
-r--------  1 teamspeak teamspeak        0 Dec 17 00:43 query_ip_blacklist.txt
-r--------  1 teamspeak teamspeak       10 Dec 17 00:43 query_ip_whitelist.txt
dr-x------  5 teamspeak teamspeak    12288 Dec 29 22:53 sql/
-r--------  1 teamspeak teamspeak      110 Jan  5 06:06 ts3db_mariadb.ini
-r-x------  1 teamspeak teamspeak 11340800 Dec 29 22:53 ts3server
-r--------  1 teamspeak teamspeak      455 Jan  1 22:57 ts3server.ini
-r--------  1 teamspeak teamspeak        0 Jan  5 05:55 .ts3server_license_accepted
-r-x------  1 teamspeak teamspeak      132 Dec 29 22:53 ts3server_minimal_runscript.sh
-rw-r--r--  1 teamspeak teamspeak        6 Jan  9 02:12 ts3server.pid
-r-x------  1 teamspeak teamspeak     2661 Dec 29 22:53 ts3server_startscript.sh
```

## Teamspeak init.d service
In order to easily start and stop the TS3 server I've opted for a int.d service. The script was originally uploaded on the TeamSpeak forums by [Wadjet](https://forum.teamspeak.com/members/159808-Wadjet?tab=aboutme) and the thread can be found [here](https://forum.teamspeak.com/threads/55383/?p=242834#post242834).

```SHELL
#!/bin/sh -e
### BEGIN INIT INFO
# Required-Start:    $network mysql
# Required-Stop:     $network
# Default-Start:     2 3 4 5
# Default-Stop:      0 1 6
# Short-Description: TeamSpeak3 Server Daemon
# Description:       Starts/Stops/Restarts the TeamSpeak Server Daemon
### END INIT INFO

set -e

PATH=/usr/local/sbin:/usr/local/bin:/sbin:/bin:/usr/sbin:/usr/bin
DESC="TeamSpeak3 Server"
NAME=teamspeak
USER=teamspeak
DIR=/usr/local/teamspeak3_server
OPTIONS=inifile=ts3server.ini license_accepted=1
DAEMON=$DIR/ts3server_startscript.sh
PIDFILE=$DIR/ts3server.pid
SCRIPTNAME=/etc/init.d/$NAME

# Gracefully exit if the package has been removed.
test -x $DAEMON || exit 0

sleep 2
sudo -u $USER $DAEMON $1 $OPTIONS
```

## TeamSpeak3 voice/text encryption
TS3 uses voice and text encryption between the client and server using AES.
I've found very little documentation about the encryption that TS3 uses but since TeamSpeak 3 is over 10 years old I think it's safe to assume that the encryption implementation is lacking to say the least.
There seems to be no client/server handshake so it's possible that the encryption is done using a hardcoded password or default certificate.
Still, it's better than no encryption at all.

To enable voice data encryption on the server, go to Manage Virtual Server > Security > Channel voice data encryption > "Globally On".
![voice data encryption settings](https://res.cloudinary.com/dbsfyc1ry/image/upload/v1578867112/carlgo11.com/posts/ts3_voip_encryption.png)

## Security Levels
Security levels in TeamSpeak3 have nothing with actual security to do.  
It's basically an anti-spam tool requiring the user to generate a new certificate on each new identity creation.  
This prevents spammers from creating hundreds of new identities and flooding a server/channel.

If you experience a lot of bots or spam users on your server you might want to set a security level between 10 and 23 but there's no real security risk if you decide to leave it at 0.
