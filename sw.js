let CACHE_NAME = 'static-v1';

self.addEventListener('install', function (event) {
    event.waitUntil(
        caches.open(CACHE_NAME).then(function (cache) {
            return cache.addAll([
                '/',
                'index',
                'tradutor',
                'referencias',
                'ajuda',
                'tutorials',
                'contato',
                '404',
                '/view/header.html',
                '/view/footer.html',
                '/css/footer.css',
                '/css/header.css',
                '/css/pagina.css',
                '/css/referencias.css',
                '/css/responsividade.css',
                '/images/logo-72.png',
                '/images/logo-192.png',
                '/images/logo-512.png',
                '/images/logo.png',
                '/images/fundo9.jpeg',
                '/images/facebook.png',
                '/images/copyright.png',
                '/images/github1.png',
                '/images/home.png',
                '/images/linkedin.png',
                '/images/membro1.jpg',
                '/images/membro2.jpg',
                '/images/menu-sand.png',
                '/files/legendas.json',
                '/files/linguagens.json',
                '/js/carregarAjuda.js',
                '/js/carregarTutorials.js',
                '/js/limpar.js',
            ]);
        })
    )
});

self.addEventListener('activate', function activator(event) {
    event.waitUntil(
        caches.keys().then(function (keys) {
            return Promise.all(keys
                .filter(function (key) {
                    return key.indexOf(CACHE_NAME) !== 0;
                })
                .map(function (key) {
                    return caches.delete(key);
                })
            );
        })
    );
});

self.addEventListener('fetch', function (event) {
    event.respondWith(
        caches.match(event.request).then(function (cachedResponse) {
            return cachedResponse || fetch(event.request);
        })
    );
});