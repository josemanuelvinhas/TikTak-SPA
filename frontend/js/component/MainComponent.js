class MainComponent extends Fronty.RouterComponent {
    constructor() {
        super('frontyapp', Handlebars.templates.main, 'maincontent');

        //Models
        this.userModel = new UserModel();

        this.indexModel = new VideosModel();
        this.searchModel = new VideosModel();
        this.homeModel = new VideosModel();

        this.videoModel = new VideoModel();

        //Service
        this.userService = new UserService();
        this.videosService = new VideosService();

        //Router
        super.setRouterConfig({
            index: {
                component: new IndexComponent(this.indexModel, this.userModel, this),
                title: 'Index'
            },
            home: {
                component: new HomePrivateComponent(this.userModel, this.homeModel, this),
                title: 'Home'
            },
            video: {
                component: new VideoComponent(this.userModel, this),
                title: 'Video'
            },
            profile: {
                component: new ProfileComponent(this.userModel, this),
                title: 'Profile'
            },
            search: {
                component: new SearchComponent(this.userModel, this.searchModel, this),
                title: 'Search'
            },
            defaultRoute: 'index'
        });

        Handlebars.registerHelper('currentPage', () => {
            return super.getCurrentPage();
        });

        //Childs
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
            this.videoModel.set(() => {
                this.videoModel.uploadErrors = '';
            });
            this.userModel.logout();
            this.userService.logout();
            this.goToPage("index");
        });

        return userbar;
    }

    _createModalLoginUserComponent() {
        var modal_login = new Fronty.ModelComponent(Handlebars.templates.modal_login, this.userModel, 'modallogin');

        modal_login.addEventListener('click', '#loginbutton', (event) => {
            this.userService.login($('#formLoginInAlias').val(), $('#formLoginInPassword').val())
                .then(() => {
                    this.userModel.setLoggeduser($('#formLoginInAlias').val());
                    $('#modallogin').modal('hide');
                    this.userModel.set((model) => {
                        model.loginError = '';
                    });
                    this.userModel.set((model) => {
                        model.registerErrors = {};
                    });
                    this.goToPage("home");
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

    _createModalUploadComponent() {
        var modal_upload = new Fronty.ModelComponent(Handlebars.templates.modal_upload, this.videoModel, 'modalupload');
        modal_upload.addModel('user', this.userModel);

        this.addEventListener('click', '#uploadbutton', (ev) => {
            ev.preventDefault();

            var formData = new FormData(document.getElementById("uploadform"));
            this.videosService.uploadvideo(formData)
                .then((response) => {
                    $('#modalupload').modal('hide');
                    $('#formUploadInVideo').val('');
                    $('#formUploadInDescripcion').val('');
                    this.videoModel.set(() => {
                        this.videoModel.uploadErrors = '';
                    });
                    this.goToPage('video?id=' + response.id_video);
                })
                .fail((xhr, errorThrown, statusText) => {
                    if (xhr.status == 400) {
                        this.videoModel.set(() => {
                            this.videoModel.uploadErrors = xhr.responseJSON;
                        });
                    } else {
                        alert('an error has occurred during request: ' + statusText + '.' + xhr.responseText);
                    }
                });
        });

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