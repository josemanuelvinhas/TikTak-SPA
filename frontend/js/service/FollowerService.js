class FollowerService {
    constructor() {
    }

    follow(username){
        return $.ajax({
            url: AppConfig.backendServer+'/rest/follower/' + username,
            method: 'POST'
        });
    }

    unfollow(username){
        return $.ajax({
            url: AppConfig.backendServer +'/rest/follower/' + username,
            method: 'DELETE'
        });
    }

}