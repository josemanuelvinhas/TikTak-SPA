class IndexComponent extends Fronty.ModelComponent {
    constructor(videosModel, userModel, router) {
        super(Handlebars.templates.index, videosModel, "maincontent", null);

        //Models
        this.videosModel = videosModel;
        this.userModel = userModel;
        this.page = 0;
        this.addModel('user', userModel);

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

        this.page = page;

        this.homeService.getPublicHomePage(page).then((data) => {

            this.videosModel.setVideos(data['videos']);
            this.videosModel.setTopUsers(data['topUsers']);
            this.videosModel.setTrends(data['trends']);
            this.videosModel.setPage(page);


            if (data['likes'] !== undefined) {
                this.videosModel.setLikes(data['likes']);
            }else{
                this.videosModel.setLikes([]);
            }

            if (data['followings'] !== undefined) {
                this.videosModel.setFollowings(data['followings']);
            }else{
                this.videosModel.setFollowings([]);
            }

            if (data['next'] !== undefined) {
                this.videosModel.setNext(data['next']);
            }else{
                this.videosModel.setNext(false);
            }

            if (data['previous'] !== undefined) {
                this.videosModel.setPrevious(data['previous']);
            }else{
                this.videosModel.setPrevious(false);
            }
        }).fail((xhr, errorThrown, statusText) => {
            if (xhr.status == 400) {
                this.router.goToPage("index");
            } else {
                alert('an error has occurred during request: ' + statusText + '.' + xhr.responseText);
            }
        });


    }


    _createTopUsersBarComponent() {
        var topUsers = new Fronty.ModelComponent(Handlebars.templates.topUsers, this.videosModel, 'topUsers');

        return topUsers;
    }

    _createTrendsBarComponent() {
        var trends = new Fronty.ModelComponent(Handlebars.templates.trends, this.videosModel, 'trends');

        return trends;
    }

    // Override
    createChildModelComponent(className, element, id, modelItem) {
        return new IndexRowComponent(modelItem, this.videosModel, this.userModel, this.router, this);
    }


}


class IndexRowComponent extends Fronty.ModelComponent {
    constructor(videoModel, videosModel, userModel, router, indexComponent) {
        super(Handlebars.templates.index_row, videoModel, null, null);

        this.indexComponent = indexComponent;

        this.userModel = userModel;
        this.addModel('user', userModel);
        this.videosModel = videosModel;
        this.addModel('home', videosModel);

        this.router = router;


        this.addEventListener('click', '#favLogin', () => {
            $('#modallogin').modal('show');
        });

        this.addEventListener('click', '#followLogin', () => {
            $('#modallogin').modal('show');
        });

        this.addEventListener('click', '.action-like', (event) => {
            var id = event.target.getAttribute('item');
            this.indexComponent.videosServices.like(id)
                .fail(() => {
                    alert('Error: like')
                })
                .always(() => {
                    this.indexComponent.updateVideos(this.indexComponent.page);
                });
        });

        this.addEventListener('click', '.action-dislike', (event) => {
            var id = event.target.getAttribute('item');
            this.indexComponent.videosServices.dislike(id)
                .fail(() => {
                    alert('Error: dislike')
                })
                .always(() => {
                    this.indexComponent.updateVideos(this.indexComponent.page);
                });
        });

        this.addEventListener('click', '.action-follow', (event) => {
            var username = event.target.getAttribute('item');
            this.indexComponent.followerServices.follow(username)
                .fail(() => {
                    alert('Error: follow')
                })
                .always(() => {
                    this.indexComponent.updateVideos(this.indexComponent.page);
                });
        });

        this.addEventListener('click', '.action-unfollow', (event) => {
            var username = event.target.getAttribute('item');
            this.indexComponent.followerServices.unfollow(username)
                .fail(() => {
                    alert('Error: unfollow')
                })
                .always(() => {
                    this.indexComponent.updateVideos(this.indexComponent.page);
                });
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