class HomePrivateComponent extends Fronty.ModelComponent {
    constructor(userModel, homeModel, router) {
        super(Handlebars.templates.home_private, homeModel, "maincontent", null);

        //Models
        this.homeModel = homeModel;
        this.userModel = userModel;
        this.addModel('user', this.homeModel);


        //Router
        this.router = router;

        //Service
        this.homeService = new HomeService();
        this.videosServices = new VideosService();
        this.followerServices = new FollowerService();

        //Childs
        this.addChildComponent(this._createTopUsersBarComponent());
        this.addChildComponent(this._createTrendsBarComponent());
    }

    onStart() {
        var page = this.router.getRouteQueryParam('page');
        this.updateVideos(page);
    }

    updateVideos(page) {

        if (page == null) {
            page = 0;
        }

        this.homeService.getPrivateHomePage(this.userModel.currentUser, page).then((data) => {

            this.homeModel.setVideos(data['videos']);
            this.homeModel.setTopUsers(data['topUsers']);
            this.homeModel.setTrends(data['trends']);
            this.homeModel.setPage(page);
            this.homeModel.setLikes(data['likes']);
            this.homeModel.setFollowings(data['followings']);


            if (data['next'] !== undefined) {
                this.homeModel.setNext(data['next']);
            }

            if (data['previous'] !== undefined) {
                this.homeModel.setPrevious(data['previous']);
            }
        });
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
                    this.homePrivateComponent.updateVideos();
                });
        });

        this.addEventListener('click', '.action-dislike', (event) => {
            var id = event.target.getAttribute('item');
            this.homePrivateComponent.videosServices.dislike(id)
                .fail(() => {
                    alert('Error: dislike')
                })
                .always(() => {
                    this.homePrivateComponent.updateVideos();
                });
        });

        this.addEventListener('click', '.action-follow', (event) => {
            var username = event.target.getAttribute('item');
            this.homePrivateComponent.followerServices.follow(username)
                .fail(() => {
                    alert('Error: follow')
                })
                .always(() => {
                    this.homePrivateComponent.updateVideos();
                });
        });

        this.addEventListener('click', '.action-unfollow', (event) => {
            var username = event.target.getAttribute('item');
            this.homePrivateComponent.followerServices.unfollow(username)
                .fail(() => {
                    alert('Error: follow')
                })
                .always(() => {
                    this.homePrivateComponent.updateVideos();
                });
        });

    }

}