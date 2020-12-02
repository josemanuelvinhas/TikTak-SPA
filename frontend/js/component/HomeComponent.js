class HomeComponent extends Fronty.ModelComponent {
    constructor(homeModel) {
        super();

        this.homeModel = homeModel;

        this.homeService = new HomeService();

    }

    onStart(){
        this.updateVideos();
    }

    updateVideos(){
        this.homeService.getPublicHomePage(0).then((data)=>{
            //var likes = data['likes'];
            //var followings = data['followings'];
            var next = data['next'];
            var previous = data['previous'];
            var topUsers = data['topUsers']
            var trends = data['trends'];

            this.homeModel.setVideos(data['videos']);

        })
    }
}