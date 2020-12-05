class VideoComponent extends Fronty.ModelComponent {
    constructor(userModel, router) {
        super(Handlebars.templates.video, userModel, "maincontent", null);

        //Models
        this.userModel = userModel;
        this.videoModel = new VideoModel();
        this.addModel('video', this.videoModel)

        //Router
        this.router = router;

        //Service
        this.videosServices = new VideosService();
        this.followerServices = new FollowerService();

        this.addEventListener('click', '.action-delete', (event) => {
            var id = event.target.getAttribute('item');
            this.videosServices.deleteVideo(id)
                .fail(() => {
                    alert('Error: delete')
                })
                .always(() => {
                    this.router.goToPage('home');
                });
        });

        this.addEventListener('click', '.action-follow', (event) => {
            var username = event.target.getAttribute('item');
            this.followerServices.follow(username)
                .fail(() => {
                    alert('Error: follow')
                })
                .always(() => {
                    this.loadVideo(this.videoModel.id);
                });
        });

        this.addEventListener('click', '.action-unfollow', (event) => {
            var username = event.target.getAttribute('item');
            this.followerServices.unfollow(username)
                .fail(() => {
                    alert('Error: unfollow')
                })
                .always(() => {
                    this.loadVideo(this.videoModel.id);
                });
        });

        this.addEventListener('click', '.action-login', () => {
            $('#modallogin').modal('show');
        });

        this.addEventListener('click', '.action-like', (event) => {
            var id = event.target.getAttribute('item');
            this.videosServices.like(id)
                .fail(() => {
                    alert('Error: like')
                })
                .always(() => {
                    this.loadVideo(id);
                });
        });

        this.addEventListener('click', '.action-dislike', (event) => {
            var id = event.target.getAttribute('item');
            this.videosServices.dislike(id)
                .fail(() => {
                    alert('Error: dislike')
                })
                .always(() => {
                    this.loadVideo(id);
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

    onStart(){
        var selectedId = this.router.getRouteQueryParam('id');
        this.loadVideo(selectedId);
    }

    loadVideo(selectedId){
        if(selectedId != null){
            this.videosServices.getVideo(selectedId)
                .then((data) =>{
                    this.videoModel.setVideo(data);
                });
        }
    }
}