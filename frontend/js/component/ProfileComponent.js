class ProfileComponent extends Fronty.ModelComponent {
    constructor(userModel, router){
        super(Handlebars.templates.profile, userModel, "maincontent", null);

        //Models
        this.userModel = userModel;
        this.profileModel = new ProfileModel();
        this.addModel("profile", this.profileModel);

        //Router
        this.router = router;

        //Service
        this.followerServices = new FollowerService();
        this.userServices = new UserService();

        this.addEventListener('click', '.action-login', () => {
            $('#modallogin').modal('show');
        });

        this.addEventListener('click', '.action-upload', () => {
            $('#modalupload').modal('show');
        });

        this.addEventListener('click', '.action-follow', (event) => {
            var username = event.target.getAttribute('item');
            this.followerServices.follow(username)
                .fail(() => {
                    alert('Error: follow')
                })
                .always(() => {
                    this.loadProfile(username);
                });
        });

        this.addEventListener('click', '.action-unfollow', (event) => {
            var username = event.target.getAttribute('item');
            this.followerServices.unfollow(username)
                .fail(() => {
                    alert('Error: unfollow')
                })
                .always(() => {
                    this.loadProfile(username);
                });
        });

    }

    onStart(){
        var username = this.router.getRouteQueryParam('username');
        this.loadProfile(username);
    }

    loadProfile(username){
        if(username !=null){
            this.userServices.getProfile(username)
                .then((data) => {
                    this.profileModel.setProfile(data);
            });
        }
    }
}