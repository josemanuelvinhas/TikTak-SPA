class HomeModel extends Fronty.model {
    constructor() {
        super('HomeModel');

        this.videos = [];
        this.next = '';
        this.previous = '';
        this.topUsers = [];
        this.trends = [];
    }

    setVideos(videos) {
        for (var video in videos) {
            this.videos.push(new VideoModel(
                video['id'],
                video['videoname'],
                video['videodescription'],
                video['videodate'],
                video['author'],
                video['nlikes']
            ));
        }
    }

    setNext(next){
        this.next = next;
    }

    setPrevious(previous){
        this.previous = previous;
    }

    setTopUsers(){

    }
}