class HomePublicComponent extends Fronty.ModelComponent {
    constructor(homeModel, userModel, router) {
        super(Handlebars.templates.home_public, homeModel, "maincontent", null);

        this.homeModel = homeModel;

        this.userModel = userModel;
        this.addModel('user', userModel);

        this.router = router;

        this.homeService = new HomeService();

        this.addChildComponent(this._createTopUsersBarComponent());

    }

    onStart(){
        this.updateVideos();
    }

    updateVideos(){
        this.homeService.getPublicHomePage(0).then((data)=>{

            //var likes = data['likes'];
            //var followings = data['followings'];

            this.homeModel.setVideos(data['videos']);
            this.homeModel.setNext(data['next']);
            this.homeModel.setPrevious(data['previous']);
            this.homeModel.setTopUsers(data['topUsers']);
            this.homeModel.setTopUsers(data['topUsers']);
            this.homeModel.setTrends(data['trends']);

        });
    }



    _createTopUsersBarComponent() {
        var topUsers = new Fronty.ModelComponent(Handlebars.templates.topUsers, this.homeModel, 'topUsers');



        return topUsers;
    }

    // Override
    /*createChildModelComponent(className, element, id, modelItem) {
        return new HomePublicRowComponent(modelItem, this.userModel, this.router, this);
    }*/


}

/*
class HomePublicRowComponent extends Fronty.ModelComponent {
    constructor(postModel, userModel, router, postsComponent) {
        super(Handlebars.templates.postrow, postModel, null, null);

        this.postsComponent = postsComponent;

        this.userModel = userModel;
        this.addModel('user', userModel); // a secondary model

        this.router = router;

        this.addEventListener('click', '.remove-button', (event) => {
            if (confirm(I18n.translate('Are you sure?'))) {
                var postId = event.target.getAttribute('item');
                this.postsComponent.postsService.deletePost(postId)
                    .fail(() => {
                        alert('post cannot be deleted')
                    })
                    .always(() => {
                        this.postsComponent.updatePosts();
                    });
            }
        });

        this.addEventListener('click', '.edit-button', (event) => {
            var postId = event.target.getAttribute('item');
            this.router.goToPage('edit-post?id=' + postId);
        });
    }

}
 */