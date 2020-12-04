class SearchComponent extends Fronty.ModelComponent {
    constructor(userModel, homeModel, router) {
        super(Handlebars.templates.search, homeModel, "maincontent", null);

        //Models
        this.homeModel = homeModel;
        this.userModel = userModel;
        this.addModel('user', userModel);

        //Router
        this.router = router;

        //Service
        this.videosServices = new VideosService();
        this.followerServices = new FollowerService();

        //Childs
        this.addChildComponent(this._createTopUsersBarComponent());
        this.addChildComponent(this._createTrendsBarComponent());
    }

    onStart() {
        var page = this.router.getRouteQueryParam('page');
        var hashtag = this.router.getRouteQueryParam('hashtag');
        this.updateSearch(hashtag,page);
    }

    updateSearch(hashtag, page) {

        if (page == null) {
            page = 0;
        }
        if (hashtag == null) {
            hashtag = '';
        }

        this.videosServices.search(hashtag, page).then((data) => {

            this.homeModel.setVideos(data['videos']);
            this.homeModel.setTopUsers(data['topUsers']);
            this.homeModel.setTrends(data['trends']);
            this.homeModel.setPage(page);

            this.homeModel.setHashtag(hashtag);

            if (data['likes'] !== undefined) {
                this.homeModel.setLikes(data['likes']);
            }

            if (data['followings'] !== undefined) {
                this.homeModel.setFollowings(data['followings']);
            }

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
        return new SearchRowComponent(modelItem, this.homeModel, this.userModel, this.router, this);
    }


}


class SearchRowComponent extends Fronty.ModelComponent {
    constructor(videoModel, homeModel, userModel, router, searchComponent) {
        super(Handlebars.templates.search_row, videoModel, null, null);

        this.searchComponent = searchComponent;

        this.userModel = userModel;
        this.addModel('user', userModel);
        this.homeModel = homeModel;
        this.addModel('home', homeModel);

        this.router = router;


        this.addEventListener('click', '#favLogin', () => {
            $('#modallogin').modal('show');
        });

        this.addEventListener('click', '#followLogin', () => {
            $('#modallogin').modal('show');
        });

        this.addEventListener('click', '.action-like', (event) => {
            var id = event.target.getAttribute('item');
            this.searchComponent.videosServices.like(id)
                .fail(() => {
                    alert('Error: like')
                })
                .always(() => {
                    this.searchComponent.updateSearch();
                });
        });

        this.addEventListener('click', '.action-dislike', (event) => {
            var id = event.target.getAttribute('item');
            this.searchComponent.videosServices.dislike(id)
                .fail(() => {
                    alert('Error: dislike')
                })
                .always(() => {
                    this.searchComponent.updateSearch();
                });
        });

        this.addEventListener('click', '.action-follow', (event) => {
            var username = event.target.getAttribute('item');
            this.searchComponent.followerServices.follow(username)
                .fail(() => {
                    alert('Error: follow')
                })
                .always(() => {
                    this.searchComponent.updateSearch();
                });
        });

        this.addEventListener('click', '.action-unfollow', (event) => {
            var username = event.target.getAttribute('item');
            this.searchComponent.followerServices.unfollow(username)
                .fail(() => {
                    alert('Error: follow')
                })
                .always(() => {
                    this.searchComponent.updateSearch();
                });
        });

    }

}