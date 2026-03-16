# HTTPS Setup (XAMPP, Multi-Device Video Call)

Use this to make camera/mic work on other devices.

## 1) Generate a trusted certificate (recommended: `mkcert`)

Run in PowerShell:

```powershell
winget install FiloSottile.mkcert
mkcert -install
```

Generate cert for your LAN IP (replace `192.168.10.4` if needed):

```powershell
New-Item -ItemType Directory -Force C:\xampp\apache\conf\ssl\capstone | Out-Null
Set-Location C:\xampp\apache\conf\ssl\capstone
mkcert 192.168.10.4 localhost 127.0.0.1 ::1
```

This creates files like:
- `192.168.10.4+3.pem`
- `192.168.10.4+3-key.pem`

## 2) Enable Apache SSL includes

Open `C:\xampp\apache\conf\httpd.conf` and ensure these are enabled (not commented):

```apache
LoadModule ssl_module modules/mod_ssl.so
LoadModule socache_shmcb_module modules/mod_socache_shmcb.so
Include conf/extra/httpd-ssl.conf
Include conf/extra/httpd-vhosts.conf
```

## 3) Add HTTPS virtual host

Open `C:\xampp\apache\conf\extra\httpd-vhosts.conf` and add:

```apache
<VirtualHost *:443>
    ServerName 192.168.10.4
    DocumentRoot "C:/xampp/htdocs/capstone_system/public"

    <Directory "C:/xampp/htdocs/capstone_system/public">
        AllowOverride All
        Require all granted
    </Directory>

    SSLEngine on
    SSLCertificateFile "C:/xampp/apache/conf/ssl/capstone/192.168.10.4+3.pem"
    SSLCertificateKeyFile "C:/xampp/apache/conf/ssl/capstone/192.168.10.4+3-key.pem"
</VirtualHost>
```

## 4) Restart Apache

Restart Apache from XAMPP Control Panel.

Then open:

```text
https://192.168.10.4/capstone_system/public
```

## 5) Enable HTTPS mode in Laravel

After SSL works, set these in `.env`:

```env
APP_URL=https://192.168.10.4/capstone_system/public
APP_FORCE_HTTPS=true
SESSION_SECURE_COOKIE=true
```

Then run:

```powershell
php artisan optimize:clear
```

## 6) Trust certificate on other devices

For phone/tablet/laptop clients, install/trust the `mkcert` root CA so browser treats HTTPS as secure.

Get root CA path:

```powershell
mkcert -CAROOT
```

Copy `rootCA.pem` from that folder to each device and install it as trusted certificate.

Without trust, camera/mic may still be blocked by browser security.
