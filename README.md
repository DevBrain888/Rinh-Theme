<p align="center">
      <img src="https://i.ibb.co/sdnxHryH/logo-RINH-theme.png" alt="Project logo" width="400">
</p>

<p align="center">
  <a href="https://github.com/DevBrain888/Rinh-Theme/stargazers"><img src="https://img.shields.io/github/stars/DevBrain888/Rinh-Theme" alt="Stars Badge"/></a>
  <a href="https://github.com/DevBrain888/Rinh-Theme/network/members"><img src="https://img.shields.io/github/forks/DevBrain888/Rinh-Theme" alt="Forks Badge"/></a>
  <a href="https://github.com/DevBrain888/Rinh-Theme/pulls"><img src="https://img.shields.io/github/issues-pr/DevBrain888/Rinh-Theme" alt="Pull Requests Badge"/></a>
  <a href="https://github.com/DevBrain888/Rinh-Theme/issues"><img src="https://img.shields.io/github/issues/DevBrain888/Rinh-Theme" alt="Issues Badge"/></a>
  <a href="https://github.com/DevBrain888/Rinh-Theme/graphs/contributors"><img alt="GitHub contributors" src="https://img.shields.io/github/contributors/DevBrain888/Rinh-Theme?color=2b9348"></a>
  <a href="https://github.com/DevBrain888/Rinh-Theme/blob/master/LICENSE"><img src="https://img.shields.io/github/license/DevBrain888/Rinh-Theme?color=2b9348" alt="License Badge"/></a>

</p>
<p align="center">
  <img src="https://img.shields.io/badge/python_3.12%2B-blue" alt="Python Version">
  <img src="https://img.shields.io/badge/version-v_1.0.0-violet" alt="Rinh-Theme Version">
</p>

## About

приложение, где администратор загружает темы и студентов, алгоритм рандомно раскидывает темы по студентам, либо староста может вручную закреплять темы за студентами своей группы.

## Documentation

### Local launch (for development)

Вариант A — Polling (просто и без внешнего URL):

Вариант B — Локальный Webhook через туннель (ngrok/Cloudflare Tunnel):

### Продакшен на Ubuntu VPS (Nginx + HTTPS + Webhook)

Требования: Ubuntu 22.04+, домен, публичный IPv4.

1) Базовая подготовка сервера


2) Клонирование проекта и зависимости


3) DNS
— У регистратора создайте A‑запись `site.example.com` → IP вашего VPS. Проверьте `dig +short site.example.com`.

4) Конфигурация приложения (.env)


5) Проверка приложения локально на сервере
```bash
python main.py
curl -I http://127.0.0.1:8000/health   # ожидается 200 OK
# Остановите Ctrl+C перед настройкой Nginx/HTTPS
```

6) Nginx (reverse‑proxy на 127.0.0.1:8000)
```bash
sudo bash -lc 'cat > /etc/nginx/sites-available/rinhtheme << "CONF"
server {
    listen 80;
    server_name site.example.com;

    location / {
        proxy_pass http://127.0.0.1:8000;
        proxy_set_header Host              $host;
        proxy_set_header X-Real-IP         $remote_addr;
        proxy_set_header X-Forwarded-For   $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
CONF'
sudo ln -sf /etc/nginx/sites-available/rinhtheme /etc/nginx/sites-enabled/rinhtheme
sudo nginx -t && sudo systemctl reload nginx
```

7) HTTPS (Let’s Encrypt)
```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d bot.example.com --redirect --agree-tos -m you@email.com -n
```

8) Запуск приложения в прод и установка webhook (разрешаем callback_query)
```bash
# В venv
source /opt/RinhTheme/.venv/bin/activate
python /opt/RinhTheme/main.py

# В другом окне/терминале
curl -I https://site.example.com/health
```
## 🚀 Distribute

### 📦 Минимальные требования
- Python 3.12+
- pip
- git
- curl
- python3-venv (на Ubuntu)
- Nginx (для продa)
- Certbot + плагин nginx (для HTTPS в проде)
- UFW (фаервол на Ubuntu, по желанию)
- ngrok или Cloudflare Tunnel для локальной разработки

### 🔧 Troubleshooting

## Contributors

Спасибо этим замечательным людям:

<!-- ALL-CONTRIBUTORS-LIST:START - Do not remove or modify this section -->
<!-- prettier-ignore -->
| [<img src="https://github.com/DevBrain888.png" width="75px;"/><br /><sub><b>DevBrain888</b></sub>]()<br />[🎨](#design-DevBrain888) [💻](https://github.com/DevBrain888/Rinh-Theme/commits?author=DevBrain888) [📖](https://github.com/DevBrain888/Rinh-Theme/commits?author=DevBrain888) [🤔](#ideas-DevBrain888) | [<img src="https://github.com/ZeroterKnows.png" width="75px;"/><br /><sub><b>ZeroterKnows</b></sub>]()<br />[🐛](https://github.com/ZeroterKnows/Rinh-Theme/issues?q=author%ZeroterKnows) [💻](https://github.com/ZeroterKnows/Rinh-Theme/commits?author=ZeroterKnows) |
| :---: | :---: |
<!-- ALL-CONTRIBUTORS-LIST:END -->


## License

[![GPLv3](https://img.shields.io/badge/License-GPLv3-blue.svg)](https://www.gnu.org/licenses/gpl-3.0)

Этот проект лицензирован в соответствии с [GNU General Public License v3.0](https://www.gnu.org/licenses/gpl-3.0).

Насколько это возможно в соответствии с законом, все авторы согласились распространять эту работу под лицензией GNU GPL версии 3.0.

