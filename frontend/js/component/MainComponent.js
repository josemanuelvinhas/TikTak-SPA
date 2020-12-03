class MainComponent extends Fronty.RouterComponent {
    constructor() {
        super('frontyapp', Handlebars.templates.main, 'maincontent');

        // models instantiation
        // we can instantiate models at any place
        this.userModel = new UserModel();
        this.homeModel = new HomeModel();

        this.userService = new UserService();

        super.setRouterConfig({
            home: {
                component: new HomePublicComponent(this.homeModel, this.userModel, this),
                title: 'Posts'
            },
            defaultRoute: 'home'
        });

        Handlebars.registerHelper('currentPage', () => {
            return super.getCurrentPage();
        });

        this.addChildComponent(this._createUserBarComponent());
        this.addChildComponent(this._createModalLoginUserComponent());
        this.addChildComponent(this._createModalRegisterUserComponent());
        this.addChildComponent(this._createModalUploadComponent());
        this.addChildComponent(this._createLanguageComponent());

    }

    start() {
        // override the start() function in order to first check if there is a logged user
        // in sessionStorage, so we try to do a relogin and start the main component
        // only when login is checked
        this.userService.loginWithSessionData()
            .then((logged) => {
                if (logged != null) {
                    this.userModel.setLoggeduser(logged);
                }
                super.start(); // now we can call start
            });
    }

    _createUserBarComponent() {
        var userbar = new Fronty.ModelComponent(Handlebars.templates.user, this.userModel, 'userbar');

        userbar.addEventListener('click', '#buttonlogin', () => {
            $('#modallogin').modal('show');
        });

        userbar.addEventListener('click', '#buttonregister', () => {
            $('#modalregister').modal('show');
        });

        userbar.addEventListener('click', '#buttonupload', () => {
            $('#modalupload').modal('show');
        });

        userbar.addEventListener('click', '#buttonlogout', () => {
            this.userModel.logout();
            this.userService.logout();
        });

        return userbar;
    }

    _createModalLoginUserComponent() {
        var modal_login = new Fronty.ModelComponent(Handlebars.templates.modal_login, this.userModel, 'modallogin');

        modal_login.addEventListener('click', '#loginbutton', (event) =>{
            this.userService.login($('#formLoginInAlias').val(),$('#formLoginInPassword').val())
                .then(()=> {
                    this.goToPage('home');
                    this.userModel.setLoggeduser($('#formLoginInAlias').val());
                    $('#modallogin').modal('hide');
                })
                .catch((error) => {
                    this.userModel.set((model) => {
                        model.loginError = error.responseText;
                    });
                    this.userModel.logout();
                });
        });


        return modal_login;
    }

    _createModalRegisterUserComponent() {
        var modal_register = new Fronty.ModelComponent(Handlebars.templates.modal_register, this.userModel, 'modalregister');

        this.addEventListener('click', '#registerbutton', () => {
            this.userService.register({
                username: $('#formRegisterInAlias').val(),
                password: $('#formRegisterInPassword').val(),
                email: $('#formRegisterInEmail').val()
            })
                .then(() => {
                    $('#modalregister').modal('hide');
                    $('#modallogin').modal('show');

                    this.userModel.set((model) => {
                        model.registerErrors = {};
                        model.registerMode = false;
                    });
                })
                .fail((xhr, errorThrown, statusText) => {
                    if (xhr.status == 400) {
                        this.userModel.set(() => {
                            this.userModel.registerErrors = xhr.responseJSON;
                        });
                    } else {
                        alert('an error has occurred during request: ' + statusText + '.' + xhr.responseText);
                    }
                });
        });

        return modal_register;
    }

    _createModalUploadComponent(){
        var modal_upload = new Fronty.ModelComponent(Handlebars.templates.modal_upload, this.userModel, 'modalupload');


        return modal_upload;
    }

    _createLanguageComponent() {
        var languageComponent = new Fronty.ModelComponent(Handlebars.templates.language, this.routerModel, 'languagecontrol');
        // language change links
        languageComponent.addEventListener('click', '#englishlink', () => {
            I18n.changeLanguage('default');
            document.location.reload();
        });

        languageComponent.addEventListener('click', '#spanishlink', () => {
            I18n.changeLanguage('es');
            document.location.reload();
        });

        languageComponent.addEventListener('click', '#galicianlink', () => {
            I18n.changeLanguage('gl');
            document.location.reload();
        });

        return languageComponent;
    }
}