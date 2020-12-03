class HomeModel extends Fronty.Model {
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

    setVideos(videos) {
        this.set((self) => {
            self.videos = videos;
        });
    }

    setNext(next) {
        this.set((self) => {
            self.next = next;
        });
    }

    setPrevious(previous) {
        this.set((self) => {
            self.previous = previous;
        });
    }

    setTopUsers(topUsers) {
        this.set((self) => {
            self.topUsers = topUsers;
        });
    }

    setTrends(trends) {
        this.set((self) => {
            self.trends = trends;
        });
    }


}