<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<title>metaframe</title>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Google+Sans:ital,opsz,wght@0,17..18,400..700;1,17..18,400..700&display=swap" rel="stylesheet">

<style>
html, body {
    margin: 0;
    height: 100%;
    background: black;
    overflow: hidden;
    font-family: 'Google Sans';
}

/* ===== BACKGROUND SLIDES ===== */
.swiper {
    width: 100%;
    height: 100%;
    position: absolute;
    inset: 0;
}

.swiper-slide img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transform: scale(1.1);
    transition: transform 8s linear;
}

/* ===== DARK OVERLAY ===== */
.overlay {
    position: absolute;
    inset: 0;
    background: radial-gradient(
        circle at bottom left,
        rgba(0,0,0,0.35),
        rgba(0,0,0,0.75)
    );
    z-index: 5;
}

/* ===== LEFT HUD (weather) ===== */
.hud-left {
    position: absolute;
    bottom: 24px;
    left: 24px;
    z-index: 20;
    color: white;
    display: flex;
    align-items: center;
    gap: 14px;
}

.icon {
    width: 56px;
    height: 56px;
    opacity: 0.9;
}

.temp {
    font-size: 64px;
    font-weight: 300;
}

/* ===== RIGHT CLOCK (Nest Hub style) ===== */
.hud-right {
    position: absolute;
    bottom: 24px;
    right: 24px;
    z-index: 20;
    color: white;
    text-align: right;
}

.time {
    font-size: 56px;
    font-weight: 300;
}

.date {
    font-size: 16px;
    opacity: 0.7;
}

/* debug */
.debug {
    position: absolute;
    top: 10px;
    left: 10px;
    font-size: 12px;
    color: #aaa;
    z-index: 30;
}
</style>
</head>

<body x-data="frame()" x-init="boot()">

<!-- DEBUG -->
<div class="debug" x-text="debug"></div>

<!-- BACKGROUND -->
<div class="swiper">
    <div class="swiper-wrapper">
        <template x-for="p in photos">
            <div class="swiper-slide">
                <img :src="p">
            </div>
        </template>
    </div>
</div>

<!-- OVERLAY -->
<div class="overlay"></div>

<!-- LEFT: WEATHER -->
<div class="hud-left">
    <img class="icon" :src="weatherIcon">
    <div class="temp" x-text="temperature + '°'"></div>
</div>

<!-- RIGHT: CLOCK -->
<div class="hud-right">
    <div class="time" x-text="time"></div>
    <div class="date" x-text="date"></div>
</div>

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<script>
function frame() {
    return {
        photos: [],
        swiper: null,
        debug: '',

        temperature: '--',
        weatherIcon: 'assets/icons/cloudy.svg',

        time: '',
        date: '',

        boot() {
            this.clock();
            this.load();
            this.loadWeather();
        },

        clock() {
            setInterval(() => {
                const now = new Date();

                this.time = now.toLocaleTimeString([], {
                    hour: '2-digit',
                    minute: '2-digit'
                });

                this.date = now.toLocaleDateString();
            }, 1000);
        },

        async load() {
            try {
                const res = await fetch('./api.php');
                const data = await res.json();

                this.photos = data.photos?.length
                    ? data.photos
                    : this.fallback();

                this.debug = `OK | P:${this.photos.length}`;

                this.initSwiper();

            } catch (e) {
                this.debug = "API FAIL";

                this.photos = this.fallback();
                this.initSwiper();
            }
        },

        fallback() {
            return [
                "https://picsum.photos/1920/1080?random=41",
                "https://picsum.photos/1920/1080?random=42"
            ];
        },

        initSwiper() {
            this.$nextTick(() => {
                if (!window.Swiper || this.swiper) return;

                this.swiper = new Swiper('.swiper', {
                    loop: true,
                    effect: 'fade',
                    fadeEffect: { crossFade: true },
                    speed: 2000,
                    autoplay: {
                        delay: 7000
                    }
                });
            });
        },

        async loadWeather() {
            try {
                const res = await fetch('./weather.php');
                const w = await res.json();

                this.temperature = Math.round(w.temp ?? 0);
                this.weatherIcon = this.mapWeather(w.code);

            } catch (e) {
                this.temperature = '--';
                this.weatherIcon = 'assets/icons/cloudy.svg';
            }
        },

        mapWeather(code) {
            const map = {
                0: 'clear_night.svg',
                1: 'mostly_sunny.svg',
                2: 'partly_cloudy.svg',
                3: 'cloudy.svg',

                45: 'cloudy.svg',
                48: 'cloudy.svg',

                51: 'drizzle.svg',
                53: 'drizzle.svg',
                55: 'drizzle.svg',

                61: 'rain_with_cloudy.svg',
                63: 'heavy_rain.svg',
                65: 'heavy_rain.svg',

                71: 'snow_with_cloudy.svg',
                73: 'snow_with_cloudy.svg',
                75: 'heavy_snow.svg',

                80: 'rain_with_cloudy.svg',
                81: 'heavy_rain.svg',
                82: 'heavy_rain.svg',

                95: 'thunderstorms.svg',
                96: 'strong_thunderstorms.svg',
                99: 'strong_thunderstorms.svg'
            };

            return `assets/icons/${map[code] || 'cloudy.svg'}`;
        }
    }
}
</script>

</body>
</html>