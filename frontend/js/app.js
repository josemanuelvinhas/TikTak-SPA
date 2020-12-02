/* Main mvcblog-front script */

//load external resources
function loadTextFile(url) {
    return new Promise((resolve, reject) => {
        $.get({
            url: url,
            cache: true,
            dataType: 'text'
        }).then((source) => {
            resolve(source);
        }).fail(() => reject());
    });
}


// Configuration
var AppConfig = {
    backendServer: 'http://localhost/TikTak-SPA'
    //backendServer: '/mvcblog'
}

Handlebars.templates = {};
Promise.all([
    I18n.initializeCurrentLanguage('js/i18n'),
    loadTextFile('templates/components/main.hbs').then((source) =>
        Handlebars.templates.main = Handlebars.compile(source)),
    loadTextFile('templates/components/languaje.hbs').then((source) =>
        Handlebars.templates.languaje = Handlebars.compile(source)),
    loadTextFile('templates/components/user.hbs').then((source) =>
        Handlebars.templates.user = Handlebars.compile(source))
])
    .then(() => {
        $(() => {
            new MainComponent().start();
        });
    }).catch((err) => {
    alert('FATAL: could not start app ' + err);
});