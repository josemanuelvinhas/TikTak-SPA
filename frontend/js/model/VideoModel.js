class VideoModel extends Fronty.Model {

    constructor(id, videoName, videoDescription, videoDate, author, nlikes) {
        super('Video Model');

        this.id = id;

        this.videoname = videoName;

        this.videodescription = videoDescription;

        this.videodate = videoDate;

        this.author = author;

        this.nlikes = nlikes;

    }

    setVideo(data) {
        this.set((self) => {
            self.id = data["video"]["id"];
            self.videoname = data["video"]["videoname"];
            self.videodescription = data["video"]["videodescription"];
            self.videodate = data["video"]["videodate"];
            self.author = data["video"]["author"];
            self.nlikes = data["video"]["nlikes"];

            if (data["like"] !== undefined){
                self.like = data["like"];
            }else{
                self.like = false;
            }

            if (data["following"] !== undefined){
                self.following = data["following"];
            }else{
                self.following = false;
            }

        });
    }

}