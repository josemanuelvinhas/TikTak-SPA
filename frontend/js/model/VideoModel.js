class VideoModel extends Fronty.Model {

    constructor(id, videoName, videoDescription, videoDate, author, nLikes) {
        super('Video Model');

        if(id){
            this.id = id;
        }

        if(videoName){
            this.videoName = videoName;
        }

        if(videoDescription){
            this.videoDescription = videoDescription;
        }

        if(videoDate){
            this.videoDate = videoDate;
        }

        if(id){
            this.author = author;
        }
        if(id){
            this.nLikes = nLikes;
        }
    }
}