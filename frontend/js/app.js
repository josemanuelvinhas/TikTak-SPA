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
    loadTextFile('templates/components/language.hbs').then((source) =>
        Handlebars.templates.language = Handlebars.compile(source)),
    loadTextFile('templates/components/user.hbs').then((source) =>
        Handlebars.templates.user = Handlebars.compile(source)),
    loadTextFile('templates/components/modal_login.hbs').then((source) =>
        Handlebars.templates.modal_login = Handlebars.compile(source)),
    loadTextFile('templates/components/modal_register.hbs').then((source) =>
        Handlebars.templates.modal_register = Handlebars.compile(source)),
    loadTextFile('templates/components/home_public.hbs').then((source) =>
        Handlebars.templates.home_public = Handlebars.compile(source)),
    loadTextFile('templates/components/video.hbs').then((source) =>
        Handlebars.templates.video = Handlebars.compile(source)),
    loadTextFile('templates/components/topUsers.hbs').then((source) =>
        Handlebars.templates.topUsers = Handlebars.compile(source))

])
    .then(() => {
        $(() => {
            new MainComponent().start();
        });
    }).catch((err) => {
    alert('FATAL: could not start app ' + err);
});