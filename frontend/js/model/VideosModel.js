class VideosModel extends Fronty.Model {
    constructor() {
        super('VideosModel');

        this.videos = [];
        this.next = false;
        this.previous = false;
        this.topUsers = [];
        this.trends = [];
        this.likes = [];
        this.followings = [];
        this.page = 0;
        this.hashtag = '';

    }

    setVideos(videos) {
        this.set((self) => {
            self.videos = [];
            for (var i = 0; i < videos.length; i++) {
                self.videos.push(new VideoModel(
                    videos[i]['id'],
                    videos[i]['videoname'],
                    videos[i]['videodescription'],
                    videos[i]['videodate'],
                    videos[i]['author'],
                    videos[i]['nlikes']
                ));
            }
        });

    }

    setPage(page) {
        this.set((self) => {
            self.page = page;
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

    setLikes(likes) {
        this.set((self) => {
            self.likes = likes;
        });
    }

    setFollowings(followings) {
        this.set((self) => {
            self.followings = followings;
        });
    }

    setHashtag(hashtag) {
        this.set((self) => {
            self.hashtag = hashtag;
        });
    }


}