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
    loadTextFile('templates/components/modal_upload.hbs').then((source) =>
        Handlebars.templates.modal_upload = Handlebars.compile(source)),
    loadTextFile('templates/components/index.hbs').then((source) =>
        Handlebars.templates.index = Handlebars.compile(source)),
    loadTextFile('templates/components/index_row.hbs').then((source) =>
        Handlebars.templates.index_row = Handlebars.compile(source)),
    loadTextFile('templates/components/home_private.hbs').then((source) =>
        Handlebars.templates.home_private = Handlebars.compile(source)),
    loadTextFile('templates/components/home_private_row.hbs').then((source) =>
        Handlebars.templates.home_private_row = Handlebars.compile(source)),
    loadTextFile('templates/components/video.hbs').then((source) =>
        Handlebars.templates.video = Handlebars.compile(source)),
    loadTextFile('templates/components/topUsers.hbs').then((source) =>
        Handlebars.templates.topUsers = Handlebars.compile(source)),
    loadTextFile('templates/components/trends.hbs').then((source) =>
        Handlebars.templates.trends = Handlebars.compile(source)),
    loadTextFile('templates/components/profile.hbs').then((source) =>
        Handlebars.templates.profile = Handlebars.compile(source)),
    loadTextFile('templates/components/search.hbs').then((source) =>
        Handlebars.templates.search = Handlebars.compile(source)),
    loadTextFile('templates/components/search_row.hbs').then((source) =>
        Handlebars.templates.search_row = Handlebars.compile(source))

])
    .then(() => {
        $(() => {
            new MainComponent().start();
        });
    }).catch((err) => {
    alert('FATAL: could not start app ' + err);
});