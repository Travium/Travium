# Travium — Travian T4.5 Private Server

[![Status](https://img.shields.io/badge/status-production-green)](https://travium.net/)
[![OS](https://img.shields.io/badge/Ubuntu-22.04%20%7C%2024.04-blue)](#supported-os)
[![OS](https://img.shields.io/badge/Debian-11%20%7C%2012%20%7C%2013-blue)](#supported-os)
[![License](https://img.shields.io/badge/license-MIT-lightgrey)](#license)
[![Discord](https://img.shields.io/badge/chat-Discord-5865F2)](https://discord.gg/TCjyvcctDg)

A fast, stable Travian T4.5 clone with a one-click installer in a single command.

Join our discord:
**Discord:** [https://discord.gg/TCjyvcctDg](https://discord.gg/TCjyvcctDg)

## Features

* 1-click automated install and configuration
* Works on fresh VMs/VPS, no prior setup required
* Opinionated, production-ready defaults
* CloudPanel integration for easy management and faster loading times
* Installer command generator: [https://init.travium.net/](https://init.travium.net/)

---

## Quick start

1. **Prepare a clean server**

   * Pick a supported OS (below), log in as `root`.
2. **Point DNS or hosts**

   * Set A records to your server IP (examples below).
3. **Run the installer**

   ```bash
   bash <(curl -skL https://init.travium.net/install.sh) \
       --domain example.com
   ```
4. **Finish setup**

   * Open the CloudPanel link shown at the end of the install.
   * Create a database.
   * Open the installer URL, fill details, click **Run Installer**.

> Prefer a prefilled command? Use the generator: [https://init.travium.net/](https://init.travium.net/)

---

## Supported OS

* 🐧 Ubuntu 24.04 LTS
* 🐧 Ubuntu 22.04 LTS
* 🐧 Debian 13 LTS
* 🐧 Debian 12 LTS
* 🐧 Debian 11 LTS

> Important: Use a fresh machine.

---

## Requirements

* Clean VM/VPS with a supported OS
* Root access
* A domain you control
* Basic ability to copy-paste a command

---

## DNS / hosts setup

Point everything to your server IP. Replace `12.13.14.15` and `example.com`.

```
12.13.14.15 example.com www.example.com
12.13.14.15 server1.example.com server2.example.com     # add more game worlds as needed
12.13.14.15 api.example.com
12.13.14.15 cdn.example.com
12.13.14.15 install.example.com
12.13.14.15 voting.example.com
12.13.14.15 payment.example.com
```

If you’re just testing, you can use your local hosts file with the same lines.

---

## What the installer gives you

* CloudPanel ready to use
* Web vhosts, PHP, DB engine, and core services configured
* Game server scaffolding and routes
* Secure defaults and sensible limits

At the end you’ll see:

* **CloudPanel URL** and credentials
* **Installer URL** to finalize the game configuration

---

## Post-install checklist

1. Log in to **CloudPanel**
   Create a database:
   [https://www.cloudpanel.io/docs/v2/frontend-area/databases/](https://www.cloudpanel.io/docs/v2/frontend-area/databases/)

2. Visit the **Installer URL**
   Fill the form with your DB details, and any options you want.
   Click **Run Installer** and let it finish.

3. Open your domain and confirm the game is live.

---

## Contributing

* Fork, branch, commit
* Keep PRs focused and tested

---

## Credits

Core authors and contributors will be listed here.
If you shipped a fix or feature, you’ll get your shout-out.

---

## License

MIT
