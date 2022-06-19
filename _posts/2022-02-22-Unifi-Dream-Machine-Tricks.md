---
category: internet security
layout: post
image:
  src: https://res.cloudinary.com/dbsfyc1ry/image/upload/v1655594243/carlgo11.com/posts/UDM-Pro.jpg
  author: Ubiquiti
  url: https://ui.com/
---

I've used Ubiquiti/UniFi products for a couple of years now and have during that time I've saved some useful commands and directories/files that I thought I'd share.  
All of these commands are tested to work with the UniFi UDM/UDM Pro. Some of the commands also work on other UniFi products but your mileage may vary.

__Non of the tricks outlined in this post should result in your device being bricked but Ubiquiti doesn't officially support tinkering with UniFi devices through SSH and it may result in data loss.__

## Commands

|Command|Description|
|-------|-----------|
|unfi-os shell|Open a shell to the UniFi-OS Ubuntu container|
|unifi-os restart| Restart all UniFi apps (Networks/Protect/Access etc)|
|ubnt-device-info summary|System info|
|set default|Factory reset device|
|sensors|Show fan RPMs & temperatures|
|tcpdump|Packet capture|

I've also found [this reddit post](https://www.reddit.com/r/Ubiquiti/comments/k2g8sk/) useful in the past.

## Paths

|Path|Type|Description|
|----|----|-----------|
|/mnt/data/unifi-os/unifi-core/config/unifi-core.*|Persistent|Location of certificate/key
|/mnt/data/unifi-os/unifi-core/logs/|Non-persistent|Location of UnFi app logs|
|/var/log/messages|Non-persistent|UnFi Network output log|
|/tmp/udapi.cfg|Non-persistent|UDM configuration|

## Useful third party projects

UniFi systems are more limited in their capabilities out of the box than some other networking vendors but the amazing community more than makes up for that!  
Here are some useful open source projects that I've used on my UDM Pro:

* [Split-VPN](https://github.com/peacey/split-vpn) - VPN client. Connect your entire LAN to any commercial VPN service!

* [UDM / UDMPro Boot Script](https://github.com/boostchicken-dev/udm-utilities/tree/master/on-boot-script) - Environment setup tool. Allows for changes in the alpine environment to persist through reboots.

* [udm-le](https://github.com/kchristensen/udm-le) - Let's Encrypt integration. For automated TLS certificates on your UDM/UDMP.

* [UniFi-API-client](https://github.com/Art-of-WiFi/UniFi-API-client) - UniFi API PHP client. Useful for external guest portals to integrate with UniFi Network.
