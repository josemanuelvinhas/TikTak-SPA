class HomeService {
    constructor() {
    }

    getPrivateHomePage(username, page){
        return $.get(AppConfig.backendServer+'/rest/home/'+ username + '/' + page);
    }

    getPublicHomePage(page){
        return $.get(AppConfig.backendServer+'/rest/home/'+ page);
    }
    
}