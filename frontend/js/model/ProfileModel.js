class ProfileModel extends Fronty.Model {
    constructor() {
        super("Profile Model");
    }

    setProfile(data){
        this.set((self) => {

            self.user = data["user"];
            self.videos = data["videos"];
            self.nVideos = data["videos"].length;

            if (data["following"] !== undefined){
                self.following = data["following"];
            }else{
                self.following = false;
            }

        });
    }
}