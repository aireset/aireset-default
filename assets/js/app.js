document.addEventListener('DOMContentLoaded', function () {
    if (document.getElementById('aireset-vue-app')) {
        new Vue({
            el: '#aireset-vue-app',
            data: {
                mensagem: 'Bem-vindo ao Aireset Plugin!',
            },
        });
    }
});
