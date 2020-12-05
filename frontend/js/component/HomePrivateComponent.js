class HomePrivateComponent extends Fronty.ModelComponent {
    constructor(userModel, homeModel, router) {
        super(Handlebars.templates.home_private, homeModel, "maincontent", null);

        //Models
        this.homeModel = homeModel; //Instance of VideosModel
        this.userModel = userModel;
        this.addModel('user', this.homeModel);

        this.page = 0;

        //Router
        this.router = router;

        //Service
        this.homeService = new HomeService();
        this.videosServices = new VideosService();
        this.followerServices = new FollowerService();

        //Childs
        this.addChildComponent(this._createTopUsersBarComponent());
        this.addChildComponent(this._createTrendsBarComponent());

        this.addEventListener('submit', '#search-top', () => {
            var value = $('#hashtag-top').val();
            this.router.goToPage('search?hashtag=' + value);
        });

        this.addEventListener('submit', '#search-right', () => {
            var value = $('#hashtag-right').val();
            this.router.goToPage('search?hashtag=' + value);
        });

    }

    onStart() {
        var page = this.router.getRouteQueryParam('page');
        this.updateVideos(page);
    }

    updateVideos(page) {

        if (page == null) {
            page = 0;
        }

        if (this.userModel.isLogged === false) {
            this.router.goToPage("index");
        } else {

            this.page = page;

            this.homeService.getPrivateHomePage(this.userModel.currentUser, page).then((data) => {

                this.homeModel.setVideos(data['videos']);
                this.homeModel.setTopUsers(data['topUsers']);
                this.homeModel.setTrends(data['trends']);
                this.homeModel.setPage(page);
                this.homeModel.setLikes(data['likes']);
                this.homeModel.setFollowings(data['followings']);


                if (data['next'] !== undefined) {
                    this.homeModel.setNext(data['next']);
                } else {
                    this.homeModel.setNext(false);
                }

                if (data['previous'] !== undefined) {
                    this.homeModel.setPrevious(data['previous']);
                } else {
                    this.homeModel.setPrevious(false);
                }
            }).fail((xhr, errorThrown, statusText) => {
                if (xhr.status == 400) {
                    this.router.goToPage("home");
                } else {
                    alert('an error has occurred during request: ' + statusText + '.' + xhr.responseText);
                }
            });
        }
    }


    _createTopUsersBarComponent() {
        var topUsers = new Fronty.ModelComponent(Handlebars.templates.topUsers, this.homeModel, 'topUsers');

        return topUsers;
    }

    _createTrendsBarComponent() {
        var trends = new Fronty.ModelComponent(Handlebars.templates.trends, this.homeModel, 'trends');

        return trends;
    }

    // Override
    createChildModelComponent(className, element, id, modelItem) {
        return new HomePrivateRowComponent(modelItem, this.homeModel, this.userModel, this.router, this);
    }


}


class HomePrivateRowComponent extends Fronty.ModelComponent {
    constructor(videoModel, homeModel, userModel, router, homePrivateComponent) {
        super(Handlebars.templates.home_private_row, videoModel, null, null);

        this.homePrivateComponent = homePrivateComponent;

        this.userModel = userModel;
        this.addModel('user', userModel);
        this.homeModel = homeModel;
        this.addModel('home', homeModel);

        this.router = router;

        this.addEventListener('click', '.action-like', (event) => {
            var id = event.target.getAttribute('item');
            this.homePrivateComponent.videosServices.like(id)
                .fail(() => {
                    alert('Error: like')
                })
                .always(() => {
                    this.homePrivateComponent.updateVideos(this.homePrivateComponent.page);
                });
        });

        this.addEventListener('click', '.action-dislike', (event) => {
            var id = event.target.getAttribute('item');
            this.homePrivateComponent.videosServices.dislike(id)
                .fail(() => {
                    alert('Error: dislike')
                })
                .always(() => {
                    this.homePrivateComponent.updateVideos(this.homePrivateComponent.page);
                });
        });

        this.addEventListener('click', '.action-follow', (event) => {
            var username = event.target.getAttribute('item');
            this.homePrivateComponent.followerServices.follow(username)
                .fail(() => {
                    alert('Error: follow')
                })
                .always(() => {
                    this.homePrivateComponent.updateVideos(this.homePrivateComponent.page);
                });
        });

        this.addEventListener('click', '.action-unfollow', (event) => {
            var username = event.target.getAttribute('item');
            this.homePrivateComponent.followerServices.unfollow(username)
                .fail(() => {
                    alert('Error: follow')
                })
                .always(() => {
                    this.homePrivateComponent.updateVideos(this.homePrivateComponent.page);
                });
        });

        this.addEventListener('load', '.action-popover', (event) => {
            var id = event.target.getAttribute('id');
            $('#' + id).popover();
        });

        this.addEventListener('click', '.action-share', (event) => {
            var item = event.target.getAttribute('item');
            var aux = document.createElement("input");

            var textoACopiar = AppConfig.frontendServer + '/#video?id=' + item;
            aux.setAttribute("value", textoACopiar);
            document.body.appendChild(aux);
            aux.select();
            document.execCommand("copy");
            document.body.removeChild(aux);
        });

        this.addEventListener('mouseover', '.action-share', (event) => {
            var item = event.target.getAttribute('item');
            $('#tooltip-'+item).tooltip("show");
        });

    }

}